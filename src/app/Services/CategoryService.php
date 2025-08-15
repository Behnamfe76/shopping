<?php

namespace Fereydooni\Shopping\app\Services;

use Fereydooni\Shopping\app\Models\Category;
use Fereydooni\Shopping\app\Repositories\Interfaces\CategoryRepositoryInterface;
use Fereydooni\Shopping\app\DTOs\CategoryDTO;
use Fereydooni\Shopping\app\Enums\CategoryStatus;
use Fereydooni\Shopping\app\Traits\HasCrudOperations;
use Fereydooni\Shopping\app\Traits\HasDefaultItem;
use Fereydooni\Shopping\app\Traits\HasSearchOperations;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\CursorPaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Str;

class CategoryService
{
    use HasCrudOperations, HasDefaultItem, HasSearchOperations;

    public function __construct(
        protected CategoryRepositoryInterface $repository
    ) {
        $this->model = Category::class;
        $this->dtoClass = CategoryDTO::class;
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

    public function simplePaginate(int $perPage = 15): Paginator
    {
        return $this->repository->simplePaginate($perPage);
    }

    public function cursorPaginate(int $perPage = 15, string $cursor = null): CursorPaginator
    {
        return $this->repository->cursorPaginate($perPage, $cursor);
    }

    public function find(int $id): ?Category
    {
        return $this->repository->find($id);
    }

    public function findDTO(int $id): ?CategoryDTO
    {
        return $this->repository->findDTO($id);
    }

    public function findBySlug(string $slug): ?Category
    {
        return $this->repository->findBySlug($slug);
    }

    public function findBySlugDTO(string $slug): ?CategoryDTO
    {
        return $this->repository->findBySlugDTO($slug);
    }

    // Override create method to handle category-specific logic
    public function create(array $data): Category
    {
        // Generate slug if not provided
        if (!isset($data['slug']) && isset($data['name'])) {
            $data['slug'] = $this->generateSlug($data['name']);
        }

        // Handle default category logic
        $this->handleDefaultItemLogic($data);

        $this->validateData($data);
        return $this->repository->create($data);
    }

    // Override createDTO method to handle category-specific logic
    public function createDTO(array $data): CategoryDTO
    {
        // Generate slug if not provided
        if (!isset($data['slug']) && isset($data['name'])) {
            $data['slug'] = $this->generateSlug($data['name']);
        }

        // Handle default category logic
        $this->handleDefaultItemLogic($data);

        $this->validateData($data);
        return $this->repository->createAndReturnDTO($data);
    }

    // Override update method to handle category-specific logic
    public function update(Category $category, array $data): bool
    {
        // Generate slug if name changed and slug not provided
        if (isset($data['name']) && !isset($data['slug'])) {
            $data['slug'] = $this->generateSlug($data['name'], $category->id);
        }

        // Handle default category logic
        $this->handleDefaultItemLogicUpdate($category, $data);

        $this->validateData($data, $category);
        return $this->repository->update($category, $data);
    }

    // Override updateDTO method to handle category-specific logic
    public function updateDTO(Category $category, array $data): ?CategoryDTO
    {
        // Generate slug if name changed and slug not provided
        if (isset($data['name']) && !isset($data['slug'])) {
            $data['slug'] = $this->generateSlug($data['name'], $category->id);
        }

        // Handle default category logic
        $this->handleDefaultItemLogicUpdate($category, $data);

        $this->validateData($data, $category);
        return $this->repository->updateAndReturnDTO($category, $data);
    }

    // Override delete method to handle category-specific logic
    public function delete(Category $category): bool
    {
        // Check if this is the last category of its type
        $this->handleLastCategoryDeletion($category);

        return $this->repository->delete($category);
    }

    // Hierarchical Operations
    public function getRootCategories(): Collection
    {
        return $this->repository->getRootCategories();
    }

    public function getRootCategoriesDTO(): Collection
    {
        return $this->repository->getRootCategoriesDTO();
    }

    public function getChildren(int $parentId): Collection
    {
        return $this->repository->getChildren($parentId);
    }

    public function getChildrenDTO(int $parentId): Collection
    {
        return $this->repository->getChildrenDTO($parentId);
    }

    public function getAncestors(int $categoryId): Collection
    {
        return $this->repository->getAncestors($categoryId);
    }

    public function getAncestorsDTO(int $categoryId): Collection
    {
        return $this->repository->getAncestorsDTO($categoryId);
    }

    public function getDescendants(int $categoryId): Collection
    {
        return $this->repository->getDescendants($categoryId);
    }

    public function getDescendantsDTO(int $categoryId): Collection
    {
        return $this->repository->getDescendantsDTO($categoryId);
    }

    public function getTree(): Collection
    {
        return $this->repository->getTree();
    }

    public function getTreeDTO(): Collection
    {
        return $this->repository->getTreeDTO();
    }

    public function moveCategory(Category $category, ?int $newParentId): bool
    {
        return $this->repository->moveCategory($category, $newParentId);
    }

    public function reorderCategories(array $orderData): bool
    {
        return $this->repository->reorderCategories($orderData);
    }

    // Status Operations
    public function findByStatus(CategoryStatus $status): Collection
    {
        return $this->repository->findByStatus($status);
    }

    public function findByStatusDTO(CategoryStatus $status): Collection
    {
        return $this->repository->findByStatusDTO($status);
    }

    // Statistics
    public function getCategoryStats(): array
    {
        return $this->repository->getCategoryStats();
    }

    public function getCategoryStatsByStatus(): array
    {
        return $this->repository->getCategoryStatsByStatus();
    }

    // Path and Depth
    public function getCategoryPath(int $categoryId): Collection
    {
        return $this->repository->getCategoryPath($categoryId);
    }

    public function getCategoryPathDTO(int $categoryId): Collection
    {
        return $this->repository->getCategoryPathDTO($categoryId);
    }

    public function getDepth(Category $category): int
    {
        return $this->repository->getDepth($category);
    }

    public function getByDepth(int $depth): Collection
    {
        return $this->repository->getByDepth($depth);
    }

    // Count Operations
    public function getCategoryCount(): int
    {
        return $this->repository->getCategoryCount();
    }

    public function getCategoryCountByStatus(CategoryStatus $status): int
    {
        return $this->repository->getCategoryCountByStatus($status);
    }

    public function getCategoryCountByParent(int $parentId): int
    {
        return $this->repository->getCategoryCountByParent($parentId);
    }

    // Validation
    public function validateCategory(array $data): bool
    {
        return $this->repository->validateCategory($data);
    }

    // Helper Methods
    protected function generateSlug(string $name, ?int $excludeId = null): string
    {
        $baseSlug = Str::slug($name);
        $slug = $baseSlug;
        $counter = 1;

        while ($this->slugExists($slug, $excludeId)) {
            $slug = $baseSlug . '-' . $counter;
            $counter++;
        }

        return $slug;
    }

    protected function slugExists(string $slug, ?int $excludeId = null): bool
    {
        $query = Category::where('slug', $slug);

        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        return $query->exists();
    }

    protected function handleLastCategoryDeletion(Category $category): void
    {
        // If this is a default category, ensure there's another default
        if ($category->is_default) {
            $otherDefaults = Category::where('is_default', true)
                ->where('id', '!=', $category->id)
                ->count();

            if ($otherDefaults === 0) {
                // Set the first available category as default
                $firstCategory = Category::where('id', '!=', $category->id)->first();
                if ($firstCategory) {
                    $firstCategory->update(['is_default' => true]);
                }
            }
        }
    }

    protected function validateData(array $data, ?Category $category = null): void
    {
        // Additional category-specific validation can be added here
        if (isset($data['parent_id']) && $data['parent_id']) {
            $parent = Category::find($data['parent_id']);
            if (!$parent) {
                throw new \InvalidArgumentException('Parent category not found');
            }

            // Prevent circular references
            if ($category && $this->wouldCreateCircularReference($category, $data['parent_id'])) {
                throw new \InvalidArgumentException('Cannot move category: would create circular reference');
            }
        }
    }

    protected function wouldCreateCircularReference(Category $category, int $newParentId): bool
    {
        if ($category->id === $newParentId) {
            return true;
        }

        $parent = Category::find($newParentId);
        if (!$parent) {
            return false;
        }

        // Check if new parent is a descendant of current category
        $descendants = $this->getDescendants($category->id);
        return $descendants->contains('id', $newParentId);
    }
}
