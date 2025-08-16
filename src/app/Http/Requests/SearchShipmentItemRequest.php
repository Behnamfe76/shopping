<?php

namespace Fereydooni\Shopping\app\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SearchShipmentItemRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('shipment-item.search');
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'query' => 'required|string|min:1|max:255',
            'per_page' => 'sometimes|integer|min:1|max:100',
            'sort_by' => 'sometimes|string|in:quantity,created_at,updated_at',
            'sort_direction' => 'sometimes|string|in:asc,desc',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'query.required' => 'Search query is required.',
            'query.string' => 'Search query must be a string.',
            'query.min' => 'Search query must be at least 1 character.',
            'query.max' => 'Search query cannot exceed 255 characters.',
            'per_page.integer' => 'Per page must be a whole number.',
            'per_page.min' => 'Per page must be at least 1.',
            'per_page.max' => 'Per page cannot exceed 100.',
            'sort_by.string' => 'Sort by must be a string.',
            'sort_by.in' => 'Invalid sort field.',
            'sort_direction.string' => 'Sort direction must be a string.',
            'sort_direction.in' => 'Sort direction must be either asc or desc.',
        ];
    }
}
