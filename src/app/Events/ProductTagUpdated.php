<?php

namespace Fereydooni\Shopping\app\Events;

use Fereydooni\Shopping\app\Models\ProductTag;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ProductTagUpdated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $tag;
    public $changes;

    /**
     * Create a new event instance.
     */
    public function __construct(ProductTag $tag, array $changes = [])
    {
        $this->tag = $tag;
        $this->changes = $changes;
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
