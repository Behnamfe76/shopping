<?php

namespace Fereydooni\Shopping\app\Http\Requests;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;
use Fereydooni\Shopping\app\DTOs\CategoryDTO;
use Fereydooni\Shopping\app\Enums\CategoryStatus;

class UpdateCategoryRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('update', $this->category);
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'user_id' => 'required|exists:users,id',
            'name' => 'required|string|max:255',
            'slug' => [
                'required',
                'string',
                'max:255',
                Rule::unique('categories', 'slug')->ignore($this->category),
            ],
            'description' => 'nullable|string',
            'parent_id' => 'nullable|integer|exists:categories,id',
            'status' => 'required|in:' . implode(',', array_column(CategoryStatus::cases(), 'value')),
            'sort_order' => 'integer|min:0',
            'is_default' => 'boolean',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return CategoryDTO::messages();
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'name' => 'category name',
            'slug' => 'category slug',
            'description' => 'category description',
            'parent_id' => 'parent category',
            'image_url' => 'image URL',
            'icon' => 'icon',
            'meta_title' => 'meta title',
            'meta_description' => 'meta description',
            'meta_keywords' => 'meta keywords',
            'is_default' => 'default category',
            'sort_order' => 'sort order',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Generate slug if not provided
        if (!$this->has('slug') || empty($this->slug)) {
            $this->merge([
                'slug' => \Illuminate\Support\Str::slug($this->name)
            ]);
        }

        // Set default values
        $this->merge([
            'sort_order' => $this->integer('sort_order', 0),
            'user_id' => $this->user()->id,
        ]);
    }
}
