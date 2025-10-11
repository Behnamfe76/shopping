<?php

namespace Fereydooni\Shopping\app\DTOs;

use Spatie\LaravelData\Data;
use Illuminate\Support\Carbon;
use Fereydooni\Shopping\app\Models\Category;
use Fereydooni\Shopping\app\Enums\CategoryStatus;

class CategoryDTO extends Data
{
    public function __construct(
        public ?int $id,
        public ?int $parent_id,
        public string $name,
        public string $slug,
        public ?string $description,
        public CategoryStatus $status,
        public int $sort_order,
        public bool $is_default,
        public ?Carbon $created_at,
        public ?Carbon $updated_at,
        public ?CategoryDTO $parent = null,
        public ?array $children = null,
        public ?int $products_count = null,
        public ?int $depth = null,
        public ?array $path = null,
        public ?array $media = null,
    ) {
    }

    public static function fromModel(Category $category): static
    {
        return new static(
            id: $category->id,
            parent_id: $category->parent_id,
            name: $category->name,
            slug: $category->slug,
            description: $category->description,
            status: $category->status ?? CategoryStatus::DRAFT,
            sort_order: $category->sort_order ?? 0,
            is_default: $category->is_default ?? false,
            created_at: $category->created_at,
            updated_at: $category->updated_at,
            parent: $category->parent ? static::fromModel($category->parent) : null,
            children: $category->children ? $category->children->map(fn($child) => static::fromModel($child))->toArray() : null,
            products_count: $category->products_count ?? null,
            depth: null, // Will be calculated separately
            path: null, // Will be calculated separately
            media: $category->getMedia() ? $category->getMedia()->toArray() : null,
        );
    }

    public static function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:categories,slug',
            'description' => 'nullable|string',
            'parent_id' => 'nullable|integer|exists:categories,id',
            'status' => 'required|in:' . implode(',', array_column(CategoryStatus::cases(), 'value')),
            'sort_order' => 'integer|min:0',
            'is_default' => 'boolean',
        ];
    }

    public static function messages(): array
    {
        return [
            'name.required' => 'Category name is required',
            'name.max' => 'Category name cannot exceed 255 characters',
            'slug.unique' => 'This slug is already taken',
            'parent_id.exists' => 'Selected parent category does not exist',
            'status.required' => 'Category status is required',
            'status.in' => 'Invalid category status selected',
            'sort_order.integer' => 'Sort order must be a number',
            'sort_order.min' => 'Sort order cannot be negative',
        ];
    }
}
