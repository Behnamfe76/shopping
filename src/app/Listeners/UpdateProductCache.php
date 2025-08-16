<?php

namespace Fereydooni\Shopping\app\Listeners;

use Fereydooni\Shopping\app\Events\ProductCreated;
use Fereydooni\Shopping\app\Events\ProductUpdated;
use Fereydooni\Shopping\app\Events\ProductDeleted;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class UpdateProductCache implements ShouldQueue
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
    public function handle($event): void
    {
        $product = $event->product;

        // Clear product-specific caches
        $this->clearProductCaches($product);

        // Clear related caches
        $this->clearRelatedCaches($product);

        Log::info('Product cache updated', [
            'product_id' => $product->id,
            'event' => get_class($event)
        ]);
    }

    /**
     * Clear product-specific caches.
     */
    private function clearProductCaches($product): void
    {
        $cacheKeys = [
            "product.{$product->id}",
            "product.slug.{$product->slug}",
            "product.sku.{$product->sku}",
            "product.{$product->id}.analytics",
            "product.{$product->id}.media",
            "product.{$product->id}.variants",
            "product.{$product->id}.reviews",
        ];

        foreach ($cacheKeys as $key) {
            Cache::forget($key);
        }
    }

    /**
     * Clear related caches.
     */
    private function clearRelatedCaches($product): void
    {
        // Clear category caches
        if ($product->category_id) {
            Cache::forget("category.{$product->category_id}.products");
            Cache::forget("category.{$product->category_id}.product_count");
        }

        // Clear brand caches
        if ($product->brand_id) {
            Cache::forget("brand.{$product->brand_id}.products");
            Cache::forget("brand.{$product->brand_id}.product_count");
        }

        // Clear general product caches
        $generalCacheKeys = [
            'products.all',
            'products.active',
            'products.featured',
            'products.in_stock',
            'products.top_selling',
            'products.most_viewed',
            'products.best_rated',
            'products.new_arrivals',
            'products.on_sale',
        ];

        foreach ($generalCacheKeys as $key) {
            Cache::forget($key);
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed($event, \Throwable $exception): void
    {
        Log::error('Failed to update product cache', [
            'product_id' => $event->product->id,
            'error' => $exception->getMessage()
        ]);
    }
}
