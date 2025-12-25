<?php

namespace Fereydooni\Shopping\app\Repositories;

use Fereydooni\Shopping\app\DTOs\ProductAttributeDTO;
use Fereydooni\Shopping\app\DTOs\ProductAttributeValueDTO;
use Fereydooni\Shopping\app\Enums\ProductAttributeInputType;
use Fereydooni\Shopping\app\Enums\ProductAttributeType;
use Fereydooni\Shopping\app\Models\ProductAttribute;
use Fereydooni\Shopping\app\Models\ProductAttributeValue;
use Fereydooni\Shopping\app\Repositories\Interfaces\ProductAttributeRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\CursorPaginator;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ProductAttributeRepository implements ProductAttributeRepositoryInterface
{
    public function all(): Collection
    {
        return ProductAttribute::ordered()->get();
    }

    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        return ProductAttribute::ordered()->paginate($perPage);
    }

    public function simplePaginate(int $perPage = 15): Paginator
    {
        return ProductAttribute::ordered()->simplePaginate($perPage);
    }

    public function cursorPaginate(int $perPage = 15, ?string $cursor = null): CursorPaginator
    {
        return ProductAttribute::ordered()->cursorPaginate($perPage, ['*'], 'cursor', $cursor);
    }

    public function find(int $id): ?ProductAttribute
    {
        return ProductAttribute::find($id);
    }

    public function findDTO(int $id): ?ProductAttributeDTO
    {
        $attribute = $this->find($id);

        return $attribute ? ProductAttributeDTO::fromModel($attribute) : null;
    }

    public function create(array $data): ProductAttribute
    {
        try {
            DB::beginTransaction();

            $data['created_by'] = auth()->id();
            $productAttribute = ProductAttribute::create($data);

            $productAttribute->values()->createMany(
                collect($data['values'])->map(fn ($value) => [
                    'value' => $value,
                    'created_by' => auth()->id(),
                ])->toArray()
            );
            DB::commit();

            return $productAttribute;
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }

    public function createAndReturnDTO(array $data): ProductAttributeDTO
    {
        $attribute = $this->create($data);

        return ProductAttributeDTO::fromModel($attribute);
    }

    public function update(ProductAttribute $productAttribute, array $data): bool
    {
        try {
            DB::beginTransaction();

            $data['updated_by'] = auth()->id();
            $productAttribute->update($data);

            // Sync attribute values: update existing, create new, delete removed
            $existingValues = $productAttribute->values()->pluck('id', 'value')->toArray();
            $newValues = collect($data['values'])->mapWithKeys(function ($value) {
                return [$value => $value];
            })->toArray();

            // Delete removed values
            $toDelete = array_diff($existingValues, $newValues);
            if (! empty($toDelete)) {
                $productAttribute->values()->whereIn('id', $toDelete)->delete();
            }

            // Add or update values
            foreach ($data['values'] as $value) {
                $productAttribute->values()->updateOrCreate(
                    ['value' => $value],
                    ['updated_by' => auth()->id()]
                );
            }
            DB::commit();

            return true;
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }

    public function updateAndReturnDTO(ProductAttribute $attribute, array $data): ?ProductAttributeDTO
    {
        $updated = $this->update($attribute, $data);

        return $updated ? ProductAttributeDTO::fromModel($attribute->fresh()) : null;
    }

    public function delete(ProductAttribute $attribute): bool
    {
        return $attribute->delete();
    }

    public function findBySlug(string $slug): ?ProductAttribute
    {
        return ProductAttribute::where('slug', $slug)->first();
    }

    public function findBySlugDTO(string $slug): ?ProductAttributeDTO
    {
        $attribute = $this->findBySlug($slug);

        return $attribute ? ProductAttributeDTO::fromModel($attribute) : null;
    }

    public function findByName(string $name): ?ProductAttribute
    {
        return ProductAttribute::where('name', $name)->first();
    }

    public function findByNameDTO(string $name): ?ProductAttributeDTO
    {
        $attribute = $this->findByName($name);

        return $attribute ? ProductAttributeDTO::fromModel($attribute) : null;
    }

    public function findByType(string $type): Collection
    {
        return ProductAttribute::byType($type)->ordered()->get();
    }

    public function findByTypeDTO(string $type): Collection
    {
        return $this->findByType($type)->map(fn ($attribute) => ProductAttributeDTO::fromModel($attribute));
    }

    public function findByInputType(string $inputType): Collection
    {
        return ProductAttribute::byInputType($inputType)->ordered()->get();
    }

    public function findByInputTypeDTO(string $inputType): Collection
    {
        return $this->findByInputType($inputType)->map(fn ($attribute) => ProductAttributeDTO::fromModel($attribute));
    }

    public function findByGroup(string $group): Collection
    {
        return ProductAttribute::byGroup($group)->ordered()->get();
    }

    public function findByGroupDTO(string $group): Collection
    {
        return $this->findByGroup($group)->map(fn ($attribute) => ProductAttributeDTO::fromModel($attribute));
    }

    public function findRequired(): Collection
    {
        return ProductAttribute::required()->ordered()->get();
    }

    public function findRequiredDTO(): Collection
    {
        return $this->findRequired()->map(fn ($attribute) => ProductAttributeDTO::fromModel($attribute));
    }

    public function findSearchable(): Collection
    {
        return ProductAttribute::searchable()->ordered()->get();
    }

    public function findSearchableDTO(): Collection
    {
        return $this->findSearchable()->map(fn ($attribute) => ProductAttributeDTO::fromModel($attribute));
    }

    public function findFilterable(): Collection
    {
        return ProductAttribute::filterable()->ordered()->get();
    }

    public function findFilterableDTO(): Collection
    {
        return $this->findFilterable()->map(fn ($attribute) => ProductAttributeDTO::fromModel($attribute));
    }

    public function findComparable(): Collection
    {
        return ProductAttribute::comparable()->ordered()->get();
    }

    public function findComparableDTO(): Collection
    {
        return $this->findComparable()->map(fn ($attribute) => ProductAttributeDTO::fromModel($attribute));
    }

    public function findVisible(): Collection
    {
        return ProductAttribute::visible()->ordered()->get();
    }

    public function findVisibleDTO(): Collection
    {
        return $this->findVisible()->map(fn ($attribute) => ProductAttributeDTO::fromModel($attribute));
    }

    public function findSystem(): Collection
    {
        return ProductAttribute::system()->ordered()->get();
    }

    public function findSystemDTO(): Collection
    {
        return $this->findSystem()->map(fn ($attribute) => ProductAttributeDTO::fromModel($attribute));
    }

    public function findCustom(): Collection
    {
        return ProductAttribute::custom()->ordered()->get();
    }

    public function findCustomDTO(): Collection
    {
        return $this->findCustom()->map(fn ($attribute) => ProductAttributeDTO::fromModel($attribute));
    }

    public function findActive(): Collection
    {
        return ProductAttribute::active()->ordered()->get();
    }

    public function findActiveDTO(): Collection
    {
        return $this->findActive()->map(fn ($attribute) => ProductAttributeDTO::fromModel($attribute));
    }

    public function toggleActive(ProductAttribute $attribute): bool
    {
        return $attribute->update(['is_active' => ! $attribute->is_active]);
    }

    public function toggleRequired(ProductAttribute $attribute): bool
    {
        return $attribute->update(['is_required' => ! $attribute->is_required]);
    }

    public function toggleSearchable(ProductAttribute $attribute): bool
    {
        return $attribute->update(['is_searchable' => ! $attribute->is_searchable]);
    }

    public function toggleFilterable(ProductAttribute $attribute): bool
    {
        return $attribute->update(['is_filterable' => ! $attribute->is_filterable]);
    }

    public function toggleComparable(ProductAttribute $attribute): bool
    {
        return $attribute->update(['is_comparable' => ! $attribute->is_comparable]);
    }

    public function toggleVisible(ProductAttribute $attribute): bool
    {
        return $attribute->update(['is_visible' => ! $attribute->is_visible]);
    }

    public function getAttributeCount(): int
    {
        return ProductAttribute::count();
    }

    public function getAttributeCountByType(string $type): int
    {
        return ProductAttribute::byType($type)->count();
    }

    public function getAttributeCountByGroup(string $group): int
    {
        return ProductAttribute::byGroup($group)->count();
    }

    public function getAttributeCountByInputType(string $inputType): int
    {
        return ProductAttribute::byInputType($inputType)->count();
    }

    public function getRequiredAttributeCount(): int
    {
        return ProductAttribute::required()->count();
    }

    public function getSearchableAttributeCount(): int
    {
        return ProductAttribute::searchable()->count();
    }

    public function getFilterableAttributeCount(): int
    {
        return ProductAttribute::filterable()->count();
    }

    public function getComparableAttributeCount(): int
    {
        return ProductAttribute::comparable()->count();
    }

    public function getVisibleAttributeCount(): int
    {
        return ProductAttribute::visible()->count();
    }

    public function getSystemAttributeCount(): int
    {
        return ProductAttribute::system()->count();
    }

    public function getCustomAttributeCount(): int
    {
        return ProductAttribute::custom()->count();
    }

    public function search(string $query): Collection
    {
        return ProductAttribute::where(function ($q) use ($query) {
            $q->where('name', 'like', "%{$query}%")
                ->orWhere('slug', 'like', "%{$query}%")
                ->orWhere('description', 'like', "%{$query}%")
                ->orWhere('group', 'like', "%{$query}%");
        })->ordered()->get();
    }

    public function searchDTO(string $query): Collection
    {
        return $this->search($query)->map(fn ($attribute) => ProductAttributeDTO::fromModel($attribute));
    }

    public function getAttributeGroups(): Collection
    {
        return ProductAttribute::select('group')
            ->whereNotNull('group')
            ->distinct()
            ->pluck('group');
    }

    public function getAttributeTypes(): Collection
    {
        return collect(ProductAttributeType::cases())->map(fn ($type) => [
            'value' => $type->value,
            'label' => $type->label(),
            'description' => $type->description(),
        ]);
    }

    public function getInputTypes(): Collection
    {
        return collect(ProductAttributeInputType::cases())->map(fn ($type) => [
            'value' => $type->value,
            'label' => $type->label(),
            'description' => $type->description(),
        ]);
    }

    public function validateAttribute(array $data): bool
    {
        $rules = ProductAttributeDTO::rules();
        $validator = validator($data, $rules);

        return ! $validator->fails();
    }

    public function generateSlug(string $name): string
    {
        $slug = Str::slug($name);
        $originalSlug = $slug;
        $counter = 1;

        while (! $this->isSlugUnique($slug)) {
            $slug = $originalSlug.'-'.$counter;
            $counter++;
        }

        return $slug;
    }

    public function isSlugUnique(string $slug, ?int $excludeId = null): bool
    {
        $query = ProductAttribute::where('slug', $slug);

        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        return ! $query->exists();
    }

    public function isNameUnique(string $name, ?int $excludeId = null): bool
    {
        $query = ProductAttribute::where('name', $name);

        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        return ! $query->exists();
    }

    public function getAttributeUsage(int $attributeId): int
    {
        // This would need to be implemented based on your product-attribute relationship
        // For now, returning a placeholder
        return 0;
    }

    public function getAttributeUsageByProduct(int $attributeId, int $productId): int
    {
        // This would need to be implemented based on your product-attribute relationship
        return 0;
    }

    public function getAttributeUsageByCategory(int $attributeId, int $categoryId): int
    {
        // This would need to be implemented based on your category-attribute relationship
        return 0;
    }

    public function getAttributeUsageByBrand(int $attributeId, int $brandId): int
    {
        // This would need to be implemented based on your brand-attribute relationship
        return 0;
    }

    public function getAttributeAnalytics(int $attributeId): array
    {
        $attribute = $this->find($attributeId);
        if (! $attribute) {
            return [];
        }

        return [
            'id' => $attribute->id,
            'name' => $attribute->name,
            'usage_count' => $this->getAttributeUsage($attributeId),
            'values_count' => $this->getAttributeValueCount($attributeId),
            'is_active' => $attribute->is_active,
            'is_required' => $attribute->is_required,
            'is_searchable' => $attribute->is_searchable,
            'is_filterable' => $attribute->is_filterable,
            'is_comparable' => $attribute->is_comparable,
            'is_visible' => $attribute->is_visible,
            'created_at' => $attribute->created_at,
            'updated_at' => $attribute->updated_at,
        ];
    }

    public function getAttributeValues(int $attributeId): Collection
    {
        return ProductAttributeValue::where('attribute_id', $attributeId)->get();
    }

    public function getAttributeValuesDTO(int $attributeId): Collection
    {
        return $this->getAttributeValues($attributeId)->map(fn ($value) => ProductAttributeValueDTO::fromModel($value));
    }

    public function addAttributeValue(int $attributeId, string $value, array $metadata = []): ProductAttributeValue
    {
        return ProductAttributeValue::create([
            'attribute_id' => $attributeId,
            'value' => $value,
            'metadata' => $metadata,
        ]);
    }

    public function addAttributeValueDTO(int $attributeId, string $value, array $metadata = []): ProductAttributeValueDTO
    {
        $attributeValue = $this->addAttributeValue($attributeId, $value, $metadata);

        return ProductAttributeValueDTO::fromModel($attributeValue);
    }

    public function removeAttributeValue(int $attributeId, int $valueId): bool
    {
        return ProductAttributeValue::where('attribute_id', $attributeId)
            ->where('id', $valueId)
            ->delete() > 0;
    }

    public function updateAttributeValue(int $attributeId, int $valueId, string $value, array $metadata = []): bool
    {
        return ProductAttributeValue::where('attribute_id', $attributeId)
            ->where('id', $valueId)
            ->update([
                'value' => $value,
                'metadata' => $metadata,
            ]) > 0;
    }

    public function getAttributeValueCount(int $attributeId): int
    {
        return ProductAttributeValue::where('attribute_id', $attributeId)->count();
    }

    public function getAttributeValueUsage(int $attributeId, int $valueId): int
    {
        // This would need to be implemented based on your product-attribute-value relationship
        return 0;
    }
}
