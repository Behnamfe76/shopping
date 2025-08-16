<?php

namespace Fereydooni\Shopping\app\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Fereydooni\Shopping\app\Models\Transaction;

class SearchTransactionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('transaction.search');
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'query' => 'nullable|string|max:255',
            'order_id' => 'nullable|integer|exists:orders,id',
            'user_id' => 'nullable|integer|exists:users,id',
            'gateway' => 'nullable|string|max:100',
            'status' => [
                'nullable',
                'string',
                Rule::in(['initiated', 'success', 'failed', 'refunded'])
            ],
            'currency' => 'nullable|string|size:3',
            'min_amount' => 'nullable|numeric|min:0',
            'max_amount' => 'nullable|numeric|min:0|gte:min_amount',
            'start_date' => 'nullable|date|before_or_equal:end_date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'transaction_id' => 'nullable|string|max:255',
            'per_page' => 'nullable|integer|min:1|max:100',
            'sort_by' => [
                'nullable',
                'string',
                Rule::in(['id', 'order_id', 'user_id', 'amount', 'status', 'payment_date', 'created_at', 'updated_at'])
            ],
            'sort_direction' => [
                'nullable',
                'string',
                Rule::in(['asc', 'desc'])
            ],
            'include_relationships' => 'nullable|array',
            'include_relationships.*' => [
                'nullable',
                'string',
                Rule::in(['order', 'user', 'order.items', 'order.statusHistory'])
            ]
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'query.max' => 'Search query cannot exceed 255 characters.',
            'order_id.exists' => 'The selected order does not exist.',
            'user_id.exists' => 'The selected user does not exist.',
            'gateway.max' => 'Gateway name cannot exceed 100 characters.',
            'status.in' => 'Status must be one of: initiated, success, failed, refunded.',
            'currency.size' => 'Currency must be exactly 3 characters.',
            'min_amount.numeric' => 'Minimum amount must be a number.',
            'min_amount.min' => 'Minimum amount cannot be negative.',
            'max_amount.numeric' => 'Maximum amount must be a number.',
            'max_amount.min' => 'Maximum amount cannot be negative.',
            'max_amount.gte' => 'Maximum amount must be greater than or equal to minimum amount.',
            'start_date.date' => 'Start date must be a valid date.',
            'start_date.before_or_equal' => 'Start date must be before or equal to end date.',
            'end_date.date' => 'End date must be a valid date.',
            'end_date.after_or_equal' => 'End date must be after or equal to start date.',
            'transaction_id.max' => 'Transaction ID cannot exceed 255 characters.',
            'per_page.integer' => 'Per page must be an integer.',
            'per_page.min' => 'Per page must be at least 1.',
            'per_page.max' => 'Per page cannot exceed 100.',
            'sort_by.in' => 'Invalid sort field.',
            'sort_direction.in' => 'Sort direction must be either asc or desc.',
            'include_relationships.array' => 'Include relationships must be an array.',
            'include_relationships.*.in' => 'Invalid relationship to include.'
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'query' => 'search query',
            'order_id' => 'order ID',
            'user_id' => 'user ID',
            'gateway' => 'payment gateway',
            'status' => 'transaction status',
            'currency' => 'currency',
            'min_amount' => 'minimum amount',
            'max_amount' => 'maximum amount',
            'start_date' => 'start date',
            'end_date' => 'end date',
            'transaction_id' => 'transaction ID',
            'per_page' => 'per page',
            'sort_by' => 'sort field',
            'sort_direction' => 'sort direction',
            'include_relationships' => 'include relationships'
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'query' => $this->query ? trim($this->query) : null,
            'gateway' => $this->gateway ? trim($this->gateway) : null,
            'currency' => $this->currency ? strtoupper(trim($this->currency)) : null,
            'transaction_id' => $this->transaction_id ? trim($this->transaction_id) : null,
            'sort_direction' => $this->sort_direction ? strtolower($this->sort_direction) : 'desc',
            'per_page' => $this->per_page ? (int) $this->per_page : 15
        ]);
    }

    /**
     * Get validated data with defaults.
     */
    public function validated($key = null, $default = null): mixed
    {
        $validated = parent::validated($key, $default);

        // Set defaults
        $validated['sort_by'] = $validated['sort_by'] ?? 'created_at';
        $validated['sort_direction'] = $validated['sort_direction'] ?? 'desc';
        $validated['per_page'] = $validated['per_page'] ?? 15;

        return $validated;
    }
}
