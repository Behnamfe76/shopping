<?php

namespace Fereydooni\Shopping\app\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Fereydooni\Shopping\app\Models\Order;

class OrderUpdated
{
    use Dispatchable, SerializesModels;

    public Order $order;
    public array $changes;

    /**
     * Create a new event instance.
     */
    public function __construct(Order $order, array $changes = [])
    {
        $this->order = $order;
        $this->changes = $changes;
    }

    /**
     * Get the channels the event should broadcast on.
     */
    public function broadcastOn(): array
    {
        return [];
    }
}
