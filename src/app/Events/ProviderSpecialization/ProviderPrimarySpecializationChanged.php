<?php

namespace Fereydooni\Shopping\App\Events\ProviderSpecialization;

use Fereydooni\Shopping\App\Models\ProviderSpecialization;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ProviderPrimarySpecializationChanged
{
    use Dispatchable, SerializesModels;

    public $specialization;

    public $previousPrimary;

    /**
     * Create a new event instance.
     */
    public function __construct(ProviderSpecialization $specialization, ?ProviderSpecialization $previousPrimary = null)
    {
        $this->specialization = $specialization;
        $this->previousPrimary = $previousPrimary;
    }

    /**
     * Get the channels the event should broadcast on.
     */
    public function broadcastOn(): array
    {
        return [];
    }

    /**
     * Get the data to broadcast.
     */
    public function broadcastWith(): array
    {
        return [
            'new_primary_id' => $this->specialization->id,
            'provider_id' => $this->specialization->provider_id,
            'specialization_name' => $this->specialization->specialization_name,
            'previous_primary_id' => $this->previousPrimary?->id,
            'previous_primary_name' => $this->previousPrimary?->specialization_name,
            'changed_at' => $this->specialization->updated_at,
        ];
    }
}
