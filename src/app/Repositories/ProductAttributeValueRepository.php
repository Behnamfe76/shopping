<?php

namespace Fereydooni\Shopping\app\Repositories;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Pagination\CursorPaginator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Fereydooni\Shopping\app\Models\ProductAttributeValue;
use Fereydooni\Shopping\app\DTOs\ProductAttributeValueDTO;
use Fereydooni\Shopping\app\Repositories\Interfaces\ProductAttributeValueRepositoryInterface;

class ProductAttributeValueRepository implements ProductAttributeValueRepositoryInterface
{
    public function all(): Collection
    {
        return ProductAttributeValue::with('attribute')->get();
    }

    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        return ProductAttributeValue::with('attribute')->paginate($perPage);
    }

    public function simplePaginate(int $perPage = 15): Paginator
    {
        return ProductAttributeValue::with('attribute')->simplePaginate($perPage);
    }

    public function cursorPaginate(int $perPage = 15, string $cursor = null): CursorPaginator
    {
        return ProductAttributeValue::with('attribute')->cursorPaginate($perPage, ['*'], 'cursor', $cursor);
    }

    public function find(int $id): ?ProductAttributeValue
    {
        return ProductAttributeValue::with('attribute')->find($id);
    }

    public function findDTO(int $id): ?ProductAttributeValueDTO
    {
        $value = $this->find($id);
        return $value ? ProductAttributeValueDTO::fromModel($value) : null;
    }

    public function create(array $data): ProductAttributeValue
    {
        if (!isset($data['slug']) && isset($data['value'])) {
            $data['slug'] = $this->generateSlug($data['value']);
        }

        return ProductAttributeValue::create($data);
    }

    public function createAndReturnDTO(array $data): ProductAttributeValueDTO
    {
        $value = $this->create($data);
        return ProductAttributeValueDTO::fromModel($value);
    }

    public function update(ProductAttributeValue $value, array $data): bool
    {
        if (isset($data['value']) && !isset($data['slug'])) {
            $data['slug'] = $this->generateSlug($data['value']);
        }

        return $value->update($data);
    }

    public function updateAndReturnDTO(ProductAttributeValue $value, array $data): ?ProductAttributeValueDTO
    {
        $updated = $this->update($value, $data);
        return $updated ? ProductAttributeValueDTO::fromModel($value->fresh()) : null;
    }

    public function delete(ProductAttributeValue $value): bool
    {
        return $value->delete();
    }

    // Attribute-specific queries
    public function findByAttributeId(int $attributeId): Collection
    {
        return ProductAttributeValue::where('attribute_id', $attributeId)
            ->with('attribute')
            ->orderBy('sort_order')
            ->get();
    }

    public function findByAttributeIdDTO(int $attributeId): Collection
    {
        return $this->findByAttributeId($attributeId)->map(function ($value) {
            return ProductAttributeValueDTO::fromModel($value);
        });
    }

    public function findByAttributeIdAndValue(int $attributeId, string $value): ?ProductAttributeValue
    {
        return ProductAttributeValue::where('attribute_id', $attributeId)
            ->where('value', $value)
            ->with('attribute')
            ->first();
    }

    public function findByAttributeIdAndValueDTO(int $attributeId, string $value): ?ProductAttributeValueDTO
    {
        $value = $this->findByAttributeIdAndValue($attributeId, $value);
        return $value ? ProductAttributeValueDTO::fromModel($value) : null;
    }

    // Value-based queries
    public function findByValue(string $value): Collection
    {
        return ProductAttributeValue::where('value', 'like', "%{$value}%")
            ->with('attribute')
            ->get();
    }

    public function findByValueDTO(string $value): Collection
    {
        return $this->findByValue($value)->map(function ($value) {
            return ProductAttributeValueDTO::fromModel($value);
        });
    }

    // Slug-based queries
    public function findBySlug(string $slug): ?ProductAttributeValue
    {
        return ProductAttributeValue::where('slug', $slug)
            ->with('attribute')
            ->first();
    }

    public function findBySlugDTO(string $slug): ?ProductAttributeValueDTO
    {
        $value = $this->findBySlug($slug);
        return $value ? ProductAttributeValueDTO::fromModel($value) : null;
    }

    // Status-based queries
    public function findActive(): Collection
    {
        return ProductAttributeValue::where('is_active', true)
            ->with('attribute')
            ->orderBy('sort_order')
            ->get();
    }

    public function findActiveDTO(): Collection
    {
        return $this->findActive()->map(function ($value) {
            return ProductAttributeValueDTO::fromModel($value);
        });
    }

    public function findDefault(): Collection
    {
        return ProductAttributeValue::where('is_default', true)
            ->with('attribute')
            ->orderBy('sort_order')
            ->get();
    }

    public function findDefaultDTO(): Collection
    {
        return $this->findDefault()->map(function ($value) {
            return ProductAttributeValueDTO::fromModel($value);
        });
    }

    // Relationship queries
    public function findByVariantId(int $variantId): Collection
    {
        return ProductAttributeValue::whereHas('variants', function ($query) use ($variantId) {
            $query->where('product_variants.id', $variantId);
        })->with('attribute')->get();
    }

    public function findByVariantIdDTO(int $variantId): Collection
    {
        return $this->findByVariantId($variantId)->map(function ($value) {
            return ProductAttributeValueDTO::fromModel($value);
        });
    }

    public function findByProductId(int $productId): Collection
    {
        return ProductAttributeValue::whereHas('variants.product', function ($query) use ($productId) {
            $query->where('products.id', $productId);
        })->with('attribute')->get();
    }

    public function findByProductIdDTO(int $productId): Collection
    {
        return $this->findByProductId($productId)->map(function ($value) {
            return ProductAttributeValueDTO::fromModel($value);
        });
    }

    public function findByCategoryId(int $categoryId): Collection
    {
        return ProductAttributeValue::whereHas('variants.product.category', function ($query) use ($categoryId) {
            $query->where('categories.id', $categoryId);
        })->with('attribute')->get();
    }

    public function findByCategoryIdDTO(int $categoryId): Collection
    {
        return $this->findByCategoryId($categoryId)->map(function ($value) {
            return ProductAttributeValueDTO::fromModel($value);
        });
    }

    public function findByBrandId(int $brandId): Collection
    {
        return ProductAttributeValue::whereHas('variants.product.brand', function ($query) use ($brandId) {
            $query->where('brands.id', $brandId);
        })->with('attribute')->get();
    }

    public function findByBrandIdDTO(int $brandId): Collection
    {
        return $this->findByBrandId($brandId)->map(function ($value) {
            return ProductAttributeValueDTO::fromModel($value);
        });
    }

    // Status management
    public function toggleActive(ProductAttributeValue $value): bool
    {
        return $value->update(['is_active' => !$value->is_active]);
    }

    public function toggleDefault(ProductAttributeValue $value): bool
    {
        return $value->update(['is_default' => !$value->is_default]);
    }

    public function setDefault(ProductAttributeValue $value): bool
    {
        // Remove default from other values in the same attribute
        ProductAttributeValue::where('attribute_id', $value->attribute_id)
            ->where('id', '!=', $value->id)
            ->update(['is_default' => false]);

        return $value->update(['is_default' => true]);
    }

    // Count methods
    public function getValueCount(): int
    {
        return ProductAttributeValue::count();
    }

    public function getValueCountByAttributeId(int $attributeId): int
    {
        return ProductAttributeValue::where('attribute_id', $attributeId)->count();
    }

    public function getActiveValueCount(): int
    {
        return ProductAttributeValue::where('is_active', true)->count();
    }

    public function getDefaultValueCount(): int
    {
        return ProductAttributeValue::where('is_default', true)->count();
    }

    public function getValueCountByVariantId(int $variantId): int
    {
        return ProductAttributeValue::whereHas('variants', function ($query) use ($variantId) {
            $query->where('product_variants.id', $variantId);
        })->count();
    }

    public function getValueCountByProductId(int $productId): int
    {
        return ProductAttributeValue::whereHas('variants.product', function ($query) use ($productId) {
            $query->where('products.id', $productId);
        })->count();
    }

    public function getValueCountByCategoryId(int $categoryId): int
    {
        return ProductAttributeValue::whereHas('variants.product.category', function ($query) use ($categoryId) {
            $query->where('categories.id', $categoryId);
        })->count();
    }

    public function getValueCountByBrandId(int $brandId): int
    {
        return ProductAttributeValue::whereHas('variants.product.brand', function ($query) use ($brandId) {
            $query->where('brands.id', $brandId);
        })->count();
    }

    // Search functionality
    public function search(string $query): Collection
    {
        return ProductAttributeValue::where(function ($q) use ($query) {
            $q->where('value', 'like', "%{$query}%")
              ->orWhere('slug', 'like', "%{$query}%")
              ->orWhere('description', 'like', "%{$query}%");
        })->with('attribute')->get();
    }

    public function searchDTO(string $query): Collection
    {
        return $this->search($query)->map(function ($value) {
            return ProductAttributeValueDTO::fromModel($value);
        });
    }

    public function searchByAttributeId(int $attributeId, string $query): Collection
    {
        return ProductAttributeValue::where('attribute_id', $attributeId)
            ->where(function ($q) use ($query) {
                $q->where('value', 'like', "%{$query}%")
                  ->orWhere('slug', 'like', "%{$query}%")
                  ->orWhere('description', 'like', "%{$query}%");
            })->with('attribute')->get();
    }

    public function searchByAttributeIdDTO(int $attributeId, string $query): Collection
    {
        return $this->searchByAttributeId($attributeId, $query)->map(function ($value) {
            return ProductAttributeValueDTO::fromModel($value);
        });
    }

    // Usage analytics
    public function getMostUsedValues(int $limit = 10): Collection
    {
        return ProductAttributeValue::orderBy('usage_count', 'desc')
            ->limit($limit)
            ->with('attribute')
            ->get();
    }

    public function getMostUsedValuesDTO(int $limit = 10): Collection
    {
        return $this->getMostUsedValues($limit)->map(function ($value) {
            return ProductAttributeValueDTO::fromModel($value);
        });
    }

    public function getLeastUsedValues(int $limit = 10): Collection
    {
        return ProductAttributeValue::orderBy('usage_count', 'asc')
            ->limit($limit)
            ->with('attribute')
            ->get();
    }

    public function getLeastUsedValuesDTO(int $limit = 10): Collection
    {
        return $this->getLeastUsedValues($limit)->map(function ($value) {
            return ProductAttributeValueDTO::fromModel($value);
        });
    }

    public function getUnusedValues(): Collection
    {
        return ProductAttributeValue::where('usage_count', 0)
            ->with('attribute')
            ->get();
    }

    public function getUnusedValuesDTO(): Collection
    {
        return $this->getUnusedValues()->map(function ($value) {
            return ProductAttributeValueDTO::fromModel($value);
        });
    }

    public function getValuesByUsageRange(int $minUsage, int $maxUsage): Collection
    {
        return ProductAttributeValue::whereBetween('usage_count', [$minUsage, $maxUsage])
            ->with('attribute')
            ->orderBy('usage_count', 'desc')
            ->get();
    }

    public function getValuesByUsageRangeDTO(int $minUsage, int $maxUsage): Collection
    {
        return $this->getValuesByUsageRange($minUsage, $maxUsage)->map(function ($value) {
            return ProductAttributeValueDTO::fromModel($value);
        });
    }

    // Validation methods
    public function validateAttributeValue(array $data): bool
    {
        $rules = ProductAttributeValueDTO::rules();
        $validator = validator($data, $rules, ProductAttributeValueDTO::messages());
        return !$validator->fails();
    }

    public function generateSlug(string $value): string
    {
        $slug = Str::slug($value);
        $originalSlug = $slug;
        $counter = 1;

        while (!$this->isSlugUnique($slug)) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }

        return $slug;
    }

    public function isSlugUnique(string $slug, ?int $excludeId = null): bool
    {
        $query = ProductAttributeValue::where('slug', $slug);

        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        return !$query->exists();
    }

    public function isValueUnique(int $attributeId, string $value, ?int $excludeId = null): bool
    {
        $query = ProductAttributeValue::where('attribute_id', $attributeId)
            ->where('value', $value);

        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        return !$query->exists();
    }

    // Usage tracking
    public function getValueUsage(int $valueId): int
    {
        $value = ProductAttributeValue::find($valueId);
        return $value ? $value->usage_count : 0;
    }

    public function getValueUsageByProduct(int $valueId, int $productId): int
    {
        return ProductAttributeValue::where('id', $valueId)
            ->whereHas('variants.product', function ($query) use ($productId) {
                $query->where('products.id', $productId);
            })->count();
    }

    public function getValueUsageByCategory(int $valueId, int $categoryId): int
    {
        return ProductAttributeValue::where('id', $valueId)
            ->whereHas('variants.product.category', function ($query) use ($categoryId) {
                $query->where('categories.id', $categoryId);
            })->count();
    }

    public function getValueUsageByBrand(int $valueId, int $brandId): int
    {
        return ProductAttributeValue::where('id', $valueId)
            ->whereHas('variants.product.brand', function ($query) use ($brandId) {
                $query->where('brands.id', $brandId);
            })->count();
    }

    public function getValueUsageByVariant(int $valueId, int $variantId): int
    {
        return ProductAttributeValue::where('id', $valueId)
            ->whereHas('variants', function ($query) use ($variantId) {
                $query->where('product_variants.id', $variantId);
            })->count();
    }

    public function getValueAnalytics(int $valueId): array
    {
        $value = ProductAttributeValue::find($valueId);
        if (!$value) {
            return [];
        }

        return [
            'usage_count' => $value->usage_count,
            'is_active' => $value->is_active,
            'is_default' => $value->is_default,
            'created_at' => $value->created_at,
            'updated_at' => $value->updated_at,
            'variants_count' => $value->variants()->count(),
            'products_count' => $value->variants()->whereHas('product')->count(),
        ];
    }

    public function incrementUsage(int $valueId): bool
    {
        return ProductAttributeValue::where('id', $valueId)
            ->increment('usage_count') > 0;
    }

    public function decrementUsage(int $valueId): bool
    {
        return ProductAttributeValue::where('id', $valueId)
            ->where('usage_count', '>', 0)
            ->decrement('usage_count') > 0;
    }

    // Relationship data
    public function getValueVariants(int $valueId): Collection
    {
        $value = ProductAttributeValue::find($valueId);
        return $value ? $value->variants : collect();
    }

    public function getValueVariantsDTO(int $valueId): Collection
    {
        return $this->getValueVariants($valueId);
    }

    public function getValueProducts(int $valueId): Collection
    {
        $value = ProductAttributeValue::find($valueId);
        return $value ? $value->variants()->with('product')->get()->pluck('product') : collect();
    }

    public function getValueProductsDTO(int $valueId): Collection
    {
        return $this->getValueProducts($valueId);
    }

    public function getValueCategories(int $valueId): Collection
    {
        $value = ProductAttributeValue::find($valueId);
        return $value ? $value->variants()->with('product.category')->get()->pluck('product.category') : collect();
    }

    public function getValueCategoriesDTO(int $valueId): Collection
    {
        return $this->getValueCategories($valueId);
    }

    public function getValueBrands(int $valueId): Collection
    {
        $value = ProductAttributeValue::find($valueId);
        return $value ? $value->variants()->with('product.brand')->get()->pluck('product.brand') : collect();
    }

    public function getValueBrandsDTO(int $valueId): Collection
    {
        return $this->getValueBrands($valueId);
    }

    // Relationship management
    public function assignToVariant(int $valueId, int $variantId): bool
    {
        $value = ProductAttributeValue::find($valueId);
        if (!$value) {
            return false;
        }

        $value->variants()->syncWithoutDetaching([$variantId]);
        $this->incrementUsage($valueId);
        return true;
    }

    public function removeFromVariant(int $valueId, int $variantId): bool
    {
        $value = ProductAttributeValue::find($valueId);
        if (!$value) {
            return false;
        }

        $value->variants()->detach($variantId);
        $this->decrementUsage($valueId);
        return true;
    }

    public function assignToProduct(int $valueId, int $productId): bool
    {
        // This would need to be implemented based on the relationship structure
        // For now, we'll return false as it's not directly related
        return false;
    }

    public function removeFromProduct(int $valueId, int $productId): bool
    {
        // This would need to be implemented based on the relationship structure
        // For now, we'll return false as it's not directly related
        return false;
    }

    public function assignToCategory(int $valueId, int $categoryId): bool
    {
        // This would need to be implemented based on the relationship structure
        // For now, we'll return false as it's not directly related
        return false;
    }

    public function removeFromCategory(int $valueId, int $categoryId): bool
    {
        // This would need to be implemented based on the relationship structure
        // For now, we'll return false as it's not directly related
        return false;
    }

    public function assignToBrand(int $valueId, int $brandId): bool
    {
        // This would need to be implemented based on the relationship structure
        // For now, we'll return false as it's not directly related
        return false;
    }

    public function removeFromBrand(int $valueId, int $brandId): bool
    {
        // This would need to be implemented based on the relationship structure
        // For now, we'll return false as it's not directly related
        return false;
    }
}
