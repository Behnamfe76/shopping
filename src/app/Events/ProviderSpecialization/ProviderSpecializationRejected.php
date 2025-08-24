<?php

namespace Fereydooni\Shopping\App\Events\ProviderSpecialization;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Fereydooni\Shopping\App\Models\ProviderSpecialization;

class ProviderSpecializationRejected
{
    use Dispatchable, SerializesModels;

    public $specialization;
    public $reason;

    /**
     * Create a new event instance.
     */
    public function __construct(ProviderSpecialization $specialization, ?string $reason = null)
    {
        $this->specialization = $specialization;
        $this->reason = $reason;
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
            'reason' => $this->reason,
            'rejected_at' => $this->specialization->updated_at,
        ];
    }
}
