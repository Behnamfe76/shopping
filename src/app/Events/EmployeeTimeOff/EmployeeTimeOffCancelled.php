<?php

namespace Fereydooni\Shopping\app\Events\EmployeeTimeOff;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Fereydooni\Shopping\app\Models\EmployeeTimeOff;

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
