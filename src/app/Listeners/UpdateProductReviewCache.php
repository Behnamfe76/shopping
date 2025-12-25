<?php

namespace Fereydooni\Shopping\app\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class UpdateProductReviewCache implements ShouldQueue
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

        // Clear product review cache
        $this->clearProductReviewCache($productId);

        // Clear review statistics cache
        $this->clearReviewStatsCache($productId);

        // Clear user review cache if applicable
        if (isset($review->user_id)) {
            $this->clearUserReviewCache($review->user_id);
        }

        Log::info('Product review cache updated', [
            'review_id' => $review->id,
            'product_id' => $productId,
        ]);
    }

    /**
     * Clear product review cache
     */
    private function clearProductReviewCache(int $productId): void
    {
        $cacheKeys = [
            "product_reviews_{$productId}",
            "product_reviews_approved_{$productId}",
            "product_reviews_pending_{$productId}",
            "product_reviews_featured_{$productId}",
            "product_reviews_recent_{$productId}",
            "product_reviews_popular_{$productId}",
        ];

        foreach ($cacheKeys as $key) {
            Cache::forget($key);
        }
    }

    /**
     * Clear review statistics cache
     */
    private function clearReviewStatsCache(int $productId): void
    {
        $cacheKeys = [
            "review_stats_{$productId}",
            "review_rating_distribution_{$productId}",
            "review_average_rating_{$productId}",
            "review_analytics_{$productId}",
        ];

        foreach ($cacheKeys as $key) {
            Cache::forget($key);
        }
    }

    /**
     * Clear user review cache
     */
    private function clearUserReviewCache(int $userId): void
    {
        $cacheKeys = [
            "user_reviews_{$userId}",
            "user_review_count_{$userId}",
            "user_review_analytics_{$userId}",
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
        Log::error('Failed to update product review cache', [
            'review_id' => $event->review->id,
            'error' => $exception->getMessage(),
        ]);
    }
}
