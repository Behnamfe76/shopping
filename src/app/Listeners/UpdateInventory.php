<?php

namespace Fereydooni\Shopping\app\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Fereydooni\Shopping\app\Events\OrderCreated;
use Fereydooni\Shopping\app\Events\OrderCancelled;

class UpdateInventory implements ShouldQueue
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

        // Update inventory for each order item
        foreach ($order->orderItems as $item) {
            // Decrease stock quantity
            $product = $item->product;
            if ($product) {
                $product->decrement('stock_quantity', $item->quantity);

                // Check for low stock alert
                if ($product->stock_quantity <= $product->low_stock_threshold) {
                    // Send low stock alert
                    \Log::warning("Low stock alert for product #{$product->id}: {$product->stock_quantity} remaining");
                }
            }
        }

        \Log::info("Inventory updated for order #{$order->id}");
    }

    /**
     * Handle order cancelled event.
     */
    public function handleOrderCancelled(OrderCancelled $event): void
    {
        $order = $event->order;

        // Restore inventory for each order item
        foreach ($order->orderItems as $item) {
            $product = $item->product;
            if ($product) {
                $product->increment('stock_quantity', $item->quantity);
            }
        }

        \Log::info("Inventory restored for cancelled order #{$order->id}");
    }
}
