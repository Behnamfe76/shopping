<?php

namespace App\Events\ProviderCertification;

use App\Models\ProviderCertification;
use App\Models\User;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ProviderCertificationRevoked
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $certification;

    public $revokedBy;

    public $reason;

    /**
     * Create a new event instance.
     */
    public function __construct(ProviderCertification $certification, User $revokedBy, ?string $reason = null)
    {
        $this->certification = $certification;
        $this->revokedBy = $revokedBy;
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
            'revoked_by' => $this->revokedBy->id,
            'reason' => $this->reason,
            'status' => $this->certification->status,
            'revoked_at' => now(),
        ];
    }
}
