<?php

namespace Fereydooni\Shopping\Events\EmployeeTraining;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Fereydooni\Shopping\Models\EmployeeTraining;

class EmployeeTrainingFailed
{
    use Dispatchable, SerializesModels;

    public EmployeeTraining $training;
    public ?string $reason;

    /**
     * Create a new event instance.
     */
    public function __construct(EmployeeTraining $training, ?string $reason = null)
    {
        $this->training = $training;
        $this->reason = $reason;
    }
}
