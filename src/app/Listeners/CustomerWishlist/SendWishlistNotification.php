<?php

namespace Fereydooni\Shopping\app\Listeners\CustomerWishlist;

use Fereydooni\Shopping\app\Events\CustomerWishlist\CustomerWishlistCreated;
use Fereydooni\Shopping\app\Events\CustomerWishlist\ProductAddedToWishlist;
use Fereydooni\Shopping\app\Events\CustomerWishlist\WishlistMadePublic;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;

class SendWishlistNotification implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Handle the event.
     */
    public function handle($event): void
    {
        if ($event instanceof CustomerWishlistCreated || $event instanceof ProductAddedToWishlist) {
            $this->sendWishlistAddedNotification($event);
        } elseif ($event instanceof WishlistMadePublic) {
            $this->sendWishlistPublicNotification($event);
        }
    }

    /**
     * Send notification when item is added to wishlist
     */
    private function sendWishlistAddedNotification($event): void
    {
        $wishlist = $event->wishlist;
        $customer = $wishlist->customer;
        $product = $wishlist->product;

        if (! $customer || ! $customer->email) {
            return;
        }

        $data = [
            'customer_name' => $customer->name,
            'product_name' => $product ? $product->name : 'Product',
            'added_at' => $wishlist->added_at,
            'product_url' => $product ? route('products.show', $product->id) : '#',
        ];

        Mail::send('emails.wishlist.item-added', $data, function ($message) use ($customer) {
            $message->to($customer->email)
                ->subject('Item Added to Your Wishlist');
        });
    }

    /**
     * Send notification when wishlist is made public
     */
    private function sendWishlistPublicNotification(WishlistMadePublic $event): void
    {
        $wishlist = $event->wishlist;
        $customer = $wishlist->customer;

        if (! $customer || ! $customer->email) {
            return;
        }

        $data = [
            'customer_name' => $customer->name,
            'wishlist_url' => route('customer.wishlist.show', $customer->id),
        ];

        Mail::send('emails.wishlist.made-public', $data, function ($message) use ($customer) {
            $message->to($customer->email)
                ->subject('Your Wishlist is Now Public');
        });
    }
}
