<?php

namespace Fereydooni\Shopping\app\Repositories\Interfaces;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Pagination\CursorPaginator;
use Fereydooni\Shopping\app\Models\Product;
use Fereydooni\Shopping\app\DTOs\ProductDTO;

interface ProductRepositoryInterface
{
    // Basic CRUD operations
    public function all(): Collection;
    public function paginate(int $perPage = 15): LengthAwarePaginator;
    public function simplePaginate(int $perPage = 15): Paginator;
    public function cursorPaginate(int $perPage = 15, string $cursor = null): CursorPaginator;
    public function find(int $id): ?Product;
    public function findDTO(int $id): ?ProductDTO;
    public function create(array $data): Product;
    public function createAndReturnDTO(array $data): ProductDTO;
    public function update(Product $product, array $data): bool;
    public function updateAndReturnDTO(Product $product, array $data): ?ProductDTO;
    public function delete(Product $product): bool;

    // Slug and SKU based queries
    public function findBySlug(string $slug): ?Product;
    public function findBySlugDTO(string $slug): ?ProductDTO;
    public function findBySku(string $sku): ?Product;
    public function findBySkuDTO(string $sku): ?ProductDTO;

    // Category and brand filtering
    public function findByCategoryId(int $categoryId): Collection;
    public function findByCategoryIdDTO(int $categoryId): Collection;
    public function findByBrandId(int $brandId): Collection;
    public function findByBrandIdDTO(int $brandId): Collection;

    // Status and type filtering
    public function findByStatus(string $status): Collection;
    public function findByStatusDTO(string $status): Collection;
    public function findByType(string $type): Collection;
    public function findByTypeDTO(string $type): Collection;

    // Active and featured products
    public function findActive(): Collection;
    public function findActiveDTO(): Collection;
    public function findFeatured(): Collection;
    public function findFeaturedDTO(): Collection;

    // Stock level filtering
    public function findInStock(): Collection;
    public function findInStockDTO(): Collection;
    public function findLowStock(int $threshold = 10): Collection;
    public function findLowStockDTO(int $threshold = 10): Collection;
    public function findOutOfStock(): Collection;
    public function findOutOfStockDTO(): Collection;

    // Status management
    public function toggleActive(Product $product): bool;
    public function toggleFeatured(Product $product): bool;
    public function publish(Product $product): bool;
    public function unpublish(Product $product): bool;
    public function archive(Product $product): bool;

    // Count methods
    public function getProductCount(): int;
    public function getProductCountByCategory(int $categoryId): int;
    public function getProductCountByBrand(int $brandId): int;
    public function getProductCountByStatus(string $status): int;
    public function getProductCountByType(string $type): int;

    // Stock and value methods
    public function getTotalStock(): int;
    public function getTotalStockByCategory(int $categoryId): int;
    public function getTotalStockByBrand(int $brandId): int;
    public function getTotalValue(): float;
    public function getTotalValueByCategory(int $categoryId): float;
    public function getTotalValueByBrand(int $brandId): float;

    // Search methods
    public function search(string $query): Collection;
    public function searchDTO(string $query): Collection;
    public function searchByCategory(int $categoryId, string $query): Collection;
    public function searchByCategoryDTO(int $categoryId, string $query): Collection;
    public function searchByBrand(int $brandId, string $query): Collection;
    public function searchByBrandDTO(int $brandId, string $query): Collection;

    // Analytics and reporting
    public function getTopSelling(int $limit = 10): Collection;
    public function getTopSellingDTO(int $limit = 10): Collection;
    public function getMostViewed(int $limit = 10): Collection;
    public function getMostViewedDTO(int $limit = 10): Collection;
    public function getMostWishlisted(int $limit = 10): Collection;
    public function getMostWishlistedDTO(int $limit = 10): Collection;
    public function getBestRated(int $limit = 10): Collection;
    public function getBestRatedDTO(int $limit = 10): Collection;
    public function getNewArrivals(int $limit = 10): Collection;
    public function getNewArrivalsDTO(int $limit = 10): Collection;
    public function getOnSale(int $limit = 10): Collection;
    public function getOnSaleDTO(int $limit = 10): Collection;

    // Price and stock range filtering
    public function getByPriceRange(float $minPrice, float $maxPrice): Collection;
    public function getByPriceRangeDTO(float $minPrice, float $maxPrice): Collection;
    public function getByStockRange(int $minStock, int $maxStock): Collection;
    public function getByStockRangeDTO(int $minStock, int $maxStock): Collection;

    // Related products
    public function getRelatedProducts(Product $product, int $limit = 5): Collection;
    public function getRelatedProductsDTO(Product $product, int $limit = 5): Collection;
    public function getCrossSellProducts(Product $product, int $limit = 5): Collection;
    public function getCrossSellProductsDTO(Product $product, int $limit = 5): Collection;
    public function getUpSellProducts(Product $product, int $limit = 5): Collection;
    public function getUpSellProductsDTO(Product $product, int $limit = 5): Collection;

    // Validation and utility methods
    public function validateProduct(array $data): bool;
    public function generateSlug(string $title): string;
    public function isSlugUnique(string $slug, ?int $excludeId = null): bool;
    public function isSkuUnique(string $sku, ?int $excludeId = null): bool;

    // Inventory management
    public function updateStock(Product $product, int $quantity, string $operation = 'decrease'): bool;
    public function reserveStock(Product $product, int $quantity): bool;
    public function releaseStock(Product $product, int $quantity): bool;
    public function getInventoryLevel(Product $product): int;

    // Analytics methods
    public function getProductAnalytics(Product $product): array;
    public function incrementViewCount(Product $product): bool;
    public function incrementWishlistCount(Product $product): bool;
    public function updateAverageRating(Product $product): bool;
}
