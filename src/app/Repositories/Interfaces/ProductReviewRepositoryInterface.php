<?php

namespace Fereydooni\Shopping\app\Repositories\Interfaces;

use Fereydooni\Shopping\app\DTOs\ProductReviewDTO;
use Fereydooni\Shopping\app\Models\ProductReview;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

interface ProductReviewRepositoryInterface
{
    // Basic CRUD operations
    public function all(): Collection;

    public function paginate(int $perPage = 15): LengthAwarePaginator;

    public function find(int $id): ?ProductReview;

    public function findDTO(int $id): ?ProductReviewDTO;

    public function create(array $data): ProductReview;

    public function createAndReturnDTO(array $data): ProductReviewDTO;

    public function update(ProductReview $review, array $data): bool;

    public function delete(ProductReview $review): bool;

    // Find by specific criteria
    public function findByProductId(int $productId): Collection;

    public function findByUserId(int $userId): Collection;

    public function findByRating(int $rating): Collection;

    public function findByStatus(string $status): Collection;

    public function findByProductIdAndUserId(int $productId, int $userId): ?ProductReview;

    public function findByProductIdAndRating(int $productId, int $rating): Collection;

    public function findByProductIdAndStatus(int $productId, string $status): Collection;

    // Status-based queries
    public function findApproved(): Collection;

    public function findPending(): Collection;

    public function findRejected(): Collection;

    public function findFeatured(): Collection;

    public function findVerified(): Collection;

    // Date and time-based queries
    public function findByDateRange(string $startDate, string $endDate): Collection;

    public function findRecent(int $days = 30): Collection;

    // Popularity and helpfulness queries
    public function findPopular(int $limit = 10): Collection;

    public function findHelpful(int $limit = 10): Collection;

    // Sentiment-based queries
    public function findBySentimentScore(float $minScore, float $maxScore): Collection;

    public function findPositive(): Collection;

    public function findNegative(): Collection;

    public function findNeutral(): Collection;

    // Status management
    public function approve(ProductReview $review): bool;

    public function reject(ProductReview $review, ?string $reason = null): bool;

    public function feature(ProductReview $review): bool;

    public function unfeature(ProductReview $review): bool;

    public function verify(ProductReview $review): bool;

    public function unverify(ProductReview $review): bool;

    // Vote management
    public function incrementHelpfulVotes(ProductReview $review): bool;

    public function decrementHelpfulVotes(ProductReview $review): bool;

    public function addVote(ProductReview $review, bool $isHelpful): bool;

    public function removeVote(ProductReview $review, int $userId): bool;

    // Search functionality
    public function search(string $query): Collection;

    // Count operations
    public function getReviewCount(): int;

    public function getReviewCountByProductId(int $productId): int;

    public function getReviewCountByUserId(int $userId): int;

    public function getReviewCountByRating(int $rating): int;

    public function getReviewCountByStatus(string $status): int;

    // Rating and analytics
    public function getAverageRating(int $productId): float;

    public function getRatingDistribution(int $productId): array;

    public function getReviewStats(int $productId): array;

    public function getReviewAnalytics(int $reviewId): array;

    public function getReviewAnalyticsByProduct(int $productId): array;

    public function getReviewAnalyticsByUser(int $userId): array;

    // Sentiment analysis
    public function analyzeSentiment(string $text): float;

    // Moderation
    public function moderateReview(ProductReview $review): bool;

    public function flagReview(ProductReview $review, string $reason): bool;

    public function getFlaggedReviews(): Collection;

    public function getModerationQueue(): Collection;
}
