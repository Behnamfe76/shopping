<?php

namespace Fereydooni\Shopping\app\Listeners\CustomerWishlist;

use Fereydooni\Shopping\app\Events\CustomerWishlist\WishlistPriceDropDetected;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;

class NotifyWishlistPriceDrop implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Handle the event.
     */
    public function handle(WishlistPriceDropDetected $event): void
    {
        $wishlist = $event->wishlist;
        $customer = $wishlist->customer;
        $product = $wishlist->product;

        // Check if customer has enabled price drop notifications
        if (! $wishlist->price_drop_notification) {
            return;
        }

        // Send email notification
        if ($customer && $customer->email) {
            $this->sendEmailNotification($customer, $product, $event);
        }

        // Send in-app notification
        $this->sendInAppNotification($customer, $product, $event);
    }

    /**
     * Send email notification for price drop
     */
    private function sendEmailNotification($customer, $product, WishlistPriceDropDetected $event): void
    {
        $data = [
            'customer_name' => $customer->name,
            'product_name' => $product->name ?? 'Product',
            'old_price' => $event->oldPrice,
            'new_price' => $event->newPrice,
            'price_drop' => $event->priceDrop,
            'price_drop_percentage' => round(($event->priceDrop / $event->oldPrice) * 100, 2),
            'product_url' => route('products.show', $product->id ?? 1),
        ];

        // You can create a dedicated mailable class for this
        Mail::send('emails.wishlist.price-drop', $data, function ($message) use ($customer) {
            $message->to($customer->email)
                ->subject('Price Drop Alert - Item in Your Wishlist');
        });
    }

    /**
     * Send in-app notification for price drop
     */
    private function sendInAppNotification($customer, $product, WishlistPriceDropDetected $event): void
    {
        // This would integrate with your notification system
        // For example, using Laravel's notification system
        $notificationData = [
            'title' => 'Price Drop Alert',
            'message' => 'The price of '.($product ? $product->name : 'an item').' in your wishlist has dropped!',
            'data' => [
                'product_id' => $product ? $product->id : null,
                'old_price' => $event->oldPrice,
                'new_price' => $event->newPrice,
                'price_drop' => $event->priceDrop,
            ],
        ];

        // Send notification to customer
        // Notification::send($customer, new WishlistPriceDropNotification($notificationData));
    }
}
