<?php

namespace Fereydooni\Shopping\app\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProviderLocationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'provider_id' => $this->provider_id,
            'location_name' => $this->location_name,
            'address' => $this->address,
            'city' => $this->city,
            'state' => $this->state,
            'postal_code' => $this->postal_code,
            'country' => $this->country,
            'phone' => $this->phone,
            'email' => $this->email,
            'website' => $this->website,
            'is_primary' => $this->is_primary,
            'is_active' => $this->is_active,
            'location_type' => $this->location_type,
            'operating_hours' => $this->when($this->operating_hours, $this->operating_hours),
            'timezone' => $this->timezone,
            'latitude' => $this->when($this->latitude, $this->latitude),
            'longitude' => $this->when($this->longitude, $this->longitude),
            'contact_person' => $this->contact_person,
            'contact_phone' => $this->contact_phone,
            'contact_email' => $this->contact_email,
            'notes' => $this->when($this->notes, $this->notes),
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),

            // Relationships
            'provider' => $this->whenLoaded('provider', function () {
                return [
                    'id' => $this->provider->id,
                    'name' => $this->provider->name,
                    'company_name' => $this->provider->company_name,
                    'status' => $this->provider->status,
                ];
            }),

            // Computed fields
            'full_address' => $this->when($this->address, function () {
                $parts = array_filter([
                    $this->address,
                    $this->city,
                    $this->state,
                    $this->postal_code,
                    $this->country,
                ]);

                return implode(', ', $parts);
            }),

            'coordinates_formatted' => $this->when($this->latitude && $this->longitude, function () {
                return [
                    'lat' => round($this->latitude, 6),
                    'lng' => round($this->longitude, 6),
                    'formatted' => "{$this->latitude}, {$this->longitude}",
                ];
            }),

            'operating_hours_formatted' => $this->when($this->operating_hours, function () {
                return $this->formatOperatingHours($this->operating_hours);
            }),

            'status_info' => [
                'is_active' => $this->is_active,
                'is_primary' => $this->is_primary,
                'status_label' => $this->getStatusLabel(),
                'status_color' => $this->getStatusColor(),
            ],

            // Geospatial data
            'geospatial' => $this->when($this->latitude && $this->longitude, function () {
                return [
                    'coordinates' => [
                        'latitude' => $this->latitude,
                        'longitude' => $this->longitude,
                    ],
                    'formatted_coordinates' => "{$this->latitude}, {$this->longitude}",
                    'has_coordinates' => true,
                ];
            }, [
                'coordinates' => null,
                'formatted_coordinates' => null,
                'has_coordinates' => false,
            ]),

            // Location type info
            'location_type_info' => [
                'type' => $this->location_type,
                'label' => $this->getLocationTypeLabel(),
                'icon' => $this->getLocationTypeIcon(),
                'category' => $this->getLocationTypeCategory(),
            ],

            // Contact info
            'contact_info' => [
                'phone' => $this->phone,
                'email' => $this->email,
                'website' => $this->website,
                'contact_person' => $this->contact_person,
                'contact_phone' => $this->contact_phone,
                'contact_email' => $this->contact_email,
            ],

            // Metadata
            'meta' => [
                'created_at_formatted' => $this->created_at?->format('M j, Y g:i A'),
                'updated_at_formatted' => $this->updated_at?->format('M j, Y g:i A'),
                'age_days' => $this->created_at?->diffInDays(now()),
                'last_updated_days' => $this->updated_at?->diffInDays(now()),
            ],
        ];
    }

    /**
     * Format operating hours for display
     */
    protected function formatOperatingHours($operatingHours): array
    {
        if (! $operatingHours || ! is_array($operatingHours)) {
            return [];
        }

        $formatted = [];
        $days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];

        foreach ($days as $day) {
            if (isset($operatingHours[$day])) {
                $hours = $operatingHours[$day];
                if (isset($hours['is_closed']) && $hours['is_closed']) {
                    $formatted[$day] = 'Closed';
                } elseif (isset($hours['open']) && isset($hours['close'])) {
                    $formatted[$day] = "{$hours['open']} - {$hours['close']}";
                    if (isset($hours['notes'])) {
                        $formatted[$day] .= " ({$hours['notes']})";
                    }
                } else {
                    $formatted[$day] = 'Not specified';
                }
            } else {
                $formatted[$day] = 'Not specified';
            }
        }

        return $formatted;
    }

    /**
     * Get status label
     */
    protected function getStatusLabel(): string
    {
        if (! $this->is_active) {
            return 'Inactive';
        }

        if ($this->is_primary) {
            return 'Primary';
        }

        return 'Active';
    }

    /**
     * Get status color
     */
    protected function getStatusColor(): string
    {
        if (! $this->is_active) {
            return 'red';
        }

        if ($this->is_primary) {
            return 'green';
        }

        return 'blue';
    }

    /**
     * Get location type label
     */
    protected function getLocationTypeLabel(): string
    {
        $labels = [
            'headquarters' => 'Headquarters',
            'warehouse' => 'Warehouse',
            'store' => 'Store',
            'office' => 'Office',
            'factory' => 'Factory',
            'distribution_center' => 'Distribution Center',
            'retail_outlet' => 'Retail Outlet',
            'service_center' => 'Service Center',
            'other' => 'Other',
        ];

        return $labels[$this->location_type] ?? 'Unknown';
    }

    /**
     * Get location type icon
     */
    protected function getLocationTypeIcon(): string
    {
        $icons = [
            'headquarters' => 'building',
            'warehouse' => 'warehouse',
            'store' => 'store',
            'office' => 'briefcase',
            'factory' => 'industry',
            'distribution_center' => 'truck',
            'retail_outlet' => 'shopping-cart',
            'service_center' => 'tools',
            'other' => 'map-marker',
        ];

        return $icons[$this->location_type] ?? 'map-marker';
    }

    /**
     * Get location type category
     */
    protected function getLocationTypeCategory(): string
    {
        $categories = [
            'headquarters' => 'administrative',
            'warehouse' => 'storage',
            'store' => 'retail',
            'office' => 'administrative',
            'factory' => 'manufacturing',
            'distribution_center' => 'logistics',
            'retail_outlet' => 'retail',
            'service_center' => 'service',
            'other' => 'miscellaneous',
        ];

        return $categories[$this->location_type] ?? 'miscellaneous';
    }
}
