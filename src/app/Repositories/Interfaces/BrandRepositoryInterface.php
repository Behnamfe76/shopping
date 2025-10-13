<?php

namespace Fereydooni\Shopping\app\Repositories\Interfaces;

use Fereydooni\Shopping\app\Models\Brand;
use Fereydooni\Shopping\app\DTOs\BrandDTO;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\CursorPaginator;
use Illuminate\Pagination\Paginator;

interface BrandRepositoryInterface
{
    /**
     * Get all brands
     */
    public function all(): Collection;

    /**
     * Get paginated brands (LengthAwarePaginator)
     */
    public function paginate(int $perPage = 15): LengthAwarePaginator;

    /**
     * Get simple paginated brands
     */
    public function simplePaginate(int $perPage = 15): Paginator;

    /**
     * Get cursor paginated brands
     */
    public function cursorPaginate(int $perPage = 15, string $cursor = null): CursorPaginator;

    /**
     * Get cursor paginated categories
     */
    public function cursorAll(int $perPage = 15, ?string $cursor = null): CursorPaginator;

    /**
     * Find brand by ID
     */
    public function find(int $id): ?Brand;

    /**
     * Find brand by ID and return DTO
     */
    public function findDTO(int $id): ?BrandDTO;

    /**
     * Find brand by slug
     */
    public function findBySlug(string $slug): ?Brand;

    /**
     * Find brand by slug and return DTO
     */
    public function findBySlugDTO(string $slug): ?BrandDTO;

    /**
     * Find active brands
     */
    public function findActive(): Collection;

    /**
     * Find active brands and return DTOs
     */
    public function findActiveDTO(): Collection;

    /**
     * Find featured brands
     */
    public function findFeatured(): Collection;

    /**
     * Find featured brands and return DTOs
     */
    public function findFeaturedDTO(): Collection;

    /**
     * Create a new brand
     */
    public function create(array $data): Brand;

    /**
     * Create a new brand and return DTO
     */
    public function createAndReturnDTO(array $data): BrandDTO;

    /**
     * Update brand
     */
    public function update(Brand $brand, array $data): bool;

    /**
     * Update brand and return DTO
     */
    public function updateAndReturnDTO(Brand $brand, array $data): ?BrandDTO;

    /**
     * Delete brand
     */
    public function delete(Brand $brand): bool;

    /**
     * Toggle active status
     */
    public function toggleActive(Brand $brand): bool;

    /**
     * Toggle featured status
     */
    public function toggleFeatured(Brand $brand): bool;

    /**
     * Get brand count
     */
    public function getBrandCount(): int;

    /**
     * Get active brand count
     */
    public function getActiveBrandCount(): int;

    /**
     * Get featured brand count
     */
    public function getFeaturedBrandCount(): int;

    /**
     * Search brands
     */
    public function search(string $query): Collection;

    /**
     * Search brands and return DTOs
     */
    public function searchDTO(string $query): Collection;

    /**
     * Get brands by first letter
     */
    public function getByFirstLetter(string $letter): Collection;

    /**
     * Get brands by first letter and return DTOs
     */
    public function getByFirstLetterDTO(string $letter): Collection;

    /**
     * Get popular brands
     */
    public function getPopularBrands(int $limit = 10): Collection;

    /**
     * Get popular brands and return DTOs
     */
    public function getPopularBrandsDTO(int $limit = 10): Collection;

    /**
     * Get brands with products
     */
    public function getBrandsWithProducts(): Collection;

    /**
     * Get brands with products and return DTOs
     */
    public function getBrandsWithProductsDTO(): Collection;

    /**
     * Validate brand data
     */
    public function validateBrand(array $data): bool;

    /**
     * Generate slug
     */
    public function generateSlug(string $name): string;

    /**
     * Check if slug is unique
     */
    public function isSlugUnique(string $slug, ?int $excludeId = null): bool;

    /**
     * Get model instance
     */
    public function getModel(): Brand;
}
