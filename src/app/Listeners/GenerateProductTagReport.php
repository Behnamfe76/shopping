<?php

namespace Fereydooni\Shopping\app\Listeners;

use Fereydooni\Shopping\app\Events\ProductTagCreated;
use Fereydooni\Shopping\app\Events\ProductTagUpdated;
use Fereydooni\Shopping\app\Events\ProductTagDeleted;
use Fereydooni\Shopping\app\Events\ProductTagBulkOperation;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class GenerateProductTagReport implements ShouldQueue
{
    use InteractsWithQueue;

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
    public function handle($event): void
    {
        // Generate tag report based on event type
        $this->generateTagReport($event);
    }

    /**
     * Generate tag report
     */
    private function generateTagReport($event): void
    {
        try {
            switch (get_class($event)) {
                case ProductTagBulkOperation::class:
                    $this->generateBulkOperationReport($event);
                    break;
                default:
                    $this->generateSingleTagReport($event);
                    break;
            }
        } catch (\Exception $e) {
            Log::error('Failed to generate product tag report', [
                'event' => get_class($event),
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Generate single tag report
     */
    private function generateSingleTagReport($event): void
    {
        $tag = $event->tag ?? null;

        if (!$tag) {
            return;
        }

        // Generate report for single tag operation
        $reportData = [
            'tag_id' => $tag->id,
            'tag_name' => $tag->name,
            'operation' => $this->getOperationType($event),
            'timestamp' => now(),
            'user_id' => auth()->id(),
        ];

        // Store or send report
        $this->storeReport($reportData);

        Log::info('Product tag report generated', $reportData);
    }

    /**
     * Generate bulk operation report
     */
    private function generateBulkOperationReport($event): void
    {
        $reportData = [
            'operation' => $event->operation,
            'tag_count' => count($event->tagIds),
            'results' => $event->results,
            'timestamp' => now(),
            'user_id' => auth()->id(),
        ];

        // Store or send report
        $this->storeReport($reportData);

        Log::info('Product tag bulk operation report generated', $reportData);
    }

    /**
     * Get operation type from event
     */
    private function getOperationType($event): string
    {
        switch (get_class($event)) {
            case ProductTagCreated::class:
                return 'created';
            case ProductTagUpdated::class:
                return 'updated';
            case ProductTagDeleted::class:
                return 'deleted';
            default:
                return 'unknown';
        }
    }

    /**
     * Store report data
     */
    private function storeReport(array $reportData): void
    {
        // Implementation for storing report data
        // This could save to database, send to external service, etc.
        Log::info('Storing tag report', $reportData);
    }
}
