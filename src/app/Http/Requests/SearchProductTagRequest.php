<?php

namespace Fereydooni\Shopping\app\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SearchProductTagRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('search', \Fereydooni\Shopping\app\Models\ProductTag::class);
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'query' => ['required', 'string', 'min:1', 'max:255'],
            'status' => ['sometimes', 'string', 'in:active,inactive,featured'],
            'color' => ['sometimes', 'string', 'max:7'],
            'icon' => ['sometimes', 'string', 'max:50'],
            'min_usage' => ['sometimes', 'integer', 'min:0'],
            'max_usage' => ['sometimes', 'integer', 'min:0'],
            'sort_by' => ['sometimes', 'string', 'in:name,slug,usage_count,created_at,updated_at'],
            'sort_order' => ['sometimes', 'string', 'in:asc,desc'],
            'limit' => ['sometimes', 'integer', 'min:1', 'max:100'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'query.required' => 'Search query is required.',
            'query.min' => 'Search query must be at least 1 character.',
            'query.max' => 'Search query cannot exceed 255 characters.',
            'status.in' => 'Status must be active, inactive, or featured.',
            'color.max' => 'Color cannot exceed 7 characters.',
            'icon.max' => 'Icon cannot exceed 50 characters.',
            'min_usage.min' => 'Minimum usage must be a positive number.',
            'max_usage.min' => 'Maximum usage must be a positive number.',
            'sort_by.in' => 'Sort by must be name, slug, usage_count, created_at, or updated_at.',
            'sort_order.in' => 'Sort order must be asc or desc.',
            'limit.min' => 'Limit must be at least 1.',
            'limit.max' => 'Limit cannot exceed 100.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'query' => 'search query',
            'status' => 'status',
            'color' => 'color',
            'icon' => 'icon',
            'min_usage' => 'minimum usage',
            'max_usage' => 'maximum usage',
            'sort_by' => 'sort by',
            'sort_order' => 'sort order',
            'limit' => 'limit',
        ];
    }
}
