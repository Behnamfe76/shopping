<?php

namespace Fereydooni\Shopping\app\Events;

use Fereydooni\Shopping\app\Models\Order;
use Fereydooni\Shopping\app\Models\OrderStatusHistory;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class OrderStatusChanged
{
    use Dispatchable, SerializesModels;

    public Order $order;

    public OrderStatusHistory $history;

    public string $oldStatus;

    public string $newStatus;

    /**
     * Create a new event instance.
     */
    public function __construct(Order $order, OrderStatusHistory $history, string $oldStatus, string $newStatus)
    {
        $this->order = $order;
        $this->history = $history;
        $this->oldStatus = $oldStatus;
        $this->newStatus = $newStatus;
    }

    /**
     * Get the event's broadcast name.
     */
    public function broadcastAs(): string
    {
        return 'order.status-changed';
    }

    /**
     * Get the data to broadcast.
     */
    public function broadcastWith(): array
    {
        return [
            'order_id' => $this->order->id,
            'old_status' => $this->oldStatus,
            'new_status' => $this->newStatus,
            'changed_by' => $this->history->changed_by,
            'changed_at' => $this->history->changed_at?->toISOString(),
            'note' => $this->history->note,
            'is_system_change' => $this->history->is_system_change,
            'change_type' => $this->history->change_type,
            'change_category' => $this->history->change_category,
            'order_total' => $this->order->total_amount,
            'order_currency' => $this->order->currency,
        ];
    }
}
