<?php

namespace Fereydooni\Shopping\app\DTOs;

use Spatie\LaravelData\Data;
use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Attributes\WithTransformer;
use Spatie\LaravelData\Transformers\DateTimeTransformer;
use Illuminate\Support\Carbon;
use Fereydooni\Shopping\app\Models\OrderItem;

class OrderItemDTO extends Data
{
    public function __construct(
        public ?int $id,
        public int $order_id,
        public int $product_id,
        public ?int $variant_id,
        public int $quantity,
        public float $price,
        public float $subtotal,
        public float $discount_amount,
        public float $tax_amount,
        public float $total_amount,
        public float $weight,
        public ?string $dimensions,
        public string $sku,
        public string $product_name,
        public ?string $variant_name,
        public ?string $notes,
        public bool $is_shipped,
        public int $shipped_quantity,
        public int $returned_quantity,
        public float $refunded_amount,
        public ?Carbon $created_at = null,
        public ?Carbon $updated_at = null,
        public ?array $order = null,
        public ?array $product = null,
        public ?array $variant = null,
        public ?array $shipment_items = null,
    ) {
    }

    public static function fromModel($orderItem): static
    {
        return new static(
            id: $orderItem->id,
            order_id: $orderItem->order_id,
            product_id: $orderItem->product_id,
            variant_id: $orderItem->variant_id,
            quantity: $orderItem->quantity,
            price: $orderItem->price,
            subtotal: $orderItem->subtotal,
            discount_amount: $orderItem->discount_amount ?? 0.0,
            tax_amount: $orderItem->tax_amount ?? 0.0,
            total_amount: $orderItem->total_amount ?? $orderItem->subtotal,
            weight: $orderItem->weight ?? 0.0,
            dimensions: $orderItem->dimensions,
            sku: $orderItem->sku,
            product_name: $orderItem->product_name,
            variant_name: $orderItem->variant_name,
            notes: $orderItem->notes,
            is_shipped: $orderItem->is_shipped ?? false,
            shipped_quantity: $orderItem->shipped_quantity ?? 0,
            returned_quantity: $orderItem->returned_quantity ?? 0,
            refunded_amount: $orderItem->refunded_amount ?? 0.0,
            created_at: $orderItem->created_at,
            updated_at: $orderItem->updated_at,
            order: $orderItem->relationLoaded('order') ? $orderItem->order->toArray() : null,
            product: $orderItem->relationLoaded('product') ? $orderItem->product->toArray() : null,
            variant: $orderItem->relationLoaded('variant') ? $orderItem->variant->toArray() : null,
            shipment_items: $orderItem->relationLoaded('shipmentItems') ? $orderItem->shipmentItems->toArray() : null,
        );
    }

    public static function rules(): array
    {
        return [
            'order_id' => 'required|integer|exists:orders,id',
            'product_id' => 'required|integer|exists:products,id',
            'variant_id' => 'nullable|integer|exists:product_variants,id',
            'quantity' => 'required|integer|min:1',
            'price' => 'required|numeric|min:0',
            'subtotal' => 'required|numeric|min:0',
            'discount_amount' => 'nullable|numeric|min:0',
            'tax_amount' => 'nullable|numeric|min:0',
            'total_amount' => 'required|numeric|min:0',
            'weight' => 'nullable|numeric|min:0',
            'dimensions' => 'nullable|string|max:255',
            'sku' => 'required|string|max:100',
            'product_name' => 'required|string|max:255',
            'variant_name' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
            'is_shipped' => 'boolean',
            'shipped_quantity' => 'integer|min:0',
            'returned_quantity' => 'integer|min:0',
            'refunded_amount' => 'numeric|min:0',
        ];
    }

    public static function messages(): array
    {
        return [
            'order_id.required' => 'Order ID is required.',
            'order_id.exists' => 'The selected order does not exist.',
            'product_id.required' => 'Product ID is required.',
            'product_id.exists' => 'The selected product does not exist.',
            'variant_id.exists' => 'The selected variant does not exist.',
            'quantity.required' => 'Quantity is required.',
            'quantity.min' => 'Quantity must be at least 1.',
            'price.required' => 'Price is required.',
            'price.min' => 'Price must be greater than or equal to 0.',
            'subtotal.required' => 'Subtotal is required.',
            'subtotal.min' => 'Subtotal must be greater than or equal to 0.',
            'total_amount.required' => 'Total amount is required.',
            'total_amount.min' => 'Total amount must be greater than or equal to 0.',
            'sku.required' => 'SKU is required.',
            'product_name.required' => 'Product name is required.',
        ];
    }

    public function calculateSubtotal(): float
    {
        return $this->price * $this->quantity;
    }

    public function calculateTotalAmount(): float
    {
        return $this->subtotal - $this->discount_amount + $this->tax_amount;
    }

    public function getRemainingQuantity(): int
    {
        return $this->quantity - $this->shipped_quantity;
    }

    public function getReturnableQuantity(): int
    {
        return $this->shipped_quantity - $this->returned_quantity;
    }

    public function isFullyShipped(): bool
    {
        return $this->shipped_quantity >= $this->quantity;
    }

    public function isFullyReturned(): bool
    {
        return $this->returned_quantity >= $this->shipped_quantity;
    }

    public function isFullyRefunded(): bool
    {
        return $this->refunded_amount >= $this->total_amount;
    }

    public function getRefundableAmount(): float
    {
        return $this->total_amount - $this->refunded_amount;
    }
}
