<?php

namespace Fereydooni\Shopping\app\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class ProviderLocationCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'data' => $this->collection,

            // Pagination information
            'pagination' => $this->when($this->resource instanceof \Illuminate\Pagination\LengthAwarePaginator, function () {
                return [
                    'current_page' => $this->resource->currentPage(),
                    'last_page' => $this->resource->lastPage(),
                    'per_page' => $this->resource->perPage(),
                    'total' => $this->resource->total(),
                    'from' => $this->resource->firstItem(),
                    'to' => $this->resource->lastItem(),
                    'has_more_pages' => $this->resource->hasMorePages(),
                    'has_pages' => $this->resource->hasPages()
                ];
            }),

            // Links for pagination
            'links' => $this->when($this->resource instanceof \Illuminate\Pagination\LengthAwarePaginator, function () {
                return [
                    'first' => $this->resource->url(1),
                    'last' => $this->resource->url($this->resource->lastPage()),
                    'prev' => $this->resource->previousPageUrl(),
                    'next' => $this->resource->nextPageUrl(),
                    'current' => $this->resource->url($this->resource->currentPage())
                ];
            }),

            // Meta information
            'meta' => [
                'count' => $this->collection->count(),
                'total_count' => $this->when($this->resource instanceof \Illuminate\Pagination\LengthAwarePaginator,
                    $this->resource->total(),
                    $this->collection->count()
                ),
                'per_page' => $this->when($this->resource instanceof \Illuminate\Pagination\LengthAwarePaginator,
                    $this->resource->perPage(),
                    null
                ),
                'current_page' => $this->when($this->resource instanceof \Illuminate\Pagination\LengthAwarePaginator,
                    $this->resource->currentPage(),
                    1
                ),
                'last_page' => $this->when($this->resource instanceof \Illuminate\Pagination\LengthAwarePaginator,
                    $this->resource->lastPage(),
                    1
                ),
                'has_pages' => $this->when($this->resource instanceof \Illuminate\Pagination\LengthAwarePaginator,
                    $this->resource->hasPages(),
                    false
                ),
                'has_more_pages' => $this->when($this->resource instanceof \Illuminate\Pagination\LengthAwarePaginator,
                    $this->resource->hasMorePages(),
                    false
                )
            ],

            // Collection statistics
            'statistics' => [
                'total_locations' => $this->collection->count(),
                'active_locations' => $this->collection->where('is_active', true)->count(),
                'inactive_locations' => $this->collection->where('is_active', false)->count(),
                'primary_locations' => $this->collection->where('is_primary', true)->count(),
                'locations_with_coordinates' => $this->collection->filter(function ($location) {
                    return $location->latitude && $location->longitude;
                })->count(),
                'locations_without_coordinates' => $this->collection->filter(function ($location) {
                    return !$location->latitude || !$location->longitude;
                })->count()
            ],

            // Location type distribution
            'location_types' => $this->collection->groupBy('location_type')->map(function ($group) {
                return [
                    'type' => $group->first()->location_type,
                    'count' => $group->count(),
                    'percentage' => round(($group->count() / $this->collection->count()) * 100, 2)
                ];
            })->values(),

            // Country distribution
            'countries' => $this->collection->groupBy('country')->map(function ($group) {
                return [
                    'country' => $group->first()->country,
                    'count' => $group->count(),
                    'percentage' => round(($group->count() / $this->collection->count()) * 100, 2)
                ];
            })->values(),

            // State distribution
            'states' => $this->collection->groupBy('state')->map(function ($group) {
                return [
                    'state' => $group->first()->state,
                    'count' => $group->count(),
                    'percentage' => round(($group->count() / $this->collection->count()) * 100, 2)
                ];
            })->values(),

            // City distribution
            'cities' => $this->collection->groupBy('city')->map(function ($group) {
                return [
                    'city' => $group->first()->city,
                    'count' => $group->count(),
                    'percentage' => round(($group->count() / $this->collection->count()) * 100, 2)
                ];
            })->values(),

            // Provider distribution
            'providers' => $this->collection->groupBy('provider_id')->map(function ($group) {
                $firstLocation = $group->first();
                return [
                    'provider_id' => $firstLocation->provider_id,
                    'provider_name' => $firstLocation->provider->name ?? 'Unknown',
                    'count' => $group->count(),
                    'percentage' => round(($group->count() / $this->collection->count()) * 100, 2)
                ];
            })->values(),

            // Distance information (if coordinates are provided in request)
            'distance_info' => $this->when($request->has('latitude') && $request->has('longitude'), function () use ($request) {
                $latitude = $request->get('latitude');
                $longitude = $request->get('longitude');

                return [
                    'reference_point' => [
                        'latitude' => $latitude,
                        'longitude' => $longitude
                    ],
                    'locations_with_distance' => $this->collection->filter(function ($location) {
                        return $location->latitude && $location->longitude;
                    })->map(function ($location) use ($latitude, $longitude) {
                        $distance = $this->calculateDistance(
                            $latitude,
                            $longitude,
                            $location->latitude,
                            $location->longitude
                        );

                        return [
                            'location_id' => $location->id,
                            'distance_km' => round($distance, 2),
                            'distance_miles' => round($distance * 0.621371, 2)
                        ];
                    })->sortBy('distance_km')->values()
                ];
            }),

            // Response metadata
            'response_meta' => [
                'generated_at' => now()->toISOString(),
                'api_version' => 'v1',
                'resource_type' => 'ProviderLocationCollection',
                'request_url' => $request->fullUrl(),
                'request_method' => $request->method(),
                'user_agent' => $request->userAgent(),
                'ip_address' => $request->ip()
            ]
        ];
    }

    /**
     * Calculate distance between two coordinates in kilometers
     */
    protected function calculateDistance(float $lat1, float $lon1, float $lat2, float $lon2): float
    {
        $earthRadius = 6371; // Earth's radius in kilometers

        $latDelta = deg2rad($lat2 - $lat1);
        $lonDelta = deg2rad($lon2 - $lon1);

        $a = sin($latDelta / 2) * sin($latDelta / 2) +
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
             sin($lonDelta / 2) * sin($lonDelta / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c;
    }
}
