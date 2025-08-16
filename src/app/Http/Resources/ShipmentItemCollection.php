<?php

namespace Fereydooni\Shopping\app\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class ShipmentItemCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'data' => $this->collection,
            'meta' => [
                'total' => $this->collection->count(),
                'per_page' => $this->perPage ?? null,
                'current_page' => $this->currentPage ?? null,
                'last_page' => $this->lastPage ?? null,
                'from' => $this->firstItem ?? null,
                'to' => $this->lastItem ?? null,
                'has_more_pages' => $this->hasMorePages() ?? false,
            ],
            'links' => [
                'first' => $this->url(1) ?? null,
                'last' => $this->url($this->lastPage) ?? null,
                'prev' => $this->previousPageUrl(),
                'next' => $this->nextPageUrl(),
            ],
            'summary' => [
                'total_items' => $this->collection->count(),
                'total_quantity' => $this->collection->sum('quantity'),
                'total_weight' => $this->collection->sum(function ($item) {
                    return $item->total_weight ?? 0;
                }),
                'total_volume' => $this->collection->sum(function ($item) {
                    return $item->total_volume ?? 0;
                }),
                'fully_shipped_count' => $this->collection->filter(function ($item) {
                    return $item->is_fully_shipped ?? false;
                })->count(),
                'partially_shipped_count' => $this->collection->filter(function ($item) {
                    return !($item->is_fully_shipped ?? false);
                })->count(),
            ],
        ];
    }
}
