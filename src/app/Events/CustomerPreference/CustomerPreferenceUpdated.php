<?php

namespace Fereydooni\Shopping\app\Events\CustomerPreference;

use Fereydooni\Shopping\app\Models\CustomerPreference;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CustomerPreferenceUpdated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * The customer preference instance.
     */
    public CustomerPreference $preference;

    /**
     * The old preference data.
     */
    public array $oldData;

    /**
     * Create a new event instance.
     */
    public function __construct(CustomerPreference $preference, array $oldData = [])
    {
        $this->preference = $preference;
        $this->oldData = $oldData;
    }

    /**
     * Get the channels the event should broadcast on.
     */
    public function broadcastOn(): array
    {
        return [];
    }
}
