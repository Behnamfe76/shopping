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

class ProviderLocationUpdated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public ProviderLocation $providerLocation;
    public ?User $user;
    public array $changes;
    public array $originalData;
    public array $newData;
    public array $geospatialChanges;

    /**
     * Create a new event instance.
     */
    public function __construct(ProviderLocation $providerLocation, array $changes, ?User $user = null)
    {
        $this->providerLocation = $providerLocation;
        $this->user = $user;
        $this->changes = $changes;

        // Get original data from the model's original attributes
        $this->originalData = $providerLocation->getOriginal();

        // Get new data
        $this->newData = $providerLocation->toArray();

        // Extract geospatial changes
        $this->geospatialChanges = $this->extractGeospatialChanges();
    }

    /**
     * Extract geospatial changes from the update
     */
    protected function extractGeospatialChanges(): array
    {
        $changes = [];

        if (isset($this->changes['latitude'])) {
            $changes['latitude'] = [
                'from' => $this->originalData['latitude'] ?? null,
                'to' => $this->changes['latitude']
            ];
        }

        if (isset($this->changes['longitude'])) {
            $changes['longitude'] = [
                'from' => $this->originalData['longitude'] ?? null,
                'to' => $this->changes['longitude']
            ];
        }

        if (isset($this->changes['address']) || isset($this->changes['city']) ||
            isset($this->changes['state']) || isset($this->changes['postal_code']) ||
            isset($this->changes['country'])) {
            $changes['address_components'] = [
                'address' => [
                    'from' => $this->originalData['address'] ?? null,
                    'to' => $this->changes['address'] ?? $this->originalData['address'] ?? null
                ],
                'city' => [
                    'from' => $this->originalData['city'] ?? null,
                    'to' => $this->changes['city'] ?? $this->originalData['city'] ?? null
                ],
                'state' => [
                    'from' => $this->originalData['state'] ?? null,
                    'to' => $this->changes['state'] ?? $this->originalData['state'] ?? null
                ],
                'postal_code' => [
                    'from' => $this->originalData['postal_code'] ?? null,
                    'to' => $this->changes['postal_code'] ?? $this->originalData['postal_code'] ?? null
                ],
                'country' => [
                    'from' => $this->originalData['country'] ?? null,
                    'to' => $this->changes['country'] ?? $this->originalData['country'] ?? null
                ]
            ];
        }

        return $changes;
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
            'event_type' => 'provider_location_updated',
            'location_id' => $this->providerLocation->id,
            'provider_id' => $this->providerLocation->provider_id,
            'location_name' => $this->providerLocation->location_name,
            'changes' => $this->changes,
            'geospatial_changes' => $this->geospatialChanges,
            'has_coordinate_changes' => isset($this->changes['latitude']) || isset($this->changes['longitude']),
            'has_address_changes' => isset($this->changes['address']) || isset($this->changes['city']) ||
                                   isset($this->changes['state']) || isset($this->changes['postal_code']) ||
                                   isset($this->changes['country']),
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
        return 'provider.location.updated';
    }

    /**
     * Check if the update includes significant changes
     */
    public function hasSignificantChanges(): bool
    {
        $significantFields = [
            'location_name', 'address', 'city', 'state', 'country',
            'latitude', 'longitude', 'is_primary', 'is_active'
        ];

        foreach ($significantFields as $field) {
            if (isset($this->changes[$field])) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check if coordinates were updated
     */
    public function coordinatesUpdated(): bool
    {
        return isset($this->changes['latitude']) || isset($this->changes['longitude']);
    }

    /**
     * Check if address was updated
     */
    public function addressUpdated(): bool
    {
        $addressFields = ['address', 'city', 'state', 'postal_code', 'country'];

        foreach ($addressFields as $field) {
            if (isset($this->changes[$field])) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check if primary status changed
     */
    public function primaryStatusChanged(): bool
    {
        return isset($this->changes['is_primary']);
    }

    /**
     * Check if active status changed
     */
    public function activeStatusChanged(): bool
    {
        return isset($this->changes['is_active']);
    }
}
