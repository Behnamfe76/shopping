<?php

namespace Fereydooni\Shopping\app\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Fereydooni\Shopping\app\Events\ProductVariantCreated;
use Fereydooni\Shopping\app\Events\ProductVariantUpdated;
use Fereydooni\Shopping\app\Events\ProductVariantDeleted;

class UpdateProductVariantCache implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Handle the event.
     */
    public function handle($event): void
    {
        $variant = $event->variant;

        // Clear variant cache
        \Cache::forget("product_variant_{$variant->id}");
        \Cache::forget("product_variant_sku_{$variant->sku}");

        if ($variant->barcode) {
            \Cache::forget("product_variant_barcode_{$variant->barcode}");
        }

        // Clear product variants cache
        \Cache::forget("product_variants_{$variant->product_id}");
        \Cache::forget("product_variants_active_{$variant->product_id}");
        \Cache::forget("product_variants_in_stock_{$variant->product_id}");

        // Clear global caches
        \Cache::forget('product_variants_all');
        \Cache::forget('product_variants_active');
        \Cache::forget('product_variants_in_stock');
        \Cache::forget('product_variants_out_of_stock');
        \Cache::forget('product_variants_low_stock');
    }
}
