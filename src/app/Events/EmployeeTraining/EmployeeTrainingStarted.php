<?php

namespace Fereydooni\Shopping\Events\EmployeeTraining;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Fereydooni\Shopping\Models\EmployeeTraining;

class EmployeeTrainingStarted
{
    use Dispatchable, SerializesModels;

    public EmployeeTraining $training;

    /**
     * Create a new event instance.
     */
    public function __construct(EmployeeTraining $training)
    {
        $this->training = $training;
    }
}
