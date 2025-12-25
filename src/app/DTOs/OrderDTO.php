<?php

namespace Fereydooni\Shopping\app\DTOs;

use Fereydooni\Shopping\app\Enums\OrderStatus;
use Fereydooni\Shopping\app\Enums\PaymentStatus;
use Illuminate\Support\Carbon;
use Spatie\LaravelData\Data;

class OrderDTO extends Data
{
    public function __construct(
        public ?int $id,
        public int $user_id,
        public OrderStatus $status,
        public float $total_amount,
        public float $discount_amount,
        public float $shipping_amount,
        public PaymentStatus $payment_status,
        public ?string $payment_method,
        public int $shipping_address_id,
        public int $billing_address_id,
        public ?Carbon $placed_at,
        public ?string $notes,
        public ?string $tracking_number,
        public ?Carbon $estimated_delivery,
        public ?Carbon $actual_delivery,
        public float $tax_amount,
        public string $currency,
        public float $exchange_rate,
        public ?string $coupon_code,
        public float $coupon_discount,
        public float $subtotal,
        public float $grand_total,
        public ?Carbon $created_at = null,
        public ?Carbon $updated_at = null,
        public ?array $items = null,
        public ?array $shipping_address = null,
        public ?array $billing_address = null,
        public ?array $user = null,
        public ?array $notes_array = null,
    ) {}

    public static function fromModel($order): static
    {
        return new static(
            id: $order->id,
            user_id: $order->user_id,
            status: $order->status,
            total_amount: $order->total_amount,
            discount_amount: $order->discount_amount,
            shipping_amount: $order->shipping_amount,
            payment_status: $order->payment_status,
            payment_method: $order->payment_method,
            shipping_address_id: $order->shipping_address_id,
            billing_address_id: $order->billing_address_id,
            placed_at: $order->placed_at,
            notes: $order->notes,
            tracking_number: $order->tracking_number,
            estimated_delivery: $order->estimated_delivery,
            actual_delivery: $order->actual_delivery,
            tax_amount: $order->tax_amount,
            currency: $order->currency,
            exchange_rate: $order->exchange_rate,
            coupon_code: $order->coupon_code,
            coupon_discount: $order->coupon_discount,
            subtotal: $order->subtotal,
            grand_total: $order->grand_total,
            created_at: $order->created_at,
            updated_at: $order->updated_at,
            items: $order->relationLoaded('items') ? $order->items->toArray() : null,
            shipping_address: $order->relationLoaded('shippingAddress') ? $order->shippingAddress->toArray() : null,
            billing_address: $order->relationLoaded('billingAddress') ? $order->billingAddress->toArray() : null,
            user: $order->relationLoaded('user') ? $order->user->toArray() : null,
            notes_array: $order->getNotes(),
        );
    }

    public static function rules(): array
    {
        return [
            'user_id' => 'required|integer|exists:users,id',
            'status' => 'required|in:'.implode(',', array_column(OrderStatus::cases(), 'value')),
            'total_amount' => 'required|numeric|min:0',
            'discount_amount' => 'nullable|numeric|min:0',
            'shipping_amount' => 'nullable|numeric|min:0',
            'payment_status' => 'required|in:'.implode(',', array_column(PaymentStatus::cases(), 'value')),
            'payment_method' => 'nullable|string|max:100',
            'shipping_address_id' => 'required|integer|exists:addresses,id',
            'billing_address_id' => 'required|integer|exists:addresses,id',
            'placed_at' => 'nullable|date',
            'notes' => 'nullable|string',
            'tracking_number' => 'nullable|string|max:100',
            'estimated_delivery' => 'nullable|date|after:placed_at',
            'actual_delivery' => 'nullable|date|after:placed_at',
            'tax_amount' => 'nullable|numeric|min:0',
            'currency' => 'nullable|string|size:3',
            'exchange_rate' => 'nullable|numeric|min:0',
            'coupon_code' => 'nullable|string|max:50',
            'coupon_discount' => 'nullable|numeric|min:0',
            'subtotal' => 'nullable|numeric|min:0',
            'grand_total' => 'nullable|numeric|min:0',
        ];
    }

