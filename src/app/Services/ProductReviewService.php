<?php

namespace Fereydooni\Shopping\app\Services;

use Fereydooni\Shopping\app\Repositories\Interfaces\ProductReviewRepositoryInterface;
use Fereydooni\Shopping\app\Models\ProductReview;
use Fereydooni\Shopping\app\DTOs\ProductReviewDTO;
use Fereydooni\Shopping\app\Traits\HasCrudOperations;
use Fereydooni\Shopping\app\Traits\HasStatusToggle;
use Fereydooni\Shopping\app\Traits\HasSearchOperations;
use Fereydooni\Shopping\app\Traits\HasVoteManagement;
use Fereydooni\Shopping\app\Traits\HasSentimentAnalysis;
use Fereydooni\Shopping\app\Traits\HasAnalyticsOperations;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class ProductReviewService
{
    use HasCrudOperations;
    use HasStatusToggle;
    use HasSearchOperations;
    use HasVoteManagement;
    use HasSentimentAnalysis;
    use HasAnalyticsOperations;

    public function __construct(
        private ProductReviewRepositoryInterface $repository
    ) {
    }

    // Basic CRUD operations
    public function all(): Collection
    {
        return $this->repository->all();
    }

    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        return $this->repository->paginate($perPage);
    }

    public function find(int $id): ?ProductReview
    {
        return $this->repository->find($id);
    }

    public function findDTO(int $id): ?ProductReviewDTO
    {
        return $this->repository->findDTO($id);
    }

    public function create(array $data): ProductReview
    {
        return $this->repository->create($data);
    }

    public function createAndReturnDTO(array $data): ProductReviewDTO
    {
        return $this->repository->createAndReturnDTO($data);
    }

    public function update(ProductReview $review, array $data): bool
    {
        return $this->repository->update($review, $data);
    }

    public function delete(ProductReview $review): bool
    {
        return $this->repository->delete($review);
    }

    // Find by specific criteria
    public function findByProductId(int $productId): Collection
    {
        return $this->repository->findByProductId($productId);
    }

    public function findByUserId(int $userId): Collection
    {
        return $this->repository->findByUserId($userId);
    }

    public function findByRating(int $rating): Collection
    {
        return $this->repository->findByRating($rating);
    }

    public function findByStatus(string $status): Collection
    {
        return $this->repository->findByStatus($status);
    }

    public function findByProductIdAndUserId(int $productId, int $userId): ?ProductReview
    {
        return $this->repository->findByProductIdAndUserId($productId, $userId);
    }

    public function findByProductIdAndRating(int $productId, int $rating): Collection
    {
        return $this->repository->findByProductIdAndRating($productId, $rating);
    }

    public function findByProductIdAndStatus(int $productId, string $status): Collection
    {
        return $this->repository->findByProductIdAndStatus($productId, $status);
    }

    // Status-based queries
    public function findApproved(): Collection
    {
        return $this->repository->findApproved();
    }

    public function findPending(): Collection
    {
        return $this->repository->findPending();
    }

    public function findRejected(): Collection
    {
        return $this->repository->findRejected();
    }

    public function findFeatured(): Collection
    {
        return $this->repository->findFeatured();
    }

    public function findVerified(): Collection
    {
        return $this->repository->findVerified();
    }

    // Date and time-based queries
    public function findByDateRange(string $startDate, string $endDate): Collection
    {
        return $this->repository->findByDateRange($startDate, $endDate);
    }

    public function findRecent(int $days = 30): Collection
    {
        return $this->repository->findRecent($days);
    }

    // Popularity and helpfulness queries
    public function findPopular(int $limit = 10): Collection
    {
        return $this->repository->findPopular($limit);
    }

    public function findHelpful(int $limit = 10): Collection
    {
        return $this->repository->findHelpful($limit);
    }

    // Sentiment-based queries
    public function findBySentimentScore(float $minScore, float $maxScore): Collection
    {
        return $this->repository->findBySentimentScore($minScore, $maxScore);
    }

    public function findPositive(): Collection
    {
        return $this->repository->findPositive();
    }

    public function findNegative(): Collection
    {
        return $this->repository->findNegative();
    }

    public function findNeutral(): Collection
    {
        return $this->repository->findNeutral();
    }

    // Status management
    public function approve(ProductReview $review): bool
    {
        return $this->repository->approve($review);
    }

    public function reject(ProductReview $review, string $reason = null): bool
    {
        return $this->repository->reject($review, $reason);
    }

    public function feature(ProductReview $review): bool
    {
        return $this->repository->feature($review);
    }

    public function unfeature(ProductReview $review): bool
    {
        return $this->repository->unfeature($review);
    }

    public function verify(ProductReview $review): bool
    {
        return $this->repository->verify($review);
    }

    public function unverify(ProductReview $review): bool
    {
        return $this->repository->unverify($review);
    }

    // Vote management
    public function incrementHelpfulVotes(ProductReview $review): bool
    {
        return $this->repository->incrementHelpfulVotes($review);
    }

    public function decrementHelpfulVotes(ProductReview $review): bool
    {
        return $this->repository->decrementHelpfulVotes($review);
    }

    public function addVote(ProductReview $review, bool $isHelpful): bool
    {
        return $this->repository->addVote($review, $isHelpful);
    }

    public function removeVote(ProductReview $review, int $userId): bool
    {
        return $this->repository->removeVote($review, $userId);
    }

    // Search functionality
    public function search(string $query): Collection
    {
        return $this->repository->search($query);
    }

    // Count operations
    public function getReviewCount(): int
    {
        return $this->repository->getReviewCount();
    }

    public function getReviewCountByProductId(int $productId): int
    {
        return $this->repository->getReviewCountByProductId($productId);
    }

    public function getReviewCountByUserId(int $userId): int
    {
        return $this->repository->getReviewCountByUserId($userId);
    }

    public function getReviewCountByRating(int $rating): int
    {
        return $this->repository->getReviewCountByRating($rating);
    }

    public function getReviewCountByStatus(string $status): int
    {
        return $this->repository->getReviewCountByStatus($status);
    }

    // Rating and analytics
    public function getAverageRating(int $productId): float
    {
        return $this->repository->getAverageRating($productId);
    }

    public function getRatingDistribution(int $productId): array
    {
        return $this->repository->getRatingDistribution($productId);
    }

    public function getReviewStats(int $productId): array
    {
        return $this->repository->getReviewStats($productId);
    }

    public function getReviewAnalytics(int $reviewId): array
    {
        return $this->repository->getReviewAnalytics($reviewId);
    }

    public function getReviewAnalyticsByProduct(int $productId): array
    {
        return $this->repository->getReviewAnalyticsByProduct($productId);
    }

    public function getReviewAnalyticsByUser(int $userId): array
    {
        return $this->repository->getReviewAnalyticsByUser($userId);
    }

    // Sentiment analysis
    public function analyzeSentiment(string $text): float
    {
        return $this->repository->analyzeSentiment($text);
    }

    // Moderation
    public function moderateReview(ProductReview $review): bool
    {
        return $this->repository->moderateReview($review);
    }

    public function flagReview(ProductReview $review, string $reason): bool
    {
        return $this->repository->flagReview($review, $reason);
    }

    public function getFlaggedReviews(): Collection
    {
        return $this->repository->getFlaggedReviews();
    }

    public function getModerationQueue(): Collection
    {
        return $this->repository->getModerationQueue();
    }

    // Additional helper methods
    public function validateReview(array $data): array
    {
        return ProductReviewDTO::rules();
    }

    public function getValidationMessages(): array
    {
        return ProductReviewDTO::messages();
    }

    public function canUserReviewProduct(int $userId, int $productId): bool
    {
        // Check if user has already reviewed this product
        $existingReview = $this->findByProductIdAndUserId($productId, $userId);
        return $existingReview === null;
    }

    public function getProductReviewSummary(int $productId): array
    {
        $stats = $this->getReviewStats($productId);
        $recentReviews = $this->findByProductId($productId)->take(5);

        return [
            'stats' => $stats,
            'recent_reviews' => $recentReviews->map(fn($review) => ProductReviewDTO::fromModel($review)),
            'average_rating' => $stats['average_rating'],
            'total_reviews' => $stats['total_reviews'],
        ];
    }

    public function getTopReviewedProducts(int $limit = 10): Collection
    {
        // This would typically involve a more complex query
        // For now, we'll return the most recent reviews
        return $this->findRecent(30)->take($limit);
    }

    public function getReviewTrends(int $productId, int $days = 30): array
    {
        $reviews = $this->findByDateRange(
            now()->subDays($days)->toDateString(),
            now()->toDateString()
        );

        $trends = [];
        for ($i = 0; $i < $days; $i++) {
            $date = now()->subDays($i)->toDateString();
            $dayReviews = $reviews->filter(fn($review) => $review->created_at->toDateString() === $date);

            $trends[$date] = [
                'count' => $dayReviews->count(),
                'average_rating' => $dayReviews->avg('rating') ?? 0,
                'positive_count' => $dayReviews->filter(fn($review) => $review->sentiment_score > 0.3)->count(),
                'negative_count' => $dayReviews->filter(fn($review) => $review->sentiment_score < -0.3)->count(),
            ];
        }

        return $trends;
    }
}
