<?php

namespace Fereydooni\Shopping\app\Actions\Category;

use Fereydooni\Shopping\app\Models\Category;
use Fereydooni\Shopping\app\Repositories\Interfaces\CategoryRepositoryInterface;
use Illuminate\Support\Str;

class UpdateCategoryAction
{
    public function __construct(
        protected CategoryRepositoryInterface $categoryRepository
    ) {
    }

    public function execute(Category $category, array $data): bool
    {
        // Generate slug if not provided
        if (isset($data['name']) && (!isset($data['slug']) || empty($data['slug']))) {
            $data['slug'] = Str::slug($data['name']);
        }

        // Ensure slug is unique (excluding current category)
        if (isset($data['slug'])) {
            $data['slug'] = $this->makeSlugUnique($data['slug'], $category->id);
        }

        return $this->categoryRepository->update($category, $data);
    }

    private function makeSlugUnique(string $slug, int $excludeId): string
    {
        $originalSlug = $slug;
        $counter = 1;

        while ($existingCategory = $this->categoryRepository->findBySlug($slug)) {
            if ($existingCategory->id === $excludeId) {
                break; // Same category, slug is fine
            }
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }

        return $slug;
    }
}
