<?php

namespace Fereydooni\Shopping\app\Actions\Category;

use Fereydooni\Shopping\app\Models\Category;
use Fereydooni\Shopping\app\Repositories\Interfaces\CategoryRepositoryInterface;
use Illuminate\Support\Str;

class CreateCategoryAction
{
    public function __construct(
        protected CategoryRepositoryInterface $categoryRepository
    ) {
    }

    public function execute(array $data): Category
    {
        // Generate slug if not provided
        if (!isset($data['slug']) || empty($data['slug'])) {
            $data['slug'] = Str::slug($data['name']);
        }

        // Ensure slug is unique
        $data['slug'] = $this->makeSlugUnique($data['slug']);

        return $this->categoryRepository->create($data);
    }

    private function makeSlugUnique(string $slug): string
    {
        $originalSlug = $slug;
        $counter = 1;

        while ($this->categoryRepository->findBySlug($slug)) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }

        return $slug;
    }
}
