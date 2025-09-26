<?php

namespace Fereydooni\Shopping\app\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProductTagRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'name' => [
                'sometimes', 'required', 'string', 'max:255',
                Rule::unique('product_tags', 'name')->ignore($this->tag)
            ],
            'slug' => [
                'sometimes', 'required', 'string', 'max:255',
                Rule::unique('product_tags', 'slug')->ignore($this->tag)
            ],
            'description' => ['sometimes', 'nullable', 'string', 'max:1000'],
            'color' => ['sometimes', 'nullable', 'string', 'max:7', 'regex:/^#[0-9A-F]{6}$/i'],
            'icon' => ['sometimes', 'nullable', 'string', 'max:50'],
            'is_active' => ['sometimes', 'boolean'],
            'is_featured' => ['sometimes', 'boolean'],
            'sort_order' => ['sometimes', 'integer', 'min:0'],
            'updated_by' => ['sometimes', 'nullable', 'integer', 'exists:users,id'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Tag name is required.',
            'name.max' => 'Tag name cannot exceed 255 characters.',
            'name.unique' => 'This tag name is already in use.',
            'slug.required' => 'Tag slug is required.',
            'slug.max' => 'Tag slug cannot exceed 255 characters.',
            'slug.unique' => 'This tag slug is already in use.',
            'description.max' => 'Tag description cannot exceed 1000 characters.',
            'color.regex' => 'Color must be a valid hex color code (e.g., #FF0000).',
            'icon.max' => 'Icon name cannot exceed 50 characters.',
            'sort_order.min' => 'Sort order must be a positive number.',
            'updated_by.exists' => 'Selected updater does not exist.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'name' => 'tag name',
            'slug' => 'tag slug',
            'description' => 'tag description',
            'color' => 'tag color',
            'icon' => 'tag icon',
            'is_active' => 'active status',
            'is_featured' => 'featured status',
            'sort_order' => 'sort order',
            'usage_count' => 'usage count',
            'updated_by' => 'updater',
        ];
    }
}
