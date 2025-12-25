<?php

namespace App\Events;

use App\Models\ProviderCommunication;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ProviderCommunicationCreated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $communication;

    /**
     * Create a new event instance.
     */
    public function __construct(ProviderCommunication $communication)
    {
        $this->communication = $communication;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('provider-communications'),
            new PrivateChannel('provider.'.$this->communication->provider_id),
            new PrivateChannel('user.'.$this->communication->user_id),
        ];
    }

    /**
     * Get the data to broadcast.
     */
    public function broadcastWith(): array
    {
        return [
            'id' => $this->communication->id,
            'provider_id' => $this->communication->provider_id,
            'user_id' => $this->communication->user_id,
            'subject' => $this->communication->subject,
            'communication_type' => $this->communication->communication_type,
            'direction' => $this->communication->direction,
            'status' => $this->communication->status,
            'priority' => $this->communication->priority,
            'is_urgent' => $this->communication->is_urgent,
            'thread_id' => $this->communication->thread_id,
            'created_at' => $this->communication->created_at,
        ];
    }

    /**
     * Get the event name.
     */
    public function broadcastAs(): string
    {
        return 'provider-communication.created';
    }
}
