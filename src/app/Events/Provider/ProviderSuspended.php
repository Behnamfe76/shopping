<?php

namespace Fereydooni\Shopping\app\Events\Provider;

use Fereydooni\Shopping\app\Models\Provider;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ProviderSuspended
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $provider;
    public $reason;

    /**
     * Create a new event instance.
     */
    public function __construct(Provider $provider, ?string $reason = null)
    {
        $this->provider = $provider;
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
            new PrivateChannel('channel-name'),
        ];
    }
}
