<?php

namespace Fereydooni\Shopping\app\Events\CustomerCommunication;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Fereydooni\Shopping\app\Models\CustomerCommunication;

class CustomerCommunicationDelivered
{
    use Dispatchable, SerializesModels;

    public CustomerCommunication $communication;

    /**
     * Create a new event instance.
     */
    public function __construct(CustomerCommunication $communication)
    {
        $this->communication = $communication;
    }
}
