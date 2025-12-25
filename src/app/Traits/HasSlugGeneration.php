<?php

namespace Fereydooni\Shopping\app\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

trait HasSlugGeneration
{
    public function generateSlug(string $title): string
    {
        $slug = Str::slug($title);
        $originalSlug = $slug;
        $counter = 1;

        while (! $this->isSlugUnique($slug)) {
            $slug = $originalSlug.'-'.$counter;
            $counter++;
        }

        return $slug;
    }

    public function isSlugUnique(string $slug, ?int $excludeId = null): bool
    {
        $query = $this->model::where('slug', $slug);

        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        return ! $query->exists();
    }

    public function regenerateSlug(Model $model, string $newTitle): bool
    {
        $newSlug = $this->generateSlug($newTitle);

        if ($newSlug !== $model->slug) {
            $model->slug = $newSlug;

            return $model->save();
        }

        return true;
    }

    public function sanitizeSlug(string $slug): string
    {
        // Remove any non-alphanumeric characters except hyphens
        $slug = preg_replace('/[^a-z0-9\-]/', '', strtolower($slug));

        // Remove multiple consecutive hyphens
        $slug = preg_replace('/-+/', '-', $slug);

        // Remove leading and trailing hyphens
        $slug = trim($slug, '-');

        return $slug;
    }
}
