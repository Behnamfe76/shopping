<?php

namespace Fereydooni\Shopping\app\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProcessTransactionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $transaction = $this->route('transaction');

        if ($this->isMethod('POST') && $this->route()->getName() === 'shopping.transactions.success') {
            return $this->user()->can('markAsSuccess', $transaction);
        }

        if ($this->isMethod('POST') && $this->route()->getName() === 'shopping.transactions.failed') {
            return $this->user()->can('markAsFailed', $transaction);
        }

        if ($this->isMethod('POST') && $this->route()->getName() === 'shopping.transactions.refund') {
            return $this->user()->can('markAsRefunded', $transaction);
        }

        return $this->user()->can('process', $transaction);
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'response_data' => [
                'nullable',
                'array',
            ],
            'response_data.gateway_response' => [
                'nullable',
                'string',
            ],
            'response_data.error_code' => [
                'nullable',
                'string',
                'max:50',
            ],
            'response_data.error_message' => [
                'nullable',
                'string',
                'max:500',
            ],
            'response_data.transaction_reference' => [
                'nullable',
                'string',
                'max:255',
            ],
            'response_data.payment_method' => [
                'nullable',
                'string',
                'max:100',
            ],
            'response_data.card_last4' => [
                'nullable',
                'string',
                'size:4',
                'regex:/^[0-9]{4}$/',
            ],
            'response_data.card_brand' => [
                'nullable',
                'string',
                'max:50',
            ],
            'response_data.card_exp_month' => [
                'nullable',
                'integer',
                'between:1,12',
            ],
            'response_data.card_exp_year' => [
                'nullable',
                'integer',
                'min:'.date('Y'),
            ],
            'response_data.billing_address' => [
                'nullable',
                'array',
            ],
            'response_data.billing_address.line1' => [
                'nullable',
                'string',
                'max:255',
            ],
            'response_data.billing_address.line2' => [
                'nullable',
                'string',
                'max:255',
            ],
            'response_data.billing_address.city' => [
                'nullable',
                'string',
                'max:100',
            ],
            'response_data.billing_address.state' => [
                'nullable',
                'string',
                'max:100',
            ],
            'response_data.billing_address.postal_code' => [
                'nullable',
                'string',
                'max:20',
            ],
            'response_data.billing_address.country' => [
                'nullable',
                'string',
                'size:2',
            ],
            'response_data.shipping_address' => [
                'nullable',
                'array',
            ],
            'response_data.shipping_address.line1' => [
                'nullable',
                'string',
                'max:255',
            ],
            'response_data.shipping_address.line2' => [
                'nullable',
                'string',
                'max:255',
            ],
            'response_data.shipping_address.city' => [
                'nullable',
                'string',
                'max:100',
            ],
            'response_data.shipping_address.state' => [
                'nullable',
                'string',
                'max:100',
            ],
            'response_data.shipping_address.postal_code' => [
                'nullable',
                'string',
                'max:20',
            ],
            'response_data.shipping_address.country' => [
                'nullable',
                'string',
                'size:2',
            ],
            'response_data.metadata' => [
                'nullable',
                'array',
            ],
            'response_data.metadata.*' => [
                'nullable',
                'string',
                'max:255',
            ],
            'refund_amount' => [
                'nullable',
                'numeric',
                'min:0.01',
            ],
            'refund_reason' => [
                'nullable',
                'string',
                'max:500',
            ],
            'refund_method' => [
                'nullable',
                'string',
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
            'response_data.array' => 'Response data must be an array.',
            'response_data.card_last4.regex' => 'Card last 4 digits must be exactly 4 numbers.',
            'response_data.card_exp_month.between' => 'Card expiration month must be between 1 and 12.',
            'response_data.card_exp_year.min' => 'Card expiration year cannot be in the past.',
            'refund_amount.numeric' => 'Refund amount must be a number.',
            'refund_amount.min' => 'Refund amount must be at least 0.01.',
            'refund_reason.max' => 'Refund reason cannot exceed 500 characters.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'response_data' => 'response data',
            'response_data.gateway_response' => 'gateway response',
            'response_data.error_code' => 'error code',
            'response_data.error_message' => 'error message',
            'response_data.transaction_reference' => 'transaction reference',
            'response_data.payment_method' => 'payment method',
            'response_data.card_last4' => 'card last 4 digits',
            'response_data.card_brand' => 'card brand',
            'response_data.card_exp_month' => 'card expiration month',
            'response_data.card_exp_year' => 'card expiration year',
            'response_data.billing_address' => 'billing address',
            'response_data.shipping_address' => 'shipping address',
            'response_data.metadata' => 'metadata',
            'refund_amount' => 'refund amount',
            'refund_reason' => 'refund reason',
            'refund_method' => 'refund method',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Ensure refund amount is properly formatted
        if ($this->has('refund_amount')) {
            $this->merge([
                'refund_amount' => (float) $this->refund_amount,
            ]);
        }
    }
}
