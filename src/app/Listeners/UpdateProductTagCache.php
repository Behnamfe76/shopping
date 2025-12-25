<?php

namespace Fereydooni\Shopping\app\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Cache;

class UpdateProductTagCache implements ShouldQueue
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
        // Clear all product tag related caches
        $this->clearProductTagCaches();
    }

    /**
     * Clear all product tag related caches
     */
    private function clearProductTagCaches(): void
    {
        // Clear various cache keys
        Cache::forget('product_tags_all');
        Cache::forget('product_tags_active');
        Cache::forget('product_tags_featured');
        Cache::forget('product_tags_popular');
        Cache::forget('product_tags_recent');
        Cache::forget('product_tags_by_color');
        Cache::forget('product_tags_by_icon');
        Cache::forget('product_tags_names');
        Cache::forget('product_tags_slugs');
        Cache::forget('product_tags_colors');
        Cache::forget('product_tags_icons');
        Cache::forget('product_tags_stats');
        Cache::forget('product_tags_tree');
        Cache::forget('product_tags_cloud');
    }
}
