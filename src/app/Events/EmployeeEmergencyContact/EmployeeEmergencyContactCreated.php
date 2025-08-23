<?php

namespace App\Events\EmployeeEmergencyContact;

use App\Models\EmployeeEmergencyContact;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class EmployeeEmergencyContactCreated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $contact;

    /**
     * Create a new event instance.
     */
    public function __construct(EmployeeEmergencyContact $contact)
    {
        $this->contact = $contact;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('employee.' . $this->contact->employee_id),
        ];
    }

    /**
     * Get the event data for broadcasting.
     */
    public function broadcastWith(): array
    {
        return [
            'contact_id' => $this->contact->id,
            'employee_id' => $this->contact->employee_id,
            'contact_name' => $this->contact->contact_name,
            'relationship' => $this->contact->relationship,
            'is_primary' => $this->contact->is_primary,
            'created_at' => $this->contact->created_at,
        ];
    }
}
