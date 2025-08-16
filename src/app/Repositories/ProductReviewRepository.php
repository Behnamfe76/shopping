<?php

namespace Fereydooni\Shopping\app\Repositories;

use Fereydooni\Shopping\app\Repositories\Interfaces\ProductReviewRepositoryInterface;
use Fereydooni\Shopping\app\Models\ProductReview;
use Fereydooni\Shopping\app\DTOs\ProductReviewDTO;
use Fereydooni\Shopping\app\Enums\ReviewStatus;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Carbon;

class ProductReviewRepository implements ProductReviewRepositoryInterface
{
    public function all(): Collection
    {
        return ProductReview::with(['product', 'user'])->get();
    }

    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        return ProductReview::with(['product', 'user'])
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    public function find(int $id): ?ProductReview
    {
        return ProductReview::with(['product', 'user', 'createdBy', 'updatedBy'])->find($id);
    }

    public function findDTO(int $id): ?ProductReviewDTO
    {
        $review = $this->find($id);
        return $review ? ProductReviewDTO::fromModel($review) : null;
    }

    public function create(array $data): ProductReview
    {
        $data['review_date'] = $data['review_date'] ?? Carbon::now();
        $data['sentiment_score'] = $data['sentiment_score'] ?? $this->analyzeSentiment($data['review']);

        $review = ProductReview::create($data);

        // Clear cache
        $this->clearCache($review->product_id);

        return $review->load(['product', 'user']);
    }

    public function createAndReturnDTO(array $data): ProductReviewDTO
    {
        $review = $this->create($data);
        return ProductReviewDTO::fromModel($review);
    }

    public function update(ProductReview $review, array $data): bool
    {
        if (isset($data['review'])) {
            $data['sentiment_score'] = $this->analyzeSentiment($data['review']);
        }

        $updated = $review->update($data);

        if ($updated) {
            $this->clearCache($review->product_id);
        }

        return $updated;
    }

    public function delete(ProductReview $review): bool
    {
        $productId = $review->product_id;
        $deleted = $review->delete();

        if ($deleted) {
            $this->clearCache($productId);
        }

        return $deleted;
    }

