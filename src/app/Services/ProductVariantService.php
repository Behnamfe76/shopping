<?php

namespace Fereydooni\Shopping\app\Services;

use Fereydooni\Shopping\app\Repositories\Interfaces\ProductVariantRepositoryInterface;
use Fereydooni\Shopping\app\Models\ProductVariant;
use Fereydooni\Shopping\app\DTOs\ProductVariantDTO;
use Fereydooni\Shopping\app\Traits\HasCrudOperations;
use Fereydooni\Shopping\app\Traits\HasStatusToggle;
use Fereydooni\Shopping\app\Traits\HasInventoryManagement;
use Fereydooni\Shopping\app\Traits\HasPricingOperations;
use Fereydooni\Shopping\app\Traits\HasBulkOperations;
use Fereydooni\Shopping\app\Traits\HasAnalyticsOperations;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class ProductVariantService
{
    use HasCrudOperations;
    use HasStatusToggle;
    use HasInventoryManagement;
    use HasPricingOperations;
    use HasBulkOperations;
    use HasAnalyticsOperations;

    public function __construct(
        private ProductVariantRepositoryInterface $repository
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

    public function find(int $id): ?ProductVariant
    {
        return $this->repository->find($id);
    }

    public function findDTO(int $id): ?ProductVariantDTO
    {
        return $this->repository->findDTO($id);
    }

    public function create(array $data): ProductVariant
    {
        return $this->repository->create($data);
    }

    public function createAndReturnDTO(array $data): ProductVariantDTO
    {
        return $this->repository->createAndReturnDTO($data);
    }

    public function update(ProductVariant $variant, array $data): bool
    {
        return $this->repository->update($variant, $data);
    }

    public function delete(ProductVariant $variant): bool
    {
        return $this->repository->delete($variant);
    }

    // Product-specific queries
    public function findByProductId(int $productId): Collection
    {
        return $this->repository->findByProductId($productId);
    }

    public function getVariantCountByProductId(int $productId): int
    {
        return $this->repository->getVariantCountByProductId($productId);
    }

    // SKU and barcode queries
    public function findBySku(string $sku): ?ProductVariant
    {
        return $this->repository->findBySku($sku);
    }

    public function findByBarcode(string $barcode): ?ProductVariant
    {
        return $this->repository->findByBarcode($barcode);
    }

    public function getVariantSkus(): Collection
    {
        return $this->repository->getVariantSkus();
    }

    public function getVariantBarcodes(): Collection
    {
        return $this->repository->getVariantBarcodes();
    }

    // Status-based queries
    public function findActive(): Collection
    {
        return $this->repository->findActive();
    }

    public function findInStock(): Collection
    {
        return $this->repository->findInStock();
    }

    public function findOutOfStock(): Collection
    {
        return $this->repository->findOutOfStock();
    }

    public function findLowStock(): Collection
    {
        return $this->repository->findLowStock();
    }

    public function getActiveVariantCount(): int
    {
        return $this->repository->getActiveVariantCount();
    }

    public function getInStockVariantCount(): int
    {
        return $this->repository->getInStockVariantCount();
    }

    public function getOutOfStockVariantCount(): int
    {
        return $this->repository->getOutOfStockVariantCount();
    }

    public function getLowStockVariantCount(): int
    {
        return $this->repository->getLowStockVariantCount();
    }

    // Range-based queries
    public function findByPriceRange(float $minPrice, float $maxPrice): Collection
    {
        return $this->repository->findByPriceRange($minPrice, $maxPrice);
    }

    public function findByStockRange(int $minStock, int $maxStock): Collection
    {
        return $this->repository->findByStockRange($minStock, $maxStock);
    }

    public function getVariantPrices(): Collection
    {
        return $this->repository->getVariantPrices();
    }

    public function getVariantWeights(): Collection
    {
        return $this->repository->getVariantWeights();
    }

    // Attribute combination queries
    public function findByAttributeCombination(int $productId, array $attributeValues): ?ProductVariant
    {
        return $this->repository->findByAttributeCombination($productId, $attributeValues);
    }

    // Status management
    public function toggleActive(ProductVariant $variant): bool
    {
        return $this->repository->toggleActive($variant);
    }

    public function toggleFeatured(ProductVariant $variant): bool
    {
        return $this->repository->toggleFeatured($variant);
    }

    // Inventory management
    public function updateStock(ProductVariant $variant, int $quantity): bool
    {
        return $this->repository->updateStock($variant, $quantity);
    }

    public function reserveStock(ProductVariant $variant, int $quantity): bool
    {
        return $this->repository->reserveStock($variant, $quantity);
    }

    public function releaseStock(ProductVariant $variant, int $quantity): bool
    {
        return $this->repository->releaseStock($variant, $quantity);
    }

    public function adjustStock(ProductVariant $variant, int $quantity, string $reason = null): bool
    {
        return $this->repository->adjustStock($variant, $quantity, $reason);
    }

    public function getVariantStock(int $variantId): int
    {
        return $this->repository->getVariantStock($variantId);
    }

    public function getVariantAvailableStock(int $variantId): int
    {
        return $this->repository->getVariantAvailableStock($variantId);
    }

    public function getVariantReservedStock(int $variantId): int
    {
        return $this->repository->getVariantReservedStock($variantId);
    }

    // Pricing management
    public function setPrice(ProductVariant $variant, float $price): bool
    {
        return $this->repository->setPrice($variant, $price);
    }

    public function setSalePrice(ProductVariant $variant, float $salePrice): bool
    {
        return $this->repository->setSalePrice($variant, $salePrice);
    }

    public function setComparePrice(ProductVariant $variant, float $comparePrice): bool
    {
        return $this->repository->setComparePrice($variant, $comparePrice);
    }

    // Search functionality
    public function search(string $query): Collection
    {
        return $this->repository->search($query);
    }

    // Analytics and reporting
    public function getVariantCount(): int
    {
        return $this->repository->getVariantCount();
    }

    public function getVariantAnalytics(int $variantId): array
    {
        return $this->repository->getVariantAnalytics($variantId);
    }

    public function getVariantAnalyticsByProduct(int $productId): array
    {
        return $this->repository->getVariantAnalyticsByProduct($productId);
    }

    public function getVariantSales(int $variantId): float
    {
        return $this->repository->getVariantSales($variantId);
    }

    public function getVariantRevenue(int $variantId): float
    {
        return $this->repository->getVariantRevenue($variantId);
    }

    public function getVariantProfit(int $variantId): float
    {
        return $this->repository->getVariantProfit($variantId);
    }

    public function getVariantMargin(int $variantId): float
    {
        return $this->repository->getVariantMargin($variantId);
    }

    // Inventory management
    public function getVariantInventory(int $variantId): array
    {
        return $this->repository->getVariantInventory($variantId);
    }

    public function getVariantInventoryHistory(int $variantId): array
    {
        return $this->repository->getVariantInventoryHistory($variantId);
    }

    public function getVariantInventoryAlerts(int $variantId): array
    {
        return $this->repository->getVariantInventoryAlerts($variantId);
    }

    // Bulk operations
    public function bulkCreate(int $productId, array $variantData): Collection
    {
        return $this->repository->bulkCreate($productId, $variantData);
    }

    public function bulkUpdate(array $variantData): bool
    {
        return $this->repository->bulkUpdate($variantData);
    }

    public function bulkDelete(array $variantIds): bool
    {
        return $this->repository->bulkDelete($variantIds);
    }

    // Import/Export operations
    public function importVariants(int $productId, array $variantData): bool
    {
        return $this->repository->importVariants($productId, $variantData);
    }

    public function exportVariants(int $productId): array
    {
        return $this->repository->exportVariants($productId);
    }

    public function syncVariants(int $productId, array $variantData): bool
    {
        return $this->repository->syncVariants($productId, $variantData);
    }

    // Validation methods
    public function validateVariant(array $data): bool
    {
        return $this->repository->validateVariant($data);
    }

    public function isSkuUnique(string $sku, ?int $excludeId = null): bool
    {
        return $this->repository->isSkuUnique($sku, $excludeId);
    }

    public function isBarcodeUnique(string $barcode, ?int $excludeId = null): bool
    {
        return $this->repository->isBarcodeUnique($barcode, $excludeId);
    }

    public function generateSku(int $productId, array $attributeValues): string
    {
        return $this->repository->generateSku($productId, $attributeValues);
    }
}
