<?php

namespace Fereydooni\Shopping\app\Services;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Pagination\CursorPaginator;
use Fereydooni\Shopping\app\Models\ProductAttributeValue;
use Fereydooni\Shopping\app\DTOs\ProductAttributeValueDTO;
use Fereydooni\Shopping\app\Repositories\Interfaces\ProductAttributeValueRepositoryInterface;

class ProductAttributeValueService
{
    public function __construct(
        private ProductAttributeValueRepositoryInterface $repository
    ) {}

    // Basic CRUD Operations
    public function all(): Collection
    {
        return $this->repository->all();
    }

    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        return $this->repository->paginate($perPage);
    }

    public function simplePaginate(int $perPage = 15): Paginator
    {
        return $this->repository->simplePaginate($perPage);
    }

    public function cursorPaginate(int $perPage = 15, string $cursor = null): CursorPaginator
    {
        return $this->repository->cursorPaginate($perPage, $cursor);
    }

    public function find(int $id): ?ProductAttributeValue
    {
        return $this->repository->find($id);
    }

    public function findDTO(int $id): ?ProductAttributeValueDTO
    {
        return $this->repository->findDTO($id);
    }

    public function create(array $data): ProductAttributeValue
    {
        return $this->repository->create($data);
    }

    public function createAndReturnDTO(array $data): ProductAttributeValueDTO
    {
        return $this->repository->createAndReturnDTO($data);
    }

    public function update(ProductAttributeValue $value, array $data): bool
    {
        return $this->repository->update($value, $data);
    }

    public function updateAndReturnDTO(ProductAttributeValue $value, array $data): ?ProductAttributeValueDTO
    {
        return $this->repository->updateAndReturnDTO($value, $data);
    }

    public function delete(ProductAttributeValue $value): bool
    {
        return $this->repository->delete($value);
    }

    // Attribute-specific queries
    public function findByAttributeId(int $attributeId): Collection
    {
        return $this->repository->findByAttributeId($attributeId);
    }

    public function findByAttributeIdDTO(int $attributeId): Collection
    {
        return $this->repository->findByAttributeIdDTO($attributeId);
    }

    public function findByAttributeIdAndValue(int $attributeId, string $value): ?ProductAttributeValue
    {
        return $this->repository->findByAttributeIdAndValue($attributeId, $value);
    }

    public function findByAttributeIdAndValueDTO(int $attributeId, string $value): ?ProductAttributeValueDTO
    {
        return $this->repository->findByAttributeIdAndValueDTO($attributeId, $value);
    }

    // Value-based queries
    public function findByValue(string $value): Collection
    {
        return $this->repository->findByValue($value);
    }

    public function findByValueDTO(string $value): Collection
    {
        return $this->repository->findByValueDTO($value);
    }

    // Slug-based queries
    public function findBySlug(string $slug): ?ProductAttributeValue
    {
        return $this->repository->findBySlug($slug);
    }

    public function findBySlugDTO(string $slug): ?ProductAttributeValueDTO
    {
        return $this->repository->findBySlugDTO($slug);
    }

    // Status-based queries
    public function findActive(): Collection
    {
        return $this->repository->findActive();
    }

    public function findActiveDTO(): Collection
    {
        return $this->repository->findActiveDTO();
    }

    public function findDefault(): Collection
    {
        return $this->repository->findDefault();
    }

    public function findDefaultDTO(): Collection
    {
        return $this->repository->findDefaultDTO();
    }

    // Relationship queries
    public function findByVariantId(int $variantId): Collection
    {
        return $this->repository->findByVariantId($variantId);
    }

    public function findByVariantIdDTO(int $variantId): Collection
    {
        return $this->repository->findByVariantIdDTO($variantId);
    }

    public function findByProductId(int $productId): Collection
    {
        return $this->repository->findByProductId($productId);
    }

    public function findByProductIdDTO(int $productId): Collection
    {
        return $this->repository->findByProductIdDTO($productId);
    }

    public function findByCategoryId(int $categoryId): Collection
    {
        return $this->repository->findByCategoryId($categoryId);
    }

    public function findByCategoryIdDTO(int $categoryId): Collection
    {
        return $this->repository->findByCategoryIdDTO($categoryId);
    }

    public function findByBrandId(int $brandId): Collection
    {
        return $this->repository->findByBrandId($brandId);
    }

    public function findByBrandIdDTO(int $brandId): Collection
    {
        return $this->repository->findByBrandIdDTO($brandId);
    }

    // Status management
    public function toggleActive(ProductAttributeValue $value): bool
    {
        return $this->repository->toggleActive($value);
    }

    public function toggleDefault(ProductAttributeValue $value): bool
    {
        return $this->repository->toggleDefault($value);
    }

    public function setDefault(ProductAttributeValue $value): bool
    {
        return $this->repository->setDefault($value);
    }

    // Count methods
    public function getValueCount(): int
    {
        return $this->repository->getValueCount();
    }

    public function getValueCountByAttributeId(int $attributeId): int
    {
        return $this->repository->getValueCountByAttributeId($attributeId);
    }

    public function getActiveValueCount(): int
    {
        return $this->repository->getActiveValueCount();
    }

    public function getDefaultValueCount(): int
    {
        return $this->repository->getDefaultValueCount();
    }

    public function getValueCountByVariantId(int $variantId): int
    {
        return $this->repository->getValueCountByVariantId($variantId);
    }

    public function getValueCountByProductId(int $productId): int
    {
        return $this->repository->getValueCountByProductId($productId);
    }

    public function getValueCountByCategoryId(int $categoryId): int
    {
        return $this->repository->getValueCountByCategoryId($categoryId);
    }

    public function getValueCountByBrandId(int $brandId): int
    {
        return $this->repository->getValueCountByBrandId($brandId);
    }

    // Search functionality
    public function search(string $query): Collection
    {
        return $this->repository->search($query);
    }

    public function searchDTO(string $query): Collection
    {
        return $this->repository->searchDTO($query);
    }

    public function searchByAttributeId(int $attributeId, string $query): Collection
    {
        return $this->repository->searchByAttributeId($attributeId, $query);
    }

    public function searchByAttributeIdDTO(int $attributeId, string $query): Collection
    {
        return $this->repository->searchByAttributeIdDTO($attributeId, $query);
    }

    // Usage analytics
    public function getMostUsedValues(int $limit = 10): Collection
    {
        return $this->repository->getMostUsedValues($limit);
    }

    public function getMostUsedValuesDTO(int $limit = 10): Collection
    {
        return $this->repository->getMostUsedValuesDTO($limit);
    }

    public function getLeastUsedValues(int $limit = 10): Collection
    {
        return $this->repository->getLeastUsedValues($limit);
    }

    public function getLeastUsedValuesDTO(int $limit = 10): Collection
    {
        return $this->repository->getLeastUsedValuesDTO($limit);
    }

    public function getUnusedValues(): Collection
    {
        return $this->repository->getUnusedValues();
    }

    public function getUnusedValuesDTO(): Collection
    {
        return $this->repository->getUnusedValuesDTO();
    }

    public function getValuesByUsageRange(int $minUsage, int $maxUsage): Collection
    {
        return $this->repository->getValuesByUsageRange($minUsage, $maxUsage);
    }

    public function getValuesByUsageRangeDTO(int $minUsage, int $maxUsage): Collection
    {
        return $this->repository->getValuesByUsageRangeDTO($minUsage, $maxUsage);
    }

    // Validation methods
    public function validateAttributeValue(array $data): bool
    {
        return $this->repository->validateAttributeValue($data);
    }

    public function generateSlug(string $value): string
    {
        return $this->repository->generateSlug($value);
    }

    public function isSlugUnique(string $slug, ?int $excludeId = null): bool
    {
        return $this->repository->isSlugUnique($slug, $excludeId);
    }

    public function isValueUnique(int $attributeId, string $value, ?int $excludeId = null): bool
    {
        return $this->repository->isValueUnique($attributeId, $value, $excludeId);
    }

    // Usage tracking
    public function getValueUsage(int $valueId): int
    {
        return $this->repository->getValueUsage($valueId);
    }

    public function getValueUsageByProduct(int $valueId, int $productId): int
    {
        return $this->repository->getValueUsageByProduct($valueId, $productId);
    }

    public function getValueUsageByCategory(int $valueId, int $categoryId): int
    {
        return $this->repository->getValueUsageByCategory($valueId, $categoryId);
    }

    public function getValueUsageByBrand(int $valueId, int $brandId): int
    {
        return $this->repository->getValueUsageByBrand($valueId, $brandId);
    }

    public function getValueUsageByVariant(int $valueId, int $variantId): int
    {
        return $this->repository->getValueUsageByVariant($valueId, $variantId);
    }

    public function getValueAnalytics(int $valueId): array
    {
        return $this->repository->getValueAnalytics($valueId);
    }

    public function incrementUsage(int $valueId): bool
    {
        return $this->repository->incrementUsage($valueId);
    }

    public function decrementUsage(int $valueId): bool
    {
        return $this->repository->decrementUsage($valueId);
    }

    // Relationship data
    public function getValueVariants(int $valueId): Collection
    {
        return $this->repository->getValueVariants($valueId);
    }

    public function getValueVariantsDTO(int $valueId): Collection
    {
        return $this->repository->getValueVariantsDTO($valueId);
    }

    public function getValueProducts(int $valueId): Collection
    {
        return $this->repository->getValueProducts($valueId);
    }

    public function getValueProductsDTO(int $valueId): Collection
    {
        return $this->repository->getValueProductsDTO($valueId);
    }

    public function getValueCategories(int $valueId): Collection
    {
        return $this->repository->getValueCategories($valueId);
    }

    public function getValueCategoriesDTO(int $valueId): Collection
    {
        return $this->repository->getValueCategoriesDTO($valueId);
    }

    public function getValueBrands(int $valueId): Collection
    {
        return $this->repository->getValueBrands($valueId);
    }

    public function getValueBrandsDTO(int $valueId): Collection
    {
        return $this->repository->getValueBrandsDTO($valueId);
    }

    // Relationship management
    public function assignToVariant(int $valueId, int $variantId): bool
    {
        return $this->repository->assignToVariant($valueId, $variantId);
    }

    public function removeFromVariant(int $valueId, int $variantId): bool
    {
        return $this->repository->removeFromVariant($valueId, $variantId);
    }

    public function assignToProduct(int $valueId, int $productId): bool
    {
        return $this->repository->assignToProduct($valueId, $productId);
    }

    public function removeFromProduct(int $valueId, int $productId): bool
    {
        return $this->repository->removeFromProduct($valueId, $productId);
    }

    public function assignToCategory(int $valueId, int $categoryId): bool
    {
        return $this->repository->assignToCategory($valueId, $categoryId);
    }

    public function removeFromCategory(int $valueId, int $categoryId): bool
    {
        return $this->repository->removeFromCategory($valueId, $categoryId);
    }

    public function assignToBrand(int $valueId, int $brandId): bool
    {
        return $this->repository->assignToBrand($valueId, $brandId);
    }

    public function removeFromBrand(int $valueId, int $brandId): bool
    {
        return $this->repository->removeFromBrand($valueId, $brandId);
    }
}
