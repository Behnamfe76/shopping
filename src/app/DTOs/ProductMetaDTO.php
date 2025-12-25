<?php

namespace Fereydooni\Shopping\app\DTOs;

use Fereydooni\Shopping\app\Enums\MetaType;
use Fereydooni\Shopping\app\Models\ProductMeta;
use Illuminate\Support\Carbon;
use Spatie\LaravelData\Data;

class ProductMetaDTO extends Data
{
    public function __construct(
        public int $product_id,
        public string $meta_key,
        public string $meta_value,
        public string $meta_type,
        public bool $is_public = true,
        public bool $is_searchable = false,
        public bool $is_filterable = false,
        public int $sort_order = 0,
        public ?string $description = null,
        public ?string $validation_rules = null,
        public ?int $created_by = null,
        public ?int $updated_by = null,
        public ?Carbon $created_at = null,
        public ?Carbon $updated_at = null,
        public ?int $id = null,
    ) {}

    public static function fromModel(ProductMeta $productMeta): self
    {
        return new self(
            product_id: $productMeta->product_id,
            meta_key: $productMeta->meta_key,
            meta_value: $productMeta->meta_value,
            meta_type: $productMeta->meta_type ?? MetaType::TEXT->value,
            is_public: $productMeta->is_public ?? true,
            is_searchable: $productMeta->is_searchable ?? false,
            is_filterable: $productMeta->is_filterable ?? false,
            sort_order: $productMeta->sort_order ?? 0,
            description: $productMeta->description,
            validation_rules: $productMeta->validation_rules,
            created_by: $productMeta->created_by,
            updated_by: $productMeta->updated_by,
            created_at: $productMeta->created_at,
            updated_at: $productMeta->updated_at,
            id: $productMeta->id,
        );
    }

    public static function rules(): array
    {
        return [
            'product_id' => ['required', 'integer', 'exists:products,id'],
            'meta_key' => ['required', 'string', 'max:255'],
            'meta_value' => ['required', 'string', 'max:65535'],
            'meta_type' => ['required', 'string', 'in:'.implode(',', array_column(MetaType::cases(), 'value'))],
            'is_public' => ['boolean'],
            'is_searchable' => ['boolean'],
            'is_filterable' => ['boolean'],
            'sort_order' => ['integer', 'min:0'],
            'description' => ['nullable', 'string', 'max:1000'],
            'validation_rules' => ['nullable', 'string', 'max:1000'],
            'created_by' => ['nullable', 'integer', 'exists:users,id'],
            'updated_by' => ['nullable', 'integer', 'exists:users,id'],
        ];
    }

    public static function messages(): array
    {
        return [
            'product_id.required' => 'Product ID is required.',
            'product_id.exists' => 'The selected product does not exist.',
            'meta_key.required' => 'Meta key is required.',
            'meta_key.max' => 'Meta key cannot exceed 255 characters.',
            'meta_value.required' => 'Meta value is required.',
            'meta_value.max' => 'Meta value cannot exceed 65535 characters.',
            'meta_type.required' => 'Meta type is required.',
            'meta_type.in' => 'Invalid meta type selected.',
            'is_public.boolean' => 'Public flag must be true or false.',
            'is_searchable.boolean' => 'Searchable flag must be true or false.',
            'is_filterable.boolean' => 'Filterable flag must be true or false.',
            'sort_order.integer' => 'Sort order must be an integer.',
            'sort_order.min' => 'Sort order cannot be negative.',
            'description.max' => 'Description cannot exceed 1000 characters.',
            'validation_rules.max' => 'Validation rules cannot exceed 1000 characters.',
            'created_by.exists' => 'The selected creator does not exist.',
            'updated_by.exists' => 'The selected updater does not exist.',
        ];
    }

    public function getMetaTypeEnum(): MetaType
    {
        return MetaType::from($this->meta_type);
    }

    public function isPublic(): bool
    {
        return $this->is_public;
    }

    public function isSearchable(): bool
    {
        return $this->is_searchable;
    }

    public function isFilterable(): bool
    {
        return $this->is_filterable;
    }

    public function getValidationRulesArray(): array
    {
        if (! $this->validation_rules) {
            return $this->getMetaTypeEnum()->getValidationRules();
        }

        return array_merge(
            $this->getMetaTypeEnum()->getValidationRules(),
            explode('|', $this->validation_rules)
        );
    }

    public function validateValue(): bool
    {
        $rules = $this->getValidationRulesArray();
        $validator = validator(['value' => $this->meta_value], ['value' => $rules]);

        return ! $validator->fails();
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'product_id' => $this->product_id,
            'meta_key' => $this->meta_key,
            'meta_value' => $this->meta_value,
            'meta_type' => $this->meta_type,
            'meta_type_label' => $this->getMetaTypeEnum()->getLabel(),
            'is_public' => $this->is_public,
            'is_searchable' => $this->is_searchable,
            'is_filterable' => $this->is_filterable,
            'sort_order' => $this->sort_order,
            'description' => $this->description,
            'validation_rules' => $this->validation_rules,
            'created_by' => $this->created_by,
            'updated_by' => $this->updated_by,
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}
