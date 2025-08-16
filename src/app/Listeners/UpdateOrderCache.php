<?php

namespace Fereydooni\Shopping\app\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Fereydooni\Shopping\app\Events\OrderCreated;
use Fereydooni\Shopping\app\Events\OrderUpdated;
use Fereydooni\Shopping\app\Events\OrderCancelled;

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
     * Handle order created event.
     */
    public function handleOrderCreated(OrderCreated $event): void
    {
        $order = $event->order;

        // Clear relevant caches
        $this->clearOrderCaches($order);

        // Warm up new caches
        $this->warmOrderCaches($order);

        \Log::info("Order cache updated for new order #{$order->id}");
    }

    /**
     * Handle order updated event.
     */
    public function handleOrderUpdated(OrderUpdated $event): void
    {
        $order = $event->order;

        // Clear relevant caches
        $this->clearOrderCaches($order);

        // Warm up updated caches
        $this->warmOrderCaches($order);

        \Log::info("Order cache updated for modified order #{$order->id}");
    }

    /**
     * Handle order cancelled event.
     */
    public function handleOrderCancelled(OrderCancelled $event): void
    {
        $order = $event->order;

        // Clear relevant caches
        $this->clearOrderCaches($order);

        \Log::info("Order cache cleared for cancelled order #{$order->id}");
    }

    /**
     * Clear order-related caches
     */
    private function clearOrderCaches($order): void
    {
        // Clear order-specific cache
        \Cache::forget("order.{$order->id}");

        // Clear user orders cache
        \Cache::forget("user.{$order->user_id}.orders");

        // Clear order count caches
        \Cache::forget('orders.count.total');
        \Cache::forget("orders.count.status.{$order->status}");
        \Cache::forget("orders.count.user.{$order->user_id}");

        // Clear revenue caches
        \Cache::forget('orders.revenue.total');
        \Cache::forget("orders.revenue.user.{$order->user_id}");
    }

    /**
     * Warm up order-related caches
     */
    private function warmOrderCaches($order): void
    {
        // Cache order data
        \Cache::put("order.{$order->id}", $order, now()->addHours(24));

        // Cache user orders (would need to fetch and cache)
        // $userOrders = Order::where('user_id', $order->user_id)->get();
        // \Cache::put("user.{$order->user_id}.orders", $userOrders, now()->addHours(1));
    }
}
