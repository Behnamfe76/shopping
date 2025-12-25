<?php

namespace Fereydooni\Shopping\app\Repositories\Interfaces;

use Fereydooni\Shopping\app\DTOs\CategoryDTO;
use Fereydooni\Shopping\app\Models\Category;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\CursorPaginator;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;

interface CategoryRepositoryInterface
{
    /**
     * Get all categories
     */
    public function all(): Collection;

    /**
     * Get paginated categories (LengthAwarePaginator)
     */
    public function paginate(int $perPage = 15): LengthAwarePaginator;

    /**
     * Get simple paginated categories
     */
    public function simplePaginate(int $perPage = 15): Paginator;

    /**
     * Get cursor paginated categories
     */
    public function cursorPaginate(int $perPage = 15, ?string $cursor = null): CursorPaginator;

    /**
     * Get cursor paginated categories
     */
    public function cursorAll(int $perPage = 15, ?string $cursor = null): CursorPaginator;

    /**
     * Find category by ID
     */
    public function find(int $id): ?Category;

    /**
     * Find category by ID and return DTO
     */
    public function findDTO(int $id): ?CategoryDTO;

    /**
     * Find category by slug
     */
    public function findBySlug(string $slug): ?Category;

    /**
     * Find category by slug and return DTO
     */
    public function findBySlugDTO(string $slug): ?CategoryDTO;

    /**
     * Create a new category
     */
    public function create(array $data): Category;

    /**
     * Create a new category and return DTO
     */
    public function createAndReturnDTO(array $data): CategoryDTO;

    /**
     * Update category
     */
    public function update(Category $category, array $data): bool;

    /**
     * Update category and return DTO
     */
    public function updateAndReturnDTO(Category $category, array $data): ?CategoryDTO;

    /**
     * Delete category
     */
    public function delete(Category $category): bool;

    /**
     * Get root categories (no parent)
     */
    public function getRootCategories(): Collection;

    /**
     * Get categories with children
     */
    public function getWithChildren(): Collection;

    /**
     * Get category tree (hierarchical)
     */
    public function getTree(): Collection;

    /**
     * Get categories by parent ID
     */
    public function getByParentId(?int $parentId): Collection;

    /**
     * Get category with all children
     */
    public function getWithAllChildren(int $categoryId): ?Category;

    /**
     * Get category with all parents
     */
    public function getWithAllParents(int $categoryId): ?Category;

    /**
     * Search categories by name
     */
    public function search(string $query): Collection;

    /**
     * Get categories with products count
     */
    public function getWithProductsCount(): Collection;

    /**
     * Check if category has children
     */
    public function hasChildren(Category $category): bool;

    /**
     * Check if category has products
     */
    public function hasProducts(Category $category): bool;

    /**
     * Move category to new parent
     */
    public function moveToParent(Category $category, ?int $newParentId): bool;

    /**
     * Get category path (breadcrumb)
     */
    public function getPath(Category $category): Collection;

    /**
     * Get category depth level
     */
    public function getDepth(Category $category): int;

    /**
     * Get categories by depth level
     */
    public function getByDepth(int $depth): Collection;

    /**
     * Get acategory's descendants
     */
    public function getDescendants(int $categoryId): \Illuminate\Support\Collection;

    /**
     * Get acategory's ansestors
     */
    public function getAncestors(int $categoryId): \Illuminate\Support\Collection;
}
