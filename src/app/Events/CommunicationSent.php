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

class CommunicationSent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public ProviderCommunication $providerCommunication,
        public ?string $sender = null,
        public ?string $recipient = null,
        public ?string $deliveryStatus = null
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
        return 'provider.communication.sent';
    }

    public function broadcastWith(): array
    {
        return [
            'id' => $this->providerCommunication->id,
            'provider_id' => $this->providerCommunication->provider_id,
            'user_id' => $this->providerCommunication->user_id,
            'subject' => $this->providerCommunication->subject,
            'communication_type' => $this->providerCommunication->communication_type,
            'sender' => $this->sender,
            'recipient' => $this->recipient,
            'delivery_status' => $this->deliveryStatus,
            'sent_at' => $this->providerCommunication->sent_at,
        ];
    }
}
