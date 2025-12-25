<?php

namespace Fereydooni\Shopping\app\Events\EmployeeTimeOff;

use Fereydooni\Shopping\app\Models\EmployeeTimeOff;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class EmployeeTimeOffCancelled
{
    use Dispatchable, SerializesModels;

    public $timeOff;

    public $cancellationReason;

    /**
     * Create a new event instance.
     */
    public function __construct(EmployeeTimeOff $timeOff, ?string $cancellationReason = null)
    {
        $this->timeOff = $timeOff;
        $this->cancellationReason = $cancellationReason;
    }
}
