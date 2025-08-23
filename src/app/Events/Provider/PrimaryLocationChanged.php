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

class PrimaryLocationChanged
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public ProviderLocation $newPrimaryLocation;
    public ?ProviderLocation $previousPrimaryLocation;
    public ?User $user;
    public array $changeData;

    /**
     * Create a new event instance.
     */
    public function __construct(
        ProviderLocation $newPrimaryLocation,
        ?ProviderLocation $previousPrimaryLocation = null,
        ?User $user = null
    ) {
        $this->newPrimaryLocation = $newPrimaryLocation;
        $this->previousPrimaryLocation = $previousPrimaryLocation;
        $this->user = $user;

        // Extract change data
        $this->changeData = [
            'provider_id' => $newPrimaryLocation->provider_id,
            'new_primary_location' => [
                'id' => $newPrimaryLocation->id,
                'location_name' => $newPrimaryLocation->location_name,
                'address' => $newPrimaryLocation->address,
                'city' => $newPrimaryLocation->city,
                'state' => $newPrimaryLocation->state,
                'country' => $newPrimaryLocation->country,
                'location_type' => $newPrimaryLocation->location_type,
                'is_primary' => $newPrimaryLocation->is_primary,
                'is_active' => $newPrimaryLocation->is_active,
                'changed_at' => now()->toISOString()
            ],
            'previous_primary_location' => $previousPrimaryLocation ? [
                'id' => $previousPrimaryLocation->id,
                'location_name' => $previousPrimaryLocation->location_name,
                'address' => $previousPrimaryLocation->address,
                'city' => $previousPrimaryLocation->city,
                'state' => $previousPrimaryLocation->state,
                'country' => $previousPrimaryLocation->country,
                'location_type' => $previousPrimaryLocation->location_type,
                'is_primary' => $previousPrimaryLocation->is_primary,
                'is_active' => $previousPrimaryLocation->is_active
            ] : null,
            'change_type' => $this->determineChangeType(),
            'timestamp' => now()->toISOString()
        ];
    }

    /**
     * Determine the type of primary location change
     */
    protected function determineChangeType(): string
    {
        if (!$this->previousPrimaryLocation) {
            return 'first_primary_set';
        }

        if ($this->previousPrimaryLocation->id === $this->newPrimaryLocation->id) {
            return 'same_location';
        }

        return 'primary_changed';
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('provider.' . $this->newPrimaryLocation->provider_id),
            new PrivateChannel('admin.provider-locations'),
        ];
    }

    /**
     * Get the data to broadcast.
     */
    public function broadcastWith(): array
    {
        return [
            'event_type' => 'primary_location_changed',
            'provider_id' => $this->newPrimaryLocation->provider_id,
            'change_data' => $this->changeData,
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
        return 'provider.primary_location.changed';
    }

    /**
     * Check if this is the first primary location being set
     */
    public function isFirstPrimary(): bool
    {
        return $this->changeData['change_type'] === 'first_primary_set';
    }

    /**
     * Check if the primary location actually changed
     */
    public function hasChanged(): bool
    {
        return $this->changeData['change_type'] === 'primary_changed';
    }

    /**
     * Check if the change involves the same location
     */
    public function isSameLocation(): bool
    {
        return $this->changeData['change_type'] === 'same_location';
    }

    /**
     * Get the provider ID
     */
    public function getProviderId(): int
    {
        return $this->newPrimaryLocation->provider_id;
    }

    /**
     * Get the new primary location
     */
    public function getNewPrimaryLocation(): ProviderLocation
    {
        return $this->newPrimaryLocation;
    }

    /**
     * Get the previous primary location
     */
    public function getPreviousPrimaryLocation(): ?ProviderLocation
    {
        return $this->previousPrimaryLocation;
    }

    /**
     * Check if there was a previous primary location
     */
    public function hadPreviousPrimary(): bool
    {
        return $this->previousPrimaryLocation !== null;
    }

    /**
     * Get the change reason (if available)
     */
    public function getChangeReason(): ?string
    {
        // This could be enhanced to include business logic for why the change occurred
        if ($this->isFirstPrimary()) {
            return 'First primary location set for provider';
        }

        if ($this->hasChanged()) {
            return 'Primary location changed by user';
        }

        return null;
    }
}
