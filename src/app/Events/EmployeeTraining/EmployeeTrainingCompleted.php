<?php

namespace Fereydooni\Shopping\Events\EmployeeTraining;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Fereydooni\Shopping\Models\EmployeeTraining;

class EmployeeTrainingCompleted
{
    use Dispatchable, SerializesModels;

    public EmployeeTraining $training;
    public ?float $score;
    public ?string $grade;

    /**
     * Create a new event instance.
     */
    public function __construct(EmployeeTraining $training, ?float $score = null, ?string $grade = null)
    {
        $this->training = $training;
        $this->score = $score;
        $this->grade = $grade;
    }
}
