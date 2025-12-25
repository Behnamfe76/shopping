<?php

namespace App\Events;

use App\Models\ProviderPerformance;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ProviderPerformanceUpdated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $providerPerformance;

    public $changes;

    public $user;

    /**
     * Create a new event instance.
     */
    public function __construct(ProviderPerformance $providerPerformance, array $changes, $user = null)
    {
        $this->providerPerformance = $providerPerformance;
        $this->changes = $changes;
        $this->user = $user;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('provider-performance.'.$this->providerPerformance->id),
            new Channel('provider-performance-updates'),
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
            'changes' => $this->changes,
            'updated_at' => $this->providerPerformance->updated_at,
            'user_id' => $this->user?->id,
        ];
    }
}
