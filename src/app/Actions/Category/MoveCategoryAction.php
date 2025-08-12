<?php

namespace Fereydooni\Shopping\app\Actions\Category;

use Fereydooni\Shopping\app\Models\Category;
use Fereydooni\Shopping\app\Repositories\Interfaces\CategoryRepositoryInterface;

class MoveCategoryAction
{
    public function __construct(
        protected CategoryRepositoryInterface $categoryRepository
    ) {
    }

    public function execute(Category $category, ?int $newParentId): bool
    {
        // Validate that new parent exists if provided
        if ($newParentId !== null) {
            $newParent = $this->categoryRepository->find($newParentId);
            if (!$newParent) {
                throw new \Exception('New parent category not found');
            }
        }

        return $this->categoryRepository->moveToParent($category, $newParentId);
    }
}
