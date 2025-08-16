<?php

namespace Fereydooni\Shopping\app\Services;

use Fereydooni\Shopping\app\Repositories\Interfaces\ProductMetaRepositoryInterface;
use Fereydooni\Shopping\app\Traits\HasCrudOperations;
use Fereydooni\Shopping\app\Traits\HasStatusToggle;
use Fereydooni\Shopping\app\Traits\HasSearchOperations;
use Fereydooni\Shopping\app\Traits\HasBulkOperations;
use Fereydooni\Shopping\app\Traits\HasImportExport;
use Fereydooni\Shopping\app\Traits\HasAnalyticsOperations;
use Fereydooni\Shopping\app\Models\ProductMeta;
use Fereydooni\Shopping\app\DTOs\ProductMetaDTO;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class ProductMetaService
{
    use HasCrudOperations;
    use HasStatusToggle;
    use HasSearchOperations;
    use HasBulkOperations;
    use HasImportExport;
    use HasAnalyticsOperations;

    public function __construct(
        private ProductMetaRepositoryInterface $repository
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

    public function find(int $id): ?ProductMeta
    {
        return $this->repository->find($id);
    }

    public function findDTO(int $id): ?ProductMetaDTO
    {
        return $this->repository->findDTO($id);
    }

    public function create(array $data): ProductMeta
    {
        return $this->repository->create($data);
    }

    public function createAndReturnDTO(array $data): ProductMetaDTO
    {
        return $this->repository->createAndReturnDTO($data);
    }

    public function update(ProductMeta $meta, array $data): bool
    {
        return $this->repository->update($meta, $data);
    }

    public function delete(ProductMeta $meta): bool
    {
        return $this->repository->delete($meta);
    }

    // Product-specific operations
    public function findByProductId(int $productId): Collection
    {
        return $this->repository->findByProductId($productId);
    }

    public function deleteByProductId(int $productId): bool
    {
        return $this->repository->deleteByProductId($productId);
    }

    public function deleteByKey(int $productId, string $metaKey): bool
    {
        return $this->repository->deleteByKey($productId, $metaKey);
    }

    // Key-based operations
    public function findByMetaKey(string $metaKey): Collection
    {
        return $this->repository->findByMetaKey($metaKey);
    }

    public function findByProductIdAndKey(int $productId, string $metaKey): ?ProductMeta
    {
        return $this->repository->findByProductIdAndKey($productId, $metaKey);
    }

    // Type-based operations
    public function findByMetaType(string $metaType): Collection
    {
        return $this->repository->findByMetaType($metaType);
    }

    // Visibility operations
    public function findPublic(): Collection
    {
        return $this->repository->findPublic();
    }

    public function findPrivate(): Collection
    {
        return $this->repository->findPrivate();
    }

    // Search and filter operations
    public function findSearchable(): Collection
    {
        return $this->repository->findSearchable();
    }

    public function findFilterable(): Collection
    {
        return $this->repository->findFilterable();
    }

    public function search(string $query): Collection
    {
        return $this->repository->search($query);
    }

    // Value-based operations
    public function findByValue(string $metaValue): Collection
    {
        return $this->repository->findByValue($metaValue);
    }

    public function findByValueLike(string $metaValue): Collection
    {
        return $this->repository->findByValueLike($metaValue);
    }

    // Status management
    public function togglePublic(ProductMeta $meta): bool
    {
        return $this->repository->togglePublic($meta);
    }

    public function toggleSearchable(ProductMeta $meta): bool
    {
        return $this->repository->toggleSearchable($meta);
    }

    public function toggleFilterable(ProductMeta $meta): bool
    {
        return $this->repository->toggleFilterable($meta);
    }

    // Analytics and reporting
    public function getMetaKeys(): Collection
    {
        return $this->repository->getMetaKeys();
    }

    public function getMetaTypes(): Collection
    {
        return $this->repository->getMetaTypes();
    }

    public function getMetaValues(string $metaKey): Collection
    {
        return $this->repository->getMetaValues($metaKey);
    }

    public function getMetaAnalytics(string $metaKey): array
    {
        return $this->repository->getMetaAnalytics($metaKey);
    }

    // Validation
    public function validateMeta(array $data): bool
    {
        return $this->repository->validateMeta($data);
    }

    public function isKeyUnique(int $productId, string $metaKey, ?int $excludeId = null): bool
    {
        return $this->repository->isKeyUnique($productId, $metaKey, $excludeId);
    }

    // Bulk operations
    public function bulkCreate(int $productId, array $metaData): Collection
    {
        return $this->repository->bulkCreate($productId, $metaData);
    }

    public function bulkUpdate(int $productId, array $metaData): bool
    {
        return $this->repository->bulkUpdate($productId, $metaData);
    }

    public function bulkDelete(int $productId, array $metaKeys): bool
    {
        return $this->repository->bulkDelete($productId, $metaKeys);
    }

    // Import/Export operations
    public function importMeta(int $productId, array $metaData): bool
    {
        return $this->repository->importMeta($productId, $metaData);
    }

    public function exportMeta(int $productId): array
    {
        return $this->repository->exportMeta($productId);
    }

    public function syncMeta(int $productId, array $metaData): bool
    {
        return $this->repository->syncMeta($productId, $metaData);
    }

    // Additional utility methods
    public function getProductMetaAsArray(int $productId): array
    {
        return $this->findByProductId($productId)
            ->mapWithKeys(function ($meta) {
                return [$meta->meta_key => $meta->meta_value];
            })
            ->toArray();
    }

    public function setProductMeta(int $productId, string $key, string $value, array $options = []): ProductMeta
    {
        $existing = $this->findByProductIdAndKey($productId, $key);

        $data = array_merge([
            'product_id' => $productId,
            'meta_key' => $key,
            'meta_value' => $value,
            'meta_type' => $options['meta_type'] ?? 'text',
            'is_public' => $options['is_public'] ?? true,
            'is_searchable' => $options['is_searchable'] ?? false,
            'is_filterable' => $options['is_filterable'] ?? false,
            'sort_order' => $options['sort_order'] ?? 0,
            'description' => $options['description'] ?? null,
            'validation_rules' => $options['validation_rules'] ?? null,
        ], $options);

        if ($existing) {
            $this->update($existing, $data);
            return $existing->fresh();
        }

        return $this->create($data);
    }

    public function getProductMeta(int $productId, string $key, $default = null)
    {
        $meta = $this->findByProductIdAndKey($productId, $key);
        return $meta ? $meta->meta_value : $default;
    }

    public function hasProductMeta(int $productId, string $key): bool
    {
        return $this->findByProductIdAndKey($productId, $key) !== null;
    }

    public function removeProductMeta(int $productId, string $key): bool
    {
        return $this->deleteByKey($productId, $key);
    }
}
