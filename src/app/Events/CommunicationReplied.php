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

class CommunicationReplied
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public ProviderCommunication $providerCommunication,
        public ?string $replier = null,
        public ?string $replyContent = null,
        public ?string $threadId = null
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
        return 'provider.communication.replied';
    }

    public function broadcastWith(): array
    {
        return [
            'id' => $this->providerCommunication->id,
            'provider_id' => $this->providerCommunication->provider_id,
            'user_id' => $this->providerCommunication->user_id,
            'subject' => $this->providerCommunication->subject,
            'replier' => $this->replier,
            'reply_content' => $this->replyContent,
            'thread_id' => $this->threadId,
            'replied_at' => $this->providerCommunication->replied_at,
        ];
    }
}
