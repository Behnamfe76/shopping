<?php

namespace Fereydooni\Shopping\app\Events;

use Fereydooni\Shopping\app\Models\ProviderInsurance;
use Fereydooni\Shopping\app\Models\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ProviderInsuranceVerified
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public ProviderInsurance $providerInsurance;

    public User $verifier;

    public array $verificationDetails;

    /**
     * Create a new event instance.
     */
    public function __construct(ProviderInsurance $providerInsurance, User $verifier, array $verificationDetails = [])
    {
        $this->providerInsurance = $providerInsurance;
        $this->verifier = $verifier;
        $this->verificationDetails = $verificationDetails;
    }

    /**
     * Get the channels the event should broadcast on.
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('provider-insurance.'.$this->providerInsurance->id),
            new Channel('provider-insurance'),
            new Channel('provider-insurance.verified'),
        ];
    }

    /**
     * Get the data to broadcast.
     */
    public function broadcastWith(): array
    {
        return [
            'id' => $this->providerInsurance->id,
            'provider_id' => $this->providerInsurance->provider_id,
            'insurance_type' => $this->providerInsurance->insurance_type,
            'policy_number' => $this->providerInsurance->policy_number,
            'provider_name' => $this->providerInsurance->provider_name,
            'coverage_amount' => $this->providerInsurance->coverage_amount,
            'start_date' => $this->providerInsurance->start_date,
            'end_date' => $this->providerInsurance->end_date,
            'status' => $this->providerInsurance->status,
            'verification_status' => $this->providerInsurance->verification_status,
            'verified_by' => $this->verifier->id,
            'verified_at' => $this->providerInsurance->verified_at,
            'verification_details' => $this->verificationDetails,
            'verifier' => [
                'id' => $this->verifier->id,
                'name' => $this->verifier->name,
                'email' => $this->verifier->email,
            ],
        ];
    }

    /**
     * Get the event's broadcast name.
     */
    public function broadcastAs(): string
    {
        return 'provider-insurance.verified';
    }
}
