<?php

namespace Fereydooni\Shopping\app\Http\Resources;

use Fereydooni\Shopping\app\Traits\GetPaginationAttibutes;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class ProductCollection extends ResourceCollection
{
    use GetPaginationAttibutes;

    /**
     * Create a new anonymous resource collection.
     */
    public function __construct($resource)
    {
        // Load the relationships
        $resource->load(['category', 'brand']);

        parent::__construct($resource);
    }

    /**
     * Transform the resource collection into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'data' => ProductResource::collection($this->collection),
            'meta' => $this->getMeta($this->resource),
            'links' => $this->getLinks($this->resource),
        ];
    }
}
