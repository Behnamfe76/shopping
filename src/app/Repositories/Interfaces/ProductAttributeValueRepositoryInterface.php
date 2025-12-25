<?php

namespace Fereydooni\Shopping\app\Repositories\Interfaces;

use Fereydooni\Shopping\app\DTOs\ProductAttributeValueDTO;
use Fereydooni\Shopping\app\Models\ProductAttributeValue;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\CursorPaginator;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;

interface ProductAttributeValueRepositoryInterface
{
    // Basic CRUD Operations
    public function all(): Collection;

    public function paginate(int $perPage = 15): LengthAwarePaginator;

    public function simplePaginate(int $perPage = 15): Paginator;

    public function cursorPaginate(int $perPage = 15, ?string $cursor = null): CursorPaginator;

    public function find(int $id): ?ProductAttributeValue;

    public function findDTO(int $id): ?ProductAttributeValueDTO;

    public function create(array $data): ProductAttributeValue;

    public function createAndReturnDTO(array $data): ProductAttributeValueDTO;

    public function update(ProductAttributeValue $value, array $data): bool;

    public function updateAndReturnDTO(ProductAttributeValue $value, array $data): ?ProductAttributeValueDTO;

    public function delete(ProductAttributeValue $value): bool;

    // Attribute-specific queries
    public function findByAttributeId(int $attributeId): Collection;

    public function findByAttributeIdDTO(int $attributeId): Collection;

    public function findByAttributeIdAndValue(int $attributeId, string $value): ?ProductAttributeValue;

    public function findByAttributeIdAndValueDTO(int $attributeId, string $value): ?ProductAttributeValueDTO;

    // Value-based queries
    public function findByValue(string $value): Collection;

    public function findByValueDTO(string $value): Collection;

    // Slug-based queries
    public function findBySlug(string $slug): ?ProductAttributeValue;

    public function findBySlugDTO(string $slug): ?ProductAttributeValueDTO;

    // Status-based queries
    public function findActive(): Collection;

    public function findActiveDTO(): Collection;

    public function findDefault(): Collection;

    public function findDefaultDTO(): Collection;

    // Relationship queries
    public function findByVariantId(int $variantId): Collection;

    public function findByVariantIdDTO(int $variantId): Collection;

    public function findByProductId(int $productId): Collection;

    public function findByProductIdDTO(int $productId): Collection;

    public function findByCategoryId(int $categoryId): Collection;

    public function findByCategoryIdDTO(int $categoryId): Collection;

    public function findByBrandId(int $brandId): Collection;

    public function findByBrandIdDTO(int $brandId): Collection;

    // Status management
    public function toggleActive(ProductAttributeValue $value): bool;

    public function toggleDefault(ProductAttributeValue $value): bool;

    public function setDefault(ProductAttributeValue $value): bool;

    // Count methods
    public function getValueCount(): int;

    public function getValueCountByAttributeId(int $attributeId): int;

    public function getActiveValueCount(): int;

    public function getDefaultValueCount(): int;

    public function getValueCountByVariantId(int $variantId): int;

    public function getValueCountByProductId(int $productId): int;

    public function getValueCountByCategoryId(int $categoryId): int;

    public function getValueCountByBrandId(int $brandId): int;

    // Search functionality
    public function search(string $query): Collection;

    public function searchDTO(string $query): Collection;

    public function searchByAttributeId(int $attributeId, string $query): Collection;

    public function searchByAttributeIdDTO(int $attributeId, string $query): Collection;

    // Usage analytics
    public function getMostUsedValues(int $limit = 10): Collection;

    public function getMostUsedValuesDTO(int $limit = 10): Collection;

    public function getLeastUsedValues(int $limit = 10): Collection;

    public function getLeastUsedValuesDTO(int $limit = 10): Collection;

    public function getUnusedValues(): Collection;

    public function getUnusedValuesDTO(): Collection;

    public function getValuesByUsageRange(int $minUsage, int $maxUsage): Collection;

    public function getValuesByUsageRangeDTO(int $minUsage, int $maxUsage): Collection;

    // Validation methods
    public function validateAttributeValue(array $data): bool;

    public function generateSlug(string $value): string;

    public function isSlugUnique(string $slug, ?int $excludeId = null): bool;

    public function isValueUnique(int $attributeId, string $value, ?int $excludeId = null): bool;

    // Usage tracking
    public function getValueUsage(int $valueId): int;

    public function getValueUsageByProduct(int $valueId, int $productId): int;

    public function getValueUsageByCategory(int $valueId, int $categoryId): int;

    public function getValueUsageByBrand(int $valueId, int $brandId): int;

    public function getValueUsageByVariant(int $valueId, int $variantId): int;

    public function getValueAnalytics(int $valueId): array;

    public function incrementUsage(int $valueId): bool;

    public function decrementUsage(int $valueId): bool;

    // Relationship data
    public function getValueVariants(int $valueId): Collection;

    public function getValueVariantsDTO(int $valueId): Collection;

    public function getValueProducts(int $valueId): Collection;

    public function getValueProductsDTO(int $valueId): Collection;

    public function getValueCategories(int $valueId): Collection;

    public function getValueCategoriesDTO(int $valueId): Collection;

    public function getValueBrands(int $valueId): Collection;

    public function getValueBrandsDTO(int $valueId): Collection;

    // Relationship management
    public function assignToVariant(int $valueId, int $variantId): bool;

    public function removeFromVariant(int $valueId, int $variantId): bool;

    public function assignToProduct(int $valueId, int $productId): bool;

    public function removeFromProduct(int $valueId, int $productId): bool;

    public function assignToCategory(int $valueId, int $categoryId): bool;

    public function removeFromCategory(int $valueId, int $categoryId): bool;

    public function assignToBrand(int $valueId, int $brandId): bool;

    public function removeFromBrand(int $valueId, int $brandId): bool;
}
