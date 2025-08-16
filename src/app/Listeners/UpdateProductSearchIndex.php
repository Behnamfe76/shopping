<?php

namespace Fereydooni\Shopping\app\Listeners;

use Fereydooni\Shopping\app\Events\ProductCreated;
use Fereydooni\Shopping\app\Events\ProductUpdated;
use Fereydooni\Shopping\app\Events\ProductDeleted;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class UpdateProductSearchIndex implements ShouldQueue
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

        // Update search index based on event type
        switch (get_class($event)) {
            case ProductCreated::class:
                $this->addToSearchIndex($product);
                break;
            case ProductUpdated::class:
                $this->updateSearchIndex($product);
                break;
            case ProductDeleted::class:
                $this->removeFromSearchIndex($product);
                break;
        }

        Log::info('Product search index updated', [
            'product_id' => $product->id,
            'event' => get_class($event)
        ]);
    }

    /**
     * Add product to search index.
     */
    private function addToSearchIndex($product): void
    {
        // Implementation for adding product to search index
        // This could use Elasticsearch, Algolia, or other search services

        // Example implementation for Elasticsearch:
        // $searchData = [
        //     'id' => $product->id,
        //     'title' => $product->title,
        //     'description' => $product->description,
        //     'sku' => $product->sku,
        //     'category' => $product->category->name ?? '',
        //     'brand' => $product->brand->name ?? '',
        //     'price' => $product->price,
        //     'status' => $product->status,
        //     'is_active' => $product->is_active,
        //     'is_featured' => $product->is_featured,
        // ];
        //
        // Elasticsearch::index([
        //     'index' => 'products',
        //     'id' => $product->id,
        //     'body' => $searchData
        // ]);
    }

    /**
     * Update product in search index.
     */
    private function updateSearchIndex($product): void
    {
        // Implementation for updating product in search index

        // Example implementation for Elasticsearch:
        // $searchData = [
        //     'title' => $product->title,
        //     'description' => $product->description,
        //     'sku' => $product->sku,
        //     'category' => $product->category->name ?? '',
        //     'brand' => $product->brand->name ?? '',
        //     'price' => $product->price,
        //     'status' => $product->status,
        //     'is_active' => $product->is_active,
        //     'is_featured' => $product->is_featured,
        // ];
        //
        // Elasticsearch::update([
        //     'index' => 'products',
        //     'id' => $product->id,
        //     'body' => ['doc' => $searchData]
        // ]);
    }

    /**
     * Remove product from search index.
     */
    private function removeFromSearchIndex($product): void
    {
        // Implementation for removing product from search index

        // Example implementation for Elasticsearch:
        // Elasticsearch::delete([
        //     'index' => 'products',
        //     'id' => $product->id
        // ]);
    }

    /**
     * Handle a job failure.
     */
    public function failed($event, \Throwable $exception): void
    {
        Log::error('Failed to update product search index', [
            'product_id' => $event->product->id,
            'error' => $exception->getMessage()
        ]);
    }
}
