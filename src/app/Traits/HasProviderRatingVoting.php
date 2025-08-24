<?php

namespace Fereydooni\Shopping\App\Traits;

use App\Models\ProviderRating;
use App\DTOs\ProviderRatingDTO;
use App\Repositories\Interfaces\ProviderRatingRepositoryInterface;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

trait HasProviderRatingVoting
{
    protected ProviderRatingRepositoryInterface $providerRatingRepository;

    /**
     * Add a helpful vote to a rating
     */
    public function addHelpfulVote(int $ratingId, int $userId): bool
    {
        try {
            $rating = $this->providerRatingRepository->find($ratingId);

            if (!$rating) {
                Log::warning("Attempted to vote on non-existent rating: {$ratingId}");
                return false;
            }

            if (!$rating->canBeVotedOn()) {
                Log::warning("Rating {$ratingId} cannot be voted on");
                return false;
            }

            $result = $this->providerRatingRepository->addHelpfulVote($rating, $userId);

            if ($result) {
                // Clear cache for this rating
                Cache::forget("rating_{$ratingId}_votes");
                Cache::forget("provider_rating_stats_{$rating->provider_id}");

                Log::info("Helpful vote added to rating {$ratingId} by user {$userId}");
            }

            return $result;
        } catch (\Exception $e) {
            Log::error("Error adding helpful vote to rating {$ratingId}: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Remove a helpful vote from a rating
     */
    public function removeHelpfulVote(int $ratingId, int $userId): bool
    {
        try {
            $rating = $this->providerRatingRepository->find($ratingId);

            if (!$rating) {
                Log::warning("Attempted to remove vote from non-existent rating: {$ratingId}");
                return false;
            }

            $result = $this->providerRatingRepository->removeHelpfulVote($rating, $userId);

            if ($result) {
                // Clear cache for this rating
                Cache::forget("rating_{$ratingId}_votes");
                Cache::forget("provider_rating_stats_{$rating->provider_id}");

                Log::info("Helpful vote removed from rating {$ratingId} by user {$userId}");
            }

            return $result;
        } catch (\Exception $e) {
            Log::error("Error removing helpful vote from rating {$ratingId}: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Add a vote to a rating (helpful or not helpful)
     */
    public function addVote(int $ratingId, int $userId, bool $isHelpful): bool
    {
        try {
            $rating = $this->providerRatingRepository->find($ratingId);

            if (!$rating) {
                Log::warning("Attempted to vote on non-existent rating: {$ratingId}");
                return false;
            }

            if (!$rating->canBeVotedOn()) {
                Log::warning("Rating {$ratingId} cannot be voted on");
                return false;
            }

            $result = $rating->addVote($userId, $isHelpful);

            if ($result) {
                // Clear cache for this rating
                Cache::forget("rating_{$ratingId}_votes");
                Cache::forget("provider_rating_stats_{$rating->provider_id}");

                Log::info("Vote added to rating {$ratingId} by user {$userId}. Helpful: " . ($isHelpful ? 'yes' : 'no'));
            }

            return $result;
        } catch (\Exception $e) {
            Log::error("Error adding vote to rating {$ratingId}: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get most helpful ratings
     */
    public function getMostHelpfulRatings(int $limit = 10): Collection
    {
        return $this->providerRatingRepository->getMostHelpfulRatings($limit);
    }

    /**
     * Get most helpful ratings as DTOs
     */
    public function getMostHelpfulRatingsDTO(int $limit = 10): Collection
    {
        return $this->providerRatingRepository->getMostHelpfulRatingsDTO($limit);
    }

    /**
     * Get ratings by helpfulness percentage
     */
    public function getRatingsByHelpfulness(float $minPercentage = 80.0): Collection
    {
        $ratings = $this->providerRatingRepository->findApproved();

        return $ratings->filter(function ($rating) use ($minPercentage) {
            return $rating->helpful_percentage >= $minPercentage;
        });
    }

    /**
     * Get user's voting history
     */
    public function getUserVotingHistory(int $userId): Collection
    {
        // This would need to be implemented in the repository
        // For now, return empty collection
        return collect();
    }

    /**
     * Check if user has voted on a rating
     */
    public function hasUserVoted(int $ratingId, int $userId): bool
    {
        $rating = $this->providerRatingRepository->find($ratingId);

        if (!$rating) {
            return false;
        }

        return $rating->votes()->where('user_id', $userId)->exists();
    }

    /**
     * Get user's vote on a rating
     */
    public function getUserVote(int $ratingId, int $userId): ?bool
    {
        $rating = $this->providerRatingRepository->find($ratingId);

        if (!$rating) {
            return null;
        }

        $vote = $rating->votes()->where('user_id', $userId)->first();

        return $vote ? $vote->is_helpful : null;
    }

    /**
     * Get rating vote statistics
     */
    public function getRatingVoteStatistics(int $ratingId): array
    {
        $rating = $this->providerRatingRepository->find($ratingId);

        if (!$rating) {
            return [
                'helpful_votes' => 0,
                'total_votes' => 0,
                'helpful_percentage' => 0,
                'user_vote' => null
            ];
        }

        return [
            'helpful_votes' => $rating->helpful_votes,
            'total_votes' => $rating->total_votes,
            'helpful_percentage' => $rating->helpful_percentage,
            'user_vote' => null // Would need to be set by caller with user context
        ];
    }

    /**
     * Get provider rating vote statistics
     */
    public function getProviderVoteStatistics(int $providerId): array
    {
        $ratings = $this->providerRatingRepository->findByProviderId($providerId);

        $totalVotes = 0;
        $totalHelpfulVotes = 0;
        $averageHelpfulPercentage = 0;

        foreach ($ratings as $rating) {
            $totalVotes += $rating->total_votes;
            $totalHelpfulVotes += $rating->helpful_votes;
            $averageHelpfulPercentage += $rating->helpful_percentage;
        }

        $ratingCount = $ratings->count();

        return [
            'total_votes' => $totalVotes,
            'total_helpful_votes' => $totalHelpfulVotes,
            'average_helpful_percentage' => $ratingCount > 0 ? round($averageHelpfulPercentage / $ratingCount, 2) : 0,
            'rating_count' => $ratingCount
        ];
    }

    /**
     * Get trending ratings (ratings with recent high vote activity)
     */
    public function getTrendingRatings(int $limit = 10): Collection
    {
        // Get ratings with recent voting activity
        $recentRatings = $this->providerRatingRepository->findByDateRange(
            now()->subDays(7)->toDateString(),
            now()->toDateString()
        );

        // Sort by helpful votes in the last 7 days
        return $recentRatings
            ->sortByDesc('helpful_votes')
            ->take($limit);
    }

    /**
     * Get controversial ratings (ratings with mixed votes)
     */
    public function getControversialRatings(int $limit = 10): Collection
    {
        $ratings = $this->providerRatingRepository->findApproved();

        // Filter ratings with mixed votes (not too high or too low helpful percentage)
        $controversial = $ratings->filter(function ($rating) {
            $percentage = $rating->helpful_percentage;
            return $percentage >= 30 && $percentage <= 70 && $rating->total_votes >= 5;
        });

        return $controversial
            ->sortByDesc('total_votes')
            ->take($limit);
    }

    /**
     * Get user's voting impact score
     */
    public function getUserVotingImpactScore(int $userId): float
    {
        // This would calculate how much a user's votes align with the community
        // For now, return a placeholder value
        return 0.0;
    }

    /**
     * Get rating quality score based on votes
     */
    public function getRatingQualityScore(int $ratingId): float
    {
        $rating = $this->providerRatingRepository->find($ratingId);

        if (!$rating) {
            return 0.0;
        }

        // Calculate quality score based on helpful votes and total votes
        $voteRatio = $rating->total_votes > 0 ? $rating->helpful_votes / $rating->total_votes : 0;
        $voteVolume = min($rating->total_votes / 10, 1.0); // Normalize to 0-1

        return round(($voteRatio * 0.7 + $voteVolume * 0.3) * 100, 2);
    }
}
