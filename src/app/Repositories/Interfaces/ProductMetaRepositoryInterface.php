<?php

namespace Fereydooni\Shopping\app\Repositories\Interfaces;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Fereydooni\Shopping\app\Models\ProductMeta;
use Fereydooni\Shopping\app\DTOs\ProductMetaDTO;

interface ProductMetaRepositoryInterface
{
    // Basic CRUD operations
    public function all(): Collection;
    public function paginate(int $perPage = 15): LengthAwarePaginator;
    public function find(int $id): ?ProductMeta;
    public function findDTO(int $id): ?ProductMetaDTO;
    public function create(array $data): ProductMeta;
    public function createAndReturnDTO(array $data): ProductMetaDTO;
    public function update(ProductMeta $meta, array $data): bool;
    public function delete(ProductMeta $meta): bool;

    // Product-specific queries
    public function findByProductId(int $productId): Collection;
    public function deleteByProductId(int $productId): bool;
    public function deleteByKey(int $productId, string $metaKey): bool;

    // Key-based queries
    public function findByMetaKey(string $metaKey): Collection;
    public function findByProductIdAndKey(int $productId, string $metaKey): ?ProductMeta;

    // Type-based queries
    public function findByMetaType(string $metaType): Collection;

    // Visibility queries
    public function findPublic(): Collection;
    public function findPrivate(): Collection;

    // Search and filter queries
    public function findSearchable(): Collection;
    public function findFilterable(): Collection;
    public function search(string $query): Collection;

    // Value-based queries
    public function findByValue(string $metaValue): Collection;
    public function findByValueLike(string $metaValue): Collection;

    // Status management
    public function togglePublic(ProductMeta $meta): bool;
    public function toggleSearchable(ProductMeta $meta): bool;
    public function toggleFilterable(ProductMeta $meta): bool;

    // Analytics and reporting
    public function getMetaKeys(): Collection;
    public function getMetaTypes(): Collection;
    public function getMetaValues(string $metaKey): Collection;
    public function getMetaAnalytics(string $metaKey): array;

    // Validation
    public function validateMeta(array $data): bool;
    public function isKeyUnique(int $productId, string $metaKey, ?int $excludeId = null): bool;

    // Bulk operations
    public function bulkCreate(int $productId, array $metaData): Collection;
    public function bulkUpdate(int $productId, array $metaData): bool;
    public function bulkDelete(int $productId, array $metaKeys): bool;

    // Import/Export operations
    public function importMeta(int $productId, array $metaData): bool;
    public function exportMeta(int $productId): array;
    public function syncMeta(int $productId, array $metaData): bool;
}
