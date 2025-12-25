<?php

namespace Fereydooni\Shopping\app\DTOs;

use Illuminate\Support\Carbon;
use Spatie\LaravelData\Data;

class ShipmentItemDTO extends Data
{
    public function __construct(
        public ?int $id,
        public int $shipment_id,
        public int $order_item_id,
        public int $quantity,
        public ?float $total_weight = null,
        public ?float $total_volume = null,
        public ?Carbon $created_at = null,
        public ?Carbon $updated_at = null,
        public ?array $shipment = null,
        public ?array $order_item = null,
    ) {}

    public static function fromModel($shipmentItem): static
    {
        return new static(
            id: $shipmentItem->id,
            shipment_id: $shipmentItem->shipment_id,
            order_item_id: $shipmentItem->order_item_id,
            quantity: $shipmentItem->quantity,
            total_weight: $shipmentItem->total_weight ?? null,
            total_volume: $shipmentItem->total_volume ?? null,
            created_at: $shipmentItem->created_at,
            updated_at: $shipmentItem->updated_at,
            shipment: $shipmentItem->relationLoaded('shipment') ? $shipmentItem->shipment->toArray() : null,
            order_item: $shipmentItem->relationLoaded('orderItem') ? $shipmentItem->orderItem->toArray() : null,
        );
    }

    public static function rules(): array
    {
        return [
            'shipment_id' => 'required|integer|exists:shipments,id',
            'order_item_id' => 'required|integer|exists:order_items,id',
            'quantity' => 'required|integer|min:1',
            'total_weight' => 'nullable|numeric|min:0',
            'total_volume' => 'nullable|numeric|min:0',
        ];
    }

    public static function messages(): array
    {
        return [
            'shipment_id.required' => 'Shipment ID is required.',
            'shipment_id.exists' => 'The selected shipment does not exist.',
            'order_item_id.required' => 'Order item ID is required.',
            'order_item_id.exists' => 'The selected order item does not exist.',
            'quantity.required' => 'Quantity is required.',
            'quantity.min' => 'Quantity must be at least 1.',
            'total_weight.min' => 'Total weight must be greater than or equal to 0.',
            'total_volume.min' => 'Total volume must be greater than or equal to 0.',
        ];
    }

    public function calculateTotalWeight(): float
    {
        if ($this->order_item && isset($this->order_item['weight'])) {
            return $this->order_item['weight'] * $this->quantity;
        }

        return $this->total_weight ?? 0.0;
    }

    public function calculateTotalVolume(): float
    {
        if ($this->order_item && isset($this->order_item['dimensions'])) {
            // Simple volume calculation based on dimensions
            // This is a placeholder - actual calculation would depend on dimension format
            return $this->total_volume ?? 0.0;
        }

        return $this->total_volume ?? 0.0;
    }

    public function getRemainingQuantity(): int
    {
        if ($this->order_item && isset($this->order_item['quantity'])) {
            return $this->order_item['quantity'] - $this->quantity;
        }

        return 0;
    }

    public function isFullyShipped(): bool
    {
        if ($this->order_item && isset($this->order_item['quantity'])) {
            return $this->quantity >= $this->order_item['quantity'];
        }

        return false;
    }

    public function getShippedPercentage(): float
    {
        if ($this->order_item && isset($this->order_item['quantity']) && $this->order_item['quantity'] > 0) {
            return ($this->quantity / $this->order_item['quantity']) * 100;
        }

        return 0.0;
    }

    public function validateQuantityAvailability(): bool
    {
        if ($this->order_item && isset($this->order_item['quantity'])) {
            return $this->quantity <= $this->order_item['quantity'];
        }

        return true;
    }

    public function getShipmentItemSummary(): array
    {
        return [
            'id' => $this->id,
            'shipment_id' => $this->shipment_id,
            'order_item_id' => $this->order_item_id,
            'quantity' => $this->quantity,
            'total_weight' => $this->calculateTotalWeight(),
            'total_volume' => $this->calculateTotalVolume(),
            'remaining_quantity' => $this->getRemainingQuantity(),
            'is_fully_shipped' => $this->isFullyShipped(),
            'shipped_percentage' => $this->getShippedPercentage(),
        ];
    }
}
