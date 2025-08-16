<?php

namespace Fereydooni\Shopping\app\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Fereydooni\Shopping\app\Models\Order;

class OrderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        /** @var Order $this->resource */
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'status' => $this->status,
            'total_amount' => $this->total_amount,
            'discount_amount' => $this->discount_amount,
            'shipping_amount' => $this->shipping_amount,
            'payment_status' => $this->payment_status,
            'payment_method' => $this->payment_method,
            'shipping_address_id' => $this->shipping_address_id,
            'billing_address_id' => $this->billing_address_id,
            'placed_at' => $this->placed_at?->toISOString(),
            'notes' => $this->when($this->notes, $this->notes),
            'tracking_number' => $this->when($this->tracking_number, $this->tracking_number),
            'estimated_delivery' => $this->when($this->estimated_delivery, $this->estimated_delivery?->toISOString()),
            'actual_delivery' => $this->when($this->actual_delivery, $this->actual_delivery?->toISOString()),
            'tax_amount' => $this->tax_amount,
            'currency' => $this->currency,
            'exchange_rate' => $this->exchange_rate,
            'coupon_code' => $this->when($this->coupon_code, $this->coupon_code),
            'coupon_discount' => $this->when($this->coupon_discount, $this->coupon_discount),
            'subtotal' => $this->subtotal,
            'grand_total' => $this->grand_total,
            'created_at' => $this->created_at->toISOString(),
            'updated_at' => $this->updated_at->toISOString(),

            // Relationships
            'user' => $this->whenLoaded('user', function () {
                return [
                    'id' => $this->user->id,
                    'name' => $this->user->name,
                    'email' => $this->user->email,
                ];
            }),

            'shipping_address' => $this->whenLoaded('shippingAddress', function () {
                return [
                    'id' => $this->shippingAddress->id,
                    'address_line_1' => $this->shippingAddress->address_line_1,
                    'address_line_2' => $this->shippingAddress->address_line_2,
                    'city' => $this->shippingAddress->city,
                    'state' => $this->shippingAddress->state,
                    'postal_code' => $this->shippingAddress->postal_code,
                    'country' => $this->shippingAddress->country,
                ];
            }),

            'billing_address' => $this->whenLoaded('billingAddress', function () {
                return [
                    'id' => $this->billingAddress->id,
                    'address_line_1' => $this->billingAddress->address_line_1,
                    'address_line_2' => $this->billingAddress->address_line_2,
                    'city' => $this->billingAddress->city,
                    'state' => $this->billingAddress->state,
                    'postal_code' => $this->billingAddress->postal_code,
                    'country' => $this->billingAddress->country,
                ];
            }),

            'order_items' => $this->whenLoaded('orderItems', function () {
                return $this->orderItems->map(function ($item) {
                    return [
                        'id' => $item->id,
                        'product_id' => $item->product_id,
                        'quantity' => $item->quantity,
                        'unit_price' => $item->unit_price,
                        'total_price' => $item->total_price,
                        'product' => $item->product ? [
                            'id' => $item->product->id,
                            'name' => $item->product->name,
                            'sku' => $item->product->sku,
                        ] : null,
                    ];
                });
            }),

            // Status indicators
            'is_pending' => $this->status === 'pending',
            'is_paid' => $this->status === 'paid',
            'is_shipped' => $this->status === 'shipped',
            'is_completed' => $this->status === 'completed',
            'is_cancelled' => $this->status === 'cancelled',

            'is_payment_pending' => $this->payment_status === 'pending',
            'is_payment_paid' => $this->payment_status === 'paid',
            'is_payment_failed' => $this->payment_status === 'failed',
            'is_payment_refunded' => $this->payment_status === 'refunded',
        ];
    }
}
