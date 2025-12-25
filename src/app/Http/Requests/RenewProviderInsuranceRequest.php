<?php

namespace Fereydooni\Shopping\app\Http\Requests;

use Fereydooni\Shopping\app\Enums\InsuranceStatus;
use Illuminate\Foundation\Http\FormRequest;

class RenewProviderInsuranceRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('renew', $this->route('providerInsurance'));
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'start_date' => [
                'required',
                'date',
                'after:today',
            ],
            'end_date' => [
                'required',
                'date',
                'after:start_date',
            ],
            'coverage_amount' => [
                'sometimes',
                'numeric',
                'min:0',
                'max:999999999.99',
            ],
            'provider_name' => [
                'sometimes',
                'string',
                'max:255',
            ],
            'policy_number' => [
                'sometimes',
                'string',
                'max:100',
            ],
            'notes' => [
                'nullable',
                'string',
                'max:1000',
            ],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'start_date.required' => 'Start date is required.',
            'start_date.after' => 'Start date must be in the future.',
            'end_date.required' => 'End date is required.',
            'end_date.after' => 'End date must be after start date.',
            'coverage_amount.numeric' => 'Coverage amount must be a number.',
            'coverage_amount.min' => 'Coverage amount must be at least 0.',
            'notes.max' => 'Notes cannot exceed 1000 characters.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'start_date' => 'start date',
            'end_date' => 'end date',
            'coverage_amount' => 'coverage amount',
            'provider_name' => 'insurance provider name',
            'policy_number' => 'policy number',
            'notes' => 'notes',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Set default status to active for renewal
        if (! $this->has('status')) {
            $this->merge(['status' => InsuranceStatus::ACTIVE]);
        }
    }
}
