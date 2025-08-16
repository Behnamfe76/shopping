<?php

namespace Fereydooni\Shopping\app\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Fereydooni\Shopping\app\Events\ProductMetaCreated;
use Fereydooni\Shopping\app\Events\ProductMetaUpdated;
use Fereydooni\Shopping\app\Events\ProductMetaDeleted;
use Illuminate\Support\Facades\Cache;

class UpdateProductMetaCache implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Handle the event.
     */
    public function handle(ProductMetaCreated|ProductMetaUpdated|ProductMetaDeleted $event): void
    {
        $productMeta = $event->productMeta;

        // Clear product meta cache
        $this->clearProductMetaCache($productMeta->product_id);

        // Clear meta keys cache
        $this->clearMetaKeysCache();

        // Clear meta types cache
        $this->clearMetaTypesCache();

        // Clear meta values cache for this key
        $this->clearMetaValuesCache($productMeta->meta_key);
    }

    /**
     * Clear product meta cache
     */
    private function clearProductMetaCache(int $productId): void
    {
        Cache::forget("product_meta_{$productId}");
        Cache::forget("product_meta_public_{$productId}");
        Cache::forget("product_meta_private_{$productId}");
    }

    /**
     * Clear meta keys cache
     */
    private function clearMetaKeysCache(): void
    {
        Cache::forget('product_meta_keys');
    }

    /**
     * Clear meta types cache
     */
    private function clearMetaTypesCache(): void
    {
        Cache::forget('product_meta_types');
    }

    /**
     * Clear meta values cache
     */
    private function clearMetaValuesCache(string $metaKey): void
    {
        Cache::forget("product_meta_values_{$metaKey}");
    }
}
