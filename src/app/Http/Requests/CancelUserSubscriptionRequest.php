<?php

namespace Fereydooni\Shopping\app\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CancelUserSubscriptionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $userSubscription = $this->route('userSubscription');

        return $this->user()->can('cancel', $userSubscription);
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'reason' => ['nullable', 'string', 'max:500'],
            'effective_date' => ['nullable', 'date', 'after_or_equal:today'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'reason.string' => 'Cancellation reason must be a string.',
            'reason.max' => 'Cancellation reason cannot exceed 500 characters.',
            'effective_date.date' => 'Effective date must be a valid date.',
            'effective_date.after_or_equal' => 'Effective date must be today or in the future.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'reason' => 'cancellation reason',
            'effective_date' => 'effective date',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        $userSubscription = $this->route('userSubscription');

        // Validate that the subscription can be cancelled
        if ($userSubscription && in_array($userSubscription->status->value, ['cancelled', 'expired'])) {
            $this->validator->errors()->add('status', 'Subscription is already cancelled or expired.');
        }

        // Set default effective date to today if not provided
        if (! $this->has('effective_date')) {
            $this->merge(['effective_date' => now()->format('Y-m-d')]);
        }
    }
}
