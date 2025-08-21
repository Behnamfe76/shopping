<?php

namespace App\Listeners\CustomerSegment;

use App\Events\CustomerSegment\CustomerSegmentActivated;
use App\Events\CustomerSegment\CustomerSegmentDeactivated;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class NotifySegmentStatusChange implements ShouldQueue
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
        try {
            if ($event instanceof CustomerSegmentActivated) {
                $this->handleSegmentActivated($event);
            } elseif ($event instanceof CustomerSegmentDeactivated) {
                $this->handleSegmentDeactivated($event);
            }
        } catch (\Exception $e) {
            Log::error('Error notifying segment status change: ' . $e->getMessage());
        }
    }

    /**
     * Handle segment activated event.
     */
    private function handleSegmentActivated(CustomerSegmentActivated $event): void
    {
        $segment = $event->segment;
        
        Log::info("Segment activated: {$segment->name}");
        
        // Here you would typically send notifications
        // For example:
        // - Send email notification to segment owner
        // - Send notification to admin users
        // - Update notification preferences
        // - Send webhook notifications
    }

    /**
     * Handle segment deactivated event.
     */
    private function handleSegmentDeactivated(CustomerSegmentDeactivated $event): void
    {
        $segment = $event->segment;
        
        Log::info("Segment deactivated: {$segment->name}");
        
        // Here you would typically send notifications
        // For example:
        // - Send email notification to segment owner
        // - Send notification to admin users
        // - Update notification preferences
        // - Send webhook notifications
    }
}
