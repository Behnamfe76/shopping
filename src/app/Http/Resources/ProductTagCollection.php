<?php

namespace Fereydooni\Shopping\app\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class ProductTagCollection extends ResourceCollection
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
                'per_page' => $this->perPage(),
                'current_page' => $this->currentPage(),
                'last_page' => $this->lastPage(),
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
            'filters' => [
                'status' => $request->get('status'),
                'featured' => $request->get('featured'),
                'color' => $request->get('color'),
                'icon' => $request->get('icon'),
                'min_usage' => $request->get('min_usage'),
                'max_usage' => $request->get('max_usage'),
                'sort_by' => $request->get('sort_by', 'name'),
                'sort_order' => $request->get('sort_order', 'asc'),
            ],
        ];
    }
}
