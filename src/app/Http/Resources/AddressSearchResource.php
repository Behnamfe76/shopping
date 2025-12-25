<?php

namespace Fereydooni\Shopping\app\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class AddressSearchResource extends ResourceCollection
{
    private string $query;

    private array $searchMetadata;

    public function __construct($resource, string $query, array $searchMetadata = [])
    {
        parent::__construct($resource);
        $this->query = $query;
        $this->searchMetadata = $searchMetadata;
    }

    /**
     * Transform the resource collection into an array.
     */
    public function toArray(Request $request): array
    {
        $data = [
            'data' => $this->collection->map(function ($address) use ($request) {
                $addressData = (new AddressResource($address))->toArray($request);

                // Add search highlighting
                $addressData['search_highlights'] = $this->getSearchHighlights($address);

                return $addressData;
            }),
        ];

        // Add search metadata
        $data['search'] = [
            'query' => $this->query,
            'total_results' => $this->collection->count(),
            'search_time' => $this->searchMetadata['search_time'] ?? null,
            'filters_applied' => $this->searchMetadata['filters'] ?? [],
            'suggestions' => $this->getSearchSuggestions(),
        ];

        // Add pagination if available
        if (method_exists($this->resource, 'currentPage')) {
            $data['pagination'] = [
                'current_page' => $this->resource->currentPage(),
                'per_page' => $this->resource->perPage(),
                'total' => $this->resource->total(),
                'last_page' => $this->resource->lastPage(),
                'from' => $this->resource->firstItem(),
                'to' => $this->resource->lastItem(),
                'has_more_pages' => $this->resource->hasMorePages(),
            ];
        }

        // Add search summary
        $data['summary'] = [
            'query' => $this->query,
            'results_count' => $this->collection->count(),
            'search_terms' => $this->extractSearchTerms(),
            'type_breakdown' => $this->getTypeBreakdown(),
            'default_addresses_found' => $this->getDefaultAddressesCount(),
        ];

        return $data;
    }

    /**
     * Get search highlights for an address.
     */
    private function getSearchHighlights($address): array
    {
        $highlights = [];
        $query = strtolower($this->query);
        $searchableFields = [
            'first_name', 'last_name', 'company_name', 'address_line_1',
            'address_line_2', 'city', 'state', 'postal_code', 'country',
        ];

        foreach ($searchableFields as $field) {
            if (isset($address->$field) && str_contains(strtolower($address->$field), $query)) {
                $highlights[$field] = $this->highlightText($address->$field, $query);
            }
        }

        return $highlights;
    }

    /**
     * Highlight search terms in text.
     */
    private function highlightText(string $text, string $query): string
    {
        $highlighted = preg_replace(
            '/('.preg_quote($query, '/').')/i',
            '<mark>$1</mark>',
            $text
        );

        return $highlighted;
    }

    /**
     * Get search suggestions.
     */
    private function getSearchSuggestions(): array
    {
        $suggestions = [];
        $query = strtolower($this->query);

        // Generate suggestions based on search results
        $cities = $this->collection->pluck('city')->unique()->filter(function ($city) use ($query) {
            return str_contains(strtolower($city), $query);
        })->take(5)->toArray();

        $states = $this->collection->pluck('state')->unique()->filter(function ($state) use ($query) {
            return str_contains(strtolower($state), $query);
        })->take(5)->toArray();

        $countries = $this->collection->pluck('country')->unique()->filter(function ($country) use ($query) {
            return str_contains(strtolower($country), $query);
        })->take(5)->toArray();

        if (! empty($cities)) {
            $suggestions['cities'] = $cities;
        }

        if (! empty($states)) {
            $suggestions['states'] = $states;
        }

        if (! empty($countries)) {
            $suggestions['countries'] = $countries;
        }

        return $suggestions;
    }

    /**
     * Extract search terms from query.
     */
    private function extractSearchTerms(): array
    {
        $terms = explode(' ', trim($this->query));

        return array_filter($terms, function ($term) {
            return strlen($term) >= 2;
        });
    }

    /**
     * Get type breakdown for search results.
     */
    private function getTypeBreakdown(): array
    {
        $billingCount = $this->collection->filter(function ($address) {
            return $address->type === 'billing';
        })->count();

        $shippingCount = $this->collection->filter(function ($address) {
            return $address->type === 'shipping';
        })->count();

        return [
            'billing' => $billingCount,
            'shipping' => $shippingCount,
        ];
    }

    /**
     * Get count of default addresses in search results.
     */
    private function getDefaultAddressesCount(): int
    {
        return $this->collection->filter(function ($address) {
            return $address->is_default;
        })->count();
    }

    /**
     * Get additional data that should be returned with the resource array.
     */
    public function with(Request $request): array
    {
        return [
            'meta' => [
                'type' => 'address_search',
                'version' => '1.0',
                'timestamp' => now()->toISOString(),
            ],
            'links' => [
                'self' => $request->url(),
                'search' => route('api.v1.addresses.search'),
                'create' => route('api.v1.addresses.store'),
            ],
        ];
    }
}
