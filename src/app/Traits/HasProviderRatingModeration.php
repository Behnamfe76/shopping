<?php

namespace Fereydooni\Shopping\App\Traits;

use App\Models\ProviderRating;
use App\Repositories\Interfaces\ProviderRatingRepositoryInterface;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

trait HasProviderRatingModeration
{
    protected ProviderRatingRepositoryInterface $providerRatingRepository;

    /**
     * Get all pending ratings for moderation
     */
    public function getPendingRatingsForModeration(): Collection
    {
        return $this->providerRatingRepository->findPending();
    }

    /**
     * Get all flagged ratings for review
     */
    public function getFlaggedRatingsForReview(): Collection
    {
        return $this->providerRatingRepository->findFlagged();
    }

    /**
     * Get all rejected ratings
     */
    public function getRejectedRatings(): Collection
    {
        return $this->providerRatingRepository->findByStatus('rejected');
    }

    /**
     * Approve a rating
     */
    public function approveRating(int $ratingId, int $moderatorId, ?string $notes = null): bool
    {
        try {
            $rating = $this->providerRatingRepository->find($ratingId);

            if (! $rating) {
                Log::warning("Attempted to approve non-existent rating: {$ratingId}");

                return false;
            }

            $result = $this->providerRatingRepository->approve($rating);

            if ($result) {
                // Clear cache for this provider's ratings
                Cache::forget("provider_ratings_{$rating->provider_id}");
                Cache::forget("provider_rating_stats_{$rating->provider_id}");

                Log::info("Rating {$ratingId} approved by moderator {$moderatorId}");
            }

            return $result;
        } catch (\Exception $e) {
            Log::error("Error approving rating {$ratingId}: ".$e->getMessage());

            return false;
        }
    }

    /**
     * Reject a rating
     */
    public function rejectRating(int $ratingId, int $moderatorId, string $reason, ?string $notes = null): bool
    {
        try {
            $rating = $this->providerRatingRepository->find($ratingId);

            if (! $rating) {
                Log::warning("Attempted to reject non-existent rating: {$ratingId}");

                return false;
            }

            $result = $this->providerRatingRepository->reject($rating, $reason);

            if ($result) {
                Log::info("Rating {$ratingId} rejected by moderator {$moderatorId}. Reason: {$reason}");
            }

            return $result;
        } catch (\Exception $e) {
            Log::error("Error rejecting rating {$ratingId}: ".$e->getMessage());

            return false;
        }
    }

    /**
     * Flag a rating for review
     */
    public function flagRating(int $ratingId, int $moderatorId, string $reason, ?string $notes = null): bool
    {
        try {
            $rating = $this->providerRatingRepository->find($ratingId);

            if (! $rating) {
                Log::warning("Attempted to flag non-existent rating: {$ratingId}");

                return false;
            }

            $result = $this->providerRatingRepository->flag($rating, $reason);

            if ($result) {
                Log::info("Rating {$ratingId} flagged by moderator {$moderatorId}. Reason: {$reason}");
            }

            return $result;
        } catch (\Exception $e) {
            Log::error("Error flagging rating {$ratingId}: ".$e->getMessage());

            return false;
        }
    }

    /**
     * Verify a rating
     */
    public function verifyRating(int $ratingId): bool
    {
        try {
            $rating = $this->providerRatingRepository->find($ratingId);

            if (! $rating) {
                Log::warning("Attempted to verify non-existent rating: {$ratingId}");

                return false;
            }

            $result = $this->providerRatingRepository->verify($rating);

            if ($result) {
                // Clear cache for this provider's ratings
                Cache::forget("provider_ratings_{$rating->provider_id}");
                Cache::forget("provider_rating_stats_{$rating->provider_id}");

                Log::info("Rating {$ratingId} verified");
            }

            return $result;
        } catch (\Exception $e) {
            Log::error("Error verifying rating {$ratingId}: ".$e->getMessage());

            return false;
        }
    }

