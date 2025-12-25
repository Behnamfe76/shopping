<?php

namespace Fereydooni\Shopping\app\Listeners;

use Fereydooni\Shopping\app\Events\ProductViewed;
use Fereydooni\Shopping\app\Events\ProductWishlisted;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class UpdateProductAnalytics implements ShouldQueue
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

        // Update analytics based on event type
        switch (get_class($event)) {
            case ProductViewed::class:
                $this->updateViewAnalytics($product, $event);
                break;
            case ProductWishlisted::class:
                $this->updateWishlistAnalytics($product, $event);
                break;
        }

        Log::info('Product analytics updated', [
            'product_id' => $product->id,
            'event' => get_class($event),
        ]);
    }

    /**
     * Update view analytics.
     */
    private function updateViewAnalytics($product, ProductViewed $event): void
    {
        // Increment view count
        $product->increment('view_count');

        // Store view analytics data
        $this->storeViewAnalytics($product, $event);
    }

    /**
     * Update wishlist analytics.
     */
    private function updateWishlistAnalytics($product, ProductWishlisted $event): void
    {
        // Increment wishlist count
        $product->increment('wishlist_count');

        // Store wishlist analytics data
        $this->storeWishlistAnalytics($product, $event);
    }

    /**
     * Store view analytics data.
     */
    private function storeViewAnalytics($product, ProductViewed $event): void
    {
        // Implementation for storing view analytics
        // This could include storing in a separate analytics table
        // or using a service like Google Analytics

        // Example implementation:
        // ProductViewAnalytics::create([
        //     'product_id' => $product->id,
        //     'ip_address' => $event->ipAddress,
        //     'user_agent' => $event->userAgent,
        //     'viewed_at' => now(),
        // ]);
    }

    /**
     * Store wishlist analytics data.
     */
    private function storeWishlistAnalytics($product, ProductWishlisted $event): void
    {
        // Implementation for storing wishlist analytics
        // This could include storing in a separate analytics table

        // Example implementation:
        // ProductWishlistAnalytics::create([
        //     'product_id' => $product->id,
        //     'user_id' => $event->userId,
        //     'wishlisted_at' => now(),
        // ]);
    }

    /**
     * Handle a job failure.
     */
    public function failed($event, \Throwable $exception): void
    {
        Log::error('Failed to update product analytics', [
            'product_id' => $event->product->id,
            'error' => $exception->getMessage(),
        ]);
    }
}
