<?php

namespace Fereydooni\Shopping\app\Http\Requests;

use Fereydooni\Shopping\app\Models\UserSubscription;
use Illuminate\Foundation\Http\FormRequest;

class UpdateUserSubscriptionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $userSubscription = $this->route('userSubscription');

        return $this->user()->can('update', $userSubscription);
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'user_id' => ['sometimes', 'integer', 'exists:users,id'],
            'subscription_id' => ['sometimes', 'integer', 'exists:subscriptions,id'],
            'order_id' => ['nullable', 'integer', 'exists:orders,id'],
            'start_date' => ['sometimes', 'date'],
            'end_date' => ['nullable', 'date', 'after:start_date'],
            'status' => ['sometimes', 'string', 'in:active,cancelled,expired,trialing,paused'],
            'next_billing_date' => ['nullable', 'date', 'after_or_equal:start_date'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'user_id.integer' => 'User ID must be a valid integer.',
            'user_id.exists' => 'Selected user does not exist.',
            'subscription_id.integer' => 'Subscription ID must be a valid integer.',
            'subscription_id.exists' => 'Selected subscription does not exist.',
            'order_id.integer' => 'Order ID must be a valid integer.',
            'order_id.exists' => 'Selected order does not exist.',
            'start_date.date' => 'Start date must be a valid date.',
            'end_date.date' => 'End date must be a valid date.',
            'end_date.after' => 'End date must be after start date.',
            'status.in' => 'Invalid status selected.',
            'next_billing_date.date' => 'Next billing date must be a valid date.',
            'next_billing_date.after_or_equal' => 'Next billing date must be after or equal to start date.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'user_id' => 'user',
            'subscription_id' => 'subscription',
            'order_id' => 'order',
            'start_date' => 'start date',
            'end_date' => 'end date',
            'status' => 'status',
            'next_billing_date' => 'next billing date',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        $userSubscription = $this->route('userSubscription');

        // Ensure user can only update own subscriptions if they don't have update.any permission
        if (! $this->user()->can('update.any', UserSubscription::class) &&
            $this->user()->can('update.own', UserSubscription::class)) {
            $this->merge(['user_id' => $this->user()->id]);
        }

        // Validate status transitions
        if ($this->has('status') && $userSubscription) {
            $this->validateStatusTransition($userSubscription, $this->get('status'));
        }
    }

    /**
     * Validate status transition.
     */
    private function validateStatusTransition(UserSubscription $userSubscription, string $newStatus): void
    {
        $currentStatus = $userSubscription->status->value;
        $allowedTransitions = [
            'trialing' => ['active', 'cancelled', 'expired'],
            'active' => ['cancelled', 'expired', 'paused'],
            'paused' => ['active', 'cancelled', 'expired'],
            'cancelled' => [],
            'expired' => ['active'],
        ];

        if (! in_array($newStatus, $allowedTransitions[$currentStatus] ?? [])) {
            $this->validator->errors()->add('status', "Cannot transition from {$currentStatus} to {$newStatus}.");
        }
    }
}
