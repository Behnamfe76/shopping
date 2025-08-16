<?php

namespace Fereydooni\Shopping\app\Repositories\Interfaces;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Fereydooni\Shopping\app\Models\ProductTag;
use Fereydooni\Shopping\app\DTOs\ProductTagDTO;

interface ProductTagRepositoryInterface
{
    // Basic CRUD operations
    public function all(): Collection;
    public function paginate(int $perPage = 15): LengthAwarePaginator;
    public function find(int $id): ?ProductTag;
    public function findDTO(int $id): ?ProductTagDTO;
    public function findBySlug(string $slug): ?ProductTag;
    public function findBySlugDTO(string $slug): ?ProductTagDTO;
    public function findByName(string $name): ?ProductTag;
    public function findByNameDTO(string $name): ?ProductTagDTO;

    // Status-based queries
    public function findActive(): Collection;
    public function findActiveDTO(): Collection;
    public function findFeatured(): Collection;
    public function findFeaturedDTO(): Collection;

    // Usage-based queries
    public function findByUsageCount(int $minCount): Collection;
    public function findByUsageCountDTO(int $minCount): Collection;
    public function findPopular(int $limit = 10): Collection;
    public function findPopularDTO(int $limit = 10): Collection;
    public function findRecent(int $limit = 10): Collection;
    public function findRecentDTO(int $limit = 10): Collection;

    // Attribute-based queries
    public function findByColor(string $color): Collection;
    public function findByColorDTO(string $color): Collection;
    public function findByIcon(string $icon): Collection;
    public function findByIconDTO(string $icon): Collection;

    // CRUD operations
    public function create(array $data): ProductTag;
    public function createAndReturnDTO(array $data): ProductTagDTO;
    public function update(ProductTag $tag, array $data): bool;
    public function delete(ProductTag $tag): bool;

    // Status management
    public function toggleActive(ProductTag $tag): bool;
    public function toggleFeatured(ProductTag $tag): bool;

    // Usage management
    public function incrementUsage(ProductTag $tag): bool;
    public function decrementUsage(ProductTag $tag): bool;

    // Count methods
    public function getTagCount(): int;
    public function getActiveTagCount(): int;
    public function getFeaturedTagCount(): int;
    public function getPopularTagCount(): int;

    // Search functionality
    public function search(string $query): Collection;
    public function searchDTO(string $query): Collection;

    // List methods
    public function getTagNames(): Collection;
    public function getTagSlugs(): Collection;
    public function getTagColors(): Collection;
    public function getTagIcons(): Collection;

    // Validation methods
    public function validateTag(array $data): bool;
    public function isNameUnique(string $name, ?int $excludeId = null): bool;
    public function isSlugUnique(string $slug, ?int $excludeId = null): bool;
    public function generateSlug(string $name): string;

    // Usage tracking
    public function getTagUsage(int $tagId): int;
    public function getTagUsageByProduct(int $tagId, int $productId): int;

    // Analytics methods
    public function getTagAnalytics(int $tagId): array;
    public function getTagAnalyticsByProduct(int $tagId): array;
    public function getTagTrends(int $tagId, string $period = 'month'): array;
    public function getTagComparison(int $tagId1, int $tagId2): array;
    public function getTagRecommendations(int $productId): array;
    public function getTagForecast(int $tagId, string $period = 'month'): array;
    public function getTagPerformance(int $tagId): array;
    public function getTagROI(int $tagId): float;
    public function getTagConversionRate(int $tagId): float;
    public function getTagAverageOrderValue(int $tagId): float;
    public function getTagCustomerRetention(int $tagId): float;

    // Bulk operations
    public function bulkCreate(array $tagData): Collection;
    public function bulkUpdate(array $tagData): bool;
    public function bulkDelete(array $tagIds): bool;

    // Import/Export
    public function importTags(array $tagData): bool;
    public function exportTags(): array;

    // Tag management
    public function syncTags(int $productId, array $tagIds): bool;
    public function mergeTags(int $tagId1, int $tagId2): bool;
    public function splitTags(int $tagId, array $newTagNames): bool;

    // Suggestions and autocomplete
    public function getTagSuggestions(string $query): Collection;
    public function getTagAutocomplete(string $query): Collection;

    // Relationships
    public function getTagRelated(int $tagId): Collection;
    public function getTagSynonyms(int $tagId): Collection;
    public function getTagHierarchy(int $tagId): array;
    public function getTagTree(): array;
    public function getTagCloud(): array;
    public function getTagStats(): array;
}
