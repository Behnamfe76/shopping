<?php

namespace Fereydooni\Shopping\app\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Fereydooni\Shopping\app\Models\Order;

class OrderPaid
{
    use Dispatchable, SerializesModels;

    public Order $order;
    public string $paymentMethod;

    /**
     * Create a new event instance.
     */
    public function __construct(Order $order, string $paymentMethod)
    {
        $this->order = $order;
        $this->paymentMethod = $paymentMethod;
    }

    /**
     * Get the channels the event should broadcast on.
     */
    public function broadcastOn(): array
    {
        return [];
    }
}
