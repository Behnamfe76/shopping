<?php

namespace Fereydooni\Shopping\app\Http\Requests;

use Fereydooni\Shopping\app\Enums\InsuranceStatus;
use Fereydooni\Shopping\app\Enums\InsuranceType;
use Fereydooni\Shopping\app\Enums\VerificationStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreProviderInsuranceRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('create', \Fereydooni\Shopping\app\Models\ProviderInsurance::class);
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'provider_id' => [
                'required',
                'integer',
                'exists:providers,id',
            ],
            'insurance_type' => [
                'required',
                'string',
                Rule::in(InsuranceType::values()),
            ],
            'policy_number' => [
                'required',
                'string',
                'max:100',
                'unique:provider_insurances,policy_number',
            ],
            'provider_name' => [
                'required',
                'string',
                'max:255',
            ],
            'coverage_amount' => [
                'required',
                'numeric',
                'min:0',
                'max:999999999.99',
            ],
            'start_date' => [
                'required',
                'date',
                'before_or_equal:end_date',
            ],
            'end_date' => [
                'required',
                'date',
                'after:start_date',
            ],
            'status' => [
                'required',
                'string',
                Rule::in(InsuranceStatus::values()),
            ],
            'verification_status' => [
                'required',
                'string',
                Rule::in(VerificationStatus::values()),
            ],
            'notes' => [
                'nullable',
                'string',
                'max:1000',
            ],
            'documents' => [
                'nullable',
                'array',
            ],
            'documents.*' => [
                'nullable',
                'string',
                'max:500',
            ],
            'verified_by' => [
                'nullable',
                'integer',
                'exists:users,id',
            ],
            'verified_at' => [
                'nullable',
                'date',
            ],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'provider_id.required' => 'Provider ID is required.',
            'provider_id.exists' => 'The selected provider does not exist.',
            'insurance_type.required' => 'Insurance type is required.',
            'insurance_type.in' => 'The selected insurance type is invalid.',
            'policy_number.required' => 'Policy number is required.',
            'policy_number.unique' => 'This policy number is already in use.',
            'provider_name.required' => 'Insurance provider name is required.',
            'coverage_amount.required' => 'Coverage amount is required.',
            'coverage_amount.numeric' => 'Coverage amount must be a number.',
            'coverage_amount.min' => 'Coverage amount must be at least 0.',
            'start_date.required' => 'Start date is required.',
            'start_date.before_or_equal' => 'Start date must be before or equal to end date.',
            'end_date.required' => 'End date is required.',
            'end_date.after' => 'End date must be after start date.',
            'status.required' => 'Status is required.',
            'status.in' => 'The selected status is invalid.',
            'verification_status.required' => 'Verification status is required.',
            'verification_status.in' => 'The selected verification status is invalid.',
            'verified_by.exists' => 'The selected verifier does not exist.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'provider_id' => 'provider',
            'insurance_type' => 'insurance type',
            'policy_number' => 'policy number',
            'provider_name' => 'insurance provider name',
            'coverage_amount' => 'coverage amount',
            'start_date' => 'start date',
            'end_date' => 'end date',
            'status' => 'status',
            'verification_status' => 'verification status',
            'notes' => 'notes',
            'documents' => 'documents',
            'verified_by' => 'verifier',
            'verified_at' => 'verification date',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Set default values if not provided
        if (! $this->has('status')) {
            $this->merge(['status' => InsuranceStatus::PENDING]);
        }

        if (! $this->has('verification_status')) {
            $this->merge(['verification_status' => VerificationStatus::PENDING]);
        }

        // Set verified_by to current user if not provided and user is verifying
        if (! $this->has('verified_by') && $this->user() && $this->verification_status === VerificationStatus::VERIFIED) {
            $this->merge(['verified_by' => $this->user()->id]);
        }

        // Set verified_at if verification is being set to verified
        if ($this->verification_status === VerificationStatus::VERIFIED && ! $this->has('verified_at')) {
            $this->merge(['verified_at' => now()]);
        }
    }
}
