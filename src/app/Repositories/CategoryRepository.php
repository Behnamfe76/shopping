<?php

namespace Fereydooni\Shopping\app\Repositories;

use Fereydooni\Shopping\app\Models\Category;
use Fereydooni\Shopping\app\Repositories\Interfaces\CategoryRepositoryInterface;
use Fereydooni\Shopping\app\DTOs\CategoryDTO;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\CursorPaginator;
use Illuminate\Pagination\Paginator;

class CategoryRepository implements CategoryRepositoryInterface
{
    public function __construct(protected Category $model)
    {
    }

    public function all(): Collection
    {
        return $this->model->all();
    }

    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        return $this->model->paginate($perPage);
    }

    public function simplePaginate(int $perPage = 15): Paginator
    {
        return $this->model->simplePaginate($perPage);
    }

    public function cursorPaginate(int $perPage = 15, string $cursor = null): CursorPaginator
    {
        return $this->model->cursorPaginate($perPage, ['*'], 'id', $cursor);
    }

    public function find(int $id): ?Category
    {
        return $this->model->find($id);
    }

    public function findBySlug(string $slug): ?Category
    {
        return $this->model->where('slug', $slug)->first();
    }

    public function findDTO(int $id): ?CategoryDTO
    {
        $category = $this->find($id);
        return $category ? CategoryDTO::fromModel($category) : null;
    }

    public function findBySlugDTO(string $slug): ?CategoryDTO
    {
        $category = $this->findBySlug($slug);
        return $category ? CategoryDTO::fromModel($category) : null;
    }

    public function create(array $data): Category
    {
        return $this->model->create($data);
    }

    public function createAndReturnDTO(array $data): CategoryDTO
    {
        $category = $this->create($data);
        return CategoryDTO::fromModel($category);
    }

    public function update(Category $category, array $data): bool
    {
        return $category->update($data);
    }

    public function updateAndReturnDTO(Category $category, array $data): ?CategoryDTO
    {
        $updated = $this->update($category, $data);
        return $updated ? CategoryDTO::fromModel($category->fresh()) : null;
    }

    public function delete(Category $category): bool
    {
        return $category->delete();
    }

    public function getRootCategories(): Collection
    {
        return $this->model->whereNull('parent_id')->get();
    }

    public function getWithChildren(): Collection
    {
        return $this->model->with('children')->get();
    }

    public function getTree(): Collection
    {
        return $this->model->with('allChildren')->whereNull('parent_id')->get();
    }

    public function getByParentId(?int $parentId): Collection
    {
        return $this->model->where('parent_id', $parentId)->get();
    }

    public function getWithAllChildren(int $categoryId): ?Category
    {
        return $this->model->with('allChildren')->find($categoryId);
    }

    public function getWithAllParents(int $categoryId): ?Category
    {
        return $this->model->with('allParents')->find($categoryId);
    }

    public function search(string $query): Collection
    {
        return $this->model->where('name', 'like', "%{$query}%")
            ->orWhere('description', 'like', "%{$query}%")
            ->get();
    }

    public function getWithProductsCount(): Collection
    {
        return $this->model->withCount('products')->get();
    }

    public function hasChildren(Category $category): bool
    {
        return $category->children()->exists();
    }

    public function hasProducts(Category $category): bool
    {
        return $category->products()->exists();
    }

    public function moveToParent(Category $category, ?int $newParentId): bool
    {
        // Prevent moving to itself or its own child
        if ($newParentId === $category->id || $this->isDescendant($category->id, $newParentId)) {
            return false;
        }

        return $category->update(['parent_id' => $newParentId]);
    }

    public function getPath(Category $category): Collection
    {
        $path = collect();
        $current = $category;

        while ($current) {
            $path->prepend($current);
            $current = $current->parent;
        }

        return $path;
    }

    public function getDepth(Category $category): int
    {
        $depth = 0;
        $current = $category;

        while ($current->parent) {
            $depth++;
            $current = $current->parent;
        }

        return $depth;
    }

    public function getByDepth(int $depth): Collection
    {
        return $this->model->whereRaw('(
            SELECT COUNT(*) FROM categories c2
            WHERE c2.id = categories.parent_id
            OR c2.parent_id = categories.parent_id
        ) = ?', [$depth])->get();
    }

    /**
     * Check if a category is a descendant of another category
     */
    private function isDescendant(int $categoryId, ?int $potentialAncestorId): bool
    {
        if (!$potentialAncestorId) {
            return false;
        }

        $category = $this->model->find($categoryId);
        if (!$category) {
            return false;
        }

        $current = $category->parent;
        while ($current) {
            if ($current->id === $potentialAncestorId) {
                return true;
            }
            $current = $current->parent;
        }

        return false;
    }
}
