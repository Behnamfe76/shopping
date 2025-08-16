<?php

namespace Fereydooni\Shopping\app\Services;

use Fereydooni\Shopping\app\Repositories\Interfaces\BrandRepositoryInterface;
use Fereydooni\Shopping\app\Models\Brand;
use Fereydooni\Shopping\app\DTOs\BrandDTO;
use Fereydooni\Shopping\app\Traits\HasCrudOperations;
use Fereydooni\Shopping\app\Traits\HasStatusToggle;
use Fereydooni\Shopping\app\Traits\HasSearchOperations;
use Fereydooni\Shopping\app\Traits\HasSlugGeneration;
use Fereydooni\Shopping\app\Traits\HasMediaOperations;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Pagination\CursorPaginator;
use Illuminate\Http\UploadedFile;

class BrandService
{
    use HasCrudOperations, HasStatusToggle, HasSearchOperations, HasSlugGeneration, HasMediaOperations;

    protected BrandRepositoryInterface $repository;

    public function __construct(BrandRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Get all brands
     */
    public function all(): Collection
    {
        return $this->repository->all();
    }

    /**
     * Get all brands as DTOs
     */
    public function allDTO(): Collection
    {
        return $this->all()->map(fn($brand) => BrandDTO::fromModel($brand));
    }

    /**
     * Get paginated brands
     */
    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        return $this->repository->paginate($perPage);
    }

    /**
     * Get simple paginated brands
     */
    public function simplePaginate(int $perPage = 15): Paginator
    {
        return $this->repository->simplePaginate($perPage);
    }

    /**
     * Get cursor paginated brands
     */
    public function cursorPaginate(int $perPage = 15, string $cursor = null): CursorPaginator
    {
        return $this->repository->cursorPaginate($perPage, $cursor);
    }

    /**
     * Find brand by ID
     */
    public function find(int $id): ?Brand
    {
        return $this->repository->find($id);
    }

    /**
     * Find brand by ID and return DTO
     */
    public function findDTO(int $id): ?BrandDTO
    {
        return $this->repository->findDTO($id);
    }

    /**
     * Find brand by slug
     */
    public function findBySlug(string $slug): ?Brand
    {
        return $this->repository->findBySlug($slug);
    }

    /**
     * Find brand by slug and return DTO
     */
    public function findBySlugDTO(string $slug): ?BrandDTO
    {
        return $this->repository->findBySlugDTO($slug);
    }

    /**
     * Create brand
     */
    public function create(array $data): Brand
    {
        return $this->repository->create($data);
    }

    /**
     * Create brand and return DTO
     */
    public function createDTO(array $data): BrandDTO
    {
        return $this->repository->createAndReturnDTO($data);
    }

    /**
     * Update brand
     */
    public function update(Brand $brand, array $data): bool
    {
        return $this->repository->update($brand, $data);
    }

    /**
     * Update brand and return DTO
     */
    public function updateDTO(Brand $brand, array $data): ?BrandDTO
    {
        return $this->repository->updateAndReturnDTO($brand, $data);
    }

    /**
     * Delete brand
     */
    public function delete(Brand $brand): bool
    {
        return $this->repository->delete($brand);
    }

    /**
     * Get active brands
     */
    public function getActive(): Collection
    {
        return $this->repository->findActive();
    }

    /**
     * Get active brands as DTOs
     */
    public function getActiveDTO(): Collection
    {
        return $this->repository->findActiveDTO();
    }

    /**
     * Get featured brands
     */
    public function getFeatured(): Collection
    {
        return $this->repository->findFeatured();
    }

    /**
     * Get featured brands as DTOs
     */
    public function getFeaturedDTO(): Collection
    {
        return $this->repository->findFeaturedDTO();
    }

    /**
     * Get popular brands
     */
    public function getPopular(int $limit = 10): Collection
    {
        return $this->repository->getPopularBrands($limit);
    }

    /**
     * Get popular brands as DTOs
     */
    public function getPopularDTO(int $limit = 10): Collection
    {
        return $this->repository->getPopularBrandsDTO($limit);
    }

    /**
     * Get brands with products
     */
    public function getWithProducts(): Collection
    {
        return $this->repository->getBrandsWithProducts();
    }

    /**
     * Get brands with products as DTOs
     */
    public function getWithProductsDTO(): Collection
    {
        return $this->repository->getBrandsWithProductsDTO();
    }

    /**
     * Search brands
     */
    public function search(string $query): Collection
    {
        return $this->repository->search($query);
    }

    /**
     * Search brands and return DTOs
     */
    public function searchDTO(string $query): Collection
    {
        return $this->repository->searchDTO($query);
    }

    /**
     * Get brands by first letter
     */
    public function getByFirstLetter(string $letter): Collection
    {
        return $this->repository->getByFirstLetter($letter);
    }

    /**
     * Get brands by first letter as DTOs
     */
    public function getByFirstLetterDTO(string $letter): Collection
    {
        return $this->repository->getByFirstLetterDTO($letter);
    }

    /**
     * Get brand count
     */
    public function getCount(): int
    {
        return $this->repository->getBrandCount();
    }

    /**
     * Get active brand count
     */
    public function getActiveCount(): int
    {
        return $this->repository->getActiveBrandCount();
    }

    /**
     * Get featured brand count
     */
    public function getFeaturedCount(): int
    {
        return $this->repository->getFeaturedBrandCount();
    }

    /**
     * Validate brand data
     */
    public function validate(array $data): bool
    {
        return $this->repository->validateBrand($data);
    }

    /**
     * Upload brand logo
     */
    public function uploadLogo(Brand $brand, UploadedFile $file)
    {
        return $this->uploadMedia($brand, $file, 'logo');
    }

    /**
     * Upload brand banner
     */
    public function uploadBanner(Brand $brand, UploadedFile $file)
    {
        return $this->uploadMedia($brand, $file, 'banner');
    }

    /**
     * Get brand logo URL
     */
    public function getLogoUrl(Brand $brand): ?string
    {
        return $this->getLogoUrl($brand);
    }

    /**
     * Get brand banner URL
     */
    public function getBannerUrl(Brand $brand): ?string
    {
        return $this->getBannerUrl($brand);
    }

    /**
     * Delete brand logo
     */
    public function deleteLogo(Brand $brand): bool
    {
        return $this->deleteAllMedia($brand, 'logo');
    }

    /**
     * Delete brand banner
     */
    public function deleteBanner(Brand $brand): bool
    {
        return $this->deleteAllMedia($brand, 'banner');
    }
}
