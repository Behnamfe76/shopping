<?php

namespace Fereydooni\Shopping\app\Listeners;

use Fereydooni\Shopping\app\Events\OrderStatusChanged;
use Fereydooni\Shopping\app\Events\OrderStatusHistoryCreated;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Cache;

class UpdateOrderCache implements ShouldQueue
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
        $orderId = $history->order_id;

        // Clear order cache
        $this->clearOrderCache($orderId);

        // Clear order timeline cache
        $this->clearTimelineCache($orderId);

        // Clear analytics cache
        $this->clearAnalyticsCache();

        // Update order status cache
        $this->updateOrderStatusCache($orderId, $history->new_status);
    }

    /**
     * Handle order status changed event.
     */
    public function handleOrderStatusChanged(OrderStatusChanged $event): void
    {
        $order = $event->order;
        $orderId = $order->id;

        // Clear order cache
        $this->clearOrderCache($orderId);

        // Clear order timeline cache
        $this->clearTimelineCache($orderId);

        // Clear analytics cache
        $this->clearAnalyticsCache();

        // Update order status cache
        $this->updateOrderStatusCache($orderId, $event->newStatus);

        // Clear user orders cache
        if ($order->user_id) {
            $this->clearUserOrdersCache($order->user_id);
        }
    }

    /**
     * Clear order cache.
     */
    private function clearOrderCache(int $orderId): void
    {
        $cacheKeys = [
            "order.{$orderId}",
            "order.{$orderId}.details",
            "order.{$orderId}.status",
            "order.{$orderId}.history",
        ];

        foreach ($cacheKeys as $key) {
            Cache::forget($key);
        }
    }

    /**
     * Clear order timeline cache.
     */
    private function clearTimelineCache(int $orderId): void
    {
        $cacheKeys = [
            "order.{$orderId}.timeline",
            "order.{$orderId}.timeline.detailed",
        ];

        foreach ($cacheKeys as $key) {
            Cache::forget($key);
        }
    }

    /**
     * Clear analytics cache.
     */
    private function clearAnalyticsCache(): void
    {
        $cacheKeys = [
            'order-status-analytics',
            'order-status-analytics.daily',
            'order-status-analytics.weekly',
            'order-status-analytics.monthly',
            'order-status-frequency',
            'order-status-distribution',
        ];

        foreach ($cacheKeys as $key) {
            Cache::forget($key);
        }
    }

    /**
     * Update order status cache.
     */
    private function updateOrderStatusCache(int $orderId, string $newStatus): void
    {
        $cacheKey = "order.{$orderId}.status";
        Cache::put($cacheKey, $newStatus, now()->addHours(24));
    }

    /**
     * Clear user orders cache.
     */
    private function clearUserOrdersCache(int $userId): void
    {
        $cacheKeys = [
            "user.{$userId}.orders",
            "user.{$userId}.orders.pending",
            "user.{$userId}.orders.completed",
            "user.{$userId}.orders.cancelled",
        ];

        foreach ($cacheKeys as $key) {
            Cache::forget($key);
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(OrderStatusHistoryCreated $event, \Throwable $exception): void
    {
        \Log::error('Failed to update order cache', [
            'history_id' => $event->history->id,
            'order_id' => $event->history->order_id,
            'error' => $exception->getMessage(),
            'trace' => $exception->getTraceAsString(),
        ]);
    }
}
