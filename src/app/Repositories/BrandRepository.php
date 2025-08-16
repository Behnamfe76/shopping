<?php

namespace Fereydooni\Shopping\app\Repositories;

use Fereydooni\Shopping\app\Repositories\Interfaces\BrandRepositoryInterface;
use Fereydooni\Shopping\app\Models\Brand;
use Fereydooni\Shopping\app\DTOs\BrandDTO;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\CursorPaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class BrandRepository implements BrandRepositoryInterface
{
    protected Brand $model;

    public function __construct(Brand $model)
    {
        $this->model = $model;
    }

    /**
     * Get all brands
     */
    public function all(): Collection
    {
        return $this->model->ordered()->get();
    }

    /**
     * Get paginated brands (LengthAwarePaginator)
     */
    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        return $this->model->ordered()->paginate($perPage);
    }

    /**
     * Get simple paginated brands
     */
    public function simplePaginate(int $perPage = 15): Paginator
    {
        return $this->model->ordered()->simplePaginate($perPage);
    }

    /**
     * Get cursor paginated brands
     */
    public function cursorPaginate(int $perPage = 15, string $cursor = null): CursorPaginator
    {
        return $this->model->ordered()->cursorPaginate($perPage, ['*'], 'cursor', $cursor);
    }

    /**
     * Find brand by ID
     */
    public function find(int $id): ?Brand
    {
        return $this->model->find($id);
    }

    /**
     * Find brand by ID and return DTO
     */
    public function findDTO(int $id): ?BrandDTO
    {
        $brand = $this->find($id);
        return $brand ? BrandDTO::fromModel($brand) : null;
    }

    /**
     * Find brand by slug
     */
    public function findBySlug(string $slug): ?Brand
    {
        return $this->model->where('slug', $slug)->first();
    }

    /**
     * Find brand by slug and return DTO
     */
    public function findBySlugDTO(string $slug): ?BrandDTO
    {
        $brand = $this->findBySlug($slug);
        return $brand ? BrandDTO::fromModel($brand) : null;
    }

    /**
     * Find active brands
     */
    public function findActive(): Collection
    {
        return $this->model->active()->ordered()->get();
    }

    /**
     * Find active brands and return DTOs
     */
    public function findActiveDTO(): Collection
    {
        return $this->findActive()->map(fn($brand) => BrandDTO::fromModel($brand));
    }

    /**
     * Find featured brands
     */
    public function findFeatured(): Collection
    {
        return $this->model->featured()->ordered()->get();
    }

    /**
     * Find featured brands and return DTOs
     */
    public function findFeaturedDTO(): Collection
    {
        return $this->findFeatured()->map(fn($brand) => BrandDTO::fromModel($brand));
    }

    /**
     * Create a new brand
     */
    public function create(array $data): Brand
    {
        // Generate slug if not provided
        if (!isset($data['slug']) || empty($data['slug'])) {
            $data['slug'] = $this->generateSlug($data['name']);
        }

        return $this->model->create($data);
    }

    /**
     * Create a new brand and return DTO
     */
    public function createAndReturnDTO(array $data): BrandDTO
    {
        $brand = $this->create($data);
        return BrandDTO::fromModel($brand);
    }

    /**
     * Update brand
     */
    public function update(Brand $brand, array $data): bool
    {
        // Generate new slug if name changed and slug is not explicitly provided
        if (isset($data['name']) && $data['name'] !== $brand->name && !isset($data['slug'])) {
            $data['slug'] = $this->generateSlug($data['name'], $brand->id);
        }

        return $brand->update($data);
    }

    /**
     * Update brand and return DTO
     */
    public function updateAndReturnDTO(Brand $brand, array $data): ?BrandDTO
    {
        $updated = $this->update($brand, $data);
        return $updated ? BrandDTO::fromModel($brand->fresh()) : null;
    }

    /**
     * Delete brand
     */
    public function delete(Brand $brand): bool
    {
        return $brand->delete();
    }

    /**
     * Toggle active status
     */
    public function toggleActive(Brand $brand): bool
    {
        return $brand->update(['is_active' => !$brand->is_active]);
    }

    /**
     * Toggle featured status
     */
    public function toggleFeatured(Brand $brand): bool
    {
        return $brand->update(['is_featured' => !$brand->is_featured]);
    }

    /**
     * Get brand count
     */
    public function getBrandCount(): int
    {
        return $this->model->count();
    }

    /**
     * Get active brand count
     */
    public function getActiveBrandCount(): int
    {
        return $this->model->active()->count();
    }

    /**
     * Get featured brand count
     */
    public function getFeaturedBrandCount(): int
    {
        return $this->model->featured()->count();
    }

    /**
     * Search brands
     */
    public function search(string $query): Collection
    {
        return $this->model->search($query)->ordered()->get();
    }

    /**
     * Search brands and return DTOs
     */
    public function searchDTO(string $query): Collection
    {
        return $this->search($query)->map(fn($brand) => BrandDTO::fromModel($brand));
    }

    /**
     * Get brands by first letter
     */
    public function getByFirstLetter(string $letter): Collection
    {
        return $this->model->byFirstLetter($letter)->ordered()->get();
    }

    /**
     * Get brands by first letter and return DTOs
     */
    public function getByFirstLetterDTO(string $letter): Collection
    {
        return $this->getByFirstLetter($letter)->map(fn($brand) => BrandDTO::fromModel($brand));
    }

    /**
     * Get popular brands
     */
    public function getPopularBrands(int $limit = 10): Collection
    {
        return $this->model->withCount('products')
            ->orderBy('products_count', 'desc')
            ->orderBy('name', 'asc')
            ->limit($limit)
            ->get();
    }

    /**
     * Get popular brands and return DTOs
     */
    public function getPopularBrandsDTO(int $limit = 10): Collection
    {
        return $this->getPopularBrands($limit)->map(fn($brand) => BrandDTO::fromModel($brand));
    }

    /**
     * Get brands with products
     */
    public function getBrandsWithProducts(): Collection
    {
        return $this->model->withCount('products')
            ->having('products_count', '>', 0)
            ->ordered()
            ->get();
    }

    /**
     * Get brands with products and return DTOs
     */
    public function getBrandsWithProductsDTO(): Collection
    {
        return $this->getBrandsWithProducts()->map(fn($brand) => BrandDTO::fromModel($brand));
    }

    /**
     * Validate brand data
     */
    public function validateBrand(array $data): bool
    {
        $validator = Validator::make($data, BrandDTO::rules(), BrandDTO::messages());

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        return true;
    }

    /**
     * Generate slug
     */
    public function generateSlug(string $name, ?int $excludeId = null): string
    {
        $baseSlug = Str::slug($name);
        $slug = $baseSlug;
        $counter = 1;

        while (!$this->isSlugUnique($slug, $excludeId)) {
            $slug = $baseSlug . '-' . $counter;
            $counter++;
        }

        return $slug;
    }

    /**
     * Check if slug is unique
     */
    public function isSlugUnique(string $slug, ?int $excludeId = null): bool
    {
        $query = $this->model->where('slug', $slug);

        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        return !$query->exists();
    }

    /**
     * Get model instance
     */
    public function getModel(): Brand
    {
        return $this->model;
    }
}
