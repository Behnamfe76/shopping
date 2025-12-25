<?php

namespace Fereydooni\Shopping\app\Services;

use Fereydooni\Shopping\app\DTOs\ProductAttributeDTO;
use Fereydooni\Shopping\app\DTOs\ProductAttributeValueDTO;
use Fereydooni\Shopping\app\Models\ProductAttribute;
use Fereydooni\Shopping\app\Models\ProductAttributeValue;
use Fereydooni\Shopping\app\Repositories\Interfaces\ProductAttributeRepositoryInterface;
use Fereydooni\Shopping\app\Traits\HasAnalyticsOperations;
use Fereydooni\Shopping\app\Traits\HasAttributeManagement;
use Fereydooni\Shopping\app\Traits\HasCrudOperations;
use Fereydooni\Shopping\app\Traits\HasSearchOperations;
use Fereydooni\Shopping\app\Traits\HasSlugGeneration;
use Fereydooni\Shopping\app\Traits\HasStatusToggle;
use Illuminate\Database\Eloquent\Collection;

class ProductAttributeService
{
    use HasAnalyticsOperations;
    use HasAttributeManagement;
    use HasCrudOperations;
    use HasSearchOperations;
    use HasSlugGeneration;
    use HasStatusToggle;

    public function __construct(
        private ProductAttributeRepositoryInterface $repository
    ) {
        $this->model = ProductAttribute::class;
        $this->dtoClass = ProductAttributeDTO::class;
    }

    public array $searchableFields = ['name', 'slug', 'description', 'group', 'unit'];

    // Basic CRUD operations (inherited from HasCrudOperations)
    public function findDTO(int $id): ?ProductAttributeDTO
    {
        return $this->repository->findDTO($id);
    }

    public function create(array $data): ProductAttribute
    {
        return $this->repository->create($data);
    }

    public function createAndReturnDTO(array $data): ProductAttributeDTO
    {
        return $this->repository->createAndReturnDTO($data);
    }

    public function update(ProductAttribute $attribute, array $data): bool
    {
        return $this->repository->update($attribute, $data);
    }

    public function updateAndReturnDTO(ProductAttribute $attribute, array $data): ?ProductAttributeDTO
    {
        return $this->repository->updateAndReturnDTO($attribute, $data);
    }

    // Find by specific fields
    public function findBySlug(string $slug): ?ProductAttribute
    {
        return $this->repository->findBySlug($slug);
    }

    public function findBySlugDTO(string $slug): ?ProductAttributeDTO
    {
        return $this->repository->findBySlugDTO($slug);
    }

    public function findByName(string $name): ?ProductAttribute
    {
        return $this->repository->findByName($name);
    }

    public function findByNameDTO(string $name): ?ProductAttributeDTO
    {
        return $this->repository->findByNameDTO($name);
    }

    // Find by type and input type
    public function findByType(string $type): Collection
    {
        return $this->repository->findByType($type);
    }

    public function findByTypeDTO(string $type): Collection
    {
        return $this->repository->findByTypeDTO($type);
    }

    public function findByInputType(string $inputType): Collection
    {
        return $this->repository->findByInputType($inputType);
    }

    public function findByInputTypeDTO(string $inputType): Collection
    {
        return $this->repository->findByInputTypeDTO($inputType);
    }

    // Find by group
    public function findByGroup(string $group): Collection
    {
        return $this->repository->findByGroup($group);
    }

    public function findByGroupDTO(string $group): Collection
    {
        return $this->repository->findByGroupDTO($group);
    }

    // Find by functionality
    public function findRequired(): Collection
    {
        return $this->repository->findRequired();
    }

    public function findRequiredDTO(): Collection
    {
        return $this->repository->findRequiredDTO();
    }

    public function findSearchable(): Collection
    {
        return $this->repository->findSearchable();
    }

    public function findSearchableDTO(): Collection
    {
        return $this->repository->findSearchableDTO();
    }

    public function findFilterable(): Collection
    {
        return $this->repository->findFilterable();
    }

    public function findFilterableDTO(): Collection
    {
        return $this->repository->findFilterableDTO();
    }

    public function findComparable(): Collection
    {
        return $this->repository->findComparable();
    }

    public function findComparableDTO(): Collection
    {
        return $this->repository->findComparableDTO();
    }

    public function findVisible(): Collection
    {
        return $this->repository->findVisible();
    }

    public function findVisibleDTO(): Collection
    {
        return $this->repository->findVisibleDTO();
    }

    // Find by system status
    public function findSystem(): Collection
    {
        return $this->repository->findSystem();
    }

    public function findSystemDTO(): Collection
    {
        return $this->repository->findSystemDTO();
    }

    public function findCustom(): Collection
    {
        return $this->repository->findCustom();
    }

    public function findCustomDTO(): Collection
    {
        return $this->repository->findCustomDTO();
    }

    // Find by active status
    public function findActive(): Collection
    {
        return $this->repository->findActive();
    }

    public function findActiveDTO(): Collection
    {
        return $this->repository->findActiveDTO();
    }

    // Toggle operations (inherited from HasStatusToggle)
    public function toggleActive(ProductAttribute $attribute): bool
    {
        return $this->repository->toggleActive($attribute);
    }

