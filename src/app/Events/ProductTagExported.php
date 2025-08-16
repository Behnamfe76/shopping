<?php

namespace Fereydooni\Shopping\app\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ProductTagExported
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $exportData;
    public $exportedBy;
    public $format;

    /**
     * Create a new event instance.
     */
    public function __construct(array $exportData, $exportedBy = null, string $format = 'json')
    {
        $this->exportData = $exportData;
        $this->exportedBy = $exportedBy;
        $this->format = $format;
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
