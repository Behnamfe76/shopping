<?php

namespace Fereydooni\Shopping\App\Events\ProviderSpecialization;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Fereydooni\Shopping\App\Models\ProviderSpecialization;

class ProviderSpecializationCreated
{
    use Dispatchable, SerializesModels;

    public $specialization;

    /**
     * Create a new event instance.
     */
    public function __construct(ProviderSpecialization $specialization)
    {
        $this->specialization = $specialization;
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
            'id' => $this->specialization->id,
            'provider_id' => $this->specialization->provider_id,
            'specialization_name' => $this->specialization->specialization_name,
            'category' => $this->specialization->category->value,
            'proficiency_level' => $this->specialization->proficiency_level->value,
            'verification_status' => $this->specialization->verification_status->value,
            'is_primary' => $this->specialization->is_primary,
            'is_active' => $this->specialization->is_active,
            'created_at' => $this->specialization->created_at,
        ];
    }
}
