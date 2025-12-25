<?php

namespace Fereydooni\Shopping\app\Events\Provider;

use Fereydooni\Shopping\app\Models\ProviderLocation;
use Fereydooni\Shopping\app\Models\User;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class LocationCoordinatesUpdated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public ProviderLocation $providerLocation;

    public ?User $user;

    public array $oldCoordinates;

    public array $newCoordinates;

    public array $coordinateData;

    public ?string $updateSource;

    /**
     * Create a new event instance.
     */
    public function __construct(
        ProviderLocation $providerLocation,
        array $oldCoordinates,
        array $newCoordinates,
        ?User $user = null,
        ?string $updateSource = null
    ) {
        $this->providerLocation = $providerLocation;
        $this->user = $user;
        $this->oldCoordinates = $oldCoordinates;
        $this->newCoordinates = $newCoordinates;
        $this->updateSource = $updateSource;

        // Extract coordinate data
        $this->coordinateData = [
            'location_id' => $providerLocation->id,
            'provider_id' => $providerLocation->provider_id,
            'location_name' => $providerLocation->location_name,
            'old_coordinates' => [
                'latitude' => $oldCoordinates['latitude'] ?? null,
                'longitude' => $oldCoordinates['longitude'] ?? null,
                'formatted' => $this->formatCoordinates($oldCoordinates['latitude'] ?? null, $oldCoordinates['longitude'] ?? null),
                'had_coordinates' => ! is_null($oldCoordinates['latitude'] ?? null) && ! is_null($oldCoordinates['longitude'] ?? null),
            ],
            'new_coordinates' => [
                'latitude' => $newCoordinates['latitude'] ?? null,
                'longitude' => $newCoordinates['longitude'] ?? null,
                'formatted' => $this->formatCoordinates($newCoordinates['latitude'] ?? null, $newCoordinates['longitude'] ?? null),
                'has_coordinates' => ! is_null($newCoordinates['latitude'] ?? null) && ! is_null($newCoordinates['longitude'] ?? null),
            ],
            'update_source' => $updateSource ?? 'manual',
            'coordinates_changed' => $this->coordinatesChanged(),
            'distance_change' => $this->calculateDistanceChange(),
            'updated_at' => now()->toISOString(),
        ];
    }

    /**
     * Format coordinates for display
     */
    protected function formatCoordinates(?float $latitude, ?float $longitude): ?string
    {
        if (is_null($latitude) || is_null($longitude)) {
            return null;
        }

        return "{$latitude}, {$longitude}";
    }

    /**
     * Check if coordinates actually changed
     */
    protected function coordinatesChanged(): bool
    {
        $oldLat = $this->oldCoordinates['latitude'] ?? null;
        $oldLng = $this->oldCoordinates['longitude'] ?? null;
        $newLat = $this->newCoordinates['latitude'] ?? null;
        $newLng = $this->newCoordinates['longitude'] ?? null;

        if (is_null($oldLat) && is_null($newLat) && is_null($oldLng) && is_null($newLng)) {
            return false; // Both were null
        }

        if (is_null($oldLat) || is_null($oldLng)) {
            return ! is_null($newLat) && ! is_null($newLng); // Was null, now has coordinates
        }

        if (is_null($newLat) || is_null($newLng)) {
            return true; // Had coordinates, now null
        }

        // Both have coordinates, check if they're different
        $latDiff = abs($oldLat - $newLat);
        $lngDiff = abs($oldLng - $newLng);

        // Consider coordinates changed if difference is more than 0.000001 degrees (roughly 11 meters)
        return $latDiff > 0.000001 || $lngDiff > 0.000001;
    }

    /**
     * Calculate distance change between old and new coordinates
     */
    protected function calculateDistanceChange(): ?array
    {
        $oldLat = $this->oldCoordinates['latitude'] ?? null;
        $oldLng = $this->oldCoordinates['longitude'] ?? null;
        $newLat = $this->newCoordinates['latitude'] ?? null;
        $newLng = $this->newCoordinates['longitude'] ?? null;

        if (is_null($oldLat) || is_null($oldLng) || is_null($newLat) || is_null($newLng)) {
            return null;
        }

        $distance = $this->calculateDistance($oldLat, $oldLng, $newLat, $newLng);

        return [
            'distance_km' => round($distance, 2),
            'distance_miles' => round($distance * 0.621371, 2),
            'distance_meters' => round($distance * 1000, 0),
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

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('provider.'.$this->providerLocation->provider_id),
            new PrivateChannel('admin.provider-locations'),
        ];
    }

    /**
     * Get the data to broadcast.
     */
    public function broadcastWith(): array
    {
        return [
            'event_type' => 'location_coordinates_updated',
            'location_id' => $this->providerLocation->id,
            'provider_id' => $this->providerLocation->provider_id,
            'location_name' => $this->providerLocation->location_name,
            'coordinate_data' => $this->coordinateData,
            'user_id' => $this->user?->id,
            'user_name' => $this->user?->name,
            'timestamp' => now()->toISOString(),
        ];
    }

    /**
     * Get the event name for broadcasting.
     */
    public function broadcastAs(): string
    {
        return 'provider.location.coordinates_updated';
    }

    /**
     * Check if coordinates were added (was null, now has coordinates)
     */
    public function coordinatesAdded(): bool
    {
        $oldLat = $this->oldCoordinates['latitude'] ?? null;
        $oldLng = $this->oldCoordinates['longitude'] ?? null;
        $newLat = $this->newCoordinates['latitude'] ?? null;
        $newLng = $this->newCoordinates['longitude'] ?? null;

        return (is_null($oldLat) || is_null($oldLng)) && ! is_null($newLat) && ! is_null($newLng);
    }

    /**
     * Check if coordinates were removed (had coordinates, now null)
     */
    public function coordinatesRemoved(): bool
    {
        $oldLat = $this->oldCoordinates['latitude'] ?? null;
        $oldLng = $this->oldCoordinates['longitude'] ?? null;
        $newLat = $this->newCoordinates['latitude'] ?? null;
        $newLng = $this->newCoordinates['longitude'] ?? null;

        return (! is_null($oldLat) && ! is_null($oldLng)) && (is_null($newLat) || is_null($newLng));
    }

    /**
     * Check if coordinates were modified (both had coordinates, but changed)
     */
    public function coordinatesModified(): bool
    {
        return $this->coordinatesChanged() && ! $this->coordinatesAdded() && ! $this->coordinatesRemoved();
    }

    /**
     * Get the update source
     */
    public function getUpdateSource(): ?string
    {
        return $this->updateSource;
    }

    /**
     * Check if update was from geocoding
     */
    public function wasGeocoded(): bool
    {
        return $this->updateSource === 'geocoding';
    }

    /**
     * Check if update was manual
     */
    public function wasManual(): bool
    {
        return $this->updateSource === 'manual';
    }

    /**
     * Check if update was from GPS
     */
    public function wasFromGPS(): bool
    {
        return $this->updateSource === 'gps';
    }
}
