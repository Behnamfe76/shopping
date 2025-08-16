<?php

namespace Fereydooni\Shopping\app\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Fereydooni\Shopping\app\Enums\ProductAttributeType;
use Fereydooni\Shopping\app\Enums\ProductAttributeInputType;

class StoreProductAttributeRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('create', \Fereydooni\Shopping\app\Models\ProductAttribute::class);
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|regex:/^[a-z0-9-]+$/|unique:product_attributes,slug',
            'description' => 'nullable|string|max:1000',
            'type' => 'required|string|in:' . implode(',', array_column(ProductAttributeType::cases(), 'value')),
            'input_type' => 'required|string|in:' . implode(',', array_column(ProductAttributeInputType::cases(), 'value')),
            'is_required' => 'boolean',
            'is_searchable' => 'boolean',
            'is_filterable' => 'boolean',
            'is_comparable' => 'boolean',
            'is_visible' => 'boolean',
            'sort_order' => 'integer|min:0',
            'validation_rules' => 'nullable|string|max:1000',
            'default_value' => 'nullable|string|max:500',
            'unit' => 'nullable|string|max:50',
            'group' => 'nullable|string|max:100',
            'is_system' => 'boolean',
            'is_active' => 'boolean',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'meta_keywords' => 'nullable|string|max:500',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'The attribute name is required.',
            'name.max' => 'The attribute name cannot exceed 255 characters.',
            'slug.regex' => 'The attribute slug can only contain lowercase letters, numbers, and hyphens.',
            'slug.max' => 'The attribute slug cannot exceed 255 characters.',
            'slug.unique' => 'This slug is already taken.',
            'description.max' => 'The description cannot exceed 1000 characters.',
            'type.required' => 'The attribute type is required.',
            'type.in' => 'The selected attribute type is invalid.',
            'input_type.required' => 'The input type is required.',
            'input_type.in' => 'The selected input type is invalid.',
            'sort_order.integer' => 'The sort order must be a number.',
            'sort_order.min' => 'The sort order must be a positive number.',
            'validation_rules.max' => 'The validation rules cannot exceed 1000 characters.',
            'default_value.max' => 'The default value cannot exceed 500 characters.',
            'unit.max' => 'The unit cannot exceed 50 characters.',
            'group.max' => 'The group name cannot exceed 100 characters.',
            'meta_title.max' => 'The meta title cannot exceed 255 characters.',
            'meta_description.max' => 'The meta description cannot exceed 500 characters.',
            'meta_keywords.max' => 'The meta keywords cannot exceed 500 characters.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'name' => 'attribute name',
            'slug' => 'attribute slug',
            'description' => 'description',
            'type' => 'attribute type',
            'input_type' => 'input type',
            'is_required' => 'required status',
            'is_searchable' => 'searchable status',
            'is_filterable' => 'filterable status',
            'is_comparable' => 'comparable status',
            'is_visible' => 'visible status',
            'sort_order' => 'sort order',
            'validation_rules' => 'validation rules',
            'default_value' => 'default value',
            'unit' => 'unit',
            'group' => 'group',
            'is_system' => 'system status',
            'is_active' => 'active status',
            'meta_title' => 'meta title',
            'meta_description' => 'meta description',
            'meta_keywords' => 'meta keywords',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Generate slug if not provided
        if (!$this->has('slug') && $this->has('name')) {
            $this->merge([
                'slug' => \Illuminate\Support\Str::slug($this->name)
            ]);
        }

        // Set default values
        $this->merge([
            'is_required' => $this->boolean('is_required', false),
            'is_searchable' => $this->boolean('is_searchable', false),
            'is_filterable' => $this->boolean('is_filterable', false),
            'is_comparable' => $this->boolean('is_comparable', false),
            'is_visible' => $this->boolean('is_visible', true),
            'is_system' => $this->boolean('is_system', false),
            'is_active' => $this->boolean('is_active', true),
            'sort_order' => $this->integer('sort_order', 0),
        ]);
    }
}

