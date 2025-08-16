<?php

namespace Fereydooni\Shopping\app\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ShipmentItemSearchResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'shipment_id' => $this->shipment_id,
            'order_item_id' => $this->order_item_id,
            'quantity' => $this->quantity,
            'total_weight' => $this->when($this->total_weight, $this->total_weight),
            'total_volume' => $this->when($this->total_volume, $this->total_volume),
            'created_at' => $this->created_at->toISOString(),
            'updated_at' => $this->updated_at->toISOString(),

            // Search-specific fields
            'search_highlight' => [
                'product_name' => $this->when($this->orderItem && $this->orderItem->product_name,
                    $this->highlightSearchTerm($this->orderItem->product_name, $request->get('query'))),
                'sku' => $this->when($this->orderItem && $this->orderItem->sku,
                    $this->highlightSearchTerm($this->orderItem->sku, $request->get('query'))),
                'variant_name' => $this->when($this->orderItem && $this->orderItem->variant_name,
                    $this->highlightSearchTerm($this->orderItem->variant_name, $request->get('query'))),
            ],

            // Relationships (simplified for search)
            'shipment' => $this->whenLoaded('shipment', function () {
                return [
                    'id' => $this->shipment->id,
                    'tracking_number' => $this->shipment->tracking_number,
                    'status' => $this->shipment->status,
                    'carrier' => $this->shipment->carrier,
                ];
            }),

            'order_item' => $this->whenLoaded('orderItem', function () {
                return [
                    'id' => $this->orderItem->id,
                    'product_id' => $this->orderItem->product_id,
                    'variant_id' => $this->orderItem->variant_id,
                    'quantity' => $this->orderItem->quantity,
                    'sku' => $this->orderItem->sku,
                    'product_name' => $this->orderItem->product_name,
                    'variant_name' => $this->orderItem->variant_name,
                    'is_shipped' => $this->orderItem->is_shipped,
                    'shipped_quantity' => $this->orderItem->shipped_quantity,
                ];
            }),

            // Calculated fields
            'remaining_quantity' => $this->when($this->orderItem,
                $this->orderItem->quantity - $this->quantity),
            'shipped_percentage' => $this->when($this->orderItem && $this->orderItem->quantity > 0,
                round(($this->quantity / $this->orderItem->quantity) * 100, 2)),
            'is_fully_shipped' => $this->when($this->orderItem,
                $this->quantity >= $this->orderItem->quantity),

            // Search metadata
            'search_score' => $this->when($this->search_score, $this->search_score),
            'search_type' => 'shipment_item',

            // Links
            'links' => [
                'self' => route('api.v1.shipments.items.show', [$this->shipment_id, $this->id]),
                'shipment' => route('api.v1.shipments.show', $this->shipment_id),
            ],
        ];
    }

    /**
     * Highlight search terms in text.
     */
    protected function highlightSearchTerm(string $text, string $query): string
    {
        if (empty($query)) {
            return $text;
        }

        $highlighted = preg_replace(
            '/(' . preg_quote($query, '/') . ')/i',
            '<mark>$1</mark>',
            $text
        );

        return $highlighted;
    }
}
