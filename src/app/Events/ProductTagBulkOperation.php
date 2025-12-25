<?php

namespace Fereydooni\Shopping\app\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ProductTagBulkOperation
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $operation;

    public $tagIds;

    public $results;

    /**
     * Create a new event instance.
     */
    public function __construct(string $operation, array $tagIds, array $results = [])
    {
        $this->operation = $operation;
        $this->tagIds = $tagIds;
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
