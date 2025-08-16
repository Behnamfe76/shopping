<?php

namespace Fereydooni\Shopping\app\Listeners;

use Fereydooni\Shopping\app\Events\ProductReviewCreated;
use Fereydooni\Shopping\app\Events\ProductReviewUpdated;
use Fereydooni\Shopping\app\Events\ProductReviewDeleted;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class UpdateProductRatingCache implements ShouldQueue
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
        $review = $event->review;
        $productId = $review->product_id;

        // Clear product rating cache
        $this->clearProductRatingCache($productId);

        // Clear product statistics cache
        $this->clearProductStatsCache($productId);

        Log::info('Product rating cache updated', [
            'review_id' => $review->id,
            'product_id' => $productId,
            'rating' => $review->rating
        ]);
    }

    /**
     * Clear product rating cache
     */
    private function clearProductRatingCache(int $productId): void
    {
        $cacheKeys = [
            "product_rating_{$productId}",
            "product_average_rating_{$productId}",
            "product_rating_count_{$productId}",
            "product_rating_distribution_{$productId}",
            "product_rating_summary_{$productId}"
        ];

        foreach ($cacheKeys as $key) {
            Cache::forget($key);
        }
    }

    /**
     * Clear product statistics cache
     */
    private function clearProductStatsCache(int $productId): void
    {
        $cacheKeys = [
            "product_stats_{$productId}",
            "product_review_summary_{$productId}",
            "product_rating_analytics_{$productId}",
            "product_performance_{$productId}"
        ];

        foreach ($cacheKeys as $key) {
            Cache::forget($key);
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed($event, \Throwable $exception): void
    {
        Log::error('Failed to update product rating cache', [
            'review_id' => $event->review->id,
            'error' => $exception->getMessage()
        ]);
    }
}
