<?php

namespace Fereydooni\Shopping\app\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductTagRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('create', \Fereydooni\Shopping\app\Models\ProductTag::class);
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255', 'unique:product_tags,name'],
            'slug' => ['required', 'string', 'max:255', 'unique:product_tags,slug'],
            'description' => ['nullable', 'string', 'max:1000'],
            'color' => ['nullable', 'string', 'max:7', 'regex:/^#[0-9A-F]{6}$/i'],
            'icon' => ['nullable', 'string', 'max:50'],
            'is_active' => ['boolean'],
            'is_featured' => ['boolean'],
            'sort_order' => ['integer', 'min:0'],
            'usage_count' => ['integer', 'min:0'],
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
        ];
    }

    public function prepareForValidation()
    {
        $this->merge([
            'usage_count' => 0,
            'is_active' => $this->boolean('is_active', false),
            'is_featured' => $this->boolean('is_featured', false),
        ]);
    }
}