    public static function messages(): array
    {
        return [
            'user_id.required' => 'User ID is required',
            'user_id.exists' => 'Selected user does not exist',
            'status.required' => 'Order status is required',
            'status.in' => 'Invalid order status selected',
            'total_amount.required' => 'Total amount is required',
            'total_amount.numeric' => 'Total amount must be a number',
            'total_amount.min' => 'Total amount cannot be negative',
            'discount_amount.numeric' => 'Discount amount must be a number',
            'discount_amount.min' => 'Discount amount cannot be negative',
            'shipping_amount.numeric' => 'Shipping amount must be a number',
            'shipping_amount.min' => 'Shipping amount cannot be negative',
            'payment_status.required' => 'Payment status is required',
            'payment_status.in' => 'Invalid payment status selected',
            'payment_method.max' => 'Payment method cannot exceed 100 characters',
            'shipping_address_id.required' => 'Shipping address is required',
            'shipping_address_id.exists' => 'Selected shipping address does not exist',
            'billing_address_id.required' => 'Billing address is required',
            'billing_address_id.exists' => 'Selected billing address does not exist',
            'placed_at.date' => 'Invalid placed date format',
            'tracking_number.max' => 'Tracking number cannot exceed 100 characters',
            'estimated_delivery.date' => 'Invalid estimated delivery date format',
            'estimated_delivery.after' => 'Estimated delivery must be after order placement',
            'actual_delivery.date' => 'Invalid actual delivery date format',
            'actual_delivery.after' => 'Actual delivery must be after order placement',
            'tax_amount.numeric' => 'Tax amount must be a number',
            'tax_amount.min' => 'Tax amount cannot be negative',
            'currency.size' => 'Currency must be exactly 3 characters',
            'exchange_rate.numeric' => 'Exchange rate must be a number',
            'exchange_rate.min' => 'Exchange rate cannot be negative',
            'coupon_code.max' => 'Coupon code cannot exceed 50 characters',
            'coupon_discount.numeric' => 'Coupon discount must be a number',
            'coupon_discount.min' => 'Coupon discount cannot be negative',
            'subtotal.numeric' => 'Subtotal must be a number',
            'subtotal.min' => 'Subtotal cannot be negative',
            'grand_total.numeric' => 'Grand total must be a number',
            'grand_total.min' => 'Grand total cannot be negative',
        ];
    }

    /**
     * Get the order's status label.
     */
    public function getStatusLabel(): string
    {
        return $this->status->label();
    }

    /**
     * Get the order's payment status label.
     */
    public function getPaymentStatusLabel(): string
    {
        return $this->payment_status->label();
    }

    /**
     * Check if the order is pending.
     */
    public function isPending(): bool
    {
        return $this->status === OrderStatus::PENDING;
    }

    /**
     * Check if the order is paid.
     */
    public function isPaid(): bool
    {
        return $this->status === OrderStatus::PAID;
    }

    /**
     * Check if the order is shipped.
     */
    public function isShipped(): bool
    {
        return $this->status === OrderStatus::SHIPPED;
    }

    /**
     * Check if the order is completed.
     */
    public function isCompleted(): bool
    {
        return $this->status === OrderStatus::COMPLETED;
    }

    /**
     * Check if the order is cancelled.
     */
    public function isCancelled(): bool
    {
        return $this->status === OrderStatus::CANCELLED;
    }

    /**
     * Check if the order payment is unpaid.
     */
    public function isPaymentUnpaid(): bool
    {
        return $this->payment_status === PaymentStatus::UNPAID;
    }

    /**
     * Check if the order payment is paid.
     */
    public function isPaymentPaid(): bool
    {
        return $this->payment_status === PaymentStatus::PAID;
    }

    /**
     * Check if the order payment is refunded.
     */
    public function isPaymentRefunded(): bool
    {
        return $this->payment_status === PaymentStatus::REFUNDED;
    }

    /**
     * Get the order total in the specified currency.
     */
    public function getTotalInCurrency(string $currency): float
    {
        if ($this->currency === $currency) {
            return $this->grand_total;
        }

        return $this->grand_total * $this->exchange_rate;
    }

    /**
     * Get order notes as array.
     */
    public function getNotesArray(): array
    {
        return $this->notes_array ?? [];
    }

    /**
     * Get notes by type.
     */
    public function getNotesByType(string $type): array
    {
        $notes = $this->getNotesArray();

        return array_filter($notes, fn ($note) => $note['type'] === $type);
    }

    /**
     * Get financial summary.
     */
    public function getFinancialSummary(): array
    {
        return [
            'subtotal' => $this->subtotal,
            'tax_amount' => $this->tax_amount,
            'shipping_amount' => $this->shipping_amount,
            'discount_amount' => $this->discount_amount,
            'coupon_discount' => $this->coupon_discount,
            'grand_total' => $this->grand_total,
            'currency' => $this->currency,
        ];
    }
}
