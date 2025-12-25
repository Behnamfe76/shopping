<?php

namespace Fereydooni\Shopping\app\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Cache;

class UpdateProductFilterCache implements ShouldQueue
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
        // Clear product filter caches
        $this->clearProductFilterCaches();
    }

    /**
     * Clear product filter caches
     */
    private function clearProductFilterCaches(): void
    {
        // Clear filter-related cache keys
        Cache::forget('product_filters_tags');
        Cache::forget('product_filters_colors');
        Cache::forget('product_filters_icons');
        Cache::forget('product_filters_active_tags');
        Cache::forget('product_filters_featured_tags');
        Cache::forget('product_filters_popular_tags');
        Cache::forget('product_filters_recent_tags');
        Cache::forget('product_filters_by_usage');
        Cache::forget('product_filters_suggestions');
        Cache::forget('product_filters_autocomplete');
    }
}
