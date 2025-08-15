<?php

namespace Fereydooni\Shopping\app\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class CategoryCollection extends ResourceCollection
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
        ];
    }

    /**
     * Get additional data that should be returned with the resource array.
     */
    public function with(Request $request): array
    {
        return [
            'meta' => [
                'type' => 'category_collection',
                'version' => '1.0',
                'timestamp' => now()->toISOString(),
            ],
        ];
    }
}
