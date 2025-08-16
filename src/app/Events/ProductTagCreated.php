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

class ProductTagCreated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $tag;

    /**
     * Create a new event instance.
     */
    public function __construct(ProductTag $tag)
    {
        $this->tag = $tag;
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
