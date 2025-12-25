<?php

namespace Fereydooni\Shopping\app\Listeners;

use Fereydooni\Shopping\app\Events\ProductVariantLowStock;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendLowStockNotification implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Handle the event.
     */
    public function handle(ProductVariantLowStock $event): void
    {
        $variant = $event->variant;
        $currentStock = $event->currentStock;
        $threshold = $event->threshold;

        // Send low stock notification
        // This is a placeholder implementation
        // In a real scenario, you would send actual notifications

        \Log::warning('Product variant low stock alert', [
            'variant_id' => $variant->id,
            'sku' => $variant->sku,
            'product_id' => $variant->product_id,
            'current_stock' => $currentStock,
            'threshold' => $threshold,
            'product_title' => $variant->product->title ?? 'Unknown Product',
        ]);

        // You could send email notifications, Slack messages, etc.
        // Example: Mail::to('admin@example.com')->send(new LowStockAlert($variant, $currentStock, $threshold));
    }
}
