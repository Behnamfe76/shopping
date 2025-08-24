<?php

namespace App\Repositories\Interfaces;

use App\Models\ProviderRating;
use App\DTOs\ProviderRatingDTO;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Pagination\CursorPaginator;

interface ProviderRatingRepositoryInterface
{
    // Basic CRUD operations
    public function all(): Collection;
    public function paginate(int $perPage = 15): LengthAwarePaginator;
    public function simplePaginate(int $perPage = 15): Paginator;
    public function cursorPaginate(int $perPage = 15, string $cursor = null): CursorPaginator;

    // Find operations
    public function find(int $id): ?ProviderRating;
    public function findDTO(int $id): ?ProviderRatingDTO;
    public function findByProviderId(int $providerId): Collection;
    public function findByProviderIdDTO(int $providerId): Collection;
    public function findByUserId(int $userId): Collection;
    public function findByUserIdDTO(int $userId): Collection;
    public function findByCategory(string $category): Collection;
    public function findByCategoryDTO(string $category): Collection;
    public function findByStatus(string $status): Collection;
    public function findByStatusDTO(string $status): Collection;
    public function findByRatingValue(float $ratingValue): Collection;
    public function findByRatingValueDTO(float $ratingValue): Collection;
    public function findByRatingRange(float $minRating, float $maxRating): Collection;
    public function findByRatingRangeDTO(float $minRating, float $maxRating): Collection;
    public function findByProviderAndCategory(int $providerId, string $category): Collection;
    public function findByProviderAndCategoryDTO(int $providerId, string $category): Collection;
    public function findByProviderAndUser(int $providerId, int $userId): Collection;
    public function findByProviderAndUserDTO(int $providerId, int $userId): Collection;

    // Status-based queries
    public function findVerified(): Collection;
    public function findVerifiedDTO(): Collection;
    public function findUnverified(): Collection;
    public function findUnverifiedDTO(): Collection;
    public function findApproved(): Collection;
    public function findApprovedDTO(): Collection;
    public function findPending(): Collection;
    public function findPendingDTO(): Collection;
    public function findFlagged(): Collection;
    public function findFlaggedDTO(): Collection;

    // Recommendation queries
    public function findRecommended(): Collection;
    public function findRecommendedDTO(): Collection;
    public function findNotRecommended(): Collection;
    public function findNotRecommendedDTO(): Collection;

    // Date-based queries
    public function findByDateRange(string $startDate, string $endDate): Collection;
    public function findByDateRangeDTO(string $startDate, string $endDate): Collection;

    // Create, Update, Delete operations
    public function create(array $data): ProviderRating;
    public function createAndReturnDTO(array $data): ProviderRatingDTO;
    public function update(ProviderRating $rating, array $data): bool;
    public function updateAndReturnDTO(ProviderRating $rating, array $data): ?ProviderRatingDTO;
    public function delete(ProviderRating $rating): bool;

    // Moderation operations
    public function approve(ProviderRating $rating): bool;
    public function reject(ProviderRating $rating, string $reason = null): bool;
    public function flag(ProviderRating $rating, string $reason = null): bool;
    public function verify(ProviderRating $rating): bool;

    // Voting operations
    public function addHelpfulVote(ProviderRating $rating, int $userId): bool;
    public function removeHelpfulVote(ProviderRating $rating, int $userId): bool;

    // Analytics and statistics
    public function getProviderAverageRating(int $providerId, string $category = null): float;
    public function getProviderRatingCount(int $providerId, string $category = null): int;
    public function getProviderRatingBreakdown(int $providerId): array;
    public function getProviderRecommendationPercentage(int $providerId): float;
    public function getUserRatingCount(int $userId): int;
    public function getUserAverageRating(int $userId): float;
    public function getTotalRatingCount(): int;
    public function getAverageRating(): float;
    public function getRatingCountByValue(float $ratingValue): int;
    public function getRatingCountByCategory(string $category): int;
    public function getVerifiedRatingCount(): int;
    public function getPendingRatingCount(): int;
    public function getFlaggedRatingCount(): int;
    public function getRecommendationPercentage(): float;

    // Search operations
    public function searchRatings(string $query): Collection;
    public function searchRatingsDTO(string $query): Collection;
    public function searchRatingsByProvider(int $providerId, string $query): Collection;
    public function searchRatingsByProviderDTO(int $providerId, string $query): Collection;

    // Import/Export operations
    public function exportRatingData(array $filters = []): string;
    public function importRatingData(string $data): bool;

    // Advanced analytics
    public function getRatingStatistics(): array;
    public function getProviderRatingStatistics(int $providerId): array;
    public function getRatingTrends(string $startDate = null, string $endDate = null): array;
    public function calculateRatingMetrics(int $providerId): array;

    // Top ratings and providers
    public function getMostHelpfulRatings(int $limit = 10): Collection;
    public function getMostHelpfulRatingsDTO(int $limit = 10): Collection;
    public function getTopRatedProviders(int $limit = 10): Collection;
    public function getTopRatedProvidersDTO(int $limit = 10): Collection;
}
