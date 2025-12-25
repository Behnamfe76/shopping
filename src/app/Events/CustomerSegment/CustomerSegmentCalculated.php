<?php

namespace App\Events\CustomerSegment;

use App\Models\CustomerSegment;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CustomerSegmentCalculated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public CustomerSegment $segment;

    public int $customerCount;

    /**
     * Create a new event instance.
     */
    public function __construct(CustomerSegment $segment, int $customerCount)
    {
        $this->segment = $segment;
        $this->customerCount = $customerCount;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('customer-segments'),
        ];
    }

    /**
     * Get the data to broadcast.
     */
    public function broadcastWith(): array
    {
        return [
            'segment_id' => $this->segment->id,
            'segment_name' => $this->segment->name,
            'segment_type' => $this->segment->type,
            'customer_count' => $this->customerCount,
            'calculated_at' => now(),
        ];
    }
}
