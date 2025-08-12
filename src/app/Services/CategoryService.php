<?php

namespace Fereydooni\Shopping\app\Services;

use Fereydooni\Shopping\app\Actions\Category\CreateCategoryAction;
use Fereydooni\Shopping\app\Actions\Category\UpdateCategoryAction;
use Fereydooni\Shopping\app\Actions\Category\DeleteCategoryAction;
use Fereydooni\Shopping\app\Actions\Category\MoveCategoryAction;
use Fereydooni\Shopping\app\Actions\Category\GetCategoryTreeAction;
use Fereydooni\Shopping\app\Actions\Category\SearchCategoriesAction;
use Fereydooni\Shopping\app\Models\Category;
use Fereydooni\Shopping\app\Repositories\Interfaces\CategoryRepositoryInterface;
use Fereydooni\Shopping\app\DTOs\CategoryDTO;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\CursorPaginator;
use Illuminate\Pagination\Paginator;

class CategoryService
{
    public function __construct(
        protected CategoryRepositoryInterface $categoryRepository,
        protected CreateCategoryAction $createAction,
        protected UpdateCategoryAction $updateAction,
        protected DeleteCategoryAction $deleteAction,
        protected MoveCategoryAction $moveAction,
        protected GetCategoryTreeAction $treeAction,
        protected SearchCategoriesAction $searchAction
    ) {
    }

    public function all(): Collection
    {
        return $this->categoryRepository->all();
    }

    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        return $this->categoryRepository->paginate($perPage);
    }

    public function simplePaginate(int $perPage = 15): Paginator
    {
        return $this->categoryRepository->simplePaginate($perPage);
    }

    public function cursorPaginate(int $perPage = 15, string $cursor = null): CursorPaginator
    {
        return $this->categoryRepository->cursorPaginate($perPage, $cursor);
    }

    public function find(int $id): ?Category
    {
        return $this->categoryRepository->find($id);
    }

    public function findBySlug(string $slug): ?Category
    {
        return $this->categoryRepository->findBySlug($slug);
    }

    public function findDTO(int $id): ?CategoryDTO
    {
        return $this->categoryRepository->findDTO($id);
    }

    public function findBySlugDTO(string $slug): ?CategoryDTO
    {
        return $this->categoryRepository->findBySlugDTO($slug);
    }

    public function create(array $data): Category
    {
        return $this->createAction->execute($data);
    }

    public function createDTO(array $data): CategoryDTO
    {
        return $this->categoryRepository->createAndReturnDTO($data);
    }

    public function update(Category $category, array $data): bool
    {
        return $this->updateAction->execute($category, $data);
    }

    public function updateDTO(Category $category, array $data): ?CategoryDTO
    {
        return $this->categoryRepository->updateAndReturnDTO($category, $data);
    }

    public function delete(Category $category): bool
    {
        return $this->deleteAction->execute($category);
    }

    public function move(Category $category, ?int $newParentId): bool
    {
        return $this->moveAction->execute($category, $newParentId);
    }

    public function getTree(): Collection
    {
        return $this->treeAction->execute();
    }

    public function search(string $query): Collection
    {
        return $this->searchAction->execute($query);
    }

    public function getRootCategories(): Collection
    {
        return $this->categoryRepository->getRootCategories();
    }

    public function getWithChildren(): Collection
    {
        return $this->categoryRepository->getWithChildren();
    }

    public function getByParentId(?int $parentId): Collection
    {
        return $this->categoryRepository->getByParentId($parentId);
    }

    public function getWithAllChildren(int $categoryId): ?Category
    {
        return $this->categoryRepository->getWithAllChildren($categoryId);
    }

    public function getWithAllParents(int $categoryId): ?Category
    {
        return $this->categoryRepository->getWithAllParents($categoryId);
    }

    public function getWithProductsCount(): Collection
    {
        return $this->categoryRepository->getWithProductsCount();
    }

    public function hasChildren(Category $category): bool
    {
        return $this->categoryRepository->hasChildren($category);
    }

    public function hasProducts(Category $category): bool
    {
        return $this->categoryRepository->hasProducts($category);
    }

    public function getPath(Category $category): Collection
    {
        return $this->categoryRepository->getPath($category);
    }

    public function getDepth(Category $category): int
    {
        return $this->categoryRepository->getDepth($category);
    }

    public function getByDepth(int $depth): Collection
    {
        return $this->categoryRepository->getByDepth($depth);
    }
}
