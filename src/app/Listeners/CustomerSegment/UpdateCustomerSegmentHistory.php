<?php

namespace App\Listeners\CustomerSegment;

use App\Events\CustomerSegment\CustomerAddedToSegment;
use App\Events\CustomerSegment\CustomerRemovedFromSegment;
use App\Models\CustomerSegmentHistory;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class UpdateCustomerSegmentHistory implements ShouldQueue
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
            if ($event instanceof CustomerAddedToSegment) {
                $this->handleCustomerAdded($event);
            } elseif ($event instanceof CustomerRemovedFromSegment) {
                $this->handleCustomerRemoved($event);
            }
        } catch (\Exception $e) {
            Log::error('Error updating customer segment history: ' . $e->getMessage());
        }
    }

    /**
     * Handle customer added to segment event.
     */
    private function handleCustomerAdded(CustomerAddedToSegment $event): void
    {
        $segment = $event->segment;
        $customerId = $event->customerId;
        
        // Create history record
        CustomerSegmentHistory::create([
            'customer_segment_id' => $segment->id,
            'customer_id' => $customerId,
            'action' => 'added',
            'performed_by' => auth()->id(),
            'metadata' => [
                'segment_name' => $segment->name,
                'segment_type' => $segment->type,
            ],
        ]);
        
        Log::info("Customer {$customerId} added to segment {$segment->name}");
    }

    /**
     * Handle customer removed from segment event.
     */
    private function handleCustomerRemoved(CustomerRemovedFromSegment $event): void
    {
        $segment = $event->segment;
        $customerId = $event->customerId;
        
        // Create history record
        CustomerSegmentHistory::create([
            'customer_segment_id' => $segment->id,
            'customer_id' => $customerId,
            'action' => 'removed',
            'performed_by' => auth()->id(),
            'metadata' => [
                'segment_name' => $segment->name,
                'segment_type' => $segment->type,
            ],
        ]);
        
        Log::info("Customer {$customerId} removed from segment {$segment->name}");
    }
}
