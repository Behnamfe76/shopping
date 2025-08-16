<?php

namespace Fereydooni\Shopping\app\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Fereydooni\Shopping\app\Models\ShipmentItem;

class ShipmentItemResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        /** @var ShipmentItem $this->resource */
        return [
            'id' => $this->id,
            'shipment_id' => $this->shipment_id,
            'order_item_id' => $this->order_item_id,
            'quantity' => $this->quantity,
            'total_weight' => $this->when($this->total_weight, $this->total_weight),
            'total_volume' => $this->when($this->total_volume, $this->total_volume),
            'created_at' => $this->created_at->toISOString(),
            'updated_at' => $this->updated_at->toISOString(),

            // Relationships
            'shipment' => $this->whenLoaded('shipment', function () {
                return [
                    'id' => $this->shipment->id,
                    'tracking_number' => $this->shipment->tracking_number,
                    'status' => $this->shipment->status,
                    'carrier' => $this->shipment->carrier,
                    'shipped_at' => $this->shipment->shipped_at?->toISOString(),
                    'estimated_delivery' => $this->shipment->estimated_delivery?->toISOString(),
                    'actual_delivery' => $this->shipment->actual_delivery?->toISOString(),
                ];
            }),

            'order_item' => $this->whenLoaded('orderItem', function () {
                return [
                    'id' => $this->orderItem->id,
                    'product_id' => $this->orderItem->product_id,
                    'variant_id' => $this->orderItem->variant_id,
                    'quantity' => $this->orderItem->quantity,
                    'price' => $this->orderItem->price,
                    'subtotal' => $this->orderItem->subtotal,
                    'total_amount' => $this->orderItem->total_amount,
                    'weight' => $this->orderItem->weight,
                    'dimensions' => $this->orderItem->dimensions,
                    'sku' => $this->orderItem->sku,
                    'product_name' => $this->orderItem->product_name,
                    'variant_name' => $this->orderItem->variant_name,
                    'is_shipped' => $this->orderItem->is_shipped,
                    'shipped_quantity' => $this->orderItem->shipped_quantity,
                    'returned_quantity' => $this->orderItem->returned_quantity,
                    'refunded_amount' => $this->orderItem->refunded_amount,
                    'product' => $this->orderItem->product ? [
                        'id' => $this->orderItem->product->id,
                        'name' => $this->orderItem->product->name,
                        'sku' => $this->orderItem->product->sku,
                        'slug' => $this->orderItem->product->slug,
                    ] : null,
                    'variant' => $this->orderItem->variant ? [
                        'id' => $this->orderItem->variant->id,
                        'name' => $this->orderItem->variant->name,
                        'sku' => $this->orderItem->variant->sku,
                    ] : null,
                ];
            }),

            // Calculated fields
            'calculated_total_weight' => $this->when($this->orderItem && $this->orderItem->weight,
                $this->quantity * $this->orderItem->weight),
            'calculated_total_volume' => $this->when($this->orderItem && $this->orderItem->volume,
                $this->quantity * $this->orderItem->volume),
            'remaining_quantity' => $this->when($this->orderItem,
                $this->orderItem->quantity - $this->quantity),
            'shipped_percentage' => $this->when($this->orderItem && $this->orderItem->quantity > 0,
                round(($this->quantity / $this->orderItem->quantity) * 100, 2)),
            'is_fully_shipped' => $this->when($this->orderItem,
                $this->quantity >= $this->orderItem->quantity),

            // Status indicators
            'can_increase_quantity' => $this->when($this->orderItem,
                $this->quantity < $this->orderItem->quantity),
            'can_decrease_quantity' => $this->quantity > 1,
            'can_delete' => true,

            // Links
            'links' => [
                'self' => route('api.v1.shipments.items.show', [$this->shipment_id, $this->id]),
                'shipment' => route('api.v1.shipments.show', $this->shipment_id),
                'order_item' => route('api.v1.orders.items.show', [$this->orderItem->order_id ?? null, $this->order_item_id]),
            ],
        ];
    }
}
