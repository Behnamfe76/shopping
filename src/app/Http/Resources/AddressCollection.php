<?php

namespace Fereydooni\Shopping\app\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Pagination\CursorPaginator;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;

class AddressCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     */
    public function toArray(Request $request): array
    {
        $data = [
            'data' => $this->collection->map(function ($address) use ($request) {
                return (new AddressResource($address))->toArray($request);
            }),
        ];

        // Add pagination metadata
        if ($this->resource instanceof LengthAwarePaginator) {
            $data['pagination'] = [
                'type' => 'length_aware',
                'current_page' => $this->resource->currentPage(),
                'per_page' => $this->resource->perPage(),
                'total' => $this->resource->total(),
                'last_page' => $this->resource->lastPage(),
                'from' => $this->resource->firstItem(),
                'to' => $this->resource->lastItem(),
                'has_more_pages' => $this->resource->hasMorePages(),
                'links' => [
                    'first' => $this->resource->url(1),
                    'last' => $this->resource->url($this->resource->lastPage()),
                    'prev' => $this->resource->previousPageUrl(),
                    'next' => $this->resource->nextPageUrl(),
                ],
            ];
        } elseif ($this->resource instanceof Paginator) {
            $data['pagination'] = [
                'type' => 'simple',
                'current_page' => $this->resource->currentPage(),
                'per_page' => $this->resource->perPage(),
                'has_more_pages' => $this->resource->hasMorePages(),
                'links' => [
                    'first' => $this->resource->url(1),
                    'prev' => $this->resource->previousPageUrl(),
                    'next' => $this->resource->nextPageUrl(),
                ],
            ];
        } elseif ($this->resource instanceof CursorPaginator) {
            $data['pagination'] = [
                'type' => 'cursor',
                'per_page' => $this->resource->perPage(),
                'has_more_pages' => $this->resource->hasMorePages(),
                'cursorPaginate' => [
                    'prev' => $this->resource->previousCursor(),
                    'next' => $this->resource->nextCursor(),
                ],
            ];
        }

        // Add collection metadata
        $data['meta'] = [
            'type' => 'address_collection',
            'version' => '1.0',
            'timestamp' => now()->toISOString(),
            'count' => $this->collection->count(),
            'total_count' => $this->getTotalCount(),
            'default_addresses' => $this->getDefaultAddressesInfo(),
            'type_breakdown' => $this->getTypeBreakdown(),
        ];

        // Add summary information
        $data['summary'] = [
            'total_addresses' => $this->getTotalCount(),
            'default_addresses' => $this->getDefaultAddressesCount(),
            'billing_addresses' => $this->getTypeCount('billing'),
            'shipping_addresses' => $this->getTypeCount('shipping'),
        ];

        return $data;
    }

    /**
     * Get the total count of addresses.
     */
    private function getTotalCount(): int
    {
        if ($this->resource instanceof LengthAwarePaginator) {
            return $this->resource->total();
        }

        return $this->collection->count();
    }

    /**
     * Get default addresses information.
     */
    private function getDefaultAddressesInfo(): array
    {
        $defaultAddresses = $this->collection->filter(function ($address) {
            return $address->is_default;
        });

        return [
            'count' => $defaultAddresses->count(),
            'ids' => $defaultAddresses->pluck('id')->toArray(),
            'types' => $defaultAddresses->pluck('type')->toArray(),
        ];
    }

    /**
     * Get the count of default addresses.
     */
    private function getDefaultAddressesCount(): int
    {
        return $this->collection->filter(function ($address) {
            return $address->is_default;
        })->count();
    }

    /**
     * Get type breakdown information.
     */
    private function getTypeBreakdown(): array
    {
        $billingCount = $this->getTypeCount('billing');
        $shippingCount = $this->getTypeCount('shipping');

        return [
            'billing' => [
                'count' => $billingCount,
                'percentage' => $this->getTotalCount() > 0 ? round(($billingCount / $this->getTotalCount()) * 100, 2) : 0,
            ],
            'shipping' => [
                'count' => $shippingCount,
                'percentage' => $this->getTotalCount() > 0 ? round(($shippingCount / $this->getTotalCount()) * 100, 2) : 0,
            ],
        ];
    }

    /**
     * Get the count of addresses by type.
     */
    private function getTypeCount(string $type): int
    {
        return $this->collection->filter(function ($address) use ($type) {
            return $address->type === $type;
        })->count();
    }

    /**
     * Get additional data that should be returned with the resource array.
     */
    public function with(Request $request): array
    {
        return [
            'links' => [
                'self' => $request->url(),
                'create' => route('api.v1.addresses.store'),
                'search' => route('api.v1.addresses.search'),
            ],
        ];
    }
}
