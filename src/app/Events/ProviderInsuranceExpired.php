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

class ProviderInsuranceExpired
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $providerInsurance;
    public $expirationDate;
    public $notificationData;

    /**
     * Create a new event instance.
     */
    public function __construct(ProviderInsurance $providerInsurance, string $expirationDate, array $notificationData = [])
    {
        $this->providerInsurance = $providerInsurance;
        $this->expirationDate = $expirationDate;
        $this->notificationData = $notificationData;
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
