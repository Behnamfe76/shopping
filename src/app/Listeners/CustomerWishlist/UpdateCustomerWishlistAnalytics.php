<?php

namespace Fereydooni\Shopping\app\Listeners\CustomerWishlist;

use Fereydooni\Shopping\app\Events\CustomerWishlist\CustomerWishlistCreated;
use Fereydooni\Shopping\app\Events\CustomerWishlist\CustomerWishlistDeleted;
use Fereydooni\Shopping\app\Events\CustomerWishlist\ProductAddedToWishlist;
use Fereydooni\Shopping\app\Events\CustomerWishlist\ProductRemovedFromWishlist;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Cache;

class UpdateCustomerWishlistAnalytics implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Handle the event.
     */
    public function handle($event): void
    {
        if ($event instanceof CustomerWishlistCreated || $event instanceof ProductAddedToWishlist) {
            $this->incrementWishlistCount($event->wishlist->customer_id);
            $this->incrementProductWishlistCount($event->wishlist->product_id);
        } elseif ($event instanceof CustomerWishlistDeleted || $event instanceof ProductRemovedFromWishlist) {
            $this->decrementWishlistCount($event->wishlist->customer_id);
            $this->decrementProductWishlistCount($event->wishlist->product_id);
        }
    }

    /**
     * Increment customer wishlist count
     */
    private function incrementWishlistCount(int $customerId): void
    {
        $cacheKey = "customer_wishlist_count_{$customerId}";
        $currentCount = Cache::get($cacheKey, 0);
        Cache::put($cacheKey, $currentCount + 1, 3600);
    }

    /**
     * Decrement customer wishlist count
     */
    private function decrementWishlistCount(int $customerId): void
    {
        $cacheKey = "customer_wishlist_count_{$customerId}";
        $currentCount = Cache::get($cacheKey, 0);
        Cache::put($cacheKey, max(0, $currentCount - 1), 3600);
    }

    /**
     * Increment product wishlist count
     */
    private function incrementProductWishlistCount(int $productId): void
    {
        $cacheKey = "product_wishlist_count_{$productId}";
        $currentCount = Cache::get($cacheKey, 0);
        Cache::put($cacheKey, $currentCount + 1, 3600);
    }

    /**
     * Decrement product wishlist count
     */
    private function decrementProductWishlistCount(int $productId): void
    {
        $cacheKey = "product_wishlist_count_{$productId}";
        $currentCount = Cache::get($cacheKey, 0);
        Cache::put($cacheKey, max(0, $currentCount - 1), 3600);
    }
}
