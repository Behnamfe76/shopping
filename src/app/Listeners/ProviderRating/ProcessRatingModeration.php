<?php

namespace Fereydooni\Shopping\App\Listeners\ProviderRating;

use Fereydooni\Shopping\App\Events\ProviderRating\ProviderRatingApproved;
use Fereydooni\Shopping\App\Events\ProviderRating\ProviderRatingCreated;
use Fereydooni\Shopping\App\Events\ProviderRating\ProviderRatingFlagged;
use Fereydooni\Shopping\App\Events\ProviderRating\ProviderRatingRejected;
use Fereydooni\Shopping\App\Events\ProviderRating\ProviderRatingUpdated;
use Fereydooni\Shopping\App\Events\ProviderRating\ProviderRatingVerified;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class ProcessRatingModeration implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Handle the event.
     */
    public function handle($event): void
    {
        try {
            $rating = $event->rating;

            switch (true) {
                case $event instanceof ProviderRatingCreated:
                    $this->processNewRating($rating);
                    break;
                case $event instanceof ProviderRatingUpdated:
                    $this->processUpdatedRating($rating);
                    break;
                case $event instanceof ProviderRatingApproved:
                    $this->processApprovedRating($rating);
                    break;
                case $event instanceof ProviderRatingRejected:
                    $this->processRejectedRating($rating);
                    break;
                case $event instanceof ProviderRatingFlagged:
                    $this->processFlaggedRating($rating);
                    break;
                case $event instanceof ProviderRatingVerified:
                    $this->processVerifiedRating($rating);
                    break;
            }

        } catch (\Exception $e) {
            Log::error('Failed to process rating moderation', [
                'event' => get_class($event),
                'rating_id' => $event->rating->id ?? null,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
        }
    }

    /**
     * Process new rating for moderation.
     */
    protected function processNewRating($rating): void
    {
        try {
            // Check if rating needs moderation
            if ($this->requiresModeration($rating)) {
                $this->flagForModeration($rating, 'New rating requires moderation');
                Log::info('New rating flagged for moderation', [
                    'rating_id' => $rating->id,
                    'provider_id' => $rating->rating->provider_id,
                ]);
            } else {
                // Auto-approve if no moderation needed
                $this->autoApproveRating($rating);
                Log::info('New rating auto-approved', [
                    'rating_id' => $rating->id,
                    'provider_id' => $rating->rating->provider_id,
                ]);
            }

        } catch (\Exception $e) {
            Log::error('Failed to process new rating moderation', [
                'rating_id' => $rating->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Process updated rating for moderation.
     */
    protected function processUpdatedRating($rating): void
    {
        try {
            // Check if updated rating needs re-moderation
            if ($this->requiresModeration($rating)) {
                $this->flagForModeration($rating, 'Updated rating requires re-moderation');
                Log::info('Updated rating flagged for re-moderation', [
                    'rating_id' => $rating->id,
                    'provider_id' => $rating->rating->provider_id,
                ]);
            }

        } catch (\Exception $e) {
            Log::error('Failed to process updated rating moderation', [
                'rating_id' => $rating->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Process approved rating.
     */
    protected function processApprovedRating($rating): void
    {
        try {
            // Update moderation status
            $rating->update(['moderation_status' => 'approved']);

            // Clear moderation cache
            $this->clearModerationCache($rating->provider_id);

            Log::info('Rating moderation processed - approved', [
                'rating_id' => $rating->id,
                'provider_id' => $rating->rating->provider_id,
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to process approved rating moderation', [
                'rating_id' => $rating->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Process rejected rating.
     */
    protected function processRejectedRating($rating): void
    {
        try {
            // Update moderation status
            $rating->update(['moderation_status' => 'rejected']);

            // Clear moderation cache
            $this->clearModerationCache($rating->provider_id);

            Log::info('Rating moderation processed - rejected', [
                'rating_id' => $rating->id,
                'provider_id' => $rating->rating->provider_id,
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to process rejected rating moderation', [
                'rating_id' => $rating->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Process flagged rating.
     */
    protected function processFlaggedRating($rating): void
    {
        try {
            // Update moderation status
            $rating->update(['moderation_status' => 'flagged']);

            // Notify moderators
            $this->notifyModerators($rating);

            // Clear moderation cache
            $this->clearModerationCache($rating->provider_id);

            Log::info('Rating moderation processed - flagged', [
                'rating_id' => $rating->id,
                'provider_id' => $rating->rating->provider_id,
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to process flagged rating moderation', [
                'rating_id' => $rating->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Process verified rating.
     */
    protected function processVerifiedRating($rating): void
    {
        try {
            // Update moderation status
            $rating->update(['moderation_status' => 'verified']);

            // Clear moderation cache
            $this->clearModerationCache($rating->provider_id);

            Log::info('Rating moderation processed - verified', [
                'rating_id' => $rating->id,
                'provider_id' => $rating->rating->provider_id,
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to process verified rating moderation', [
                'rating_id' => $rating->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Check if rating requires moderation.
     */
    protected function requiresModeration($rating): bool
    {
        try {
            // Check for profanity in title or comment
            if ($this->containsProfanity($rating->title) || $this->containsProfanity($rating->comment)) {
                return true;
            }

            // Check for suspicious patterns
            if ($this->hasSuspiciousPatterns($rating)) {
                return true;
            }

            // Check user reputation
            if ($this->hasLowUserReputation($rating->user_id)) {
                return true;
            }

            // Check for spam indicators
            if ($this->hasSpamIndicators($rating)) {
                return true;
            }

            return false;

        } catch (\Exception $e) {
            Log::error('Failed to check if rating requires moderation', [
                'rating_id' => $rating->id,
                'error' => $e->getMessage(),
            ]);

            return true; // Default to requiring moderation on error
        }
    }

    /**
     * Check if text contains profanity.
     */
    protected function containsProfanity(?string $text): bool
    {
        if (! $text) {
            return false;
        }

        // Simple profanity check (in production, use a proper profanity filter)
        $profanityWords = ['bad_word', 'inappropriate', 'spam'];

        foreach ($profanityWords as $word) {
            if (stripos($text, $word) !== false) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check for suspicious patterns.
     */
    protected function hasSuspiciousPatterns($rating): bool
    {
        // Check for excessive capitalization
        if ($rating->title && strtoupper($rating->title) === $rating->title && strlen($rating->title) > 10) {
            return true;
        }

        // Check for repetitive characters
        if ($rating->comment && $this->hasRepetitiveCharacters($rating->comment)) {
            return true;
        }

        // Check for suspicious rating values
        if ($rating->rating_value < 1 || $rating->rating_value > 5) {
            return true;
        }

        return false;
    }

    /**
     * Check for repetitive characters.
     */
    protected function hasRepetitiveCharacters(string $text): bool
    {
        $length = strlen($text);
        if ($length < 10) {
            return false;
        }

        // Check for patterns like "aaaaa" or "!!!!!"
        for ($i = 0; $i < $length - 4; $i++) {
            if ($text[$i] === $text[$i + 1] &&
                $text[$i] === $text[$i + 2] &&
                $text[$i] === $text[$i + 3] &&
                $text[$i] === $text[$i + 4]) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check user reputation.
     */
    protected function hasLowUserReputation(int $userId): bool
    {
        try {
            // Get user's rating history
            $userRatings = \App\Models\ProviderRating::where('user_id', $userId)->count();

            // New users with no history might need moderation
            if ($userRatings === 0) {
                return true;
            }

            // Check for previously rejected ratings
            $rejectedCount = \App\Models\ProviderRating::where('user_id', $userId)
                ->where('status', 'rejected')
                ->count();

            // If user has many rejected ratings, require moderation
            if ($rejectedCount > 2) {
                return true;
            }

            return false;

        } catch (\Exception $e) {
            Log::error('Failed to check user reputation', [
                'user_id' => $userId,
                'error' => $e->getMessage(),
            ]);

            return true; // Default to requiring moderation on error
        }
    }

    /**
     * Check for spam indicators.
     */
    protected function hasSpamIndicators($rating): bool
    {
        // Check for suspicious IP patterns
        if ($rating->ip_address && $this->isSuspiciousIP($rating->ip_address)) {
            return true;
        }

        // Check for rapid posting
        if ($this->isRapidPosting($rating->user_id)) {
            return true;
        }

        return false;
    }

    /**
     * Check if IP is suspicious.
     */
    protected function isSuspiciousIP(string $ip): bool
    {
        // Simple IP check (in production, use proper IP reputation service)
        // This is a placeholder implementation
        return false;
    }

    /**
     * Check for rapid posting.
     */
    protected function isRapidPosting(int $userId): bool
    {
        try {
            // Check if user has posted multiple ratings in short time
            $recentRatings = \App\Models\ProviderRating::where('user_id', $userId)
                ->where('created_at', '>=', now()->subMinutes(5))
                ->count();

            return $recentRatings > 3;

        } catch (\Exception $e) {
            Log::error('Failed to check rapid posting', [
                'user_id' => $userId,
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }

    /**
     * Flag rating for moderation.
     */
    protected function flagForModeration($rating, string $reason): void
    {
        try {
            $rating->update([
                'status' => 'flagged',
                'moderation_reason' => $reason,
                'flagged_at' => now(),
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to flag rating for moderation', [
                'rating_id' => $rating->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Auto-approve rating.
     */
    protected function autoApproveRating($rating): void
    {
        try {
            $rating->update([
                'status' => 'approved',
                'approved_at' => now(),
                'moderation_status' => 'auto_approved',
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to auto-approve rating', [
                'rating_id' => $rating->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Notify moderators of flagged rating.
     */
    protected function notifyModerators($rating): void
    {
        try {
            // Get users with moderation permissions
            $moderators = \App\Models\User::permission('provider-rating.moderate')->get();

            foreach ($moderators as $moderator) {
                // Send notification to moderator
                // $moderator->notify(new RatingModerationRequired($rating));

                Log::info('Moderator notified of flagged rating', [
                    'moderator_id' => $moderator->id,
                    'rating_id' => $rating->id,
                ]);
            }

        } catch (\Exception $e) {
            Log::error('Failed to notify moderators', [
                'rating_id' => $rating->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Clear moderation cache.
     */
    protected function clearModerationCache(int $providerId): void
    {
        try {
            Cache::forget("provider_moderation_{$providerId}");
            Cache::forget("provider_flagged_ratings_{$providerId}");

            Log::info('Provider moderation cache cleared', ['provider_id' => $providerId]);
        } catch (\Exception $e) {
            Log::error('Failed to clear provider moderation cache', [
                'provider_id' => $providerId,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
