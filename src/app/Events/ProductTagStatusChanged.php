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

class ProductTagStatusChanged
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $tag;
    public $oldStatus;
    public $newStatus;
    public $statusType;

    /**
     * Create a new event instance.
     */
    public function __construct(ProductTag $tag, $oldStatus, $newStatus, string $statusType = 'active')
    {
        $this->tag = $tag;
        $this->oldStatus = $oldStatus;
        $this->newStatus = $newStatus;
        $this->statusType = $statusType;
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
