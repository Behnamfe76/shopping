<?php

namespace Fereydooni\Shopping\app\Listeners;

use Fereydooni\Shopping\app\Events\ProductVariantStockUpdated;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class UpdateProductInventoryCache implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Handle the event.
     */
    public function handle(ProductVariantStockUpdated $event): void
    {
        $variant = $event->variant;

        // Clear inventory-related caches
        \Cache::forget("product_inventory_{$variant->product_id}");
        \Cache::forget("product_stock_{$variant->product_id}");
        \Cache::forget("product_variants_in_stock_{$variant->product_id}");
        \Cache::forget("product_variants_out_of_stock_{$variant->product_id}");
        \Cache::forget("product_variants_low_stock_{$variant->product_id}");

        // Clear variant-specific inventory cache
        \Cache::forget("variant_inventory_{$variant->id}");
        \Cache::forget("variant_stock_{$variant->id}");

        // Clear global inventory caches
        \Cache::forget('inventory_alerts');
        \Cache::forget('low_stock_variants');
        \Cache::forget('out_of_stock_variants');
    }
}
