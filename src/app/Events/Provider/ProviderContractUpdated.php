<?php

namespace Fereydooni\Shopping\app\Events\Provider;

use Fereydooni\Shopping\app\Models\ProviderContract;
use Fereydooni\Shopping\app\Models\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ProviderContractUpdated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public ProviderContract $contract;
    public User $updatedBy;
    public array $changes;

    /**
     * Create a new event instance.
     *
     * @param ProviderContract $contract
     * @param User $updatedBy
     * @param array $changes
     */
    public function __construct(ProviderContract $contract, User $updatedBy, array $changes = [])
    {
        $this->contract = $contract;
        $this->updatedBy = $updatedBy;
        $this->changes = $changes;
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
