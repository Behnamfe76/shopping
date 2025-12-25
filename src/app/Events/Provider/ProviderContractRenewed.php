<?php

namespace Fereydooni\Shopping\app\Events\Provider;

use Fereydooni\Shopping\app\Models\ProviderContract;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ProviderContractRenewed
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $contract;

    public $renewedAt;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(ProviderContract $contract)
    {
        $this->contract = $contract;
        $this->renewedAt = now();
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
            'renewed_at' => $this->renewedAt->toISOString(),
            'status' => $this->contract->status,
            'start_date' => $this->contract->start_date?->toISOString(),
            'end_date' => $this->contract->end_date?->toISOString(),
            'renewal_date' => $this->contract->renewal_date?->toISOString(),
        ];
    }
}
