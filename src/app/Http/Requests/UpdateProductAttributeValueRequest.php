<?php

namespace Fereydooni\Shopping\app\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProductAttributeValueRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('update', $this->route('value'));
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $valueId = $this->route('value')->id;

        return [
            'attribute_id' => 'sometimes|integer|exists:product_attributes,id',
            'value' => 'sometimes|string|max:1000',
            'slug' => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('product_attribute_values', 'slug')->ignore($valueId),
            ],
            'description' => 'nullable|string|max:1000',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'nullable|boolean',
            'is_default' => 'nullable|boolean',
            'color_code' => 'nullable|string|max:7|regex:/^#[0-9A-F]{6}$/i',
            'image_url' => 'nullable|url|max:500',
            'meta_data' => 'nullable|array',
            'usage_count' => 'nullable|integer|min:0',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'attribute_id.integer' => 'Attribute ID must be a number',
            'attribute_id.exists' => 'Selected attribute does not exist',
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
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'attribute_id' => 'attribute',
            'value' => 'attribute value',
            'slug' => 'slug',
            'description' => 'description',
            'sort_order' => 'sort order',
            'is_active' => 'active status',
            'is_default' => 'default status',
            'color_code' => 'color code',
            'image_url' => 'image URL',
            'meta_data' => 'metadata',
            'usage_count' => 'usage count',
        ];
    }
}
