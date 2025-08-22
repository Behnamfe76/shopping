<?php

namespace Fereydooni\Shopping\app\Events\Employee;

use Fereydooni\Shopping\app\Models\Employee;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class EmployeeDeleted
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Employee $employee;

    /**
     * Create a new event instance.
     */
    public function __construct(Employee $employee)
    {
        $this->employee = $employee;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('employees'),
        ];
    }

    /**
     * Get the data to broadcast.
     */
    public function broadcastWith(): array
    {
        return [
            'employee_id' => $this->employee->id,
            'employee_name' => $this->employee->first_name . ' ' . $this->employee->last_name,
            'department' => $this->employee->department,
            'position' => $this->employee->position,
            'deleted_at' => now(),
        ];
    }
}
