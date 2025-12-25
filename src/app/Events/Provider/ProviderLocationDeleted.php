<?php

namespace Fereydooni\Shopping\app\Events\Provider;

use Fereydooni\Shopping\app\Models\ProviderLocation;
use Fereydooni\Shopping\app\Models\User;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ProviderLocationDeleted
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public ProviderLocation $providerLocation;

    public ?User $user;

    public ?string $reason;

    public array $locationData;

    public array $geospatialData;

    /**
     * Create a new event instance.
     */
    public function __construct(ProviderLocation $providerLocation, ?User $user = null, ?string $reason = null)
    {
        $this->providerLocation = $providerLocation;
        $this->user = $user;
        $this->reason = $reason;

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
            'deleted_at' => now()->toISOString(),
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
            'event_type' => 'provider_location_deleted',
            'location_id' => $this->providerLocation->id,
            'provider_id' => $this->providerLocation->provider_id,
            'location_name' => $this->providerLocation->location_name,
            'location_type' => $this->providerLocation->location_type,
            'was_primary' => $this->providerLocation->is_primary,
            'was_active' => $this->providerLocation->is_active,
            'address' => $this->providerLocation->address,
            'city' => $this->providerLocation->city,
            'state' => $this->providerLocation->state,
            'country' => $this->providerLocation->country,
            'geospatial' => $this->geospatialData,
            'reason' => $this->reason,
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
        return 'provider.location.deleted';
    }

    /**
     * Check if the deleted location was primary
     */
    public function wasPrimary(): bool
    {
        return $this->providerLocation->is_primary;
    }

    /**
     * Check if the deleted location was active
     */
    public function wasActive(): bool
    {
        return $this->providerLocation->is_active;
    }

    /**
     * Check if the deleted location had coordinates
     */
    public function hadCoordinates(): bool
    {
        return ! is_null($this->providerLocation->latitude) && ! is_null($this->providerLocation->longitude);
    }

    /**
     * Get the deletion reason
     */
    public function getReason(): ?string
    {
        return $this->reason;
    }

    /**
     * Check if deletion was performed by admin
     */
    public function deletedByAdmin(): bool
    {
        return $this->user && $this->user->hasRole(['admin', 'manager']);
    }

    /**
     * Check if deletion was performed by provider
     */
    public function deletedByProvider(): bool
    {
        return $this->user && $this->user->hasRole('provider');
    }
}
