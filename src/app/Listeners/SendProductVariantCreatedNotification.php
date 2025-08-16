<?php

namespace Fereydooni\Shopping\app\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Fereydooni\Shopping\app\Events\ProductVariantCreated;

class SendProductVariantCreatedNotification implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Handle the event.
     */
    public function handle(ProductVariantCreated $event): void
    {
        $variant = $event->variant;

        // Send notification to admin
        // This is a placeholder implementation
        // In a real scenario, you would send actual notifications

        \Log::info('Product variant created: ' . $variant->sku, [
            'variant_id' => $variant->id,
            'product_id' => $variant->product_id,
            'sku' => $variant->sku,
            'price' => $variant->price,
            'stock' => $variant->stock,
        ]);
    }
}
