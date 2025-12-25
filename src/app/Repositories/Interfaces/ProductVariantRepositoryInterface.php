<?php

namespace Fereydooni\Shopping\app\Repositories\Interfaces;

use Fereydooni\Shopping\app\DTOs\ProductVariantDTO;
use Fereydooni\Shopping\app\Models\ProductVariant;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

interface ProductVariantRepositoryInterface
{
    // Basic CRUD operations
    public function all(): Collection;

    public function paginate(int $perPage = 15): LengthAwarePaginator;

    public function find(int $id): ?ProductVariant;

    public function findDTO(int $id): ?ProductVariantDTO;

    public function create(array $data): ProductVariant;

    public function createAndReturnDTO(array $data): ProductVariantDTO;

    public function update(ProductVariant $variant, array $data): bool;

    public function delete(ProductVariant $variant): bool;

    // Product-specific queries
    public function findByProductId(int $productId): Collection;

    public function getVariantCountByProductId(int $productId): int;

    // SKU and barcode queries
    public function findBySku(string $sku): ?ProductVariant;

    public function findByBarcode(string $barcode): ?ProductVariant;

    public function getVariantSkus(): Collection;

    public function getVariantBarcodes(): Collection;

    // Status-based queries
    public function findActive(): Collection;

    public function findInStock(): Collection;

    public function findOutOfStock(): Collection;

    public function findLowStock(): Collection;

    public function getActiveVariantCount(): int;

    public function getInStockVariantCount(): int;

    public function getOutOfStockVariantCount(): int;

    public function getLowStockVariantCount(): int;

    // Range-based queries
    public function findByPriceRange(float $minPrice, float $maxPrice): Collection;

    public function findByStockRange(int $minStock, int $maxStock): Collection;

    public function getVariantPrices(): Collection;

    public function getVariantWeights(): Collection;

    // Attribute combination queries
    public function findByAttributeCombination(int $productId, array $attributeValues): ?ProductVariant;

    // Status management
    public function toggleActive(ProductVariant $variant): bool;

    public function toggleFeatured(ProductVariant $variant): bool;

    // Inventory management
    public function updateStock(ProductVariant $variant, int $quantity): bool;

    public function reserveStock(ProductVariant $variant, int $quantity): bool;

    public function releaseStock(ProductVariant $variant, int $quantity): bool;

    public function adjustStock(ProductVariant $variant, int $quantity, ?string $reason = null): bool;

    public function getVariantStock(int $variantId): int;

    public function getVariantAvailableStock(int $variantId): int;

    public function getVariantReservedStock(int $variantId): int;

    // Pricing management
    public function setPrice(ProductVariant $variant, float $price): bool;

    public function setSalePrice(ProductVariant $variant, float $salePrice): bool;

    public function setComparePrice(ProductVariant $variant, float $comparePrice): bool;

    // Search functionality
    public function search(string $query): Collection;

    // Analytics and reporting
    public function getVariantCount(): int;

    public function getVariantAnalytics(int $variantId): array;

    public function getVariantAnalyticsByProduct(int $productId): array;

    public function getVariantSales(int $variantId): float;

    public function getVariantRevenue(int $variantId): float;

    public function getVariantProfit(int $variantId): float;

    public function getVariantMargin(int $variantId): float;

    // Inventory management
    public function getVariantInventory(int $variantId): array;

    public function getVariantInventoryHistory(int $variantId): array;

    public function getVariantInventoryAlerts(int $variantId): array;

    // Bulk operations
    public function bulkCreate(int $productId, array $variantData): Collection;

    public function bulkUpdate(array $variantData): bool;

    public function bulkDelete(array $variantIds): bool;

    // Import/Export operations
    public function importVariants(int $productId, array $variantData): bool;

    public function exportVariants(int $productId): array;

    public function syncVariants(int $productId, array $variantData): bool;

    // Validation methods
    public function validateVariant(array $data): bool;

    public function isSkuUnique(string $sku, ?int $excludeId = null): bool;

    public function isBarcodeUnique(string $barcode, ?int $excludeId = null): bool;

    public function generateSku(int $productId, array $attributeValues): string;
}
