<?php

namespace Fereydooni\Shopping\app\Events;

use Fereydooni\Shopping\app\Models\ProviderCommunication;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ProviderCommunicationDeleted
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public ProviderCommunication $providerCommunication,
        public ?string $deletedBy = null,
        public ?string $reason = null
    ) {}

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('provider.' . $this->providerCommunication->provider_id),
            new PrivateChannel('user.' . $this->providerCommunication->user_id),
        ];
    }

    public function broadcastAs(): string
    {
        return 'provider.communication.deleted';
    }

    public function broadcastWith(): array
    {
        return [
            'id' => $this->providerCommunication->id,
            'provider_id' => $this->providerCommunication->provider_id,
            'user_id' => $this->providerCommunication->user_id,
            'deleted_by' => $this->deletedBy,
            'reason' => $this->reason,
            'deleted_at' => now(),
        ];
    }
}
