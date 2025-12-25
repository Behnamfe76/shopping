<?php

namespace Fereydooni\Shopping\app\Listeners;

use Fereydooni\Shopping\app\Events\ProductVariantOutOfStock;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendOutOfStockNotification implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Handle the event.
     */
    public function handle(ProductVariantOutOfStock $event): void
    {
        $variant = $event->variant;

        // Send out of stock notification
        // This is a placeholder implementation
        // In a real scenario, you would send actual notifications

        \Log::error('Product variant out of stock alert', [
            'variant_id' => $variant->id,
            'sku' => $variant->sku,
            'product_id' => $variant->product_id,
            'product_title' => $variant->product->title ?? 'Unknown Product',
        ]);

        // You could send email notifications, Slack messages, etc.
        // Example: Mail::to('admin@example.com')->send(new OutOfStockAlert($variant));
    }
}
