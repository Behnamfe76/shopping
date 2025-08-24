<?php

namespace App\Events;

use App\Models\ProviderPerformance;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ProviderPerformanceVerified
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $providerPerformance;
    public $verifier;
    public $verificationDetails;

    /**
     * Create a new event instance.
     */
    public function __construct(ProviderPerformance $providerPerformance, $verifier, array $verificationDetails = [])
    {
        $this->providerPerformance = $providerPerformance;
        $this->verifier = $verifier;
        $this->verificationDetails = $verificationDetails;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('provider-performance.' . $this->providerPerformance->id),
            new Channel('provider-performance-verifications'),
        ];
    }

    /**
     * Get the data to broadcast.
     */
    public function broadcastWith(): array
    {
        return [
            'id' => $this->providerPerformance->id,
            'provider_id' => $this->providerPerformance->provider_id,
            'verifier_id' => $this->verifier->id,
            'verifier_name' => $this->verifier->name,
            'verified_at' => $this->providerPerformance->verified_at,
            'verification_details' => $this->verificationDetails,
        ];
    }
}
