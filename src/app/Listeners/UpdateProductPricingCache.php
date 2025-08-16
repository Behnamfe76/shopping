<?php

namespace Fereydooni\Shopping\app\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Fereydooni\Shopping\app\Events\ProductVariantPriceUpdated;

class UpdateProductPricingCache implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Handle the event.
     */
    public function handle(ProductVariantPriceUpdated $event): void
    {
        $variant = $event->variant;

        // Clear pricing-related caches
        \Cache::forget("product_pricing_{$variant->product_id}");
        \Cache::forget("product_price_range_{$variant->product_id}");
        \Cache::forget("product_discounts_{$variant->product_id}");

        // Clear variant-specific pricing cache
        \Cache::forget("variant_pricing_{$variant->id}");
        \Cache::forget("variant_price_{$variant->id}");

        // Clear global pricing caches
        \Cache::forget('product_price_ranges');
        \Cache::forget('product_discounts');
        \Cache::forget('featured_products');
    }
}
