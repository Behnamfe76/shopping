<?php

namespace Fereydooni\Shopping\app\Repositories\Interfaces;

use Fereydooni\Shopping\app\DTOs\CustomerWishlistDTO;
use Fereydooni\Shopping\app\Models\CustomerWishlist;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\CursorPaginator;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;

interface CustomerWishlistRepositoryInterface
{
    // Basic CRUD operations
    public function all(): Collection;

    public function paginate(int $perPage = 15): LengthAwarePaginator;

    public function simplePaginate(int $perPage = 15): Paginator;

    public function cursorPaginate(int $perPage = 15, ?string $cursor = null): CursorPaginator;

    // Find operations
    public function find(int $id): ?CustomerWishlist;

    public function findDTO(int $id): ?CustomerWishlistDTO;

    public function findByCustomerId(int $customerId): Collection;

    public function findByCustomerIdDTO(int $customerId): Collection;

    public function findByProductId(int $productId): Collection;

    public function findByProductIdDTO(int $productId): Collection;

    public function findByCustomerAndProduct(int $customerId, int $productId): ?CustomerWishlist;

    public function findByCustomerAndProductDTO(int $customerId, int $productId): ?CustomerWishlistDTO;

    // Visibility-based queries
    public function findPublic(): Collection;

    public function findPublicDTO(): Collection;

    public function findPrivate(): Collection;

    public function findPrivateDTO(): Collection;

    // Priority-based queries
    public function findByPriority(string $priority): Collection;

    public function findByPriorityDTO(string $priority): Collection;

    // Date range queries
    public function findByDateRange(string $startDate, string $endDate): Collection;

    public function findByDateRangeDTO(string $startDate, string $endDate): Collection;

    // Price range queries
    public function findByPriceRange(float $minPrice, float $maxPrice): Collection;

    public function findByPriceRangeDTO(float $minPrice, float $maxPrice): Collection;

    // Price drop queries
    public function findWithPriceDrops(): Collection;

    public function findWithPriceDropsDTO(): Collection;

    // Notification queries
    public function findNotified(): Collection;

    public function findNotifiedDTO(): Collection;

    public function findNotNotified(): Collection;

    public function findNotNotifiedDTO(): Collection;

    // Create and Update operations
    public function create(array $data): CustomerWishlist;

    public function createAndReturnDTO(array $data): CustomerWishlistDTO;

    public function update(CustomerWishlist $wishlist, array $data): bool;

    public function updateAndReturnDTO(CustomerWishlist $wishlist, array $data): ?CustomerWishlistDTO;

    public function delete(CustomerWishlist $wishlist): bool;

    // Wishlist management operations
    public function addToWishlist(int $customerId, int $productId, array $data = []): bool;

    public function addToWishlistDTO(int $customerId, int $productId, array $data = []): ?CustomerWishlistDTO;

    public function removeFromWishlist(int $customerId, int $productId): bool;

    public function isInWishlist(int $customerId, int $productId): bool;

    // Status management operations
    public function makePublic(CustomerWishlist $wishlist): bool;

    public function makePrivate(CustomerWishlist $wishlist): bool;

    public function setPriority(CustomerWishlist $wishlist, string $priority): bool;

    public function markAsNotified(CustomerWishlist $wishlist): bool;

    public function markAsNotNotified(CustomerWishlist $wishlist): bool;

    // Price management operations
    public function updateCurrentPrice(CustomerWishlist $wishlist, float $currentPrice): bool;

    public function checkPriceDrop(CustomerWishlist $wishlist): bool;

    // Statistics operations
    public function getWishlistCount(): int;

    public function getWishlistCountByCustomer(int $customerId): int;

    public function getWishlistCountByProduct(int $productId): int;

    public function getPublicWishlistCount(): int;

    public function getPrivateWishlistCount(): int;

    public function getWishlistCountByPriority(string $priority): int;

    // Value calculations
    public function getTotalWishlistValue(): float;

    public function getTotalWishlistValueByCustomer(int $customerId): float;

    public function getAverageWishlistValue(): float;

    public function getAverageWishlistValueByCustomer(int $customerId): float;

    // Search operations
    public function search(string $query): Collection;

    public function searchDTO(string $query): Collection;

    public function searchByCustomer(int $customerId, string $query): Collection;

    public function searchByCustomerDTO(int $customerId, string $query): Collection;

    // Popular products
    public function getMostWishlistedProducts(int $limit = 10): Collection;

    public function getMostWishlistedProductsDTO(int $limit = 10): Collection;

    public function getMostWishlistedProductsByCategory(int $categoryId, int $limit = 10): Collection;

    public function getMostWishlistedProductsByCategoryDTO(int $categoryId, int $limit = 10): Collection;

    // Recent additions
    public function getRecentWishlistAdditions(int $limit = 10): Collection;

    public function getRecentWishlistAdditionsDTO(int $limit = 10): Collection;

    public function getRecentWishlistAdditionsByCustomer(int $customerId, int $limit = 10): Collection;

    public function getRecentWishlistAdditionsByCustomerDTO(int $customerId, int $limit = 10): Collection;

    // Sorting operations
    public function getWishlistByPriority(int $customerId): Collection;

    public function getWishlistByPriorityDTO(int $customerId): Collection;

    public function getWishlistByDateAdded(int $customerId, string $order = 'desc'): Collection;

    public function getWishlistByDateAddedDTO(int $customerId, string $order = 'desc'): Collection;

    public function getWishlistByPrice(int $customerId, string $order = 'desc'): Collection;

    public function getWishlistByPriceDTO(int $customerId, string $order = 'desc'): Collection;

    // Analytics operations
    public function getWishlistStats(): array;

    public function getWishlistStatsByCustomer(int $customerId): array;

    public function getWishlistStatsByProduct(int $productId): array;

    public function getWishlistStatsByCategory(int $categoryId): array;

    public function getWishlistGrowthStats(string $period = 'monthly'): array;

    public function getWishlistGrowthStatsByCustomer(int $customerId, string $period = 'monthly'): array;

    public function getPriceDropStats(): array;

    public function getPriceDropStatsByCustomer(int $customerId): array;

    // Validation operations
    public function validateWishlist(array $data): bool;

    // Recommendation operations
    public function getWishlistRecommendations(int $customerId, int $limit = 10): Collection;

    public function getWishlistRecommendationsDTO(int $customerId, int $limit = 10): Collection;

    public function getSimilarWishlists(int $customerId, int $limit = 10): Collection;

    public function getSimilarWishlistsDTO(int $customerId, int $limit = 10): Collection;

    // Analytics operations
    public function getWishlistAnalytics(int $customerId): array;

    public function getWishlistAnalyticsByProduct(int $productId): array;

    public function getWishlistAnalyticsByCategory(int $categoryId): array;

    // Import/Export operations
    public function exportWishlist(int $customerId): array;

    public function importWishlist(int $customerId, array $wishlistItems): bool;

    // Bulk operations
    public function clearWishlist(int $customerId): bool;

    public function moveWishlistItem(int $wishlistId, int $newPosition): bool;

    public function duplicateWishlist(int $sourceCustomerId, int $targetCustomerId): bool;
}
