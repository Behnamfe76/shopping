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

class ProviderCertificationUpdated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $certification;
    public $changes;

    /**
     * Create a new event instance.
     */
    public function __construct(ProviderCertification $certification, array $changes = [])
    {
        $this->certification = $certification;
        $this->changes = $changes;
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
            'status' => $this->certification->status,
            'changes' => $this->changes,
            'updated_at' => $this->certification->updated_at,
        ];
    }
}
