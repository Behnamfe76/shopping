<?php

namespace Fereydooni\Shopping\app\Listeners\CustomerWishlist;

use Fereydooni\Shopping\app\Events\CustomerWishlist\CustomerWishlistCreated;
use Fereydooni\Shopping\app\Events\CustomerWishlist\CustomerWishlistUpdated;
use Fereydooni\Shopping\app\Events\CustomerWishlist\CustomerWishlistDeleted;
use Fereydooni\Shopping\app\Events\CustomerWishlist\ProductAddedToWishlist;
use Fereydooni\Shopping\app\Events\CustomerWishlist\ProductRemovedFromWishlist;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class LogCustomerWishlistActivity implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Handle the event.
     */
    public function handle($event): void
    {
        $logData = [
            'timestamp' => now()->toISOString(),
            'event' => get_class($event),
        ];

        if ($event instanceof CustomerWishlistCreated) {
            $logData['wishlist_id'] = $event->wishlist->id;
            $logData['customer_id'] = $event->wishlist->customer_id;
            $logData['product_id'] = $event->wishlist->product_id;
            $logData['action'] = 'wishlist_created';
        } elseif ($event instanceof CustomerWishlistUpdated) {
            $logData['wishlist_id'] = $event->wishlist->id;
            $logData['customer_id'] = $event->wishlist->customer_id;
            $logData['product_id'] = $event->wishlist->product_id;
            $logData['action'] = 'wishlist_updated';
        } elseif ($event instanceof CustomerWishlistDeleted) {
            $logData['wishlist_id'] = $event->wishlist->id;
            $logData['customer_id'] = $event->wishlist->customer_id;
            $logData['product_id'] = $event->wishlist->product_id;
            $logData['action'] = 'wishlist_deleted';
        } elseif ($event instanceof ProductAddedToWishlist) {
            $logData['wishlist_id'] = $event->wishlist->id;
            $logData['customer_id'] = $event->wishlist->customer_id;
            $logData['product_id'] = $event->wishlist->product_id;
            $logData['action'] = 'product_added_to_wishlist';
        } elseif ($event instanceof ProductRemovedFromWishlist) {
            $logData['wishlist_id'] = $event->wishlist->id;
            $logData['customer_id'] = $event->wishlist->customer_id;
            $logData['product_id'] = $event->wishlist->product_id;
            $logData['action'] = 'product_removed_from_wishlist';
        }

        Log::info('Customer Wishlist Activity', $logData);
    }
}
