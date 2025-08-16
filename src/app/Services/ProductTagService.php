<?php

namespace Fereydooni\Shopping\app\Services;

use Fereydooni\Shopping\app\Models\ProductTag;
use Fereydooni\Shopping\app\Repositories\Interfaces\ProductTagRepositoryInterface;
use Fereydooni\Shopping\app\DTOs\ProductTagDTO;
use Fereydooni\Shopping\app\Traits\HasCrudOperations;
use Fereydooni\Shopping\app\Traits\HasStatusToggle;
use Fereydooni\Shopping\app\Traits\HasSlugGeneration;
use Fereydooni\Shopping\app\Traits\HasSearchOperations;
use Fereydooni\Shopping\app\Traits\HasBulkOperations;
use Fereydooni\Shopping\app\Traits\HasAnalyticsOperations;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Str;

class ProductTagService
{
    use HasCrudOperations, HasStatusToggle, HasSlugGeneration, HasSearchOperations, HasBulkOperations, HasAnalyticsOperations;

    public function __construct(
        protected ProductTagRepositoryInterface $repository
    ) {
        $this->model = ProductTag::class;
        $this->dtoClass = ProductTagDTO::class;
    }

    // Basic CRUD Operations (inherited from HasCrudOperations)
    public function all(): Collection
    {
        return $this->repository->all();
    }

    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        return $this->repository->paginate($perPage);
    }

    public function find(int $id): ?ProductTag
    {
        return $this->repository->find($id);
    }

    public function findDTO(int $id): ?ProductTagDTO
    {
        return $this->repository->findDTO($id);
    }

    public function findBySlug(string $slug): ?ProductTag
    {
        return $this->repository->findBySlug($slug);
    }

    public function findBySlugDTO(string $slug): ?ProductTagDTO
    {
        return $this->repository->findBySlugDTO($slug);
    }

    public function findByName(string $name): ?ProductTag
    {
        return $this->repository->findByName($name);
    }

    public function findByNameDTO(string $name): ?ProductTagDTO
    {
        return $this->repository->findByNameDTO($name);
    }

    // Override create method to handle tag-specific logic
    public function create(array $data): ProductTag
    {
        // Generate slug if not provided
        if (!isset($data['slug']) && isset($data['name'])) {
            $data['slug'] = $this->generateSlug($data['name']);
        }

        $this->validateData($data);
        return $this->repository->create($data);
    }

    public function createAndReturnDTO(array $data): ProductTagDTO
    {
        // Generate slug if not provided
        if (!isset($data['slug']) && isset($data['name'])) {
            $data['slug'] = $this->generateSlug($data['name']);
        }

        $this->validateData($data);
        return $this->repository->createAndReturnDTO($data);
    }

    public function update(ProductTag $tag, array $data): bool
    {
        $this->validateData($data, $tag->id);
        return $this->repository->update($tag, $data);
    }

    public function delete(ProductTag $tag): bool
    {
        return $this->repository->delete($tag);
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

    public function findFeatured(): Collection
    {
        return $this->repository->findFeatured();
    }

    public function findFeaturedDTO(): Collection
    {
        return $this->repository->findFeaturedDTO();
    }

    // Usage-based queries
    public function findByUsageCount(int $minCount): Collection
    {
        return $this->repository->findByUsageCount($minCount);
    }

    public function findByUsageCountDTO(int $minCount): Collection
    {
        return $this->repository->findByUsageCountDTO($minCount);
    }

    public function findPopular(int $limit = 10): Collection
    {
        return $this->repository->findPopular($limit);
    }

    public function findPopularDTO(int $limit = 10): Collection
    {
        return $this->repository->findPopularDTO($limit);
    }

    public function findRecent(int $limit = 10): Collection
    {
        return $this->repository->findRecent($limit);
    }

    public function findRecentDTO(int $limit = 10): Collection
    {
        return $this->repository->findRecentDTO($limit);
    }

    // Attribute-based queries
    public function findByColor(string $color): Collection
    {
        return $this->repository->findByColor($color);
    }

    public function findByColorDTO(string $color): Collection
    {
        return $this->repository->findByColorDTO($color);
    }

    public function findByIcon(string $icon): Collection
    {
        return $this->repository->findByIcon($icon);
    }

    public function findByIconDTO(string $icon): Collection
    {
        return $this->repository->findByIconDTO($icon);
    }

    // Status management (inherited from HasStatusToggle)
    public function toggleActive(ProductTag $tag): bool
    {
        return $this->repository->toggleActive($tag);
    }

    public function toggleFeatured(ProductTag $tag): bool
    {
        return $this->repository->toggleFeatured($tag);
    }

    // Usage management
    public function incrementUsage(ProductTag $tag): bool
    {
        return $this->repository->incrementUsage($tag);
    }

    public function decrementUsage(ProductTag $tag): bool
    {
        return $this->repository->decrementUsage($tag);
    }

    // Count methods
    public function getTagCount(): int
    {
        return $this->repository->getTagCount();
    }

    public function getActiveTagCount(): int
    {
        return $this->repository->getActiveTagCount();
    }

    public function getFeaturedTagCount(): int
    {
        return $this->repository->getFeaturedTagCount();
    }

    public function getPopularTagCount(): int
    {
        return $this->repository->getPopularTagCount();
    }

    // Search functionality (inherited from HasSearchOperations)
    public function search(string $query): Collection
    {
        return $this->repository->search($query);
    }

    public function searchDTO(string $query): Collection
    {
        return $this->repository->searchDTO($query);
    }

    // List methods
    public function getTagNames(): Collection
    {
        return $this->repository->getTagNames();
    }

    public function getTagSlugs(): Collection
    {
        return $this->repository->getTagSlugs();
    }

    public function getTagColors(): Collection
    {
        return $this->repository->getTagColors();
    }

    public function getTagIcons(): Collection
    {
        return $this->repository->getTagIcons();
    }

    // Validation methods
    public function validateTag(array $data): bool
    {
        return $this->repository->validateTag($data);
    }

    public function isNameUnique(string $name, ?int $excludeId = null): bool
    {
        return $this->repository->isNameUnique($name, $excludeId);
    }

    public function isSlugUnique(string $slug, ?int $excludeId = null): bool
    {
        return $this->repository->isSlugUnique($slug, $excludeId);
    }

    public function generateSlug(string $name): string
    {
        return $this->repository->generateSlug($name);
    }

    // Usage tracking
    public function getTagUsage(int $tagId): int
    {
        return $this->repository->getTagUsage($tagId);
    }

    public function getTagUsageByProduct(int $tagId, int $productId): int
    {
        return $this->repository->getTagUsageByProduct($tagId, $productId);
    }

    // Analytics methods (inherited from HasAnalyticsOperations)
    public function getTagAnalytics(int $tagId): array
    {
        return $this->repository->getTagAnalytics($tagId);
    }

    public function getTagAnalyticsByProduct(int $tagId): array
    {
        return $this->repository->getTagAnalyticsByProduct($tagId);
    }

    public function getTagTrends(int $tagId, string $period = 'month'): array
    {
        return $this->repository->getTagTrends($tagId, $period);
    }

    public function getTagComparison(int $tagId1, int $tagId2): array
    {
        return $this->repository->getTagComparison($tagId1, $tagId2);
    }

    public function getTagRecommendations(int $productId): array
    {
        return $this->repository->getTagRecommendations($productId);
    }

    public function getTagForecast(int $tagId, string $period = 'month'): array
    {
        return $this->repository->getTagForecast($tagId, $period);
    }

    public function getTagPerformance(int $tagId): array
    {
        return $this->repository->getTagPerformance($tagId);
    }

    public function getTagROI(int $tagId): float
    {
        return $this->repository->getTagROI($tagId);
    }

    public function getTagConversionRate(int $tagId): float
    {
        return $this->repository->getTagConversionRate($tagId);
    }

    public function getTagAverageOrderValue(int $tagId): float
    {
        return $this->repository->getTagAverageOrderValue($tagId);
    }

    public function getTagCustomerRetention(int $tagId): float
    {
        return $this->repository->getTagCustomerRetention($tagId);
    }

    // Bulk operations (inherited from HasBulkOperations)
    public function bulkCreate(array $tagData): Collection
    {
        return $this->repository->bulkCreate($tagData);
    }

    public function bulkUpdate(array $tagData): bool
    {
        return $this->repository->bulkUpdate($tagData);
    }

    public function bulkDelete(array $tagIds): bool
    {
        return $this->repository->bulkDelete($tagIds);
    }

    // Import/Export
    public function importTags(array $tagData): bool
    {
        return $this->repository->importTags($tagData);
    }

    public function exportTags(): array
    {
        return $this->repository->exportTags();
    }

    // Tag management
    public function syncTags(int $productId, array $tagIds): bool
    {
        return $this->repository->syncTags($productId, $tagIds);
    }

    public function mergeTags(int $tagId1, int $tagId2): bool
    {
        return $this->repository->mergeTags($tagId1, $tagId2);
    }

    public function splitTags(int $tagId, array $newTagNames): bool
    {
        return $this->repository->splitTags($tagId, $newTagNames);
    }

    // Suggestions and autocomplete
    public function getTagSuggestions(string $query): Collection
    {
        return $this->repository->getTagSuggestions($query);
    }

    public function getTagAutocomplete(string $query): Collection
    {
        return $this->repository->getTagAutocomplete($query);
    }

    // Relationships
    public function getTagRelated(int $tagId): Collection
    {
        return $this->repository->getTagRelated($tagId);
    }

    public function getTagSynonyms(int $tagId): Collection
    {
        return $this->repository->getTagSynonyms($tagId);
    }

    public function getTagHierarchy(int $tagId): array
    {
        return $this->repository->getTagHierarchy($tagId);
    }

    public function getTagTree(): array
    {
        return $this->repository->getTagTree();
    }

    public function getTagCloud(): array
    {
        return $this->repository->getTagCloud();
    }

    public function getTagStats(): array
    {
        return $this->repository->getTagStats();
    }

    // Performance optimization methods
    public function optimizeTagQueries(): void
    {
        // Implementation for query optimization
    }

    public function cacheTagData(): void
    {
        // Implementation for caching
    }

    public function indexTagData(): void
    {
        // Implementation for indexing
    }
}
