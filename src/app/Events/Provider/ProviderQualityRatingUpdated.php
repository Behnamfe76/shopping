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

class ProviderQualityRatingUpdated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $provider;
    public $oldRating;
    public $newRating;

    /**
     * Create a new event instance.
     */
    public function __construct(Provider $provider, float $oldRating, float $newRating)
    {
        $this->provider = $provider;
        $this->oldRating = $oldRating;
        $this->newRating = $newRating;
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
