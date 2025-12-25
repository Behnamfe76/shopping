<?php

namespace Fereydooni\Shopping\app\Http\Requests;

use Fereydooni\Shopping\app\Enums\InsuranceStatus;
use Fereydooni\Shopping\app\Enums\InsuranceType;
use Fereydooni\Shopping\app\Enums\VerificationStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProviderInsuranceRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('update', $this->route('providerInsurance'));
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $providerInsurance = $this->route('providerInsurance');
        $providerInsuranceId = $providerInsurance ? $providerInsurance->id : null;

        return [
            'provider_id' => [
                'sometimes',
                'integer',
                'exists:providers,id',
            ],
            'insurance_type' => [
                'sometimes',
                'string',
                Rule::in(InsuranceType::values()),
            ],
            'policy_number' => [
                'sometimes',
                'string',
                'max:100',
                Rule::unique('provider_insurances', 'policy_number')->ignore($providerInsuranceId),
            ],
            'provider_name' => [
                'sometimes',
                'string',
                'max:255',
            ],
            'coverage_amount' => [
                'sometimes',
                'numeric',
                'min:0',
                'max:999999999.99',
            ],
            'start_date' => [
                'sometimes',
                'date',
                'before_or_equal:end_date',
            ],
            'end_date' => [
                'sometimes',
                'date',
                'after:start_date',
            ],
            'status' => [
                'sometimes',
                'string',
                Rule::in(InsuranceStatus::values()),
            ],
            'verification_status' => [
                'sometimes',
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
            'provider_id.exists' => 'The selected provider does not exist.',
            'insurance_type.in' => 'The selected insurance type is invalid.',
            'policy_number.unique' => 'This policy number is already in use.',
            'coverage_amount.numeric' => 'Coverage amount must be a number.',
            'coverage_amount.min' => 'Coverage amount must be at least 0.',
            'start_date.before_or_equal' => 'Start date must be before or equal to end date.',
            'end_date.after' => 'End date must be after start date.',
            'status.in' => 'The selected status is invalid.',
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
        // Set verified_by to current user if verification status is being set to verified
        if ($this->has('verification_status') &&
            $this->verification_status === VerificationStatus::VERIFIED &&
            ! $this->has('verified_by')) {
            $this->merge(['verified_by' => $this->user()->id]);
        }

        // Set verified_at if verification is being set to verified
        if ($this->has('verification_status') &&
            $this->verification_status === VerificationStatus::VERIFIED &&
            ! $this->has('verified_at')) {
            $this->merge(['verified_at' => now()]);
        }

        // Clear verified_by and verified_at if verification status is being set to pending or rejected
        if ($this->has('verification_status') &&
            in_array($this->verification_status, [VerificationStatus::PENDING, VerificationStatus::REJECTED])) {
            $this->merge([
                'verified_by' => null,
                'verified_at' => null,
            ]);
        }
    }
}
