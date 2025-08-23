<?php

namespace Fereydooni\Shopping\app\Events\Provider;

use Fereydooni\Shopping\app\Models\ProviderContract;
use Illuminate\Foundation\Auth\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ProviderContractSigned
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $contract;
    public $signer;
    public $signedAt;

    /**
     * Create a new event instance.
     *
     * @param ProviderContract $contract
     * @param User $signer
     * @return void
     */
    public function __construct(ProviderContract $contract, User $signer)
    {
        $this->contract = $contract;
        $this->signer = $signer;
        $this->signedAt = now();
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
            'signer_id' => $this->signer->id,
            'signer_name' => $this->signer->name,
            'signed_at' => $this->signedAt->toISOString(),
            'status' => $this->contract->status,
            'start_date' => $this->contract->start_date?->toISOString(),
            'end_date' => $this->contract->end_date?->toISOString(),
        ];
    }
}
