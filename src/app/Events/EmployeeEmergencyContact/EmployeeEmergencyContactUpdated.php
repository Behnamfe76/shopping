<?php

namespace App\Events\EmployeeEmergencyContact;

use App\Models\EmployeeEmergencyContact;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class EmployeeEmergencyContactUpdated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $contact;

    public $changes;

    /**
     * Create a new event instance.
     */
    public function __construct(EmployeeEmergencyContact $contact, array $changes = [])
    {
        $this->contact = $contact;
        $this->changes = $changes;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('employee.'.$this->contact->employee_id),
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
            'changes' => $this->changes,
            'updated_at' => $this->contact->updated_at,
        ];
    }
}
