<?php

namespace Fereydooni\Shopping\app\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ProductTagSynced
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $productId;

    public $tagIds;

    public $syncedBy;

    public $results;

    /**
     * Create a new event instance.
     */
    public function __construct(int $productId, array $tagIds, $syncedBy = null, array $results = [])
    {
        $this->productId = $productId;
        $this->tagIds = $tagIds;
        $this->syncedBy = $syncedBy;
        $this->results = $results;
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
