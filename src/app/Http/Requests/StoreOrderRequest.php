<?php

namespace Fereydooni\Shopping\app\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreOrderRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('order.create');
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'user_id' => 'required|exists:users,id',
            'status' => 'required|string|in:pending,paid,shipped,completed,cancelled',
            'total_amount' => 'required|numeric|min:0',
            'discount_amount' => 'nullable|numeric|min:0',
            'shipping_amount' => 'nullable|numeric|min:0',
            'payment_status' => 'required|string|in:pending,paid,failed,refunded',
            'payment_method' => 'required|string|max:255',
            'shipping_address_id' => 'required|exists:addresses,id',
            'billing_address_id' => 'required|exists:addresses,id',
            'placed_at' => 'nullable|date',
            'notes' => 'nullable|string|max:1000',
            'tracking_number' => 'nullable|string|max:255',
            'estimated_delivery' => 'nullable|date|after:today',
            'actual_delivery' => 'nullable|date',
            'tax_amount' => 'nullable|numeric|min:0',
            'currency' => 'required|string|max:3',
            'exchange_rate' => 'nullable|numeric|min:0',
            'coupon_code' => 'nullable|string|max:50',
            'coupon_discount' => 'nullable|numeric|min:0',
            'subtotal' => 'required|numeric|min:0',
            'grand_total' => 'required|numeric|min:0',
            'order_items' => 'required|array|min:1',
            'order_items.*.product_id' => 'required|exists:products,id',
            'order_items.*.quantity' => 'required|integer|min:1',
            'order_items.*.unit_price' => 'required|numeric|min:0',
            'order_items.*.total_price' => 'required|numeric|min:0',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'user_id.required' => 'User ID is required.',
            'user_id.exists' => 'The selected user does not exist.',
            'status.required' => 'Order status is required.',
            'status.in' => 'Invalid order status.',
            'total_amount.required' => 'Total amount is required.',
            'total_amount.numeric' => 'Total amount must be a number.',
            'total_amount.min' => 'Total amount must be at least 0.',
            'payment_status.required' => 'Payment status is required.',
            'payment_status.in' => 'Invalid payment status.',
            'payment_method.required' => 'Payment method is required.',
            'shipping_address_id.required' => 'Shipping address is required.',
            'shipping_address_id.exists' => 'The selected shipping address does not exist.',
            'billing_address_id.required' => 'Billing address is required.',
            'billing_address_id.exists' => 'The selected billing address does not exist.',
            'currency.required' => 'Currency is required.',
            'currency.max' => 'Currency code must not exceed 3 characters.',
            'subtotal.required' => 'Subtotal is required.',
            'grand_total.required' => 'Grand total is required.',
            'order_items.required' => 'Order items are required.',
            'order_items.min' => 'At least one order item is required.',
            'order_items.*.product_id.required' => 'Product ID is required for each item.',
            'order_items.*.product_id.exists' => 'The selected product does not exist.',
            'order_items.*.quantity.required' => 'Quantity is required for each item.',
            'order_items.*.quantity.integer' => 'Quantity must be a whole number.',
            'order_items.*.quantity.min' => 'Quantity must be at least 1.',
            'order_items.*.unit_price.required' => 'Unit price is required for each item.',
            'order_items.*.unit_price.numeric' => 'Unit price must be a number.',
            'order_items.*.unit_price.min' => 'Unit price must be at least 0.',
            'order_items.*.total_price.required' => 'Total price is required for each item.',
            'order_items.*.total_price.numeric' => 'Total price must be a number.',
            'order_items.*.total_price.min' => 'Total price must be at least 0.',
        ];
    }
}
