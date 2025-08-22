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

class SalaryUpdated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Employee $employee;
    public float $oldSalary;
    public float $newSalary;
    public string $effectiveDate;

    /**
     * Create a new event instance.
     */
    public function __construct(Employee $employee, float $oldSalary, float $newSalary, string $effectiveDate = null)
    {
        $this->employee = $employee;
        $this->oldSalary = $oldSalary;
        $this->newSalary = $newSalary;
        $this->effectiveDate = $effectiveDate ?? now()->toDateString();
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
            'old_salary' => $this->oldSalary,
            'new_salary' => $this->newSalary,
            'salary_change' => $this->newSalary - $this->oldSalary,
            'effective_date' => $this->effectiveDate,
            'updated_at' => now(),
        ];
    }
}
