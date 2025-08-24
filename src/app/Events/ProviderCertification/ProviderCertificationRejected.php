<?php

namespace App\Events\ProviderCertification;

use App\Models\ProviderCertification;
use App\Models\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ProviderCertificationRejected
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $certification;
    public $rejectedBy;
    public $reason;

    /**
     * Create a new event instance.
     */
    public function __construct(ProviderCertification $certification, User $rejectedBy, ?string $reason = null)
    {
        $this->certification = $certification;
        $this->rejectedBy = $rejectedBy;
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
            new PrivateChannel('provider-certification.' . $this->certification->id),
            new PrivateChannel('provider.' . $this->certification->provider_id),
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
            'rejected_by' => $this->rejectedBy->id,
            'reason' => $this->reason,
            'verification_status' => $this->certification->verification_status,
            'rejected_at' => now(),
        ];
    }
}
