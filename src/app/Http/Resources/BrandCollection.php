<?php

namespace Fereydooni\Shopping\app\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class BrandCollection extends ResourceCollection
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
                'featured_count' => $this->collection->where('is_featured', true)->count(),
                'active_count' => $this->collection->where('is_active', true)->count(),
                'has_featured' => $this->collection->where('is_featured', true)->isNotEmpty(),
            ],
        ];
    }

    /**
     * Get additional data that should be returned with the resource array.
     */
    public function with(Request $request): array
    {
        return [
            'success' => true,
            'message' => 'Brands retrieved successfully',
        ];
    }
}
