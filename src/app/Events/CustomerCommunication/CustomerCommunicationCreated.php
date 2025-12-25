<?php

namespace Fereydooni\Shopping\app\Events\CustomerCommunication;

use Fereydooni\Shopping\app\Models\CustomerCommunication;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CustomerCommunicationCreated
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
