<?php

namespace App\Events\EmployeeEmergencyContact;

use App\Models\EmployeeEmergencyContact;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class EmployeeEmergencyContactSetPrimary
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $contact;

    public $previousPrimary;

    /**
     * Create a new event instance.
     */
    public function __construct(EmployeeEmergencyContact $contact, ?EmployeeEmergencyContact $previousPrimary = null)
    {
        $this->contact = $contact;
        $this->previousPrimary = $previousPrimary;
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
            'previous_primary_id' => $this->previousPrimary?->id,
            'previous_primary_name' => $this->previousPrimary?->contact_name,
            'set_at' => now(),
        ];
    }
}
