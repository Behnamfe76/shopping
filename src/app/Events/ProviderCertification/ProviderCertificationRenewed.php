<?php

namespace App\Events\ProviderCertification;

use App\Models\ProviderCertification;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ProviderCertificationRenewed
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $certification;
    public $oldExpiryDate;
    public $newExpiryDate;

    /**
     * Create a new event instance.
     */
    public function __construct(ProviderCertification $certification, string $oldExpiryDate, string $newExpiryDate)
    {
        $this->certification = $certification;
        $this->oldExpiryDate = $oldExpiryDate;
        $this->newExpiryDate = $newExpiryDate;
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
            'old_expiry_date' => $this->oldExpiryDate,
            'new_expiry_date' => $this->newExpiryDate,
            'status' => $this->certification->status,
            'renewed_at' => now(),
        ];
    }
}
