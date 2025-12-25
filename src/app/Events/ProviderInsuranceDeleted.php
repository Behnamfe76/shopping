<?php

namespace App\Events;

use App\Models\ProviderInsurance;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ProviderInsuranceDeleted
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $providerInsurance;

    public $deletedBy;

    public $reason;

    /**
     * Create a new event instance.
     */
    public function __construct(ProviderInsurance $providerInsurance, int $deletedBy, ?string $reason = null)
    {
        $this->providerInsurance = $providerInsurance;
        $this->deletedBy = $deletedBy;
        $this->reason = $reason;
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
