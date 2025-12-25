<?php

namespace Fereydooni\Shopping\Events\EmployeeTraining;

use Fereydooni\Shopping\Models\EmployeeTraining;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class EmployeeTrainingCreated
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
