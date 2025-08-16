<?php

namespace Fereydooni\Shopping\app\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SearchProductAttributeValueRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('search', \Fereydooni\Shopping\app\Models\ProductAttributeValue::class);
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'query' => 'required|string|min:2|max:255',
            'attribute_id' => 'nullable|integer|exists:product_attributes,id',
            'is_active' => 'nullable|boolean',
            'is_default' => 'nullable|boolean',
            'limit' => 'nullable|integer|min:1|max:100',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'query.required' => 'Search query is required',
            'query.string' => 'Search query must be a string',
            'query.min' => 'Search query must be at least 2 characters',
            'query.max' => 'Search query cannot exceed 255 characters',
            'attribute_id.integer' => 'Attribute ID must be a number',
            'attribute_id.exists' => 'Selected attribute does not exist',
            'is_active.boolean' => 'Active filter must be true or false',
            'is_default.boolean' => 'Default filter must be true or false',
            'limit.integer' => 'Limit must be a number',
            'limit.min' => 'Limit must be at least 1',
            'limit.max' => 'Limit cannot exceed 100',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'query' => 'search query',
            'attribute_id' => 'attribute',
            'is_active' => 'active filter',
            'is_default' => 'default filter',
            'limit' => 'limit',
        ];
    }
}
