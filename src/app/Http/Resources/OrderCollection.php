<?php

namespace Fereydooni\Shopping\app\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class OrderCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'data' => $this->collection,
            'meta' => [
                'current_page' => $this->currentPage(),
                'last_page' => $this->lastPage(),
                'per_page' => $this->perPage(),
                'total' => $this->total(),
                'from' => $this->firstItem(),
                'to' => $this->lastItem(),
                'has_more_pages' => $this->hasMorePages(),
            ],
            'links' => [
                'first' => $this->url(1),
                'last' => $this->url($this->lastPage()),
                'prev' => $this->previousPageUrl(),
                'next' => $this->nextPageUrl(),
            ],
            'summary' => [
                'total_orders' => $this->total(),
                'pending_orders' => $this->collection->where('status', 'pending')->count(),
                'paid_orders' => $this->collection->where('status', 'paid')->count(),
                'shipped_orders' => $this->collection->where('status', 'shipped')->count(),
                'completed_orders' => $this->collection->where('status', 'completed')->count(),
                'cancelled_orders' => $this->collection->where('status', 'cancelled')->count(),
            ],
        ];
    }
}
