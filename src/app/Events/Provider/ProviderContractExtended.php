<?php

namespace Fereydooni\Shopping\app\Events\Provider;

use Fereydooni\Shopping\app\Models\Provider;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ProviderContractExtended
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $provider;

    public $oldEndDate;

    public $newEndDate;

    /**
     * Create a new event instance.
     */
    public function __construct(Provider $provider, string $oldEndDate, string $newEndDate)
    {
        $this->provider = $provider;
        $this->oldEndDate = $oldEndDate;
        $this->newEndDate = $newEndDate;
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
