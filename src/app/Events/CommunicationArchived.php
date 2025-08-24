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

class CommunicationArchived
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public ProviderCommunication $providerCommunication,
        public ?string $archiver = null,
        public ?string $archiveReason = null
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
        return 'provider.communication.archived';
    }

    public function broadcastWith(): array
    {
        return [
            'id' => $this->providerCommunication->id,
            'provider_id' => $this->providerCommunication->provider_id,
            'user_id' => $this->providerCommunication->user_id,
            'subject' => $this->providerCommunication->subject,
            'archiver' => $this->archiver,
            'archive_reason' => $this->archiveReason,
            'archived_at' => now(),
        ];
    }
}
