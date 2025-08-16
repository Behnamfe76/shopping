<?php

namespace Fereydooni\Shopping\app\Traits;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

trait HasSlugGeneration
{
    /**
     * Generate a unique slug from a string
     */
    public function generateSlug(string $name, ?int $excludeId = null): string
    {
        $baseSlug = Str::slug($name);
        $slug = $baseSlug;
        $counter = 1;

        while (!$this->isSlugUnique($slug, $excludeId)) {
            $slug = $baseSlug . '-' . $counter;
            $counter++;
        }

        return $slug;
    }

    /**
     * Check if a slug is unique
     */
    public function isSlugUnique(string $slug, ?int $excludeId = null): bool
    {
        $query = $this->repository->getModel()->where('slug', $slug);

        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        return !$query->exists();
    }

    /**
     * Validate slug
     */
    public function validateSlug(string $slug, ?int $excludeId = null): bool
    {
        $rules = [
            'slug' => 'required|string|max:255|regex:/^[a-z0-9-]+$/',
        ];

        $data = ['slug' => $slug];

        $validator = Validator::make($data, $rules);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        return $this->isSlugUnique($slug, $excludeId);
    }

    /**
     * Regenerate slug for an item
     */
    public function regenerateSlug(object $item, string $newName): bool
    {
        $newSlug = $this->generateSlug($newName, $item->id);

        return $this->repository->update($item, ['slug' => $newSlug]);
    }

    /**
     * Sanitize slug
     */
    public function sanitizeSlug(string $slug): string
    {
        // Remove any non-alphanumeric characters except hyphens
        $slug = preg_replace('/[^a-z0-9-]/', '', strtolower($slug));

        // Remove multiple consecutive hyphens
        $slug = preg_replace('/-+/', '-', $slug);

        // Remove leading and trailing hyphens
        $slug = trim($slug, '-');

        return $slug;
    }

    /**
     * Find item by slug
     */
    public function findBySlug(string $slug): ?object
    {
        return $this->repository->findBySlug($slug);
    }

    /**
     * Find item by slug and return as DTO
     */
    public function findBySlugDTO(string $slug): ?object
    {
        return $this->repository->findBySlugDTO($slug);
    }

    /**
     * Get items by first letter of slug
     */
    public function getByFirstLetter(string $letter): Collection
    {
        return $this->repository->getByFirstLetter($letter);
    }

    /**
     * Get items by first letter of slug as DTOs
     */
    public function getByFirstLetterDTO(string $letter): Collection
    {
        return $this->repository->getByFirstLetterDTO($letter);
    }
}
