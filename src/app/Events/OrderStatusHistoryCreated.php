<?php

namespace Fereydooni\Shopping\app\Events;

use Fereydooni\Shopping\app\Models\OrderStatusHistory;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class OrderStatusHistoryCreated
{
    use Dispatchable, SerializesModels;

    public OrderStatusHistory $history;

    /**
     * Create a new event instance.
     */
    public function __construct(OrderStatusHistory $history)
    {
        $this->history = $history;
    }

    /**
     * Get the event's broadcast name.
     */
    public function broadcastAs(): string
    {
        return 'order-status-history.created';
    }

    /**
     * Get the data to broadcast.
     */
    public function broadcastWith(): array
    {
        return [
            'id' => $this->history->id,
            'order_id' => $this->history->order_id,
            'old_status' => $this->history->old_status,
            'new_status' => $this->history->new_status,
            'changed_by' => $this->history->changed_by,
            'changed_at' => $this->history->changed_at?->toISOString(),
            'note' => $this->history->note,
            'is_system_change' => $this->history->is_system_change,
            'change_type' => $this->history->change_type,
            'change_category' => $this->history->change_category,
        ];
    }
}
