<?php

namespace Fereydooni\Shopping\app\Events\Provider;

use Fereydooni\Shopping\app\Models\Provider;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ProviderCreditLimitUpdated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $provider;

    public $oldLimit;

    public $newLimit;

    /**
     * Create a new event instance.
     */
    public function __construct(Provider $provider, float $oldLimit, float $newLimit)
    {
        $this->provider = $provider;
        $this->oldLimit = $oldLimit;
        $this->newLimit = $newLimit;
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
