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
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class UpdateProviderRatingMetrics implements ShouldQueue
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

            if (! $provider) {
                Log::warning('Provider not found for rating metrics update', ['rating_id' => $rating->id]);

                return;
            }

            switch (true) {
                case $event instanceof ProviderRatingCreated:
                    $this->updateMetricsForCreated($rating, $provider);
                    break;
                case $event instanceof ProviderRatingUpdated:
                    $this->updateMetricsForUpdated($rating, $provider);
                    break;
                case $event instanceof ProviderRatingApproved:
                    $this->updateMetricsForApproved($rating, $provider);
                    break;
                case $event instanceof ProviderRatingRejected:
                    $this->updateMetricsForRejected($rating, $provider);
                    break;
                case $event instanceof ProviderRatingFlagged:
                    $this->updateMetricsForFlagged($rating, $provider);
                    break;
                case $event instanceof ProviderRatingVerified:
                    $this->updateMetricsForVerified($rating, $provider);
                    break;
            }

            // Clear metrics cache
            $this->clearMetricsCache($provider->id);

        } catch (\Exception $e) {
            Log::error('Failed to update provider rating metrics', [
                'event' => get_class($event),
                'rating_id' => $event->rating->id ?? null,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
        }
    }

    /**
     * Update metrics for rating created.
     */
    protected function updateMetricsForCreated($rating, $provider): void
    {
        $this->updateOverallMetrics($provider->id);
        $this->updateCategoryMetrics($provider->id, $rating->category);
        $this->updateUserMetrics($rating->user_id);

        Log::info('Provider rating metrics updated for created rating', [
            'rating_id' => $rating->id,
            'provider_id' => $provider->id,
        ]);
    }

    /**
     * Update metrics for rating updated.
     */
    protected function updateMetricsForUpdated($rating, $provider): void
    {
        $this->updateOverallMetrics($provider->id);
        $this->updateCategoryMetrics($provider->id, $rating->category);

        Log::info('Provider rating metrics updated for updated rating', [
            'rating_id' => $rating->id,
            'provider_id' => $provider->id,
        ]);
    }

    /**
     * Update metrics for rating approved.
     */
    protected function updateMetricsForApproved($rating, $provider): void
    {
        $this->updateOverallMetrics($provider->id);
        $this->updateCategoryMetrics($provider->id, $rating->category);
        $this->updateApprovedMetrics($provider->id);

        Log::info('Provider rating metrics updated for approved rating', [
            'rating_id' => $rating->id,
            'provider_id' => $provider->id,
        ]);
    }

    /**
     * Update metrics for rating rejected.
     */
    protected function updateMetricsForRejected($rating, $provider): void
    {
        $this->updateRejectedMetrics($provider->id);

        Log::info('Provider rating metrics updated for rejected rating', [
            'rating_id' => $rating->id,
            'provider_id' => $provider->id,
        ]);
    }

    /**
     * Update metrics for rating flagged.
     */
    protected function updateMetricsForFlagged($rating, $provider): void
    {
        $this->updateFlaggedMetrics($provider->id);

        Log::info('Provider rating metrics updated for flagged rating', [
            'rating_id' => $rating->id,
            'provider_id' => $provider->id,
        ]);
    }

    /**
     * Update metrics for rating verified.
     */
    protected function updateMetricsForVerified($rating, $provider): void
    {
        $this->updateVerifiedMetrics($provider->id);
        $this->updateOverallMetrics($provider->id);

        Log::info('Provider rating metrics updated for verified rating', [
            'rating_id' => $rating->id,
            'provider_id' => $provider->id,
        ]);
    }

    /**
     * Update overall provider rating metrics.
     */
    protected function updateOverallMetrics(int $providerId): void
    {
        try {
            $metrics = DB::table('provider_ratings')
                ->where('provider_id', $providerId)
                ->where('status', 'approved')
                ->selectRaw('
                    COUNT(*) as total_ratings,
                    AVG(rating_value) as average_rating,
                    COUNT(CASE WHEN would_recommend = 1 THEN 1 END) as recommended_count,
                    COUNT(CASE WHEN would_recommend = 0 THEN 1 END) as not_recommended_count
                ')
                ->first();

            if ($metrics) {
                $recommendationPercentage = $metrics->total_ratings > 0
                    ? round(($metrics->recommended_count / $metrics->total_ratings) * 100, 2)
                    : 0;

                // Update provider record with new metrics
                DB::table('providers')
                    ->where('id', $providerId)
                    ->update([
                        'total_ratings' => $metrics->total_ratings,
                        'average_rating' => round($metrics->average_rating ?? 0, 2),
                        'recommendation_percentage' => $recommendationPercentage,
                        'rating_metrics_updated_at' => now(),
                    ]);
            }

        } catch (\Exception $e) {
            Log::error('Failed to update overall provider rating metrics', [
                'provider_id' => $providerId,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Update category-specific rating metrics.
     */
    protected function updateCategoryMetrics(int $providerId, string $category): void
    {
        try {
            $metrics = DB::table('provider_ratings')
                ->where('provider_id', $providerId)
                ->where('category', $category)
                ->where('status', 'approved')
                ->selectRaw('
                    COUNT(*) as category_ratings,
                    AVG(rating_value) as category_average
                ')
                ->first();

            if ($metrics) {
                // Store category metrics (this could be in a separate table or JSON field)
                // For now, we'll log the metrics
                Log::info('Category rating metrics calculated', [
                    'provider_id' => $providerId,
                    'category' => $category,
                    'ratings_count' => $metrics->category_ratings,
                    'average_rating' => round($metrics->category_average ?? 0, 2),
                ]);
            }

        } catch (\Exception $e) {
            Log::error('Failed to update category rating metrics', [
                'provider_id' => $providerId,
                'category' => $category,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Update user rating metrics.
     */
    protected function updateUserMetrics(int $userId): void
    {
        try {
            $metrics = DB::table('provider_ratings')
                ->where('user_id', $userId)
                ->selectRaw('
                    COUNT(*) as total_ratings_given,
                    AVG(rating_value) as average_rating_given
                ')
                ->first();

            if ($metrics) {
                // Update user rating metrics (this could be in a separate table or user record)
                Log::info('User rating metrics calculated', [
                    'user_id' => $userId,
                    'total_ratings_given' => $metrics->total_ratings_given,
                    'average_rating_given' => round($metrics->average_rating_given ?? 0, 2),
                ]);
            }

        } catch (\Exception $e) {
            Log::error('Failed to update user rating metrics', [
                'user_id' => $userId,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Update approved rating metrics.
     */
    protected function updateApprovedMetrics(int $providerId): void
    {
        try {
            $approvedCount = DB::table('provider_ratings')
                ->where('provider_id', $providerId)
                ->where('status', 'approved')
                ->count();

            // Update provider record if you have an approved_ratings field
            // DB::table('providers')->where('id', $providerId)->update(['approved_ratings_count' => $approvedCount]);

            Log::info('Approved rating metrics updated', [
                'provider_id' => $providerId,
                'approved_count' => $approvedCount,
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to update approved rating metrics', [
                'provider_id' => $providerId,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Update rejected rating metrics.
     */
    protected function updateRejectedMetrics(int $providerId): void
    {
        try {
            $rejectedCount = DB::table('provider_ratings')
                ->where('provider_id', $providerId)
                ->where('status', 'rejected')
                ->count();

            // Update provider record if you have a rejected_ratings field
            // DB::table('providers')->where('id', $providerId)->update(['rejected_ratings_count' => $rejectedCount]);

            Log::info('Rejected rating metrics updated', [
                'provider_id' => $providerId,
                'rejected_count' => $rejectedCount,
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to update rejected rating metrics', [
                'provider_id' => $providerId,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Update flagged rating metrics.
     */
    protected function updateFlaggedMetrics(int $providerId): void
    {
        try {
            $flaggedCount = DB::table('provider_ratings')
                ->where('provider_id', $providerId)
                ->where('status', 'flagged')
                ->count();

            // Update provider record if you have a flagged_ratings field
            // DB::table('providers')->where('id', $providerId)->update(['flagged_ratings_count' => $flaggedCount]);

            Log::info('Flagged rating metrics updated', [
                'provider_id' => $providerId,
                'flagged_count' => $flaggedCount,
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to update flagged rating metrics', [
                'provider_id' => $providerId,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Update verified rating metrics.
     */
    protected function updateVerifiedMetrics(int $providerId): void
    {
        try {
            $verifiedCount = DB::table('provider_ratings')
                ->where('provider_id', $providerId)
                ->where('is_verified', true)
                ->where('status', 'approved')
                ->count();

            // Update provider record if you have a verified_ratings field
            // DB::table('providers')->where('id', $providerId)->update(['verified_ratings_count' => $verifiedCount]);

            Log::info('Verified rating metrics updated', [
                'provider_id' => $providerId,
                'verified_count' => $verifiedCount,
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to update verified rating metrics', [
                'provider_id' => $providerId,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Clear metrics cache.
     */
    protected function clearMetricsCache(int $providerId): void
    {
        try {
            Cache::forget("provider_metrics_{$providerId}");
            Cache::forget("provider_rating_breakdown_{$providerId}");
            Cache::forget("provider_recommendation_stats_{$providerId}");

            Log::info('Provider rating metrics cache cleared', ['provider_id' => $providerId]);
        } catch (\Exception $e) {
            Log::error('Failed to clear provider rating metrics cache', [
                'provider_id' => $providerId,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
