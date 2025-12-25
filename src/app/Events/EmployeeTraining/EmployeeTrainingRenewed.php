<?php

namespace Fereydooni\Shopping\Events\EmployeeTraining;

use Fereydooni\Shopping\Models\EmployeeTraining;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class EmployeeTrainingRenewed
{
    use Dispatchable, SerializesModels;

    public EmployeeTraining $training;

    public ?string $renewalDate;

    /**
     * Create a new event instance.
     */
    public function __construct(EmployeeTraining $training, ?string $renewalDate = null)
    {
        $this->training = $training;
        $this->renewalDate = $renewalDate;
    }
}