    public function findByProductId(int $productId): Collection
    {
        return ProductReview::with(['user'])
            ->where('product_id', $productId)
            ->where('status', ReviewStatus::APPROVED)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function findByUserId(int $userId): Collection
    {
        return ProductReview::with(['product'])
            ->where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function findByRating(int $rating): Collection
    {
        return ProductReview::with(['product', 'user'])
            ->where('rating', $rating)
            ->where('status', ReviewStatus::APPROVED)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function findByStatus(string $status): Collection
    {
        return ProductReview::with(['product', 'user'])
            ->where('status', $status)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function findByProductIdAndUserId(int $productId, int $userId): ?ProductReview
    {
        return ProductReview::where('product_id', $productId)
            ->where('user_id', $userId)
            ->first();
    }

    public function findByProductIdAndRating(int $productId, int $rating): Collection
    {
        return ProductReview::with(['user'])
            ->where('product_id', $productId)
            ->where('rating', $rating)
            ->where('status', ReviewStatus::APPROVED)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function findByProductIdAndStatus(int $productId, string $status): Collection
    {
        return ProductReview::with(['user'])
            ->where('product_id', $productId)
            ->where('status', $status)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function findApproved(): Collection
    {
        return ProductReview::with(['product', 'user'])
            ->approved()
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function findPending(): Collection
    {
        return ProductReview::with(['product', 'user'])
            ->pending()
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function findRejected(): Collection
    {
        return ProductReview::with(['product', 'user'])
            ->rejected()
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function findFeatured(): Collection
    {
        return ProductReview::with(['product', 'user'])
            ->featured()
            ->where('status', ReviewStatus::APPROVED)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function findVerified(): Collection
    {
        return ProductReview::with(['product', 'user'])
            ->verified()
            ->where('status', ReviewStatus::APPROVED)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function findByDateRange(string $startDate, string $endDate): Collection
    {
        return ProductReview::with(['product', 'user'])
            ->whereBetween('created_at', [$startDate, $endDate])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function findRecent(int $days = 30): Collection
    {
        return ProductReview::with(['product', 'user'])
            ->recent($days)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function findPopular(int $limit = 10): Collection
    {
        return ProductReview::with(['product', 'user'])
            ->popular()
            ->where('status', ReviewStatus::APPROVED)
            ->limit($limit)
            ->get();
    }

    public function findHelpful(int $limit = 10): Collection
    {
        return ProductReview::with(['product', 'user'])
            ->where('status', ReviewStatus::APPROVED)
            ->where('helpful_votes', '>', 0)
            ->orderBy('helpful_votes', 'desc')
            ->limit($limit)
            ->get();
    }

    public function findBySentimentScore(float $minScore, float $maxScore): Collection
    {
        return ProductReview::with(['product', 'user'])
            ->whereBetween('sentiment_score', [$minScore, $maxScore])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function findPositive(): Collection
    {
        return ProductReview::with(['product', 'user'])
            ->positive()
            ->where('status', ReviewStatus::APPROVED)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function findNegative(): Collection
    {
        return ProductReview::with(['product', 'user'])
            ->negative()
            ->where('status', ReviewStatus::APPROVED)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function findNeutral(): Collection
    {
        return ProductReview::with(['product', 'user'])
            ->neutral()
            ->where('status', ReviewStatus::APPROVED)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function approve(ProductReview $review): bool
    {
        $updated = $review->update([
            'status' => ReviewStatus::APPROVED,
            'moderation_status' => 'approved',
            'updated_by' => auth()->id()
        ]);

        if ($updated) {
            $this->clearCache($review->product_id);
        }

        return $updated;
    }

    public function reject(ProductReview $review, string $reason = null): bool
    {
        $updated = $review->update([
            'status' => ReviewStatus::REJECTED,
            'moderation_status' => 'rejected',
            'moderation_notes' => $reason,
            'updated_by' => auth()->id()
        ]);

        if ($updated) {
            $this->clearCache($review->product_id);
        }

        return $updated;
    }

    public function feature(ProductReview $review): bool
    {
        $updated = $review->update([
            'is_featured' => true,
            'updated_by' => auth()->id()
        ]);

        if ($updated) {
            $this->clearCache($review->product_id);
        }

        return $updated;
    }

    public function unfeature(ProductReview $review): bool
    {
        $updated = $review->update([
            'is_featured' => false,
            'updated_by' => auth()->id()
        ]);

        if ($updated) {
            $this->clearCache($review->product_id);
        }

        return $updated;
    }

    public function verify(ProductReview $review): bool
    {
        $updated = $review->update([
            'is_verified' => true,
            'updated_by' => auth()->id()
        ]);

        if ($updated) {
            $this->clearCache($review->product_id);
        }

        return $updated;
    }

    public function unverify(ProductReview $review): bool
    {
        $updated = $review->update([
            'is_verified' => false,
            'updated_by' => auth()->id()
        ]);

        if ($updated) {
            $this->clearCache($review->product_id);
        }

        return $updated;
    }

    public function incrementHelpfulVotes(ProductReview $review): bool
    {
        $updated = $review->increment('helpful_votes');

        if ($updated) {
            $this->clearCache($review->product_id);
        }

        return $updated;
    }

    public function decrementHelpfulVotes(ProductReview $review): bool
    {
        $updated = $review->decrement('helpful_votes');

        if ($updated) {
            $this->clearCache($review->product_id);
        }

        return $updated;
    }

    public function addVote(ProductReview $review, bool $isHelpful): bool
    {
        $review->increment('total_votes');

        if ($isHelpful) {
            $review->increment('helpful_votes');
        }

        $this->clearCache($review->product_id);

        return true;
    }

    public function removeVote(ProductReview $review, int $userId): bool
    {
        // This would typically involve a separate votes table
        // For now, we'll just return true
        return true;
    }

    public function search(string $query): Collection
    {
        return ProductReview::with(['product', 'user'])
            ->where(function ($q) use ($query) {
                $q->where('review', 'like', "%{$query}%")
                  ->orWhere('title', 'like', "%{$query}%")
                  ->orWhere('pros', 'like', "%{$query}%")
                  ->orWhere('cons', 'like', "%{$query}%");
            })
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function getReviewCount(): int
    {
        return Cache::remember('product_reviews_count', 3600, function () {
            return ProductReview::count();
        });
    }

    public function getReviewCountByProductId(int $productId): int
    {
        return Cache::remember("product_reviews_count_{$productId}", 3600, function () use ($productId) {
            return ProductReview::where('product_id', $productId)
                ->where('status', ReviewStatus::APPROVED)
                ->count();
        });
    }

    public function getReviewCountByUserId(int $userId): int
    {
        return ProductReview::where('user_id', $userId)->count();
    }

    public function getReviewCountByRating(int $rating): int
    {
        return ProductReview::where('rating', $rating)
            ->where('status', ReviewStatus::APPROVED)
            ->count();
    }

    public function getReviewCountByStatus(string $status): int
    {
        return ProductReview::where('status', $status)->count();
    }

    public function getAverageRating(int $productId): float
    {
        return Cache::remember("product_average_rating_{$productId}", 3600, function () use ($productId) {
            return ProductReview::where('product_id', $productId)
                ->where('status', ReviewStatus::APPROVED)
                ->avg('rating') ?? 0.0;
        });
    }

    public function getRatingDistribution(int $productId): array
    {
        return Cache::remember("product_rating_distribution_{$productId}", 3600, function () use ($productId) {
            $distribution = [];

            for ($i = 1; $i <= 5; $i++) {
                $distribution[$i] = ProductReview::where('product_id', $productId)
                    ->where('rating', $i)
                    ->where('status', ReviewStatus::APPROVED)
                    ->count();
            }

            return $distribution;
        });
    }

    public function getReviewStats(int $productId): array
    {
        return Cache::remember("product_review_stats_{$productId}", 3600, function () use ($productId) {
            $reviews = ProductReview::where('product_id', $productId)
                ->where('status', ReviewStatus::APPROVED);

            return [
                'total_reviews' => $reviews->count(),
                'average_rating' => $reviews->avg('rating') ?? 0.0,
                'rating_distribution' => $this->getRatingDistribution($productId),
                'featured_reviews' => $reviews->where('is_featured', true)->count(),
                'verified_reviews' => $reviews->where('is_verified', true)->count(),
                'verified_purchases' => $reviews->where('verified_purchase', true)->count(),
            ];
        });
    }

    public function getReviewAnalytics(int $reviewId): array
    {
        $review = $this->find($reviewId);

        if (!$review) {
            return [];
        }

        return [
            'helpful_percentage' => $review->getHelpfulPercentage(),
            'sentiment_label' => $review->getSentimentLabel(),
            'days_since_created' => $review->created_at->diffInDays(now()),
            'total_votes' => $review->total_votes,
            'helpful_votes' => $review->helpful_votes,
        ];
    }

    public function getReviewAnalyticsByProduct(int $productId): array
    {
        return Cache::remember("product_review_analytics_{$productId}", 3600, function () use ($productId) {
            $reviews = ProductReview::where('product_id', $productId)
                ->where('status', ReviewStatus::APPROVED);

            return [
                'total_reviews' => $reviews->count(),
                'average_sentiment' => $reviews->avg('sentiment_score') ?? 0.0,
                'positive_reviews' => $reviews->positive()->count(),
                'negative_reviews' => $reviews->negative()->count(),
                'neutral_reviews' => $reviews->neutral()->count(),
                'total_votes' => $reviews->sum('total_votes'),
                'total_helpful_votes' => $reviews->sum('helpful_votes'),
            ];
        });
    }

    public function getReviewAnalyticsByUser(int $userId): array
    {
        $reviews = ProductReview::where('user_id', $userId);

        return [
            'total_reviews' => $reviews->count(),
            'approved_reviews' => $reviews->where('status', ReviewStatus::APPROVED)->count(),
            'pending_reviews' => $reviews->where('status', ReviewStatus::PENDING)->count(),
            'rejected_reviews' => $reviews->where('status', ReviewStatus::REJECTED)->count(),
            'average_rating' => $reviews->where('status', ReviewStatus::APPROVED)->avg('rating') ?? 0.0,
            'featured_reviews' => $reviews->where('is_featured', true)->count(),
            'verified_reviews' => $reviews->where('is_verified', true)->count(),
        ];
    }

    public function analyzeSentiment(string $text): float
    {
        // Simple sentiment analysis implementation
        // In a real application, you would use a proper sentiment analysis service
        $positiveWords = ['good', 'great', 'excellent', 'amazing', 'wonderful', 'perfect', 'love', 'like', 'best', 'awesome'];
        $negativeWords = ['bad', 'terrible', 'awful', 'horrible', 'worst', 'hate', 'dislike', 'poor', 'disappointing'];

        $text = strtolower($text);
        $words = str_word_count($text, 1);

        $positiveCount = 0;
        $negativeCount = 0;

        foreach ($words as $word) {
            if (in_array($word, $positiveWords)) {
                $positiveCount++;
            } elseif (in_array($word, $negativeWords)) {
                $negativeCount++;
            }
        }

        $total = count($words);

        if ($total === 0) {
            return 0.0;
        }

        return ($positiveCount - $negativeCount) / $total;
    }

    public function moderateReview(ProductReview $review): bool
    {
        // Auto-moderation based on sentiment and content
        $sentiment = $this->analyzeSentiment($review->review);

        if ($sentiment < -0.5) {
            return $this->reject($review, 'Automatically rejected due to negative sentiment');
        }

        if (strlen($review->review) < 10) {
            return $this->reject($review, 'Review too short');
        }

        return $this->approve($review);
    }

    public function flagReview(ProductReview $review, string $reason): bool
    {
        return $review->update([
            'status' => ReviewStatus::FLAGGED,
            'moderation_status' => 'flagged',
            'moderation_notes' => $reason,
            'updated_by' => auth()->id()
        ]);
    }

    public function getFlaggedReviews(): Collection
    {
        return ProductReview::with(['product', 'user'])
            ->where('status', ReviewStatus::FLAGGED)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function getModerationQueue(): Collection
    {
        return ProductReview::with(['product', 'user'])
            ->where('status', ReviewStatus::PENDING)
            ->orderBy('created_at', 'asc')
            ->get();
    }

    private function clearCache(int $productId): void
    {
        Cache::forget("product_reviews_count_{$productId}");
        Cache::forget("product_average_rating_{$productId}");
        Cache::forget("product_rating_distribution_{$productId}");
        Cache::forget("product_review_stats_{$productId}");
        Cache::forget("product_review_analytics_{$productId}");
    }
}
