<?php

namespace Fereydooni\Shopping\app\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateTransactionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('update', $this->route('transaction'));
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $transaction = $this->route('transaction');

        return [
            'order_id' => [
                'sometimes',
                'integer',
                'exists:orders,id',
            ],
            'user_id' => [
                'sometimes',
                'integer',
                'exists:users,id',
            ],
            'gateway' => [
                'sometimes',
                'string',
                'max:50',
                Rule::in(['stripe', 'paypal', 'square', 'braintree', 'adyen', 'razorpay', 'mollie', 'klarna']),
            ],
            'transaction_id' => [
                'sometimes',
                'string',
                'max:255',
                Rule::unique('transactions', 'transaction_id')->ignore($transaction->id),
            ],
            'amount' => [
                'sometimes',
                'numeric',
                'min:0.01',
                'max:999999.99',
            ],
            'currency' => [
                'sometimes',
                'string',
                'size:3',
                Rule::in(['USD', 'EUR', 'GBP', 'CAD', 'AUD', 'JPY', 'INR', 'BRL', 'MXN', 'SGD']),
            ],
            'status' => [
                'sometimes',
                'string',
                Rule::in(['initiated', 'success', 'failed', 'refunded']),
            ],
            'payment_date' => [
                'sometimes',
                'date',
                'before_or_equal:now',
            ],
            'response_data' => [
                'sometimes',
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
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'order_id.exists' => 'The selected order does not exist.',
            'user_id.exists' => 'The selected user does not exist.',
            'gateway.in' => 'The selected payment gateway is not supported.',
            'transaction_id.unique' => 'This transaction ID has already been used.',
            'amount.numeric' => 'Amount must be a number.',
            'amount.min' => 'Amount must be at least 0.01.',
            'amount.max' => 'Amount cannot exceed 999,999.99.',
            'currency.size' => 'Currency must be exactly 3 characters.',
            'currency.in' => 'The selected currency is not supported.',
            'status.in' => 'The selected status is not valid.',
            'payment_date.date' => 'Payment date must be a valid date.',
            'payment_date.before_or_equal' => 'Payment date cannot be in the future.',
            'response_data.array' => 'Response data must be an array.',
            'response_data.card_last4.regex' => 'Card last 4 digits must be exactly 4 numbers.',
            'response_data.card_exp_month.between' => 'Card expiration month must be between 1 and 12.',
            'response_data.card_exp_year.min' => 'Card expiration year cannot be in the past.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'order_id' => 'order ID',
            'user_id' => 'user ID',
            'gateway' => 'payment gateway',
            'transaction_id' => 'transaction ID',
            'amount' => 'amount',
            'currency' => 'currency',
            'status' => 'status',
            'payment_date' => 'payment date',
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
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Ensure amount is properly formatted
        if ($this->has('amount')) {
            $this->merge([
                'amount' => (float) $this->amount,
            ]);
        }

        // Ensure currency is uppercase
        if ($this->has('currency')) {
            $this->merge([
                'currency' => strtoupper($this->currency),
            ]);
        }

        // Ensure gateway is lowercase
        if ($this->has('gateway')) {
            $this->merge([
                'gateway' => strtolower($this->gateway),
            ]);
        }
    }
}
