<?php

namespace Fereydooni\Shopping\app\DTOs;

use Illuminate\Support\Carbon;
use Spatie\LaravelData\Data;

class ProductAttributeValueDTO extends Data
{
    public function __construct(
        public ?int $id,
        public int $attribute_id,
        public string $value,
        public ?string $slug = null,
        public ?string $description = null,
        public int $sort_order = 0,
        public bool $is_active = true,
        public bool $is_default = false,
        public ?string $color_code = null,
        public ?string $image_url = null,
        public ?array $meta_data = null,
        public int $usage_count = 0,
        public ?int $created_by = null,
        public ?int $updated_by = null,
        public ?Carbon $created_at = null,
        public ?Carbon $updated_at = null,
        public ?ProductAttributeDTO $attribute = null,
    ) {}

    public static function fromModel($attributeValue): static
    {
        return new static(
            id: $attributeValue->id,
            attribute_id: $attributeValue->attribute_id,
            value: $attributeValue->value,
            slug: $attributeValue->slug,
            description: $attributeValue->description,
            sort_order: $attributeValue->sort_order ?? 0,
            is_active: $attributeValue->is_active ?? true,
            is_default: $attributeValue->is_default ?? false,
            color_code: $attributeValue->color_code,
            image_url: $attributeValue->image_url,
            meta_data: $attributeValue->meta_data ? (is_array($attributeValue->meta_data) ? $attributeValue->meta_data : json_decode($attributeValue->meta_data, true)) : null,
            usage_count: $attributeValue->usage_count ?? 0,
            created_by: $attributeValue->created_by,
            updated_by: $attributeValue->updated_by,
            created_at: $attributeValue->created_at,
            updated_at: $attributeValue->updated_at,
            attribute: $attributeValue->attribute ? ProductAttributeDTO::fromModel($attributeValue->attribute) : null,
        );
    }

    public static function rules(): array
    {
        return [
            'attribute_id' => 'required|integer|exists:product_attributes,id',
            'value' => 'required|string|max:1000',
            'slug' => 'nullable|string|max:255|unique:product_attribute_values,slug',
            'description' => 'nullable|string|max:1000',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'nullable|boolean',
            'is_default' => 'nullable|boolean',
            'color_code' => 'nullable|string|max:7|regex:/^#[0-9A-F]{6}$/i',
            'image_url' => 'nullable|url|max:500',
            'meta_data' => 'nullable|array',
            'usage_count' => 'nullable|integer|min:0',
            'created_by' => 'nullable|integer|exists:users,id',
            'updated_by' => 'nullable|integer|exists:users,id',
        ];
    }

    public static function messages(): array
    {
        return [
            'attribute_id.required' => 'Attribute ID is required',
            'attribute_id.integer' => 'Attribute ID must be a number',
            'attribute_id.exists' => 'Selected attribute does not exist',
            'value.required' => 'Attribute value is required',
            'value.string' => 'Attribute value must be a string',
            'value.max' => 'Attribute value cannot exceed 1000 characters',
            'slug.string' => 'Slug must be a string',
            'slug.max' => 'Slug cannot exceed 255 characters',
            'slug.unique' => 'This slug is already in use',
            'description.string' => 'Description must be a string',
            'description.max' => 'Description cannot exceed 1000 characters',
            'sort_order.integer' => 'Sort order must be a number',
            'sort_order.min' => 'Sort order cannot be negative',
            'is_active.boolean' => 'Active status must be true or false',
            'is_default.boolean' => 'Default status must be true or false',
            'color_code.string' => 'Color code must be a string',
            'color_code.max' => 'Color code cannot exceed 7 characters',
            'color_code.regex' => 'Color code must be a valid hex color (e.g., #FF0000)',
            'image_url.url' => 'Image URL must be a valid URL',
            'image_url.max' => 'Image URL cannot exceed 500 characters',
            'meta_data.array' => 'Metadata must be an array',
            'usage_count.integer' => 'Usage count must be a number',
            'usage_count.min' => 'Usage count cannot be negative',
            'created_by.integer' => 'Created by must be a number',
            'created_by.exists' => 'Selected user does not exist',
            'updated_by.integer' => 'Updated by must be a number',
            'updated_by.exists' => 'Selected user does not exist',
        ];
    }
}
