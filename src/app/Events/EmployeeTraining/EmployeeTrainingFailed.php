<?php

namespace Fereydooni\Shopping\Events\EmployeeTraining;

use Fereydooni\Shopping\Models\EmployeeTraining;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

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
