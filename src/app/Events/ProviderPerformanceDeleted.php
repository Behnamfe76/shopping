<?php

namespace App\Events;

use App\Models\ProviderPerformance;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ProviderPerformanceDeleted
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $providerPerformance;
    public $user;
    public $reason;

    /**
     * Create a new event instance.
     */
    public function __construct(ProviderPerformance $providerPerformance, $user = null, $reason = null)
    {
        $this->providerPerformance = $providerPerformance;
        $this->user = $user;
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
            new Channel('provider-performance-deletions'),
        ];
    }

    /**
     * Get the data to broadcast.
     */
    public function broadcastWith(): array
    {
        return [
            'id' => $this->providerPerformance->id,
            'provider_id' => $this->providerPerformance->provider_id,
            'deleted_at' => now(),
            'user_id' => $this->user?->id,
            'reason' => $this->reason,
        ];
    }
}
