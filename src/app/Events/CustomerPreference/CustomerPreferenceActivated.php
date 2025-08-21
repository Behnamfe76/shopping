<?php

namespace Fereydooni\Shopping\app\Events\CustomerPreference;

use Fereydooni\Shopping\app\Models\CustomerPreference;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CustomerPreferenceActivated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * The customer preference instance.
     */
    public CustomerPreference $preference;

    /**
     * Create a new event instance.
     */
    public function __construct(CustomerPreference $preference)
    {
        $this->preference = $preference;
    }

    /**
     * Get the channels the event should broadcast on.
     */
    public function broadcastOn(): array
    {
        return [];
    }
}
