<?php

namespace Fereydooni\Shopping\app\Listeners;

use Fereydooni\Shopping\app\Events\ProductTagCreated;
use Fereydooni\Shopping\app\Events\ProductTagUpdated;
use Fereydooni\Shopping\app\Events\ProductTagDeleted;
use Fereydooni\Shopping\app\Events\ProductTagSynced;
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
        // Update search index for products that use this tag
        $this->updateProductSearchIndex($event);
    }

    /**
     * Update product search index
     */
    private function updateProductSearchIndex($event): void
    {
        try {
            if ($event instanceof ProductTagSynced) {
                // Update search index for the specific product
                $this->updateProductIndex($event->productId);
            } else {
                // Update search index for all products using this tag
                $tagId = $event->tag->id ?? null;
                if ($tagId) {
                    $this->updateProductsByTag($tagId);
                }
            }
        } catch (\Exception $e) {
            Log::error('Failed to update product search index', [
                'event' => get_class($event),
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Update specific product index
     */
    private function updateProductIndex(int $productId): void
    {
        // Implementation for updating specific product search index
        // This would typically involve updating Elasticsearch, Algolia, or similar
        Log::info('Updating product search index', ['product_id' => $productId]);
    }

    /**
     * Update products by tag
     */
    private function updateProductsByTag(int $tagId): void
    {
        // Implementation for updating all products that use this tag
        // This would typically involve updating Elasticsearch, Algolia, or similar
        Log::info('Updating products search index by tag', ['tag_id' => $tagId]);
    }
}
