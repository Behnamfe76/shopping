<?php

namespace Fereydooni\Shopping\app\DTOs;

use Fereydooni\Shopping\app\Models\ProductTag;
use Illuminate\Support\Str;

class ProductTagDTO
{
    public string $name;
    public string $slug;
    public ?string $description;
    public ?string $color;
    public ?string $icon;
    public bool $is_active;
    public bool $is_featured;
    public int $sort_order;
    public int $usage_count;
    public ?int $created_by;
    public ?int $updated_by;
    public ?int $id;
    public ?string $created_at;
    public ?string $updated_at;

    public function __construct(
        string $name,
        string $slug,
        ?string $description = null,
        ?string $color = null,
        ?string $icon = null,
        bool $is_active = true,
        bool $is_featured = false,
        int $sort_order = 0,
        int $usage_count = 0,
        ?int $created_by = null,
        ?int $updated_by = null,
        ?int $id = null,
        ?string $created_at = null,
        ?string $updated_at = null
    ) {
        $this->name = $name;
        $this->slug = $slug;
        $this->description = $description;
        $this->color = $color;
        $this->icon = $icon;
        $this->is_active = $is_active;
        $this->is_featured = $is_featured;
        $this->sort_order = $sort_order;
        $this->usage_count = $usage_count;
        $this->created_by = $created_by;
        $this->updated_by = $updated_by;
        $this->id = $id;
        $this->created_at = $created_at;
        $this->updated_at = $updated_at;
    }

    public static function fromModel(ProductTag $tag): self
    {
        return new self(
            name: $tag->name,
            slug: $tag->slug,
            description: $tag->description,
            color: $tag->color,
            icon: $tag->icon,
            is_active: $tag->is_active,
            is_featured: $tag->is_featured,
            sort_order: $tag->sort_order,
            usage_count: $tag->usage_count,
            created_by: $tag->created_by,
            updated_by: $tag->updated_by,
            id: $tag->id,
            created_at: $tag->created_at?->toISOString(),
            updated_at: $tag->updated_at?->toISOString()
        );
    }

    public static function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', 'unique:product_tags,slug'],
            'description' => ['nullable', 'string', 'max:1000'],
            'color' => ['nullable', 'string', 'max:7', 'regex:/^#[0-9A-F]{6}$/i'],
            'icon' => ['nullable', 'string', 'max:50'],
            'is_active' => ['boolean'],
            'is_featured' => ['boolean'],
            'sort_order' => ['integer', 'min:0'],
            'usage_count' => ['integer', 'min:0'],
            'created_by' => ['nullable', 'integer'],
            'updated_by' => ['nullable', 'integer'],
        ];
    }

    public static function messages(): array
    {
        return [
            'name.required' => 'Tag name is required.',
            'name.max' => 'Tag name cannot exceed 255 characters.',
            'slug.required' => 'Tag slug is required.',
            'slug.unique' => 'This tag slug is already in use.',
            'slug.max' => 'Tag slug cannot exceed 255 characters.',
            'description.max' => 'Tag description cannot exceed 1000 characters.',
            'color.regex' => 'Color must be a valid hex color code (e.g., #FF0000).',
            'icon.max' => 'Icon name cannot exceed 50 characters.',
            'sort_order.min' => 'Sort order must be a positive number.',
            'usage_count.min' => 'Usage count must be a positive number.',
        ];
    }

    public static function generateSlug(string $name): string
    {
        return Str::slug($name);
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'color' => $this->color,
            'icon' => $this->icon,
            'is_active' => $this->is_active,
            'is_featured' => $this->is_featured,
            'sort_order' => $this->sort_order,
            'usage_count' => $this->usage_count,
            'created_by' => $this->created_by,
            'updated_by' => $this->updated_by,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
