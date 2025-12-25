<?php

namespace Fereydooni\Shopping\app\Listeners;

use Fereydooni\Shopping\app\Events\ProductReviewCreated;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

class SendProductReviewCreatedNotification implements ShouldQueue
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
    public function handle(ProductReviewCreated $event): void
    {
        $review = $event->review;

        // Log the review creation
        Log::info('Product review created', [
            'review_id' => $review->id,
            'product_id' => $review->product_id,
            'user_id' => $review->user_id,
            'rating' => $review->rating,
        ]);

        // Send notification to product owner/admin
        // This is a placeholder - implement actual notification logic
        // Notification::send($adminUsers, new ProductReviewCreatedNotification($review));

        // Send confirmation to review author
        // Notification::send($review->user, new ReviewSubmittedNotification($review));
    }

    /**
     * Handle a job failure.
     */
    public function failed(ProductReviewCreated $event, \Throwable $exception): void
    {
        Log::error('Failed to send product review created notification', [
            'review_id' => $event->review->id,
            'error' => $exception->getMessage(),
        ]);
    }
}
