<?php

namespace Fereydooni\Shopping\app\Events;

use Fereydooni\Shopping\app\Models\ProviderCommunication;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ProviderCommunicationUpdated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public ProviderCommunication $providerCommunication,
        public array $changes,
        public ?string $updatedBy = null
    ) {}

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('provider.'.$this->providerCommunication->provider_id),
            new PrivateChannel('user.'.$this->providerCommunication->user_id),
        ];
    }

    public function broadcastAs(): string
    {
        return 'provider.communication.updated';
    }

    public function broadcastWith(): array
    {
        return [
            'id' => $this->providerCommunication->id,
            'provider_id' => $this->providerCommunication->provider_id,
            'user_id' => $this->providerCommunication->user_id,
            'changes' => $this->changes,
            'updated_by' => $this->updatedBy,
            'updated_at' => $this->providerCommunication->updated_at,
        ];
    }
}