    /**
     * Get moderation statistics
     */
    public function getModerationStatistics(): array
    {
        return [
            'pending' => $this->providerRatingRepository->getPendingRatingCount(),
            'flagged' => $this->providerRatingRepository->getFlaggedRatingCount(),
            'rejected' => $this->providerRatingRepository->findByStatus('rejected')->count(),
            'approved' => $this->providerRatingRepository->findByStatus('approved')->count(),
            'verified' => $this->providerRatingRepository->getVerifiedRatingCount(),
        ];
    }

    /**
     * Bulk approve ratings
     */
    public function bulkApproveRatings(array $ratingIds, int $moderatorId): array
    {
        $results = [
            'success' => [],
            'failed' => [],
        ];

        foreach ($ratingIds as $ratingId) {
            if ($this->approveRating($ratingId, $moderatorId)) {
                $results['success'][] = $ratingId;
            } else {
                $results['failed'][] = $ratingId;
            }
        }

        return $results;
    }

    /**
     * Bulk reject ratings
     */
    public function bulkRejectRatings(array $ratingIds, int $moderatorId, string $reason): array
    {
        $results = [
            'success' => [],
            'failed' => [],
        ];

        foreach ($ratingIds as $ratingId) {
            if ($this->rejectRating($ratingId, $moderatorId, $reason)) {
                $results['success'][] = $ratingId;
            } else {
                $results['failed'][] = $ratingId;
            }
        }

        return $results;
    }

    /**
     * Get ratings requiring immediate attention
     */
    public function getRatingsRequiringAttention(): Collection
    {
        // Get flagged ratings and ratings with suspicious patterns
        $flagged = $this->providerRatingRepository->findFlagged();

        // You can add more logic here for suspicious patterns
        // For example, ratings with too many votes in short time, etc.

        return $flagged;
    }

    /**
     * Check if rating content is appropriate
     */
    public function isRatingContentAppropriate(string $content): bool
    {
        // Basic content filtering - you can enhance this with more sophisticated algorithms
        $inappropriateWords = [
            'spam', 'fake', 'scam', 'fraud', 'illegal', 'inappropriate',
        ];

        $content = strtolower($content);

        foreach ($inappropriateWords as $word) {
            if (str_contains($content, $word)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Auto-flag suspicious ratings
     */
    public function autoFlagSuspiciousRatings(): int
    {
        $flaggedCount = 0;

        // Get recent ratings
        $recentRatings = $this->providerRatingRepository->findByDateRange(
            now()->subDays(7)->toDateString(),
            now()->toDateString()
        );

        foreach ($recentRatings as $rating) {
            if ($this->shouldAutoFlagRating($rating)) {
                $this->flagRating(
                    $rating->id,
                    1, // System moderator ID
                    'Auto-flagged for suspicious activity',
                    'Automatically flagged by system'
                );
                $flaggedCount++;
            }
        }

        return $flaggedCount;
    }

    /**
     * Check if rating should be auto-flagged
     */
    protected function shouldAutoFlagRating(ProviderRating $rating): bool
    {
        // Check for suspicious patterns
        $suspicious = false;

        // Multiple ratings from same IP in short time
        $recentRatingsFromIP = $this->providerRatingRepository
            ->findByDateRange(
                now()->subHours(1)->toDateString(),
                now()->toDateString()
            )
            ->where('ip_address', $rating->ip_address)
            ->where('id', '!=', $rating->id);

        if ($recentRatingsFromIP->count() >= 3) {
            $suspicious = true;
        }

        // Rating with inappropriate content
        if (! $this->isRatingContentAppropriate($rating->comment)) {
            $suspicious = true;
        }

        // Extremely high or low ratings from new users
        if ($rating->rating_value <= 1 || $rating->rating_value >= 5) {
            $userRatingCount = $this->providerRatingRepository->getUserRatingCount($rating->user_id);
            if ($userRatingCount <= 2) {
                $suspicious = true;
            }
        }

        return $suspicious;
    }
}
