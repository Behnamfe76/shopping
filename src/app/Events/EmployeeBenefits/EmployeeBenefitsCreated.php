<?php

namespace Fereydooni\Shopping\app\Events\EmployeeBenefits;

use Fereydooni\Shopping\app\Models\EmployeeBenefits;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class EmployeeBenefitsCreated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $benefit;

    /**
     * Create a new event instance.
     */
    public function __construct(EmployeeBenefits $benefit)
    {
        $this->benefit = $benefit;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('employee-benefits'),
            new PrivateChannel('employee.'.$this->benefit->employee_id),
        ];
    }

    /**
     * Get the data to broadcast.
     */
    public function broadcastWith(): array
    {
        return [
            'benefit_id' => $this->benefit->id,
            'employee_id' => $this->benefit->employee_id,
            'benefit_type' => $this->benefit->benefit_type->value,
            'benefit_name' => $this->benefit->benefit_name,
            'status' => $this->benefit->status->value,
            'effective_date' => $this->benefit->effective_date,
            'created_at' => $this->benefit->created_at,
        ];
    }

    /**
     * Get the event name for broadcasting.
     */
    public function broadcastAs(): string
    {
        return 'employee-benefits.created';
    }
}
