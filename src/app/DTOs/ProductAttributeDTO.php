<?php

namespace Fereydooni\Shopping\app\DTOs;

use Spatie\LaravelData\Data;

use Illuminate\Support\Carbon;
use Fereydooni\Shopping\app\Models\ProductAttribute;
use Fereydooni\Shopping\app\Enums\ProductAttributeType;
use Fereydooni\Shopping\app\Enums\ProductAttributeInputType;

class ProductAttributeDTO extends Data
{
    public function __construct(
        public string $name,
        public string $slug,
        public ?string $description = null,
        public ProductAttributeType $type,
        public ProductAttributeInputType $input_type,
        public bool $is_required = false,
        public bool $is_searchable = false,
        public bool $is_filterable = false,
        public bool $is_comparable = false,
        public bool $is_visible = true,
        public int $sort_order = 0,
        public ?string $validation_rules = null,
        public ?string $default_value = null,
        public ?string $unit = null,
        public ?string $group = null,
        public bool $is_system = false,
        public bool $is_active = true,
        public ?string $meta_title = null,
        public ?string $meta_description = null,
        public ?string $meta_keywords = null,
        public ?int $created_by = null,
        public ?int $updated_by = null,
        public ?Carbon $created_at = null,
        public ?Carbon $updated_at = null,
        public ?int $id = null,
    ) {
    }

    public static function fromModel(ProductAttribute $attribute): self
    {
        return new self(
            name: $attribute->name,
            slug: $attribute->slug,
            description: $attribute->description,
            type: $attribute->type,
            input_type: $attribute->input_type,
            is_required: $attribute->is_required,
            is_searchable: $attribute->is_searchable,
            is_filterable: $attribute->is_filterable,
            is_comparable: $attribute->is_comparable,
            is_visible: $attribute->is_visible,
            sort_order: $attribute->sort_order,
            validation_rules: $attribute->validation_rules,
            default_value: $attribute->default_value,
            unit: $attribute->unit,
            group: $attribute->group,
            is_system: $attribute->is_system,
            is_active: $attribute->is_active,
            meta_title: $attribute->meta_title,
            meta_description: $attribute->meta_description,
            meta_keywords: $attribute->meta_keywords,
            created_by: $attribute->created_by,
            updated_by: $attribute->updated_by,
            created_at: $attribute->created_at,
            updated_at: $attribute->updated_at,
            id: $attribute->id,
        );
    }

    public static function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', 'regex:/^[a-z0-9-]+$/'],
            'description' => ['nullable', 'string', 'max:1000'],
            'type' => ['required', 'string', 'in:' . implode(',', array_column(ProductAttributeType::cases(), 'value'))],
            'input_type' => ['required', 'string', 'in:' . implode(',', array_column(ProductAttributeInputType::cases(), 'value'))],
            'is_required' => ['boolean'],
            'is_searchable' => ['boolean'],
            'is_filterable' => ['boolean'],
            'is_comparable' => ['boolean'],
            'is_visible' => ['boolean'],
            'sort_order' => ['integer', 'min:0'],
            'validation_rules' => ['nullable', 'string', 'max:1000'],
            'default_value' => ['nullable', 'string', 'max:500'],
            'unit' => ['nullable', 'string', 'max:50'],
            'group' => ['nullable', 'string', 'max:100'],
            'is_system' => ['boolean'],
            'is_active' => ['boolean'],
            'meta_title' => ['nullable', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string', 'max:500'],
            'meta_keywords' => ['nullable', 'string', 'max:500'],
            'created_by' => ['nullable', 'integer', 'exists:users,id'],
            'updated_by' => ['nullable', 'integer', 'exists:users,id'],
        ];
    }

    public static function messages(): array
    {
        return [
            'name.required' => 'The attribute name is required.',
            'name.max' => 'The attribute name cannot exceed 255 characters.',
            'slug.required' => 'The attribute slug is required.',
            'slug.regex' => 'The attribute slug can only contain lowercase letters, numbers, and hyphens.',
            'slug.max' => 'The attribute slug cannot exceed 255 characters.',
            'description.max' => 'The description cannot exceed 1000 characters.',
            'type.required' => 'The attribute type is required.',
            'type.in' => 'The selected attribute type is invalid.',
            'input_type.required' => 'The input type is required.',
            'input_type.in' => 'The selected input type is invalid.',
            'sort_order.min' => 'The sort order must be a positive number.',
            'validation_rules.max' => 'The validation rules cannot exceed 1000 characters.',
            'default_value.max' => 'The default value cannot exceed 500 characters.',
            'unit.max' => 'The unit cannot exceed 50 characters.',
            'group.max' => 'The group name cannot exceed 100 characters.',
            'meta_title.max' => 'The meta title cannot exceed 255 characters.',
            'meta_description.max' => 'The meta description cannot exceed 500 characters.',
            'meta_keywords.max' => 'The meta keywords cannot exceed 500 characters.',
            'created_by.exists' => 'The selected creator does not exist.',
            'updated_by.exists' => 'The selected updater does not exist.',
        ];
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'type' => $this->type->value,
            'type_label' => $this->type->label(),
            'type_description' => $this->type->description(),
            'input_type' => $this->input_type->value,
            'input_type_label' => $this->input_type->label(),
            'input_type_description' => $this->input_type->description(),
            'is_required' => $this->is_required,
            'is_searchable' => $this->is_searchable,
            'is_filterable' => $this->is_filterable,
            'is_comparable' => $this->is_comparable,
            'is_visible' => $this->is_visible,
            'sort_order' => $this->sort_order,
            'validation_rules' => $this->validation_rules,
            'default_value' => $this->default_value,
            'unit' => $this->unit,
            'group' => $this->group,
            'is_system' => $this->is_system,
            'is_active' => $this->is_active,
            'meta_title' => $this->meta_title,
            'meta_description' => $this->meta_description,
            'meta_keywords' => $this->meta_keywords,
            'created_by' => $this->created_by,
            'updated_by' => $this->updated_by,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
