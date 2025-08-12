<?php

namespace Fereydooni\Shopping\app\Actions\Category;

use Fereydooni\Shopping\app\Models\Category;
use Fereydooni\Shopping\app\Repositories\Interfaces\CategoryRepositoryInterface;

class DeleteCategoryAction
{
    public function __construct(
        protected CategoryRepositoryInterface $categoryRepository
    ) {
    }

    public function execute(Category $category): bool
    {
        // Check if category has products
        if ($this->categoryRepository->hasProducts($category)) {
            throw new \Exception('Cannot delete category with products');
        }

        // Check if category has children
        if ($this->categoryRepository->hasChildren($category)) {
            throw new \Exception('Cannot delete category with children');
        }

        return $this->categoryRepository->delete($category);
    }
}
