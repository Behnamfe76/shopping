<?php

namespace Fereydooni\Shopping\app\Http\Requests;

use Fereydooni\Shopping\app\Models\UserSubscription;
use Illuminate\Foundation\Http\FormRequest;

class StoreUserSubscriptionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('create', UserSubscription::class);
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'user_id' => ['required', 'integer', 'exists:users,id'],
            'subscription_id' => ['required', 'integer', 'exists:subscriptions,id'],
            'order_id' => ['nullable', 'integer', 'exists:orders,id'],
            'start_date' => ['required', 'date', 'after_or_equal:today'],
            'end_date' => ['nullable', 'date', 'after:start_date'],
            'status' => ['required', 'string', 'in:active,cancelled,expired,trialing,paused'],
            'next_billing_date' => ['nullable', 'date', 'after_or_equal:start_date'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'user_id.required' => 'User is required.',
            'user_id.integer' => 'User ID must be a valid integer.',
            'user_id.exists' => 'Selected user does not exist.',
            'subscription_id.required' => 'Subscription is required.',
            'subscription_id.integer' => 'Subscription ID must be a valid integer.',
            'subscription_id.exists' => 'Selected subscription does not exist.',
            'order_id.integer' => 'Order ID must be a valid integer.',
            'order_id.exists' => 'Selected order does not exist.',
            'start_date.required' => 'Start date is required.',
            'start_date.date' => 'Start date must be a valid date.',
            'start_date.after_or_equal' => 'Start date must be today or in the future.',
            'end_date.date' => 'End date must be a valid date.',
            'end_date.after' => 'End date must be after start date.',
            'status.required' => 'Status is required.',
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
        // Ensure user_id is set to current user if not provided and user can only create own subscriptions
        if (! $this->has('user_id') && $this->user()->can('create.own', UserSubscription::class)) {
            $this->merge(['user_id' => $this->user()->id]);
        }

        // Set default status to trialing if not provided
        if (! $this->has('status')) {
            $this->merge(['status' => 'trialing']);
        }

        // Set start_date to today if not provided
        if (! $this->has('start_date')) {
            $this->merge(['start_date' => now()->format('Y-m-d')]);
        }
    }
}
