<?php

namespace Fereydooni\Shopping\app\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Fereydooni\Shopping\app\Models\Order;

class OrderShipped
{
    use Dispatchable, SerializesModels;

    public Order $order;
    public ?string $trackingNumber;

    /**
     * Create a new event instance.
     */
    public function __construct(Order $order, ?string $trackingNumber = null)
    {
        $this->order = $order;
        $this->trackingNumber = $trackingNumber;
    }

    /**
     * Get the channels the event should broadcast on.
     */
    public function broadcastOn(): array
    {
        return [];
    }
}
