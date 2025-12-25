<?php

namespace Fereydooni\Shopping\app\Repositories;

use Fereydooni\Shopping\app\DTOs\ProductMetaDTO;
use Fereydooni\Shopping\app\Models\ProductMeta;
use Fereydooni\Shopping\app\Repositories\Interfaces\ProductMetaRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ProductMetaRepository implements ProductMetaRepositoryInterface
{
    public function all(): Collection
    {
        return ProductMeta::orderBy('sort_order')->get();
    }

    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        return ProductMeta::orderBy('sort_order')->paginate($perPage);
    }

    public function find(int $id): ?ProductMeta
    {
        return ProductMeta::find($id);
    }

    public function findDTO(int $id): ?ProductMetaDTO
    {
        $meta = $this->find($id);

        return $meta ? ProductMetaDTO::fromModel($meta) : null;
    }

    public function create(array $data): ProductMeta
    {
        return ProductMeta::create($data);
    }

    public function createAndReturnDTO(array $data): ProductMetaDTO
    {
        $meta = $this->create($data);

        return ProductMetaDTO::fromModel($meta);
    }

    public function update(ProductMeta $meta, array $data): bool
    {
        return $meta->update($data);
    }

    public function delete(ProductMeta $meta): bool
    {
        return $meta->delete();
    }

    public function findByProductId(int $productId): Collection
    {
        return ProductMeta::where('product_id', $productId)
            ->orderBy('sort_order')
            ->get();
    }

    public function deleteByProductId(int $productId): bool
    {
        return ProductMeta::where('product_id', $productId)->delete() > 0;
    }

    public function deleteByKey(int $productId, string $metaKey): bool
    {
        return ProductMeta::where('product_id', $productId)
            ->where('meta_key', $metaKey)
            ->delete() > 0;
    }

    public function findByMetaKey(string $metaKey): Collection
    {
        return ProductMeta::where('meta_key', $metaKey)
            ->orderBy('sort_order')
            ->get();
    }

    public function findByProductIdAndKey(int $productId, string $metaKey): ?ProductMeta
    {
        return ProductMeta::where('product_id', $productId)
            ->where('meta_key', $metaKey)
            ->first();
    }

    public function findByMetaType(string $metaType): Collection
    {
        return ProductMeta::where('meta_type', $metaType)
            ->orderBy('sort_order')
            ->get();
    }

    public function findPublic(): Collection
    {
        return ProductMeta::where('is_public', true)
            ->orderBy('sort_order')
            ->get();
    }

    public function findPrivate(): Collection
    {
        return ProductMeta::where('is_public', false)
            ->orderBy('sort_order')
            ->get();
    }

    public function findSearchable(): Collection
    {
        return ProductMeta::where('is_searchable', true)
            ->orderBy('sort_order')
            ->get();
    }

    public function findFilterable(): Collection
    {
        return ProductMeta::where('is_filterable', true)
            ->orderBy('sort_order')
            ->get();
    }

    public function search(string $query): Collection
    {
        return ProductMeta::where(function ($q) use ($query) {
            $q->where('meta_key', 'like', "%{$query}%")
                ->orWhere('meta_value', 'like', "%{$query}%")
                ->orWhere('description', 'like', "%{$query}%");
        })
            ->orderBy('sort_order')
            ->get();
    }

    public function findByValue(string $metaValue): Collection
    {
        return ProductMeta::where('meta_value', $metaValue)
            ->orderBy('sort_order')
            ->get();
    }

    public function findByValueLike(string $metaValue): Collection
    {
        return ProductMeta::where('meta_value', 'like', "%{$metaValue}%")
            ->orderBy('sort_order')
            ->get();
    }

    public function togglePublic(ProductMeta $meta): bool
    {
        return $meta->update(['is_public' => ! $meta->is_public]);
    }

    public function toggleSearchable(ProductMeta $meta): bool
    {
        return $meta->update(['is_searchable' => ! $meta->is_searchable]);
    }

    public function toggleFilterable(ProductMeta $meta): bool
    {
        return $meta->update(['is_filterable' => ! $meta->is_filterable]);
    }

    public function getMetaKeys(): Collection
    {
        return ProductMeta::select('meta_key')
            ->distinct()
            ->orderBy('meta_key')
            ->get()
            ->pluck('meta_key');
    }

    public function getMetaTypes(): Collection
    {
        return ProductMeta::select('meta_type')
            ->distinct()
            ->orderBy('meta_type')
            ->get()
            ->pluck('meta_type');
    }

    public function getMetaValues(string $metaKey): Collection
    {
        return ProductMeta::where('meta_key', $metaKey)
            ->select('meta_value')
            ->distinct()
            ->orderBy('meta_value')
            ->get()
            ->pluck('meta_value');
    }

    public function getMetaAnalytics(string $metaKey): array
    {
        $analytics = ProductMeta::where('meta_key', $metaKey)
            ->selectRaw('
                COUNT(*) as total_count,
                COUNT(DISTINCT product_id) as unique_products,
                COUNT(CASE WHEN is_public = 1 THEN 1 END) as public_count,
                COUNT(CASE WHEN is_searchable = 1 THEN 1 END) as searchable_count,
                COUNT(CASE WHEN is_filterable = 1 THEN 1 END) as filterable_count
            ')
            ->first();

        return [
            'total_count' => $analytics->total_count ?? 0,
            'unique_products' => $analytics->unique_products ?? 0,
            'public_count' => $analytics->public_count ?? 0,
            'searchable_count' => $analytics->searchable_count ?? 0,
            'filterable_count' => $analytics->filterable_count ?? 0,
        ];
    }

    public function validateMeta(array $data): bool
    {
        $rules = ProductMetaDTO::rules();
        $validator = Validator::make($data, $rules);

        return ! $validator->fails();
    }

    public function isKeyUnique(int $productId, string $metaKey, ?int $excludeId = null): bool
    {
        $query = ProductMeta::where('product_id', $productId)
            ->where('meta_key', $metaKey);

        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        return ! $query->exists();
    }

    public function bulkCreate(int $productId, array $metaData): Collection
    {
        $created = collect();

        foreach ($metaData as $data) {
            $data['product_id'] = $productId;
            if ($this->validateMeta($data)) {
                $created->push($this->create($data));
            }
        }

        return $created;
    }

    public function bulkUpdate(int $productId, array $metaData): bool
    {
        $success = true;

        foreach ($metaData as $data) {
            if (isset($data['id'])) {
                $meta = $this->find($data['id']);
                if ($meta && $meta->product_id === $productId) {
                    if (! $this->update($meta, $data)) {
                        $success = false;
                    }
                }
            }
        }

        return $success;
    }

    public function bulkDelete(int $productId, array $metaKeys): bool
    {
        return ProductMeta::where('product_id', $productId)
            ->whereIn('meta_key', $metaKeys)
            ->delete() > 0;
    }

    public function importMeta(int $productId, array $metaData): bool
    {
        DB::beginTransaction();

        try {
            // Delete existing meta for this product
            $this->deleteByProductId($productId);

            // Create new meta
            $this->bulkCreate($productId, $metaData);

            DB::commit();

            return true;
        } catch (\Exception $e) {
            DB::rollBack();

            return false;
        }
    }

    public function exportMeta(int $productId): array
    {
        return ProductMeta::where('product_id', $productId)
            ->orderBy('sort_order')
            ->get()
            ->map(function ($meta) {
                return [
                    'meta_key' => $meta->meta_key,
                    'meta_value' => $meta->meta_value,
                    'meta_type' => $meta->meta_type,
                    'is_public' => $meta->is_public,
                    'is_searchable' => $meta->is_searchable,
                    'is_filterable' => $meta->is_filterable,
                    'sort_order' => $meta->sort_order,
                    'description' => $meta->description,
                    'validation_rules' => $meta->validation_rules,
                ];
            })
            ->toArray();
    }

    public function syncMeta(int $productId, array $metaData): bool
    {
        DB::beginTransaction();

        try {
            $existingKeys = ProductMeta::where('product_id', $productId)
                ->pluck('meta_key')
                ->toArray();

            $newKeys = array_column($metaData, 'meta_key');

            // Delete meta that are not in the new data
            $keysToDelete = array_diff($existingKeys, $newKeys);
            if (! empty($keysToDelete)) {
                $this->bulkDelete($productId, $keysToDelete);
            }

            // Update or create meta
            foreach ($metaData as $data) {
                $existing = $this->findByProductIdAndKey($productId, $data['meta_key']);

                if ($existing) {
                    $this->update($existing, $data);
                } else {
                    $this->create(array_merge($data, ['product_id' => $productId]));
                }
            }

            DB::commit();

            return true;
        } catch (\Exception $e) {
            DB::rollBack();

            return false;
        }
    }
}
