<?php

namespace Fereydooni\Shopping\app\Listeners\ProviderRating;

use Fereydooni\Shopping\app\Events\ProviderRating\ProviderRatingCreated;
use Fereydooni\Shopping\app\Events\ProviderRating\ProviderRatingUpdated;
use Fereydooni\Shopping\app\Events\ProviderRating\ProviderRatingApproved;
use Fereydooni\Shopping\app\Events\ProviderRating\ProviderRatingRejected;
use Fereydooni\Shopping\app\Events\ProviderRating\ProviderRatingFlagged;
use Fereydooni\Shopping\app\Events\ProviderRating\ProviderRatingVerified;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class UpdateProviderRatingRecord implements ShouldQueue
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

            if (!$provider) {
                Log::warning('Provider not found for rating', ['rating_id' => $rating->id]);
                return;
            }

            switch (true) {
                case $event instanceof ProviderRatingCreated:
                    $this->handleRatingCreated($rating, $provider);
                    break;
                case $event instanceof ProviderRatingUpdated:
                    $this->handleRatingUpdated($rating, $provider);
                    break;
                case $event instanceof ProviderRatingApproved:
                    $this->handleRatingApproved($rating, $provider);
                    break;
                case $event instanceof ProviderRatingRejected:
                    $this->handleRatingRejected($rating, $provider);
                    break;
                case $event instanceof ProviderRatingFlagged:
                    $this->handleRatingFlagged($rating, $provider);
                    break;
                case $event instanceof ProviderRatingVerified:
                    $this->handleRatingVerified($rating, $provider);
                    break;
            }

            // Clear provider rating cache
            $this->clearProviderRatingCache($provider->id);

        } catch (\Exception $e) {
            Log::error('Failed to update provider rating record', [
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
    protected function handleRatingCreated($rating, $provider): void
    {
        // Update provider's rating count and average
        $this->updateProviderRatingStats($provider->id);

        // Update category-specific stats if category exists
        if ($rating->category) {
            $this->updateProviderCategoryRatingStats($provider->id, $rating->category);
        }

        Log::info('Provider rating record updated for created rating', [
            'rating_id' => $rating->id,
            'provider_id' => $provider->id
        ]);
    }

    /**
     * Handle rating updated event.
     */
    protected function handleRatingUpdated($rating, $provider): void
    {
        // Recalculate provider's rating stats
        $this->updateProviderRatingStats($provider->id);

        // Update category-specific stats if category exists
        if ($rating->category) {
            $this->updateProviderCategoryRatingStats($provider->id, $rating->category);
        }

        Log::info('Provider rating record updated for updated rating', [
            'rating_id' => $rating->id,
            'provider_id' => $provider->id
        ]);
    }

    /**
     * Handle rating approved event.
     */
    protected function handleRatingApproved($rating, $provider): void
    {
        // Update provider's verified rating count
        $this->updateProviderVerifiedRatingStats($provider->id);

        // Update overall rating stats
        $this->updateProviderRatingStats($provider->id);

        Log::info('Provider rating record updated for approved rating', [
            'rating_id' => $rating->id,
            'provider_id' => $provider->id
        ]);
    }

    /**
     * Handle rating rejected event.
     */
    protected function handleRatingRejected($rating, $provider): void
    {
        // Update provider's rejected rating count
        $this->updateProviderRejectedRatingStats($provider->id);

        Log::info('Provider rating record updated for rejected rating', [
            'rating_id' => $rating->id,
            'provider_id' => $provider->id
        ]);
    }

    /**
     * Handle rating flagged event.
     */
    protected function handleRatingFlagged($rating, $provider): void
    {
        // Update provider's flagged rating count
        $this->updateProviderFlaggedRatingStats($provider->id);

        Log::info('Provider rating record updated for flagged rating', [
            'rating_id' => $rating->id,
            'provider_id' => $provider->id
        ]);
    }

    /**
     * Handle rating verified event.
     */
    protected function handleRatingVerified($rating, $provider): void
    {
        // Update provider's verified rating count
        $this->updateProviderVerifiedRatingStats($provider->id);

        // Update overall rating stats
        $this->updateProviderRatingStats($provider->id);

        Log::info('Provider rating record updated for verified rating', [
            'rating_id' => $rating->id,
            'provider_id' => $provider->id
        ]);
    }

    /**
     * Update provider rating statistics.
     */
    protected function updateProviderRatingStats(int $providerId): void
    {
        try {
            $provider = \Fereydooni\Shopping\app\Models\Provider::find($providerId);
            if (!$provider) return;

            // Calculate new average rating
            $averageRating = $provider->ratings()
                ->where('status', 'approved')
                ->avg('rating_value') ?? 0;

            // Get total rating count
            $totalRatings = $provider->ratings()
                ->where('status', 'approved')
                ->count();

            // Update provider record
            $provider->update([
                'average_rating' => round($averageRating, 2),
                'total_ratings' => $totalRatings,
                'rating_updated_at' => now()
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to update provider rating stats', [
                'provider_id' => $providerId,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Update provider category rating statistics.
     */
    protected function updateProviderCategoryRatingStats(int $providerId, string $category): void
    {
        try {
            $provider = \Fereydooni\Shopping\app\Models\Provider::find($providerId);
            if (!$provider) return;

            // Calculate category-specific average rating
            $categoryAverage = $provider->ratings()
                ->where('status', 'approved')
                ->where('category', $category)
                ->avg('rating_value') ?? 0;

            // Get category-specific rating count
            $categoryCount = $provider->ratings()
                ->where('status', 'approved')
                ->where('category', $category)
                ->count();

            // Update provider's category ratings (assuming there's a JSON field or related table)
            // This would depend on your specific implementation
            Log::info('Category rating stats updated', [
                'provider_id' => $providerId,
                'category' => $category,
                'average' => $categoryAverage,
                'count' => $categoryCount
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to update provider category rating stats', [
                'provider_id' => $providerId,
                'category' => $category,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Update provider verified rating statistics.
     */
    protected function updateProviderVerifiedRatingStats(int $providerId): void
    {
        try {
            $provider = \Fereydooni\Shopping\app\Models\Provider::find($providerId);
            if (!$provider) return;

            $verifiedCount = $provider->ratings()
                ->where('status', 'approved')
                ->where('is_verified', true)
                ->count();

            // Update provider record if you have a verified_ratings field
            // $provider->update(['verified_ratings_count' => $verifiedCount]);

            Log::info('Provider verified rating stats updated', [
                'provider_id' => $providerId,
                'verified_count' => $verifiedCount
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to update provider verified rating stats', [
                'provider_id' => $providerId,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Update provider rejected rating statistics.
     */
    protected function updateProviderRejectedRatingStats(int $providerId): void
    {
        try {
            $provider = \Fereydooni\Shopping\app\Models\Provider::find($providerId);
            if (!$provider) return;

            $rejectedCount = $provider->ratings()
                ->where('status', 'rejected')
                ->count();

            // Update provider record if you have a rejected_ratings field
            // $provider->update(['rejected_ratings_count' => $rejectedCount]);

            Log::info('Provider rejected rating stats updated', [
                'provider_id' => $providerId,
                'rejected_count' => $rejectedCount
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to update provider rejected rating stats', [
                'provider_id' => $providerId,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Update provider flagged rating statistics.
     */
    protected function updateProviderFlaggedRatingStats(int $providerId): void
    {
        try {
            $provider = \Fereydooni\Shopping\app\Models\Provider::find($providerId);
            if (!$provider) return;

            $flaggedCount = $provider->ratings()
                ->where('status', 'flagged')
                ->count();

            // Update provider record if you have a flagged_ratings field
            // $provider->update(['flagged_ratings_count' => $flaggedCount]);

            Log::info('Provider flagged rating stats updated', [
                'provider_id' => $providerId,
                'flagged_count' => $flaggedCount
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to update provider flagged rating stats', [
                'provider_id' => $providerId,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Clear provider rating cache.
     */
    protected function clearProviderRatingCache(int $providerId): void
    {
        try {
            Cache::forget("provider_rating_{$providerId}");
            Cache::forget("provider_rating_stats_{$providerId}");
            Cache::forget("provider_rating_breakdown_{$providerId}");

            Log::info('Provider rating cache cleared', ['provider_id' => $providerId]);
        } catch (\Exception $e) {
            Log::error('Failed to clear provider rating cache', [
                'provider_id' => $providerId,
                'error' => $e->getMessage()
            ]);
        }
    }
}
