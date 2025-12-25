<?php

namespace Fereydooni\Shopping\app\Listeners\CustomerWishlist;

use Fereydooni\Shopping\app\Events\CustomerWishlist\CustomerWishlistCreated;
use Fereydooni\Shopping\app\Events\CustomerWishlist\CustomerWishlistDeleted;
use Fereydooni\Shopping\app\Events\CustomerWishlist\ProductAddedToWishlist;
use Fereydooni\Shopping\app\Events\CustomerWishlist\ProductRemovedFromWishlist;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Cache;

class UpdateWishlistRecommendations implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Handle the event.
     */
    public function handle($event): void
    {
        if ($event instanceof CustomerWishlistCreated || $event instanceof ProductAddedToWishlist) {
            $this->updateCustomerRecommendations($event->wishlist->customer_id);
            $this->updateProductRecommendations($event->wishlist->product_id);
        } elseif ($event instanceof CustomerWishlistDeleted || $event instanceof ProductRemovedFromWishlist) {
            $this->updateCustomerRecommendations($event->wishlist->customer_id);
            $this->updateProductRecommendations($event->wishlist->product_id);
        }
    }

    /**
     * Update customer recommendations based on wishlist changes
     */
    private function updateCustomerRecommendations(int $customerId): void
    {
        $cacheKey = "customer_recommendations_{$customerId}";
        Cache::forget($cacheKey);

        // Trigger recommendation calculation
        // This would typically call a service to recalculate recommendations
    }

    /**
     * Update product recommendations based on wishlist changes
     */
    private function updateProductRecommendations(int $productId): void
    {
        $cacheKey = "product_recommendations_{$productId}";
        Cache::forget($cacheKey);

        // Trigger recommendation calculation for similar products
        // This would typically call a service to recalculate recommendations
    }
}
