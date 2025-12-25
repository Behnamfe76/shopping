<?php

namespace Fereydooni\Shopping\App\Events\ProviderSpecialization;

use Fereydooni\Shopping\App\Models\ProviderSpecialization;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ProviderSpecializationVerified
{
    use Dispatchable, SerializesModels;

    public $specialization;

    public $verifiedBy;

    /**
     * Create a new event instance.
     */
    public function __construct(ProviderSpecialization $specialization, int $verifiedBy)
    {
        $this->specialization = $specialization;
        $this->verifiedBy = $verifiedBy;
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
            'verified_at' => $this->specialization->verified_at,
            'verified_by' => $this->verifiedBy,
        ];
    }
}
