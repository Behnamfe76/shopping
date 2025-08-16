<?php

namespace Fereydooni\Shopping\app\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Fereydooni\Shopping\app\Models\UserSubscription;

class ActivateUserSubscriptionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $userSubscription = $this->route('userSubscription');
        return $this->user()->can('activate', $userSubscription);
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            // No additional validation rules needed for activation
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            // No custom messages needed
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        $userSubscription = $this->route('userSubscription');

        // Validate that the subscription can be activated
        if ($userSubscription && !in_array($userSubscription->status->value, ['trialing', 'paused', 'expired'])) {
            $this->validator->errors()->add('status', 'Subscription cannot be activated from current status.');
        }
    }
}
