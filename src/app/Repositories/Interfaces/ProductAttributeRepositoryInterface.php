<?php

namespace Fereydooni\Shopping\app\Repositories\Interfaces;

use Fereydooni\Shopping\app\DTOs\ProductAttributeDTO;
use Fereydooni\Shopping\app\DTOs\ProductAttributeValueDTO;
use Fereydooni\Shopping\app\Models\ProductAttribute;
use Fereydooni\Shopping\app\Models\ProductAttributeValue;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\CursorPaginator;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;

interface ProductAttributeRepositoryInterface
{
    // Basic CRUD operations
    public function all(): Collection;

    public function paginate(int $perPage = 15): LengthAwarePaginator;

    public function simplePaginate(int $perPage = 15): Paginator;

    public function cursorPaginate(int $perPage = 15, ?string $cursor = null): CursorPaginator;

    public function find(int $id): ?ProductAttribute;

    public function findDTO(int $id): ?ProductAttributeDTO;

    public function create(array $data): ProductAttribute;

    public function createAndReturnDTO(array $data): ProductAttributeDTO;

    public function update(ProductAttribute $attribute, array $data): bool;

    public function updateAndReturnDTO(ProductAttribute $attribute, array $data): ?ProductAttributeDTO;

    public function delete(ProductAttribute $attribute): bool;

    // Find by specific fields
    public function findBySlug(string $slug): ?ProductAttribute;

    public function findBySlugDTO(string $slug): ?ProductAttributeDTO;

    public function findByName(string $name): ?ProductAttribute;

    public function findByNameDTO(string $name): ?ProductAttributeDTO;

    // Find by type and input type
    public function findByType(string $type): Collection;

    public function findByTypeDTO(string $type): Collection;

    public function findByInputType(string $inputType): Collection;

    public function findByInputTypeDTO(string $inputType): Collection;

    // Find by group
    public function findByGroup(string $group): Collection;

    public function findByGroupDTO(string $group): Collection;

    // Find by functionality
    public function findRequired(): Collection;

    public function findRequiredDTO(): Collection;

    public function findSearchable(): Collection;

    public function findSearchableDTO(): Collection;

    public function findFilterable(): Collection;

    public function findFilterableDTO(): Collection;

    public function findComparable(): Collection;

    public function findComparableDTO(): Collection;

    public function findVisible(): Collection;

    public function findVisibleDTO(): Collection;

    // Find by system status
    public function findSystem(): Collection;

    public function findSystemDTO(): Collection;

    public function findCustom(): Collection;

    public function findCustomDTO(): Collection;

    // Find by active status
    public function findActive(): Collection;

    public function findActiveDTO(): Collection;

    // Toggle operations
    public function toggleActive(ProductAttribute $attribute): bool;

    public function toggleRequired(ProductAttribute $attribute): bool;

    public function toggleSearchable(ProductAttribute $attribute): bool;

    public function toggleFilterable(ProductAttribute $attribute): bool;

    public function toggleComparable(ProductAttribute $attribute): bool;

    public function toggleVisible(ProductAttribute $attribute): bool;

    // Count operations
    public function getAttributeCount(): int;

    public function getAttributeCountByType(string $type): int;

    public function getAttributeCountByGroup(string $group): int;

    public function getAttributeCountByInputType(string $inputType): int;

    public function getRequiredAttributeCount(): int;

    public function getSearchableAttributeCount(): int;

    public function getFilterableAttributeCount(): int;

    public function getComparableAttributeCount(): int;

    public function getVisibleAttributeCount(): int;

    public function getSystemAttributeCount(): int;

    public function getCustomAttributeCount(): int;

    // Search operations
    public function search(string $query): Collection;

    public function searchDTO(string $query): Collection;

    // Analytics and reporting
    public function getAttributeGroups(): Collection;

    public function getAttributeTypes(): Collection;

    public function getInputTypes(): Collection;

    // Validation operations
    public function validateAttribute(array $data): bool;

    public function generateSlug(string $name): string;

    public function isSlugUnique(string $slug, ?int $excludeId = null): bool;

    public function isNameUnique(string $name, ?int $excludeId = null): bool;

    // Usage tracking
    public function getAttributeUsage(int $attributeId): int;

    public function getAttributeUsageByProduct(int $attributeId, int $productId): int;

    public function getAttributeUsageByCategory(int $attributeId, int $categoryId): int;

    public function getAttributeUsageByBrand(int $attributeId, int $brandId): int;

    public function getAttributeAnalytics(int $attributeId): array;

    // Attribute value management
    public function getAttributeValues(int $attributeId): Collection;

    public function getAttributeValuesDTO(int $attributeId): Collection;

    public function addAttributeValue(int $attributeId, string $value, array $metadata = []): ProductAttributeValue;

    public function addAttributeValueDTO(int $attributeId, string $value, array $metadata = []): ProductAttributeValueDTO;

    public function removeAttributeValue(int $attributeId, int $valueId): bool;

    public function updateAttributeValue(int $attributeId, int $valueId, string $value, array $metadata = []): bool;

    public function getAttributeValueCount(int $attributeId): int;

    public function getAttributeValueUsage(int $attributeId, int $valueId): int;
}
