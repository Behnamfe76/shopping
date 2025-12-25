<?php

namespace Fereydooni\Shopping\app\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateOrderRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $order = $this->route('order');

        return $this->user()->can('update', $order);
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'user_id' => 'sometimes|exists:users,id',
            'status' => 'sometimes|string|in:pending,paid,shipped,completed,cancelled',
            'total_amount' => 'sometimes|numeric|min:0',
            'discount_amount' => 'nullable|numeric|min:0',
            'shipping_amount' => 'nullable|numeric|min:0',
            'payment_status' => 'sometimes|string|in:pending,paid,failed,refunded',
            'payment_method' => 'sometimes|string|max:255',
            'shipping_address_id' => 'sometimes|exists:addresses,id',
            'billing_address_id' => 'sometimes|exists:addresses,id',
            'placed_at' => 'nullable|date',
            'notes' => 'nullable|string|max:1000',
            'tracking_number' => 'nullable|string|max:255',
            'estimated_delivery' => 'nullable|date',
            'actual_delivery' => 'nullable|date',
            'tax_amount' => 'nullable|numeric|min:0',
            'currency' => 'sometimes|string|max:3',
            'exchange_rate' => 'nullable|numeric|min:0',
            'coupon_code' => 'nullable|string|max:50',
            'coupon_discount' => 'nullable|numeric|min:0',
            'subtotal' => 'sometimes|numeric|min:0',
            'grand_total' => 'sometimes|numeric|min:0',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'user_id.exists' => 'The selected user does not exist.',
            'status.in' => 'Invalid order status.',
            'total_amount.numeric' => 'Total amount must be a number.',
            'total_amount.min' => 'Total amount must be at least 0.',
            'payment_status.in' => 'Invalid payment status.',
            'shipping_address_id.exists' => 'The selected shipping address does not exist.',
            'billing_address_id.exists' => 'The selected billing address does not exist.',
            'currency.max' => 'Currency code must not exceed 3 characters.',
            'subtotal.numeric' => 'Subtotal must be a number.',
            'grand_total.numeric' => 'Grand total must be a number.',
        ];
    }
}
