<?php

namespace Fereydooni\Shopping\app\Events\Provider;

use Fereydooni\Shopping\app\Models\ProviderLocation;
use Fereydooni\Shopping\app\Models\User;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ProviderLocationCreated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public ProviderLocation $providerLocation;

    public ?User $user;

    public array $locationData;

    public array $geospatialData;

    /**
     * Create a new event instance.
     */
    public function __construct(ProviderLocation $providerLocation, ?User $user = null)
    {
        $this->providerLocation = $providerLocation;
        $this->user = $user;

        // Extract location data for event
        $this->locationData = [
            'id' => $providerLocation->id,
            'provider_id' => $providerLocation->provider_id,
            'location_name' => $providerLocation->location_name,
            'address' => $providerLocation->address,
            'city' => $providerLocation->city,
            'state' => $providerLocation->state,
            'country' => $providerLocation->country,
            'location_type' => $providerLocation->location_type,
            'is_primary' => $providerLocation->is_primary,
            'is_active' => $providerLocation->is_active,
            'created_at' => $providerLocation->created_at?->toISOString(),
        ];

        // Extract geospatial data
        $this->geospatialData = [
            'latitude' => $providerLocation->latitude,
            'longitude' => $providerLocation->longitude,
            'has_coordinates' => ! is_null($providerLocation->latitude) && ! is_null($providerLocation->longitude),
            'coordinates_formatted' => $providerLocation->latitude && $providerLocation->longitude
                ? "{$providerLocation->latitude}, {$providerLocation->longitude}"
                : null,
        ];
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
            'event_type' => 'provider_location_created',
            'location_id' => $this->providerLocation->id,
            'provider_id' => $this->providerLocation->provider_id,
            'location_name' => $this->providerLocation->location_name,
            'location_type' => $this->providerLocation->location_type,
            'is_primary' => $this->providerLocation->is_primary,
            'is_active' => $this->providerLocation->is_active,
            'address' => $this->providerLocation->address,
            'city' => $this->providerLocation->city,
            'state' => $this->providerLocation->state,
            'country' => $this->providerLocation->country,
            'geospatial' => $this->geospatialData,
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
        return 'provider.location.created';
    }
}
