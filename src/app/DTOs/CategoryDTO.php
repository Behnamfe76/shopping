<?php

namespace Fereydooni\Shopping\app\DTOs;

use Spatie\LaravelData\Data;
use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Attributes\WithTransformer;
use Spatie\LaravelData\Transformers\DateTimeTransformer;
use Illuminate\Support\Carbon;

class CategoryDTO extends Data
{
    public function __construct(
        public ?int $id,
        public ?int $parent_id,
        public string $name,
        public string $slug,
        public ?string $description,
        public ?Carbon $created_at,
        public ?Carbon $updated_at,
        public ?CategoryDTO $parent = null,
        public ?array $children = null,
        public ?int $products_count = null,
        public ?int $depth = null,
        public ?array $path = null,
    ) {
    }

    public static function fromModel($category): static
    {
        return new static(
            id: $category->id,
            parent_id: $category->parent_id,
            name: $category->name,
            slug: $category->slug,
            description: $category->description,
            created_at: $category->created_at,
            updated_at: $category->updated_at,
            parent: $category->parent ? static::fromModel($category->parent) : null,
            children: $category->children ? $category->children->map(fn($child) => static::fromModel($child))->toArray() : null,
            products_count: $category->products_count ?? null,
            depth: null, // Will be calculated separately
            path: null, // Will be calculated separately
        );
    }

    public static function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:categories,slug',
            'description' => 'nullable|string',
            'parent_id' => 'nullable|integer|exists:categories,id',
        ];
    }

    public static function messages(): array
    {
        return [
            'name.required' => 'Category name is required',
            'name.max' => 'Category name cannot exceed 255 characters',
            'slug.unique' => 'This slug is already taken',
            'parent_id.exists' => 'Selected parent category does not exist',
        ];
    }
}
