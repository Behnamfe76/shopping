<?php

namespace Fereydooni\Shopping\app\Actions\Category;

use Fereydooni\Shopping\app\Repositories\Interfaces\CategoryRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class SearchCategoriesAction
{
    public function __construct(
        protected CategoryRepositoryInterface $categoryRepository
    ) {
    }

    public function execute(string $query): Collection
    {
        if (empty(trim($query))) {
            return collect();
        }

        return $this->categoryRepository->search($query);
    }
}
