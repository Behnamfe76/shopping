<?php

namespace Fereydooni\Shopping\app\Listeners;

use Fereydooni\Shopping\app\Events\ProductCreated;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

class SendProductCreatedNotification implements ShouldQueue
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
    public function handle(ProductCreated $event): void
    {
        $product = $event->product;

        // Log the product creation
        Log::info('Product created', [
            'product_id' => $product->id,
            'product_title' => $product->title,
            'created_by' => auth()->id(),
            'created_at' => $product->created_at,
        ]);

        // Send notification to admin users
        // You can implement your notification logic here
        // Example: Notification::send($adminUsers, new ProductCreatedNotification($product));

        // Send email notification to product manager
        if ($product->category && $product->category->manager_email) {
            // Example: Mail::to($product->category->manager_email)->send(new ProductCreatedMail($product));
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(ProductCreated $event, \Throwable $exception): void
    {
        Log::error('Failed to send product created notification', [
            'product_id' => $event->product->id,
            'error' => $exception->getMessage(),
        ]);
    }
}
