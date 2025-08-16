<?php

namespace Fereydooni\Shopping\app\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreShipmentItemRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('shipment-item.create');
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'order_item_id' => 'required|integer|exists:order_items,id',
            'quantity' => 'required|integer|min:1',
            'total_weight' => 'nullable|numeric|min:0',
            'total_volume' => 'nullable|numeric|min:0',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'order_item_id.required' => 'Order item ID is required.',
            'order_item_id.integer' => 'Order item ID must be a whole number.',
            'order_item_id.exists' => 'The selected order item does not exist.',
            'quantity.required' => 'Quantity is required.',
            'quantity.integer' => 'Quantity must be a whole number.',
            'quantity.min' => 'Quantity must be at least 1.',
            'total_weight.numeric' => 'Total weight must be a number.',
            'total_weight.min' => 'Total weight must be at least 0.',
            'total_volume.numeric' => 'Total volume must be a number.',
            'total_volume.min' => 'Total volume must be at least 0.',
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            // Check if the order item is available for shipping
            if ($this->has('order_item_id') && $this->has('quantity')) {
                $orderItemId = $this->input('order_item_id');
                $quantity = $this->input('quantity');

                // This would typically check against the service
                // For now, we'll add a basic validation
                if ($quantity > 1000) {
                    $validator->errors()->add('quantity', 'Quantity cannot exceed 1000.');
                }
            }
        });
    }
}
