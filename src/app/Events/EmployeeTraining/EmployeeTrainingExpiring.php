<?php

namespace Fereydooni\Shopping\Events\EmployeeTraining;

use Fereydooni\Shopping\Models\EmployeeTraining;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class EmployeeTrainingExpiring
{
    use Dispatchable, SerializesModels;

    public EmployeeTraining $training;

    public int $daysUntilExpiry;

    /**
     * Create a new event instance.
     */
    public function __construct(EmployeeTraining $training, int $daysUntilExpiry)
    {
        $this->training = $training;
        $this->daysUntilExpiry = $daysUntilExpiry;
    }
}
