<?php

namespace Fereydooni\Shopping\app\Http\Requests;

use Fereydooni\Shopping\app\Enums\InsuranceStatus;
use Fereydooni\Shopping\app\Enums\InsuranceType;
use Fereydooni\Shopping\app\Enums\VerificationStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SearchProviderInsuranceRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('search', \Fereydooni\Shopping\app\Models\ProviderInsurance::class);
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
                'max:255',
            ],
            'provider_id' => [
                'nullable',
                'integer',
                'exists:providers,id',
            ],
            'insurance_type' => [
                'nullable',
                'string',
                Rule::in(InsuranceType::values()),
            ],
            'status' => [
                'nullable',
                'string',
                Rule::in(InsuranceStatus::values()),
            ],
            'verification_status' => [
                'nullable',
                'string',
                Rule::in(VerificationStatus::values()),
            ],
            'start_date' => [
                'nullable',
                'date',
            ],
            'end_date' => [
                'nullable',
                'date',
                'after_or_equal:start_date',
            ],
            'min_coverage' => [
                'nullable',
                'numeric',
                'min:0',
            ],
            'max_coverage' => [
                'nullable',
                'numeric',
                'min:0',
                'gte:min_coverage',
            ],
            'sort_by' => [
                'nullable',
                'string',
                Rule::in(['created_at', 'updated_at', 'start_date', 'end_date', 'coverage_amount', 'status', 'verification_status']),
            ],
            'sort_direction' => [
                'nullable',
                'string',
                Rule::in(['asc', 'desc']),
            ],
            'per_page' => [
                'nullable',
                'integer',
                'min:1',
                'max:100',
            ],
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
            'provider_id.exists' => 'The selected provider does not exist.',
            'insurance_type.in' => 'The selected insurance type is invalid.',
            'status.in' => 'The selected status is invalid.',
            'verification_status.in' => 'The selected verification status is invalid.',
            'end_date.after_or_equal' => 'End date must be after or equal to start date.',
            'min_coverage.numeric' => 'Minimum coverage must be a number.',
            'min_coverage.min' => 'Minimum coverage must be at least 0.',
            'max_coverage.numeric' => 'Maximum coverage must be a number.',
            'max_coverage.min' => 'Maximum coverage must be at least 0.',
            'max_coverage.gte' => 'Maximum coverage must be greater than or equal to minimum coverage.',
            'sort_by.in' => 'The selected sort field is invalid.',
            'sort_direction.in' => 'Sort direction must be either ascending or descending.',
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
            'provider_id' => 'provider',
            'insurance_type' => 'insurance type',
            'status' => 'status',
            'verification_status' => 'verification status',
            'start_date' => 'start date',
            'end_date' => 'end date',
            'min_coverage' => 'minimum coverage',
            'max_coverage' => 'maximum coverage',
            'sort_by' => 'sort field',
            'sort_direction' => 'sort direction',
            'per_page' => 'per page',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Set default values
        if (! $this->has('sort_by')) {
            $this->merge(['sort_by' => 'created_at']);
        }

        if (! $this->has('sort_direction')) {
            $this->merge(['sort_direction' => 'desc']);
        }

        if (! $this->has('per_page')) {
            $this->merge(['per_page' => 15]);
        }
    }
}
