<?php

namespace App\Listeners\CustomerSegment;

use App\Events\CustomerSegment\CustomerSegmentCalculated;
use App\Events\CustomerSegment\CustomerSegmentCreated;
use App\Events\CustomerSegment\CustomerSegmentUpdated;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class UpdateSegmentAnalytics implements ShouldQueue
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
            if ($event instanceof CustomerSegmentCreated) {
                $this->handleSegmentCreated($event);
            } elseif ($event instanceof CustomerSegmentUpdated) {
                $this->handleSegmentUpdated($event);
            } elseif ($event instanceof CustomerSegmentCalculated) {
                $this->handleSegmentCalculated($event);
            }
        } catch (\Exception $e) {
            Log::error('Error updating segment analytics: ' . $e->getMessage());
        }
    }

    /**
     * Handle segment created event.
     */
    private function handleSegmentCreated(CustomerSegmentCreated $event): void
    {
        $segment = $event->segment;
        
        // Update analytics for new segment
        Log::info("Segment analytics updated for new segment: {$segment->name}");
        
        // Here you would typically update analytics cache, metrics, etc.
        // For example:
        // - Update segment count metrics
        // - Update segment type distribution
        // - Update segment growth trends
    }

    /**
     * Handle segment updated event.
     */
    private function handleSegmentUpdated(CustomerSegmentUpdated $event): void
    {
        $segment = $event->segment;
        
        // Update analytics for updated segment
        Log::info("Segment analytics updated for segment: {$segment->name}");
        
        // Here you would typically update analytics cache, metrics, etc.
        // For example:
        // - Update segment performance metrics
        // - Update segment criteria analytics
        // - Update segment status analytics
    }

    /**
     * Handle segment calculated event.
     */
    private function handleSegmentCalculated(CustomerSegmentCalculated $event): void
    {
        $segment = $event->segment;
        $customerCount = $event->customerCount;
        
        // Update analytics for calculated segment
        Log::info("Segment analytics updated for calculated segment: {$segment->name} with {$customerCount} customers");
        
        // Here you would typically update analytics cache, metrics, etc.
        // For example:
        // - Update customer count metrics
        // - Update segment performance analytics
        // - Update segment growth analytics
        // - Update segment overlap analytics
    }
}
