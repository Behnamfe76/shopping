<?php

namespace Fereydooni\Shopping\app\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class ProviderInsuranceCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     */
    public function toArray(Request $request): array
    {
        $data = parent::toArray($request);

        // Add pagination metadata if available
        if (method_exists($this->resource, 'currentPage')) {
            $data['pagination'] = [
                'current_page' => $this->resource->currentPage(),
                'last_page' => $this->resource->lastPage(),
                'per_page' => $this->resource->perPage(),
                'total' => $this->resource->total(),
                'from' => $this->resource->firstItem(),
                'to' => $this->resource->lastItem(),
                'has_more_pages' => $this->resource->hasMorePages(),
            ];

            // Add pagination links
            $data['links'] = [
                'first' => $this->resource->url(1),
                'last' => $this->resource->url($this->resource->lastPage()),
                'prev' => $this->resource->previousPageUrl(),
                'next' => $this->resource->nextPageUrl(),
            ];
        }

        // Add collection metadata
        $data['meta'] = [
            'count' => $this->collection->count(),
            'total_count' => method_exists($this->resource, 'total') ? $this->resource->total() : $this->collection->count(),
            'per_page' => method_exists($this->resource, 'perPage') ? $this->resource->perPage() : null,
            'current_page' => method_exists($this->resource, 'currentPage') ? $this->resource->currentPage() : 1,
            'last_page' => method_exists($this->resource, 'lastPage') ? $this->resource->lastPage() : 1,
        ];

        return $data;
    }

    /**
     * Get additional data that should be returned with the resource array.
     */
    public function with(Request $request): array
    {
        return [
            'success' => true,
            'message' => 'Provider insurance records retrieved successfully',
            'timestamp' => now()->toISOString(),
        ];
    }
}
