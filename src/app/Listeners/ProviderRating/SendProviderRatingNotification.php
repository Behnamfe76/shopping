<?php

namespace Fereydooni\Shopping\app\Listeners\ProviderRating;

use Fereydooni\Shopping\app\Events\ProviderRating\ProviderRatingCreated;
use Fereydooni\Shopping\app\Events\ProviderRating\ProviderRatingUpdated;
use Fereydooni\Shopping\app\Events\ProviderRating\ProviderRatingApproved;
use Fereydooni\Shopping\app\Events\ProviderRating\ProviderRatingRejected;
use Fereydooni\Shopping\app\Events\ProviderRating\ProviderRatingFlagged;
use Fereydooni\Shopping\app\Events\ProviderRating\ProviderRatingVerified;
use Fereydooni\Shopping\app\Notifications\ProviderRating\RatingReceived;
use Fereydooni\Shopping\app\Notifications\ProviderRating\RatingApproved;
use Fereydooni\Shopping\app\Notifications\ProviderRating\RatingRejected;
use Fereydooni\Shopping\app\Notifications\ProviderRating\RatingFlagged;
use Fereydooni\Shopping\app\Notifications\ProviderRating\RatingVerified;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class SendProviderRatingNotification implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Handle the event.
     */
    public function handle($event): void
    {
        try {
            $rating = $event->rating;
            $provider = $rating->provider;
            $user = $rating->user;

            switch (true) {
                case $event instanceof ProviderRatingCreated:
                    $this->handleRatingCreated($rating, $provider, $user);
                    break;
                case $event instanceof ProviderRatingUpdated:
                    $this->handleRatingUpdated($rating, $provider, $user);
                    break;
                case $event instanceof ProviderRatingApproved:
                    $this->handleRatingApproved($rating, $provider, $user);
                    break;
                case $event instanceof ProviderRatingRejected:
                    $this->handleRatingRejected($rating, $provider, $user);
                    break;
                case $event instanceof ProviderRatingFlagged:
                    $this->handleRatingFlagged($rating, $provider, $user);
                    break;
                case $event instanceof ProviderRatingVerified:
                    $this->handleRatingVerified($rating, $provider, $user);
                    break;
            }
        } catch (\Exception $e) {
            Log::error('Failed to send provider rating notification', [
                'event' => get_class($event),
                'rating_id' => $event->rating->id ?? null,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }

    /**
     * Handle rating created event.
     */
    protected function handleRatingCreated($rating, $provider, $user): void
    {
        // Notify provider of new rating
        if ($provider && $provider->user) {
            $provider->user->notify(new RatingReceived($rating));
        }

        // Log the notification
        Log::info('Provider rating notification sent', [
            'rating_id' => $rating->id,
            'provider_id' => $rating->provider_id,
            'user_id' => $rating->user_id,
            'type' => 'created'
        ]);
    }

    /**
     * Handle rating updated event.
     */
    protected function handleRatingUpdated($rating, $provider, $user): void
    {
        // Notify provider of rating update
        if ($provider && $provider->user) {
            $provider->user->notify(new RatingReceived($rating, 'updated'));
        }

        Log::info('Provider rating update notification sent', [
            'rating_id' => $rating->id,
            'provider_id' => $rating->provider_id,
            'user_id' => $rating->user_id,
            'type' => 'updated'
        ]);
    }

    /**
     * Handle rating approved event.
     */
    protected function handleRatingApproved($rating, $provider, $user): void
    {
        // Notify user that their rating was approved
        if ($user) {
            $user->notify(new RatingApproved($rating));
        }

        Log::info('Provider rating approved notification sent', [
            'rating_id' => $rating->id,
            'provider_id' => $rating->provider_id,
            'user_id' => $rating->user_id,
            'type' => 'approved'
        ]);
    }

    /**
     * Handle rating rejected event.
     */
    protected function handleRatingRejected($rating, $provider, $user): void
    {
        // Notify user that their rating was rejected
        if ($user) {
            $user->notify(new RatingRejected($rating));
        }

        Log::info('Provider rating rejected notification sent', [
            'rating_id' => $rating->id,
            'provider_id' => $rating->provider_id,
            'user_id' => $rating->user_id,
            'type' => 'rejected'
        ]);
    }

    /**
     * Handle rating flagged event.
     */
    protected function handleRatingFlagged($rating, $provider, $user): void
    {
        // Notify moderators of flagged rating
        $this->notifyModerators(new RatingFlagged($rating));

        Log::info('Provider rating flagged notification sent', [
            'rating_id' => $rating->id,
            'provider_id' => $rating->provider_id,
            'user_id' => $rating->user_id,
            'type' => 'flagged'
        ]);
    }

    /**
     * Handle rating verified event.
     */
    protected function handleRatingVerified($rating, $provider, $user): void
    {
        // Notify user that their rating was verified
        if ($user) {
            $user->notify(new RatingVerified($rating));
        }

        Log::info('Provider rating verified notification sent', [
            'rating_id' => $rating->id,
            'provider_id' => $rating->provider_id,
            'user_id' => $rating->user_id,
            'type' => 'verified'
        ]);
    }

    /**
     * Notify moderators of flagged content.
     */
    protected function notifyModerators($notification): void
    {
        // Get users with moderation permissions
        $moderators = \App\Models\User::permission('provider-rating.moderate')->get();

        foreach ($moderators as $moderator) {
            $moderator->notify($notification);
        }
    }
}
