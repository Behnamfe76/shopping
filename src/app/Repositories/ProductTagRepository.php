<?php

namespace Fereydooni\Shopping\app\Repositories;

use Fereydooni\Shopping\app\DTOs\ProductTagDTO;
use Fereydooni\Shopping\app\Models\ProductTag;
use Fereydooni\Shopping\app\Repositories\Interfaces\ProductTagRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ProductTagRepository implements ProductTagRepositoryInterface
{
    protected array $searchableFields = ['name', 'description', 'slug'];

    public function all(): Collection
    {
        return ProductTag::orderBy('sort_order')->get();
    }

    public function find(int $id): ?ProductTag
    {
        return ProductTag::find($id);
    }

    public function findDTO(int $id): ?ProductTagDTO
    {
        $tag = $this->find($id);

        return $tag ? ProductTagDTO::fromModel($tag) : null;
    }

    public function findBySlug(string $slug): ?ProductTag
    {
        return ProductTag::where('slug', $slug)->first();
    }

    public function findBySlugDTO(string $slug): ?ProductTagDTO
    {
        $tag = $this->findBySlug($slug);

        return $tag ? ProductTagDTO::fromModel($tag) : null;
    }

    public function findByName(string $name): ?ProductTag
    {
        return ProductTag::where('name', $name)->first();
    }

    public function findByNameDTO(string $name): ?ProductTagDTO
    {
        $tag = $this->findByName($name);

        return $tag ? ProductTagDTO::fromModel($tag) : null;
    }

    public function findActive(): Collection
    {
        return ProductTag::where('is_active', true)->orderBy('sort_order')->get();
    }

    public function findActiveDTO(): Collection
    {
        return $this->findActive()->map(fn ($tag) => ProductTagDTO::fromModel($tag));
    }

    public function findFeatured(): Collection
    {
        return ProductTag::where('is_featured', true)->orderBy('sort_order')->get();
    }

    public function findFeaturedDTO(): Collection
    {
        return $this->findFeatured()->map(fn ($tag) => ProductTagDTO::fromModel($tag));
    }

    public function findByUsageCount(int $minCount): Collection
    {
        return ProductTag::where('usage_count', '>=', $minCount)->orderBy('usage_count', 'desc')->get();
    }

    public function findByUsageCountDTO(int $minCount): Collection
    {
        return $this->findByUsageCount($minCount)->map(fn ($tag) => ProductTagDTO::fromModel($tag));
    }

    public function findPopular(int $limit = 10): Collection
    {
        return ProductTag::orderBy('usage_count', 'desc')->limit($limit)->get();
    }

    public function findPopularDTO(int $limit = 10): Collection
    {
        return $this->findPopular($limit)->map(fn ($tag) => ProductTagDTO::fromModel($tag));
    }

    public function findRecent(int $limit = 10): Collection
    {
        return ProductTag::orderBy('created_at', 'desc')->limit($limit)->get();
    }

    public function findRecentDTO(int $limit = 10): Collection
    {
        return $this->findRecent($limit)->map(fn ($tag) => ProductTagDTO::fromModel($tag));
    }

    public function findByColor(string $color): Collection
    {
        return ProductTag::where('color', $color)->get();
    }

    public function findByColorDTO(string $color): Collection
    {
        return $this->findByColor($color)->map(fn ($tag) => ProductTagDTO::fromModel($tag));
    }

    public function findByIcon(string $icon): Collection
    {
        return ProductTag::where('icon', $icon)->get();
    }

    public function findByIconDTO(string $icon): Collection
    {
        return $this->findByIcon($icon)->map(fn ($tag) => ProductTagDTO::fromModel($tag));
    }

    public function create(array $data): ProductTag
    {
        return ProductTag::create($data);
    }

    public function createAndReturnDTO(array $data): ProductTagDTO
    {
        $data['created_by'] = request()->user()->id;
        $tag = $this->create($data);

        return ProductTagDTO::fromModel($tag);
    }

    public function update(ProductTag $tag, array $data): bool
    {
        return $tag->update($data);
    }

    public function delete(ProductTag $tag): bool
    {
        return $tag->delete();
    }

    public function toggleActive(ProductTag $tag): bool
    {
        return $tag->update(['is_active' => ! $tag->is_active]);
    }

    public function toggleFeatured(ProductTag $tag): bool
    {
        return $tag->update(['is_featured' => ! $tag->is_featured]);
    }

    public function incrementUsage(ProductTag $tag): bool
    {
        return $tag->increment('usage_count');
    }

    public function decrementUsage(ProductTag $tag): bool
    {
        return $tag->decrement('usage_count');
    }

    public function getTagCount(): int
    {
        return ProductTag::count();
    }

    public function getActiveTagCount(): int
    {
        return ProductTag::where('is_active', true)->count();
    }

    public function getFeaturedTagCount(): int
    {
        return ProductTag::where('is_featured', true)->count();
    }

    public function getPopularTagCount(): int
    {
        return ProductTag::where('usage_count', '>', 0)->count();
    }

    public function search(string $query): Collection
    {
        return ProductTag::where('name', 'like', "%{$query}%")
            ->orWhere('description', 'like', "%{$query}%")
            ->orWhere('slug', 'like', "%{$query}%")
            ->get();
    }

    public function searchDTO(string $query): Collection
    {
        return $this->search($query)->map(fn ($tag) => ProductTagDTO::fromModel($tag));
    }

    public function getTagNames(): Collection
    {
        return ProductTag::pluck('name');
    }

    public function getTagSlugs(): Collection
    {
        return ProductTag::pluck('slug');
    }

    public function getTagColors(): Collection
    {
        return ProductTag::whereNotNull('color')->pluck('color')->unique();
    }

    public function getTagIcons(): Collection
    {
        return ProductTag::whereNotNull('icon')->pluck('icon')->unique();
    }

    public function validateTag(array $data): bool
    {
        $rules = ProductTagDTO::rules();
        $validator = validator($data, $rules);

        return ! $validator->fails();
    }

    public function isNameUnique(string $name, ?int $excludeId = null): bool
    {
        $query = ProductTag::where('name', $name);
        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        return ! $query->exists();
    }

    public function isSlugUnique(string $slug, ?int $excludeId = null): bool
    {
        $query = ProductTag::where('slug', $slug);
        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        return ! $query->exists();
    }

    public function generateSlug(string $name): string
    {
        return Str::slug($name);
    }

    public function getTagUsage(int $tagId): int
    {
        $tag = $this->find($tagId);

        return $tag ? $tag->usage_count : 0;
    }

    public function getTagUsageByProduct(int $tagId, int $productId): int
    {
        return DB::table('product_tag_product')
            ->where('product_tag_id', $tagId)
            ->where('product_id', $productId)
            ->count();
    }

    public function getTagAnalytics(int $tagId): array
    {
        $tag = $this->find($tagId);
        if (! $tag) {
            return [];
        }

        return [
            'usage_count' => $tag->usage_count,
            'is_active' => $tag->is_active,
            'is_featured' => $tag->is_featured,
            'created_at' => $tag->created_at,
            'updated_at' => $tag->updated_at,
        ];
    }

    public function getTagAnalyticsByProduct(int $tagId): array
    {
        $tag = $this->find($tagId);
        if (! $tag) {
            return [];
        }

        $products = $tag->products;

        return [
            'total_products' => $products->count(),
            'products' => $products->pluck('id')->toArray(),
        ];
    }

    public function getTagTrends(int $tagId, string $period = 'month'): array
    {
        // Implementation for tag trends
        return [
            'tag_id' => $tagId,
            'period' => $period,
            'trends' => [],
        ];
    }

    public function getTagComparison(int $tagId1, int $tagId2): array
    {
        $tag1 = $this->find($tagId1);
        $tag2 = $this->find($tagId2);

        if (! $tag1 || ! $tag2) {
            return [];
        }

        return [
            'tag1' => ProductTagDTO::fromModel($tag1)->toArray(),
            'tag2' => ProductTagDTO::fromModel($tag2)->toArray(),
            'comparison' => [
                'usage_difference' => $tag1->usage_count - $tag2->usage_count,
                'featured_difference' => $tag1->is_featured - $tag2->is_featured,
            ],
        ];
    }

    public function getTagRecommendations(int $productId): array
    {
        // Implementation for tag recommendations
        return [
            'product_id' => $productId,
            'recommendations' => [],
        ];
    }

    public function getTagForecast(int $tagId, string $period = 'month'): array
    {
        // Implementation for tag forecast
        return [
            'tag_id' => $tagId,
            'period' => $period,
            'forecast' => [],
        ];
    }

    public function getTagPerformance(int $tagId): array
    {
        $tag = $this->find($tagId);
        if (! $tag) {
            return [];
        }

        return [
            'usage_count' => $tag->usage_count,
            'is_active' => $tag->is_active,
            'is_featured' => $tag->is_featured,
            'performance_score' => $this->calculatePerformanceScore($tag),
        ];
    }

    public function getTagROI(int $tagId): float
    {
        // Implementation for ROI calculation
        return 0.0;
    }

    public function getTagConversionRate(int $tagId): float
    {
        // Implementation for conversion rate calculation
        return 0.0;
    }

    public function getTagAverageOrderValue(int $tagId): float
    {
        // Implementation for average order value calculation
        return 0.0;
    }

    public function getTagCustomerRetention(int $tagId): float
    {
        // Implementation for customer retention calculation
        return 0.0;
    }

    public function bulkCreate(array $tagData): Collection
    {
        $tags = collect();
        foreach ($tagData as $data) {
            $tags->push($this->create($data));
        }

        return $tags;
    }

    public function bulkUpdate(array $tagData): bool
    {
        foreach ($tagData as $data) {
            if (isset($data['id'])) {
                $tag = $this->find($data['id']);
                if ($tag) {
                    unset($data['id']);
                    $this->update($tag, $data);
                }
            }
        }

        return true;
    }

    public function bulkDelete(array $tagIds): bool
    {
        return ProductTag::whereIn('id', $tagIds)->delete() > 0;
    }

    public function importTags(array $tagData): bool
    {
        return $this->bulkCreate($tagData)->count() > 0;
    }

    public function exportTags(): array
    {
        return $this->all()->map(fn ($tag) => ProductTagDTO::fromModel($tag)->toArray())->toArray();
    }

    public function syncTags(int $productId, array $tagIds): bool
    {
        $product = \Fereydooni\Shopping\app\Models\Product::find($productId);
        if (! $product) {
            return false;
        }

        $product->tags()->sync($tagIds);

        return true;
    }

    public function mergeTags(int $tagId1, int $tagId2): bool
    {
        $tag1 = $this->find($tagId1);
        $tag2 = $this->find($tagId2);

        if (! $tag1 || ! $tag2) {
            return false;
        }

        // Merge products from tag2 to tag1
        $tag1->products()->attach($tag2->products->pluck('id')->toArray());

        // Update usage count
        $tag1->update(['usage_count' => $tag1->usage_count + $tag2->usage_count]);

        // Delete tag2
        $tag2->delete();

        return true;
    }

    public function splitTags(int $tagId, array $newTagNames): bool
    {
        $tag = $this->find($tagId);
        if (! $tag) {
            return false;
        }

        $products = $tag->products;
        $tag->delete();

        foreach ($newTagNames as $name) {
            $newTag = $this->create([
                'name' => $name,
                'slug' => $this->generateSlug($name),
            ]);
            $newTag->products()->attach($products->pluck('id')->toArray());
        }

        return true;
    }

    public function getTagSuggestions(string $query): Collection
    {
        return $this->search($query)->take(5);
    }

    public function getTagAutocomplete(string $query): Collection
    {
        return ProductTag::where('name', 'like', "%{$query}%")
            ->orWhere('slug', 'like', "%{$query}%")
            ->limit(10)
            ->get();
    }

    public function getTagRelated(int $tagId): Collection
    {
        $tag = $this->find($tagId);
        if (! $tag) {
            return collect();
        }

        $productIds = $tag->products->pluck('id');

        return ProductTag::whereHas('products', function ($query) use ($productIds) {
            $query->whereIn('product_id', $productIds);
        })->where('id', '!=', $tagId)->get();
    }

    public function getTagSynonyms(int $tagId): Collection
    {
        // Implementation for tag synonyms
        return collect();
    }

    public function getTagHierarchy(int $tagId): array
    {
        // Implementation for tag hierarchy
        return [];
    }

    public function getTagTree(): array
    {
        // Implementation for tag tree
        return [];
    }

    public function getTagCloud(): array
    {
        return ProductTag::select('name', 'usage_count')
            ->orderBy('usage_count', 'desc')
            ->get()
            ->toArray();
    }

    public function getTagStats(): array
    {
        return [
            'total_tags' => $this->getTagCount(),
            'active_tags' => $this->getActiveTagCount(),
            'featured_tags' => $this->getFeaturedTagCount(),
            'popular_tags' => $this->getPopularTagCount(),
        ];
    }

    private function calculatePerformanceScore(ProductTag $tag): float
    {
        $score = 0;
        $score += $tag->usage_count * 10;
        $score += $tag->is_active ? 50 : 0;
        $score += $tag->is_featured ? 100 : 0;

        return $score;
    }
}
