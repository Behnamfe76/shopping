<?php

namespace Fereydooni\Shopping\app\Listeners;

use Fereydooni\Shopping\app\Events\OrderTimelineGenerated;
use Fereydooni\Shopping\app\Services\OrderStatusHistoryService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Storage;

class GenerateTimelineReport implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Create the event listener.
     */
    public function __construct(
        protected OrderStatusHistoryService $orderStatusHistoryService
    ) {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(OrderTimelineGenerated $event): void
    {
        $order = $event->order;
        $timeline = $event->timeline;

        // Generate PDF report
        $pdfPath = $this->generatePdfReport($order, $timeline);

        // Generate CSV report
        $csvPath = $this->generateCsvReport($order, $timeline);

        // Store report metadata
        $this->storeReportMetadata($order, $pdfPath, $csvPath);

        // Send report to relevant users
        $this->sendReportToUsers($order, $pdfPath, $csvPath);
    }

    /**
     * Generate PDF report.
     */
    private function generatePdfReport($order, $timeline): string
    {
        $pdf = \PDF::loadView('shopping::reports.order-timeline', [
            'order' => $order,
            'timeline' => $timeline,
            'generated_at' => now(),
        ]);

        $filename = "order-{$order->id}-timeline-".now()->format('Y-m-d-H-i-s').'.pdf';
        $path = "reports/order-timelines/{$filename}";

        Storage::put($path, $pdf->output());

        return $path;
    }

    /**
     * Generate CSV report.
     */
    private function generateCsvReport($order, $timeline): string
    {
        $filename = "order-{$order->id}-timeline-".now()->format('Y-m-d-H-i-s').'.csv';
        $path = "reports/order-timelines/{$filename}";

        $csvData = [];
        $csvData[] = ['Event ID', 'Order ID', 'Old Status', 'New Status', 'Changed By', 'Changed At', 'Note', 'Change Type', 'Change Category'];

        foreach ($timeline as $event) {
            $csvData[] = [
                $event->id,
                $event->order_id,
                $event->old_status,
                $event->new_status,
                $event->changed_by,
                $event->changed_at?->format('Y-m-d H:i:s'),
                $event->note,
                $event->change_type,
                $event->change_category,
            ];
        }

        $csvContent = '';
        foreach ($csvData as $row) {
            $csvContent .= implode(',', array_map(function ($field) {
                return '"'.str_replace('"', '""', $field).'"';
            }, $row))."\n";
        }

        Storage::put($path, $csvContent);

        return $path;
    }

    /**
     * Store report metadata.
     */
    private function storeReportMetadata($order, string $pdfPath, string $csvPath): void
    {
        $metadata = [
            'order_id' => $order->id,
            'generated_at' => now()->toISOString(),
            'pdf_path' => $pdfPath,
            'csv_path' => $csvPath,
            'total_events' => $order->statusHistory()->count(),
            'report_type' => 'timeline',
            'generated_by' => auth()->id(),
        ];

        $metadataKey = "reports.order-{$order->id}.timeline.".now()->format('Y-m-d-H-i-s');
        Storage::put("reports/metadata/{$metadataKey}.json", json_encode($metadata));
    }

    /**
     * Send report to relevant users.
     */
    private function sendReportToUsers($order, string $pdfPath, string $csvPath): void
    {
        // Send to order owner
        if ($order->user && $order->user->email) {
            \Mail::send('shopping::emails.timeline-report', [
                'order' => $order,
                'pdfPath' => $pdfPath,
                'csvPath' => $csvPath,
            ], function ($message) use ($order, $pdfPath, $csvPath) {
                $message->to($order->user->email)
                    ->subject("Order #{$order->id} Timeline Report")
                    ->attach(Storage::path($pdfPath), ['as' => basename($pdfPath)])
                    ->attach(Storage::path($csvPath), ['as' => basename($csvPath)]);
            });
        }

        // Send to admin users
        $adminUsers = \App\Models\User::whereHas('roles', function ($query) {
            $query->where('name', 'admin');
        })->get();

        foreach ($adminUsers as $admin) {
            \Mail::send('shopping::emails.timeline-report-admin', [
                'order' => $order,
                'admin' => $admin,
                'pdfPath' => $pdfPath,
                'csvPath' => $csvPath,
            ], function ($message) use ($admin, $order, $pdfPath, $csvPath) {
                $message->to($admin->email)
                    ->subject("Order #{$order->id} Timeline Report - Admin")
                    ->attach(Storage::path($pdfPath), ['as' => basename($pdfPath)])
                    ->attach(Storage::path($csvPath), ['as' => basename($csvPath)]);
            });
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(OrderTimelineGenerated $event, \Throwable $exception): void
    {
        \Log::error('Failed to generate timeline report', [
            'order_id' => $event->order->id,
            'total_events' => $event->totalEvents,
            'error' => $exception->getMessage(),
            'trace' => $exception->getTraceAsString(),
        ]);
    }
}
