<?php

namespace App\Listeners\CustomerSegment;

use App\Events\CustomerSegment\CustomerAddedToSegment;
use App\Events\CustomerSegment\CustomerRemovedFromSegment;
use App\Events\CustomerSegment\CustomerSegmentActivated;
use App\Events\CustomerSegment\CustomerSegmentCalculated;
use App\Events\CustomerSegment\CustomerSegmentCreated;
use App\Events\CustomerSegment\CustomerSegmentDeactivated;
use App\Events\CustomerSegment\CustomerSegmentDeleted;
use App\Events\CustomerSegment\CustomerSegmentRecalculated;
use App\Events\CustomerSegment\CustomerSegmentUpdated;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class LogSegmentActivity implements ShouldQueue
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
                $this->logSegmentCreated($event);
            } elseif ($event instanceof CustomerSegmentUpdated) {
                $this->logSegmentUpdated($event);
            } elseif ($event instanceof CustomerSegmentDeleted) {
                $this->logSegmentDeleted($event);
            } elseif ($event instanceof CustomerSegmentActivated) {
                $this->logSegmentActivated($event);
            } elseif ($event instanceof CustomerSegmentDeactivated) {
                $this->logSegmentDeactivated($event);
            } elseif ($event instanceof CustomerSegmentCalculated) {
                $this->logSegmentCalculated($event);
            } elseif ($event instanceof CustomerSegmentRecalculated) {
                $this->logSegmentRecalculated($event);
            } elseif ($event instanceof CustomerAddedToSegment) {
                $this->logCustomerAdded($event);
            } elseif ($event instanceof CustomerRemovedFromSegment) {
                $this->logCustomerRemoved($event);
            }
        } catch (\Exception $e) {
            Log::error('Error logging segment activity: ' . $e->getMessage());
        }
    }

    /**
     * Log segment created activity.
     */
    private function logSegmentCreated(CustomerSegmentCreated $event): void
    {
        $segment = $event->segment;
        
        Log::info("Customer segment created", [
            'segment_id' => $segment->id,
            'segment_name' => $segment->name,
            'segment_type' => $segment->type,
            'created_by' => auth()->id(),
            'created_at' => $segment->created_at,
        ]);
    }

    /**
     * Log segment updated activity.
     */
    private function logSegmentUpdated(CustomerSegmentUpdated $event): void
    {
        $segment = $event->segment;
        
        Log::info("Customer segment updated", [
            'segment_id' => $segment->id,
            'segment_name' => $segment->name,
            'updated_by' => auth()->id(),
            'updated_at' => $segment->updated_at,
        ]);
    }

    /**
     * Log segment deleted activity.
     */
    private function logSegmentDeleted(CustomerSegmentDeleted $event): void
    {
        $segment = $event->segment;
        
        Log::info("Customer segment deleted", [
            'segment_id' => $segment->id,
            'segment_name' => $segment->name,
            'deleted_by' => auth()->id(),
            'deleted_at' => now(),
        ]);
    }

    /**
     * Log segment activated activity.
     */
    private function logSegmentActivated(CustomerSegmentActivated $event): void
    {
        $segment = $event->segment;
        
        Log::info("Customer segment activated", [
            'segment_id' => $segment->id,
            'segment_name' => $segment->name,
            'activated_by' => auth()->id(),
            'activated_at' => now(),
        ]);
    }

    /**
     * Log segment deactivated activity.
     */
    private function logSegmentDeactivated(CustomerSegmentDeactivated $event): void
    {
        $segment = $event->segment;
        
        Log::info("Customer segment deactivated", [
            'segment_id' => $segment->id,
            'segment_name' => $segment->name,
            'deactivated_by' => auth()->id(),
            'deactivated_at' => now(),
        ]);
    }

    /**
     * Log segment calculated activity.
     */
    private function logSegmentCalculated(CustomerSegmentCalculated $event): void
    {
        $segment = $event->segment;
        $customerCount = $event->customerCount;
        
        Log::info("Customer segment calculated", [
            'segment_id' => $segment->id,
            'segment_name' => $segment->name,
            'customer_count' => $customerCount,
            'calculated_by' => auth()->id(),
            'calculated_at' => now(),
        ]);
    }

    /**
     * Log segment recalculated activity.
     */
    private function logSegmentRecalculated(CustomerSegmentRecalculated $event): void
    {
        Log::info("All customer segments recalculated", [
            'recalculated_by' => auth()->id(),
            'recalculated_at' => now(),
        ]);
    }

    /**
     * Log customer added to segment activity.
     */
    private function logCustomerAdded(CustomerAddedToSegment $event): void
    {
        $segment = $event->segment;
        $customerId = $event->customerId;
        
        Log::info("Customer added to segment", [
            'segment_id' => $segment->id,
            'segment_name' => $segment->name,
            'customer_id' => $customerId,
            'added_by' => auth()->id(),
            'added_at' => now(),
        ]);
    }

    /**
     * Log customer removed from segment activity.
     */
    private function logCustomerRemoved(CustomerRemovedFromSegment $event): void
    {
        $segment = $event->segment;
        $customerId = $event->customerId;
        
        Log::info("Customer removed from segment", [
            'segment_id' => $segment->id,
            'segment_name' => $segment->name,
            'customer_id' => $customerId,
            'removed_by' => auth()->id(),
            'removed_at' => now(),
        ]);
    }
}
