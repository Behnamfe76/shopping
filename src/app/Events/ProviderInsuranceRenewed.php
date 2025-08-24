<?php

namespace App\Events;

use App\Models\ProviderInsurance;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ProviderInsuranceRenewed
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $providerInsurance;
    public $renewalDetails;
    public $newDates;

    /**
     * Create a new event instance.
     */
    public function __construct(ProviderInsurance $providerInsurance, array $renewalDetails, array $newDates)
    {
        $this->providerInsurance = $providerInsurance;
        $this->renewalDetails = $renewalDetails;
        $this->newDates = $newDates;
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
