<?php

namespace App\Events\ProviderCertification;

use App\Models\ProviderCertification;
use App\Models\User;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ProviderCertificationSuspended
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $certification;

    public $suspendedBy;

    public $reason;

    /**
     * Create a new event instance.
     */
    public function __construct(ProviderCertification $certification, User $suspendedBy, ?string $reason = null)
    {
        $this->certification = $certification;
        $this->suspendedBy = $suspendedBy;
        $this->reason = $reason;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('provider-certification.'.$this->certification->id),
            new PrivateChannel('provider.'.$this->certification->provider_id),
        ];
    }

    /**
     * Get the data to broadcast.
     */
    public function broadcastWith(): array
    {
        return [
            'certification_id' => $this->certification->id,
            'provider_id' => $this->certification->provider_id,
            'certification_name' => $this->certification->certification_name,
            'suspended_by' => $this->suspendedBy->id,
            'reason' => $this->reason,
            'status' => $this->certification->status,
            'suspended_at' => now(),
        ];
    }
}
