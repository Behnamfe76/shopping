<?php

namespace App\Events\CustomerSegment;

use App\Models\CustomerSegment;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CustomerRemovedFromSegment
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public CustomerSegment $segment;

    public int $customerId;

    /**
     * Create a new event instance.
     */
    public function __construct(CustomerSegment $segment, int $customerId)
    {
        $this->segment = $segment;
        $this->customerId = $customerId;
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
            'customer_id' => $this->customerId,
            'removed_at' => now(),
        ];
    }
}
