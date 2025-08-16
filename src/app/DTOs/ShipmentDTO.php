<?php

namespace Fereydooni\Shopping\app\DTOs;

use Spatie\LaravelData\Data;
use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Attributes\WithTransformer;
use Spatie\LaravelData\Transformers\DateTimeTransformer;
use Illuminate\Support\Carbon;
use Fereydooni\Shopping\app\Enums\ShipmentStatus;

class ShipmentDTO extends Data
{
    public function __construct(
        public ?int $id,
        public int $order_id,
        public string $carrier,
        public ?string $tracking_number,
        public ShipmentStatus $status,
        public ?Carbon $shipped_at,
        public ?Carbon $delivered_at,
        public ?Carbon $estimated_delivery,
        public ?Carbon $actual_delivery,
        public float $shipping_cost,
        public float $weight,
        public ?string $dimensions,
        public int $package_count,
        public bool $is_signature_required,
        public bool $is_insured,
        public float $insurance_amount,
        public ?string $notes,
        public ?int $created_by,
        public ?int $updated_by,
        public ?Carbon $created_at = null,
        public ?Carbon $updated_at = null,
        public ?array $order = null,
        public ?array $items = null,
    ) {
    }

    public static function fromModel($shipment): static
    {
        return new static(
            id: $shipment->id,
            order_id: $shipment->order_id,
            carrier: $shipment->carrier,
            tracking_number: $shipment->tracking_number,
            status: $shipment->status,
            shipped_at: $shipment->shipped_at,
            delivered_at: $shipment->delivered_at,
            estimated_delivery: $shipment->estimated_delivery,
            actual_delivery: $shipment->actual_delivery,
            shipping_cost: $shipment->shipping_cost,
            weight: $shipment->weight,
            dimensions: $shipment->dimensions,
            package_count: $shipment->package_count,
            is_signature_required: $shipment->is_signature_required,
            is_insured: $shipment->is_insured,
            insurance_amount: $shipment->insurance_amount,
            notes: $shipment->notes,
            created_by: $shipment->created_by,
            updated_by: $shipment->updated_by,
            created_at: $shipment->created_at,
            updated_at: $shipment->updated_at,
            order: $shipment->relationLoaded('order') ? $shipment->order->toArray() : null,
            items: $shipment->relationLoaded('items') ? $shipment->items->toArray() : null,
        );
    }

    public static function rules(): array
    {
        return [
            'order_id' => 'required|integer|exists:orders,id',
            'carrier' => 'required|string|max:100|in:fedex,ups,usps,dhl,amazon,other',
            'tracking_number' => 'nullable|string|max:100|unique:shipments,tracking_number',
            'status' => 'required|in:' . implode(',', array_column(ShipmentStatus::cases(), 'value')),
            'shipped_at' => 'nullable|date|before_or_equal:now',
            'delivered_at' => 'nullable|date|after:shipped_at',
            'estimated_delivery' => 'nullable|date|after:shipped_at',
            'actual_delivery' => 'nullable|date|after:shipped_at',
            'shipping_cost' => 'required|numeric|min:0',
            'weight' => 'required|numeric|min:0',
            'dimensions' => 'nullable|string|max:100',
            'package_count' => 'required|integer|min:1',
            'is_signature_required' => 'boolean',
            'is_insured' => 'boolean',
            'insurance_amount' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string|max:1000',
            'created_by' => 'nullable|integer|exists:users,id',
            'updated_by' => 'nullable|integer|exists:users,id',
        ];
    }

    public static function messages(): array
    {
        return [
            'order_id.required' => 'Order ID is required.',
            'order_id.exists' => 'The selected order does not exist.',
            'carrier.required' => 'Carrier is required.',
            'carrier.in' => 'The selected carrier is not valid.',
            'tracking_number.unique' => 'This tracking number is already in use.',
            'status.required' => 'Status is required.',
            'status.in' => 'The selected status is not valid.',
            'shipped_at.before_or_equal' => 'Shipped date cannot be in the future.',
            'delivered_at.after' => 'Delivered date must be after shipped date.',
            'estimated_delivery.after' => 'Estimated delivery must be after shipped date.',
            'actual_delivery.after' => 'Actual delivery must be after shipped date.',
            'shipping_cost.required' => 'Shipping cost is required.',
            'shipping_cost.min' => 'Shipping cost must be at least 0.',
            'weight.required' => 'Weight is required.',
            'weight.min' => 'Weight must be at least 0.',
            'package_count.required' => 'Package count is required.',
            'package_count.min' => 'Package count must be at least 1.',
            'insurance_amount.min' => 'Insurance amount must be at least 0.',
        ];
    }

    public static function carriers(): array
    {
        return [
            'fedex' => 'FedEx',
            'ups' => 'UPS',
            'usps' => 'USPS',
            'dhl' => 'DHL',
            'amazon' => 'Amazon',
            'other' => 'Other',
        ];
    }

    public function isPending(): bool
    {
        return $this->status === ShipmentStatus::PENDING;
    }

    public function isInTransit(): bool
    {
        return $this->status === ShipmentStatus::IN_TRANSIT;
    }

    public function isDelivered(): bool
    {
        return $this->status === ShipmentStatus::DELIVERED;
    }

    public function isReturned(): bool
    {
        return $this->status === ShipmentStatus::RETURNED;
    }

    public function isOverdue(): bool
    {
        if (!$this->estimated_delivery || $this->isDelivered()) {
            return false;
        }

        return $this->estimated_delivery->isPast();
    }

    public function isDelayed(): bool
    {
        if (!$this->estimated_delivery || $this->isDelivered()) {
            return false;
        }

        return $this->estimated_delivery->isPast() && !$this->isDelivered();
    }

    public function isOnTime(): bool
    {
        if (!$this->estimated_delivery || $this->isDelivered()) {
            return false;
        }

        return $this->estimated_delivery->isFuture() || $this->actual_delivery?->lte($this->estimated_delivery);
    }

    public function getCarrierLabel(): string
    {
        return self::carriers()[$this->carrier] ?? $this->carrier;
    }

    public function getStatusLabel(): string
    {
        return $this->status->label();
    }

    public function getTotalValue(): float
    {
        return $this->shipping_cost + $this->insurance_amount;
    }

    public function getDeliveryTime(): ?int
    {
        if (!$this->shipped_at || !$this->actual_delivery) {
            return null;
        }

        return $this->shipped_at->diffInDays($this->actual_delivery);
    }

    public function getEstimatedDeliveryTime(): ?int
    {
        if (!$this->shipped_at || !$this->estimated_delivery) {
            return null;
        }

        return $this->shipped_at->diffInDays($this->estimated_delivery);
    }
}
