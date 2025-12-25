<?php

namespace Fereydooni\Shopping\app\Events\Employee;

use Fereydooni\Shopping\app\Models\Employee;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class EmployeeUpdated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Employee $employee;

    public array $changes;

    /**
     * Create a new event instance.
     */
    public function __construct(Employee $employee, array $changes = [])
    {
        $this->employee = $employee;
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
            'employee_name' => $this->employee->first_name.' '.$this->employee->last_name,
            'changes' => $this->changes,
            'updated_at' => $this->employee->updated_at,
        ];
    }
}
