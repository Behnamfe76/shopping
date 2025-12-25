<?php

namespace Fereydooni\Shopping\app\Events\Provider;

use Fereydooni\Shopping\app\Models\ProviderContract;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ProviderContractCreated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $contract;

    public $createdAt;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(ProviderContract $contract)
    {
        $this->contract = $contract;
        $this->createdAt = now();
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('provider-contracts');
    }

    /**
     * Get the data to broadcast.
     *
     * @return array
     */
    public function broadcastWith()
    {
        return [
            'contract_id' => $this->contract->id,
            'contract_number' => $this->contract->contract_number,
            'provider_id' => $this->contract->provider_id,
            'contract_type' => $this->contract->contract_type,
            'title' => $this->contract->title,
            'created_at' => $this->createdAt->toISOString(),
            'status' => $this->contract->status,
        ];
    }
}
