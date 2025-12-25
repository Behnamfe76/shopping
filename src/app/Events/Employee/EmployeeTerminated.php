<?php

namespace Fereydooni\Shopping\app\Events\Employee;

use Fereydooni\Shopping\app\Models\Employee;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class EmployeeTerminated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Employee $employee;

    public string $reason;

    /**
     * Create a new event instance.
     */
    public function __construct(Employee $employee, string $reason = '')
    {
        $this->employee = $employee;
        $this->reason = $reason;
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
            'status' => 'terminated',
            'reason' => $this->reason,
            'terminated_at' => now(),
        ];
    }
}
