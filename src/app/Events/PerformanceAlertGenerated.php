<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PerformanceAlertGenerated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $alertData;
    public $thresholdInformation;
    public $providerInformation;

    /**
     * Create a new event instance.
     */
    public function __construct(array $alertData, array $thresholdInformation, array $providerInformation)
    {
        $this->alertData = $alertData;
        $this->thresholdInformation = $thresholdInformation;
        $this->providerInformation = $providerInformation;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new Channel('performance-alerts'),
            new PrivateChannel('provider.' . $this->providerInformation['id']),
        ];
    }

    /**
     * Get the data to broadcast.
     */
    public function broadcastWith(): array
    {
        return [
            'alert_type' => $this->alertData['type'],
            'severity' => $this->alertData['severity'],
            'message' => $this->alertData['message'],
            'provider_id' => $this->providerInformation['id'],
            'provider_name' => $this->providerInformation['name'],
            'threshold_value' => $this->thresholdInformation['value'],
            'current_value' => $this->thresholdInformation['current'],
            'generated_at' => now(),
        ];
    }
}
