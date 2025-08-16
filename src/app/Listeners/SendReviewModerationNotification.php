<?php

namespace Fereydooni\Shopping\app\Listeners;

use Fereydooni\Shopping\app\Events\ProductReviewCreated;
use Fereydooni\Shopping\app\Events\ProductReviewFlagged;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class SendReviewModerationNotification implements ShouldQueue
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

        // Log moderation event
        Log::info('Review moderation notification sent', [
            'review_id' => $review->id,
            'product_id' => $review->product_id,
            'user_id' => $review->user_id,
            'event_type' => get_class($event)
        ]);

        // Send notification to moderators
        // This is a placeholder - implement actual notification logic
        // $moderators = User::whereHas('roles', function($query) {
        //     $query->where('name', 'product-review-moderator');
        // })->get();
        //
        // Notification::send($moderators, new ReviewModerationNotification($review, $event));

        // Send notification to review author if flagged
        if ($event instanceof ProductReviewFlagged) {
            // Notification::send($review->user, new ReviewFlaggedNotification($review, $event->reason));
            Log::info('Review flagged notification sent to author', [
                'review_id' => $review->id,
                'reason' => $event->reason
            ]);
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed($event, \Throwable $exception): void
    {
        Log::error('Failed to send review moderation notification', [
            'review_id' => $event->review->id,
            'error' => $exception->getMessage()
        ]);
    }
}
