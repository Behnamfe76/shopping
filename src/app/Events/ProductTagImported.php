<?php

namespace Fereydooni\Shopping\app\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ProductTagImported
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $importData;
    public $results;
    public $importedBy;

    /**
     * Create a new event instance.
     */
    public function __construct(array $importData, array $results, $importedBy = null)
    {
        $this->importData = $importData;
        $this->results = $results;
        $this->importedBy = $importedBy;
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
