<?php

namespace App\Events;

use App\Models\ProviderInsurance;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ProviderInsuranceDocumentUploaded
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $providerInsurance;

    public $documentInfo;

    public $uploader;

    /**
     * Create a new event instance.
     */
    public function __construct(ProviderInsurance $providerInsurance, array $documentInfo, int $uploader)
    {
        $this->providerInsurance = $providerInsurance;
        $this->documentInfo = $documentInfo;
        $this->uploader = $uploader;
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
