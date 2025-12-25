<?php

namespace App\Listeners;

use App\Events\PerformanceReportGenerated;
use App\Notifications\PerformanceReportGeneratedNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

class SendPerformanceReportNotification implements ShouldQueue
{
    use InteractsWithQueue;

    public $queue = 'notifications';

    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(PerformanceReportGenerated $event): void
    {
        try {
            Log::info('Sending performance report notification', [
                'report_type' => $event->reportType,
                'generated_by' => $event->generatedBy->id ?? 'system',
                'report_data' => $event->reportData,
            ]);

            // Get users who should receive this notification
            $recipients = $this->getNotificationRecipients($event);

            // Send notifications
            foreach ($recipients as $recipient) {
                $recipient->notify(new PerformanceReportGeneratedNotification(
                    $event->reportType,
                    $event->reportData,
                    $event->generatedBy
                ));
            }

            Log::info('Performance report notifications sent successfully', [
                'recipients_count' => count($recipients),
                'report_type' => $event->reportType,
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to send performance report notifications', [
                'error' => $e->getMessage(),
                'report_type' => $event->reportType,
                'trace' => $e->getTraceAsString(),
            ]);
        }
    }

    /**
     * Get users who should receive the notification
     */
    private function getNotificationRecipients(PerformanceReportGenerated $event): array
    {
        // This would typically query the database for users with appropriate permissions
        // For now, return an empty array - implement based on your user management system

        $recipients = [];

        // Example: Get users with report viewing permissions
        // $recipients = User::permission('view-performance-reports')->get();

        // Example: Get specific users based on report type
        switch ($event->reportType) {
            case 'summary':
                // $recipients = User::role('manager')->get();
                break;
            case 'detailed':
                // $recipients = User::role('analyst')->get();
                break;
            case 'trend':
                // $recipients = User::role('strategist')->get();
                break;
            default:
                // $recipients = User::permission('view-performance-reports')->get();
                break;
        }

        return $recipients;
    }

    /**
     * Handle a job failure.
     */
    public function failed(PerformanceReportGenerated $event, \Throwable $exception): void
    {
        Log::error('Failed to send performance report notification', [
            'event' => $event,
            'error' => $exception->getMessage(),
            'trace' => $exception->getTraceAsString(),
        ]);
    }
}
