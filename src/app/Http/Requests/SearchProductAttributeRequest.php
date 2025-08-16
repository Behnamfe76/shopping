<?php

namespace Fereydooni\Shopping\app\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SearchProductAttributeRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('search', \Fereydooni\Shopping\app\Models\ProductAttribute::class);
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'query' => 'required|string|min:2|max:255',
            'type' => 'nullable|string',
            'group' => 'nullable|string',
            'input_type' => 'nullable|string',
            'is_required' => 'nullable|boolean',
            'is_searchable' => 'nullable|boolean',
            'is_filterable' => 'nullable|boolean',
            'is_comparable' => 'nullable|boolean',
            'is_visible' => 'nullable|boolean',
            'is_system' => 'nullable|boolean',
            'is_active' => 'nullable|boolean',
            'per_page' => 'nullable|integer|min:1|max:100',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'query.required' => 'Search query is required.',
            'query.min' => 'Search query must be at least 2 characters.',
            'query.max' => 'Search query cannot exceed 255 characters.',
            'per_page.integer' => 'Per page must be a number.',
            'per_page.min' => 'Per page must be at least 1.',
            'per_page.max' => 'Per page cannot exceed 100.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'query' => 'search query',
            'per_page' => 'per page',
        ];
    }
}

