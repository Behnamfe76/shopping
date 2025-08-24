<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SearchPerformanceRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('search', 'App\Models\ProviderPerformance');
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'query' => [
                'required',
                'string',
                'min:2',
                'max:255'
            ],
            'provider_id' => [
                'sometimes',
                'integer',
                'exists:providers,id'
            ],
            'grade' => [
                'sometimes',
                'string',
                'in:A,B,C,D,F'
            ],
            'period_type' => [
                'sometimes',
                'string',
                'in:daily,weekly,monthly,quarterly,yearly'
            ],
            'verified' => [
                'sometimes',
                'boolean'
            ],
            'sort_by' => [
                'sometimes',
                'string',
                'in:performance_score,revenue,orders,created_at,updated_at'
            ],
            'sort_direction' => [
                'sometimes',
                'string',
                'in:asc,desc'
            ],
            'per_page' => [
                'sometimes',
                'integer',
                'min:1',
                'max:100'
            ]
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
            'provider_id.exists' => 'The selected provider does not exist.',
            'grade.in' => 'Invalid grade selected.',
            'period_type.in' => 'Invalid period type selected.',
            'verified.boolean' => 'Verified filter must be true or false.',
            'sort_by.in' => 'Invalid sort field selected.',
            'sort_direction.in' => 'Sort direction must be ascending or descending.',
            'per_page.min' => 'Per page must be at least 1.',
            'per_page.max' => 'Per page cannot exceed 100.'
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'query' => 'search query',
            'provider_id' => 'provider',
            'grade' => 'performance grade',
            'period_type' => 'period type',
            'verified' => 'verification status',
            'sort_by' => 'sort field',
            'sort_direction' => 'sort direction',
            'per_page' => 'results per page'
        ];
    }
}
