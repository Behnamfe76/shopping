<?php

namespace Fereydooni\Shopping\app\Actions\Category;

use Fereydooni\Shopping\app\Repositories\Interfaces\CategoryRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class GetCategoryTreeAction
{
    public function __construct(
        protected CategoryRepositoryInterface $categoryRepository
    ) {
    }

    public function execute(): Collection
    {
        return $this->categoryRepository->getTree();
    }
}
