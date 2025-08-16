<?php

namespace Fereydooni\Shopping\app\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;
use Fereydooni\Shopping\app\Events\OrderStatusHistoryCreated;
use Fereydooni\Shopping\app\Events\OrderStatusChanged;

class LogStatusChange implements ShouldQueue
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
    public function handle(OrderStatusHistoryCreated $event): void
    {
        $history = $event->history;

        Log::info('Order status history created', [
            'history_id' => $history->id,
            'order_id' => $history->order_id,
            'old_status' => $history->old_status,
            'new_status' => $history->new_status,
            'changed_by' => $history->changed_by,
            'changed_at' => $history->changed_at?->toISOString(),
            'note' => $history->note,
            'is_system_change' => $history->is_system_change,
            'change_type' => $history->change_type,
            'change_category' => $history->change_category,
            'ip_address' => $history->ip_address,
            'user_agent' => $history->user_agent,
        ]);
    }

    /**
     * Handle order status changed event.
     */
    public function handleOrderStatusChanged(OrderStatusChanged $event): void
    {
        $order = $event->order;
        $history = $event->history;

        Log::info('Order status changed', [
            'order_id' => $order->id,
            'old_status' => $event->oldStatus,
            'new_status' => $event->newStatus,
            'history_id' => $history->id,
            'changed_by' => $history->changed_by,
            'changed_at' => $history->changed_at?->toISOString(),
            'note' => $history->note,
            'is_system_change' => $history->is_system_change,
            'change_type' => $history->change_type,
            'change_category' => $history->change_category,
            'order_total' => $order->total_amount,
            'order_currency' => $order->currency,
        ]);
    }

    /**
     * Handle a job failure.
     */
    public function failed(OrderStatusHistoryCreated $event, \Throwable $exception): void
    {
        Log::error('Failed to log status change', [
            'history_id' => $event->history->id,
            'order_id' => $event->history->order_id,
            'error' => $exception->getMessage(),
            'trace' => $exception->getTraceAsString(),
        ]);
    }
}
