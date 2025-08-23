<?php

namespace Fereydooni\Shopping\app\Events\Provider;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Fereydooni\Shopping\app\Models\ProviderLocation;
use Fereydooni\Shopping\app\Models\User;

class LocationGeocoded
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public ProviderLocation $providerLocation;
    public ?User $user;
    public array $geocodingResults;
    public array $geocodingData;
    public ?string $geocodingService;
    public ?string $geocodingMethod;

    /**
     * Create a new event instance.
     */
    public function __construct(
        ProviderLocation $providerLocation,
        array $geocodingResults,
        ?User $user = null,
        ?string $geocodingService = null,
        ?string $geocodingMethod = null
    ) {
        $this->providerLocation = $providerLocation;
        $this->user = $user;
        $this->geocodingResults = $geocodingResults;
        $this->geocodingService = $geocodingService;
        $this->geocodingMethod = $geocodingMethod;

        // Extract geocoding data
        $this->geocodingData = [
            'location_id' => $providerLocation->id,
            'provider_id' => $providerLocation->provider_id,
            'location_name' => $providerLocation->location_name,
            'address' => $providerLocation->address,
            'city' => $providerLocation->city,
            'state' => $providerLocation->state,
            'country' => $providerLocation->country,
            'postal_code' => $providerLocation->postal_code,
            'old_coordinates' => [
                'latitude' => $providerLocation->getOriginal('latitude'),
                'longitude' => $providerLocation->getOriginal('longitude')
            ],
            'new_coordinates' => [
                'latitude' => $providerLocation->latitude,
                'longitude' => $providerLocation->longitude
            ],
            'geocoding_results' => $geocodingResults,
            'geocoding_service' => $geocodingService,
            'geocoding_method' => $geocodingMethod,
            'coordinates_changed' => $this->coordinatesChanged(),
            'address_verified' => $this->isAddressVerified(),
            'geocoding_success' => $this->isGeocodingSuccessful(),
            'accuracy_level' => $this->getAccuracyLevel(),
            'geocoded_at' => now()->toISOString()
        ];
    }

    /**
     * Check if coordinates actually changed
     */
    protected function coordinatesChanged(): bool
    {
        $oldLat = $this->geocodingData['old_coordinates']['latitude'];
        $oldLng = $this->geocodingData['old_coordinates']['longitude'];
        $newLat = $this->geocodingData['new_coordinates']['latitude'];
        $newLng = $this->geocodingData['new_coordinates']['longitude'];

        if (is_null($oldLat) && is_null($oldLng) && !is_null($newLat) && !is_null($newLng)) {
            return true; // Coordinates were added
        }

        if (!is_null($oldLat) && !is_null($oldLng) && is_null($newLat) && is_null($newLng)) {
            return true; // Coordinates were removed
        }

        if (!is_null($oldLat) && !is_null($oldLng) && !is_null($newLat) && !is_null($newLng)) {
            // Check if coordinates are significantly different (more than 1 meter)
            $distance = $this->calculateDistance($oldLat, $oldLng, $newLat, $newLng);
            return $distance > 0.001; // 1 meter threshold
        }

        return false;
    }

    /**
     * Check if address was verified during geocoding
     */
    protected function isAddressVerified(): bool
    {
        return isset($this->geocodingResults['verified']) && $this->geocodingResults['verified'] === true;
    }

    /**
     * Check if geocoding was successful
     */
    protected function isGeocodingSuccessful(): bool
    {
        return isset($this->geocodingResults['success']) && $this->geocodingResults['success'] === true;
    }

    /**
     * Get the accuracy level of the geocoding
     */
    protected function getAccuracyLevel(): string
    {
        if (!isset($this->geocodingResults['accuracy'])) {
            return 'unknown';
        }

        $accuracy = $this->geocodingResults['accuracy'];

        if ($accuracy <= 0.001) { // 1 meter
            return 'exact';
        } elseif ($accuracy <= 0.01) { // 10 meters
            return 'high';
        } elseif ($accuracy <= 0.1) { // 100 meters
            return 'medium';
        } elseif ($accuracy <= 1.0) { // 1 kilometer
            return 'low';
        } else {
            return 'very_low';
        }
    }

    /**
     * Calculate distance between two coordinate pairs
     */
    protected function calculateDistance(float $lat1, float $lng1, float $lat2, float $lng2): float
    {
        $earthRadius = 6371000; // Earth's radius in meters

        $latDelta = deg2rad($lat2 - $lat1);
        $lngDelta = deg2rad($lng2 - $lng1);

        $a = sin($latDelta / 2) * sin($latDelta / 2) +
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
             sin($lngDelta / 2) * sin($lngDelta / 2);

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
            new PrivateChannel('provider.' . $this->providerLocation->provider_id),
            new PrivateChannel('admin.provider-locations'),
        ];
    }

    /**
     * Get the data to broadcast.
     */
    public function broadcastWith(): array
    {
        return [
            'event_type' => 'location_geocoded',
            'location_id' => $this->providerLocation->id,
            'provider_id' => $this->providerLocation->provider_id,
            'location_name' => $this->providerLocation->location_name,
            'geocoding_data' => $this->geocodingData,
            'user_id' => $this->user?->id,
            'user_name' => $this->user?->name,
            'timestamp' => now()->toISOString()
        ];
    }

    /**
     * Get the event name for broadcasting.
     */
    public function broadcastAs(): string
    {
        return 'provider.location.geocoded';
    }

    /**
     * Check if coordinates were added (first time geocoding)
     */
    public function coordinatesAdded(): bool
    {
        $oldLat = $this->geocodingData['old_coordinates']['latitude'];
        $oldLng = $this->geocodingData['old_coordinates']['longitude'];
        $newLat = $this->geocodingData['new_coordinates']['latitude'];
        $newLng = $this->geocodingData['new_coordinates']['longitude'];

        return is_null($oldLat) && is_null($oldLng) && !is_null($newLat) && !is_null($newLng);
    }

    /**
     * Check if coordinates were removed
     */
    public function coordinatesRemoved(): bool
    {
        $oldLat = $this->geocodingData['old_coordinates']['latitude'];
        $oldLng = $this->geocodingData['old_coordinates']['longitude'];
        $newLat = $this->geocodingData['new_coordinates']['latitude'];
        $newLng = $this->geocodingData['new_coordinates']['longitude'];

        return !is_null($oldLat) && !is_null($oldLng) && is_null($newLat) && is_null($newLng);
    }

    /**
     * Check if coordinates were updated
     */
    public function coordinatesUpdated(): bool
    {
        $oldLat = $this->geocodingData['old_coordinates']['latitude'];
        $oldLng = $this->geocodingData['old_coordinates']['longitude'];
        $newLat = $this->geocodingData['new_coordinates']['latitude'];
        $newLng = $this->geocodingData['new_coordinates']['longitude'];

        return !is_null($oldLat) && !is_null($oldLng) && !is_null($newLat) && !is_null($newLng);
    }

    /**
     * Get the geocoding service used
     */
    public function getGeocodingService(): ?string
    {
        return $this->geocodingService;
    }

    /**
     * Get the geocoding method used
     */
    public function getGeocodingMethod(): ?string
    {
        return $this->geocodingMethod;
    }

    /**
     * Check if the geocoding has high accuracy
     */
    public function hasHighAccuracy(): bool
    {
        return in_array($this->getAccuracyLevel(), ['exact', 'high']);
    }

    /**
     * Get the distance change if coordinates were updated
     */
    public function getDistanceChange(): ?float
    {
        if (!$this->coordinatesUpdated()) {
            return null;
        }

        $oldLat = $this->geocodingData['old_coordinates']['latitude'];
        $oldLng = $this->geocodingData['old_coordinates']['longitude'];
        $newLat = $this->geocodingData['new_coordinates']['latitude'];
        $newLng = $this->geocodingData['new_coordinates']['longitude'];

        return $this->calculateDistance($oldLat, $oldLng, $newLat, $newLng);
    }

    /**
     * Get the formatted address from geocoding results
     */
    public function getFormattedAddress(): ?string
    {
        return $this->geocodingResults['formatted_address'] ?? null;
    }

    /**
     * Get the place ID from geocoding results
     */
    public function getPlaceId(): ?string
    {
        return $this->geocodingResults['place_id'] ?? null;
    }

    /**
     * Check if the geocoding was done automatically
     */
    public function wasAutomatic(): bool
    {
        return $this->geocodingMethod === 'automatic';
    }

    /**
     * Check if the geocoding was done manually
     */
    public function wasManual(): bool
    {
        return $this->geocodingMethod === 'manual';
    }
}
