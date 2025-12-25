<?php

namespace Fereydooni\Shopping\app\Events\Provider;

use Fereydooni\Shopping\app\Models\ProviderContract;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ProviderContractExpiring
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public ProviderContract $contract;

    public int $daysUntilExpiry;

    /**
     * Create a new event instance.
     */
    public function __construct(ProviderContract $contract, int $daysUntilExpiry)
    {
        $this->contract = $contract;
        $this->daysUntilExpiry = $daysUntilExpiry;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('channel-name');
    }
}
