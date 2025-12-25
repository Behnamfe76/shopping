<?php

namespace Fereydooni\Shopping\app\Http\Requests;

use Fereydooni\Shopping\app\Models\UserSubscription;
use Illuminate\Foundation\Http\FormRequest;

class SearchUserSubscriptionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('search', UserSubscription::class);
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'query' => ['required', 'string', 'min:2', 'max:255'],
            'user_id' => ['nullable', 'integer', 'exists:users,id'],
            'status' => ['nullable', 'string', 'in:active,cancelled,expired,trialing,paused'],
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
            'sort_by' => ['nullable', 'string', 'in:created_at,updated_at,start_date,end_date,next_billing_date'],
            'sort_direction' => ['nullable', 'string', 'in:asc,desc'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'query.required' => 'Search query is required.',
            'query.string' => 'Search query must be a string.',
            'query.min' => 'Search query must be at least 2 characters.',
            'query.max' => 'Search query cannot exceed 255 characters.',
            'user_id.integer' => 'User ID must be a valid integer.',
            'user_id.exists' => 'Selected user does not exist.',
            'status.in' => 'Invalid status selected.',
            'start_date.date' => 'Start date must be a valid date.',
            'end_date.date' => 'End date must be a valid date.',
            'end_date.after_or_equal' => 'End date must be after or equal to start date.',
            'per_page.integer' => 'Per page must be a valid integer.',
            'per_page.min' => 'Per page must be at least 1.',
            'per_page.max' => 'Per page cannot exceed 100.',
            'sort_by.in' => 'Invalid sort field selected.',
            'sort_direction.in' => 'Sort direction must be asc or desc.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'query' => 'search query',
            'user_id' => 'user',
            'status' => 'status',
            'start_date' => 'start date',
            'end_date' => 'end date',
            'per_page' => 'per page',
            'sort_by' => 'sort by',
            'sort_direction' => 'sort direction',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Ensure user can only search own subscriptions if they don't have search.any permission
        if (! $this->user()->can('search.any', UserSubscription::class) &&
            $this->user()->can('search.own', UserSubscription::class)) {
            $this->merge(['user_id' => $this->user()->id]);
        }

        // Set default values
        if (! $this->has('per_page')) {
            $this->merge(['per_page' => 15]);
        }

        if (! $this->has('sort_by')) {
            $this->merge(['sort_by' => 'created_at']);
        }

        if (! $this->has('sort_direction')) {
            $this->merge(['sort_direction' => 'desc']);
        }
    }
}
