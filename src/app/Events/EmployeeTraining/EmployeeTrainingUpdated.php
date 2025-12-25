<?php

namespace Fereydooni\Shopping\Events\EmployeeTraining;

use Fereydooni\Shopping\Models\EmployeeTraining;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class EmployeeTrainingUpdated
{
    use Dispatchable, SerializesModels;

    public EmployeeTraining $training;

    public array $changes;

    /**
     * Create a new event instance.
     */
    public function __construct(EmployeeTraining $training, array $changes = [])
    {
        $this->training = $training;
        $this->changes = $changes;
    }
}