    public function toggleRequired(ProductAttribute $attribute): bool
    {
        return $this->repository->toggleRequired($attribute);
    }

    public function toggleSearchable(ProductAttribute $attribute): bool
    {
        return $this->repository->toggleSearchable($attribute);
    }

    public function toggleFilterable(ProductAttribute $attribute): bool
    {
        return $this->repository->toggleFilterable($attribute);
    }

    public function toggleComparable(ProductAttribute $attribute): bool
    {
        return $this->repository->toggleComparable($attribute);
    }

    public function toggleVisible(ProductAttribute $attribute): bool
    {
        return $this->repository->toggleVisible($attribute);
    }

    // Count operations
    public function getAttributeCount(): int
    {
        return $this->repository->getAttributeCount();
    }

    public function getAttributeCountByType(string $type): int
    {
        return $this->repository->getAttributeCountByType($type);
    }

    public function getAttributeCountByGroup(string $group): int
    {
        return $this->repository->getAttributeCountByGroup($group);
    }

    public function getAttributeCountByInputType(string $inputType): int
    {
        return $this->repository->getAttributeCountByInputType($inputType);
    }

    public function getRequiredAttributeCount(): int
    {
        return $this->repository->getRequiredAttributeCount();
    }

    public function getSearchableAttributeCount(): int
    {
        return $this->repository->getSearchableAttributeCount();
    }

    public function getFilterableAttributeCount(): int
    {
        return $this->repository->getFilterableAttributeCount();
    }

    public function getComparableAttributeCount(): int
    {
        return $this->repository->getComparableAttributeCount();
    }

    public function getVisibleAttributeCount(): int
    {
        return $this->repository->getVisibleAttributeCount();
    }

    public function getSystemAttributeCount(): int
    {
        return $this->repository->getSystemAttributeCount();
    }

    public function getCustomAttributeCount(): int
    {
        return $this->repository->getCustomAttributeCount();
    }

    // Search operations (inherited from HasSearchOperations)
    public function search(string $query): Collection
    {
        return $this->repository->search($query);
    }

    public function searchDTO(string $query): Collection
    {
        return $this->repository->searchDTO($query);
    }

    // Analytics and reporting (inherited from HasAnalyticsOperations)
    public function getAttributeGroups(): Collection
    {
        return $this->repository->getAttributeGroups();
    }

    public function getAttributeTypes(): Collection
    {
        return $this->repository->getAttributeTypes();
    }

    public function getInputTypes(): Collection
    {
        return $this->repository->getInputTypes();
    }

    // Validation operations (inherited from HasAttributeManagement)
    public function validateAttribute(array $data): bool
    {
        return $this->repository->validateAttribute($data);
    }

    public function generateSlug(string $name): string
    {
        return $this->repository->generateSlug($name);
    }

    public function isSlugUnique(string $slug, ?int $excludeId = null): bool
    {
        return $this->repository->isSlugUnique($slug, $excludeId);
    }

    public function isNameUnique(string $name, ?int $excludeId = null): bool
    {
        return $this->repository->isNameUnique($name, $excludeId);
    }

    // Usage tracking (inherited from HasAnalyticsOperations)
    public function getAttributeUsage(int $attributeId): int
    {
        return $this->repository->getAttributeUsage($attributeId);
    }

    public function getAttributeUsageByProduct(int $attributeId, int $productId): int
    {
        return $this->repository->getAttributeUsageByProduct($attributeId, $productId);
    }

    public function getAttributeUsageByCategory(int $attributeId, int $categoryId): int
    {
        return $this->repository->getAttributeUsageByCategory($attributeId, $categoryId);
    }

    public function getAttributeUsageByBrand(int $attributeId, int $brandId): int
    {
        return $this->repository->getAttributeUsageByBrand($attributeId, $brandId);
    }

    public function getAttributeAnalytics(int $attributeId): array
    {
        return $this->repository->getAttributeAnalytics($attributeId);
    }

    // Attribute value management
    public function getAttributeValues(int $attributeId): Collection
    {
        return $this->repository->getAttributeValues($attributeId);
    }

    public function getAttributeValuesDTO(int $attributeId): Collection
    {
        return $this->repository->getAttributeValuesDTO($attributeId);
    }

    public function addAttributeValue(int $attributeId, string $value, array $metadata = []): ProductAttributeValue
    {
        return $this->repository->addAttributeValue($attributeId, $value, $metadata);
    }

    public function addAttributeValueDTO(int $attributeId, string $value, array $metadata = []): ProductAttributeValueDTO
    {
        return $this->repository->addAttributeValueDTO($attributeId, $value, $metadata);
    }

    public function removeAttributeValue(int $attributeId, int $valueId): bool
    {
        return $this->repository->removeAttributeValue($attributeId, $valueId);
    }

    public function updateAttributeValue(int $attributeId, int $valueId, string $value, array $metadata = []): bool
    {
        return $this->repository->updateAttributeValue($attributeId, $valueId, $value, $metadata);
    }

    public function getAttributeValueCount(int $attributeId): int
    {
        return $this->repository->getAttributeValueCount($attributeId);
    }

    public function getAttributeValueUsage(int $attributeId, int $valueId): int
    {
        return $this->repository->getAttributeValueUsage($attributeId, $valueId);
    }
}
