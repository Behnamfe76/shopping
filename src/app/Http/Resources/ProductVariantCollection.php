<?php

namespace Fereydooni\Shopping\app\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class ProductVariantCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'data' => $this->collection,
            'meta' => [
                'total' => $this->total(),
                'per_page' => $this->perPage(),
                'current_page' => $this->currentPage(),
                'last_page' => $this->lastPage(),
                'from' => $this->firstItem(),
                'to' => $this->lastItem(),
                'variant_count' => $this->count(),
                'active_variants' => $this->collection->where('is_active', true)->count(),
                'in_stock_variants' => $this->collection->where('stock_quantity', '>', 0)->count(),
                'out_of_stock_variants' => $this->collection->where('stock_quantity', '<=', 0)->count(),
                'low_stock_variants' => $this->collection->filter(function ($variant) {
                    return $variant->stock <= $variant->low_stock_threshold && $variant->stock > 0;
                })->count(),
            ],
            'links' => [
                'first' => $this->url(1),
                'last' => $this->url($this->lastPage()),
                'prev' => $this->previousPageUrl(),
                'next' => $this->nextPageUrl(),
            ],
        ];
    }
}
