<?php

namespace Fereydooni\Shopping\app\Events\EmployeeTimeOff;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Fereydooni\Shopping\app\Models\EmployeeTimeOff;

class EmployeeTimeOffRejected
{
    use Dispatchable, SerializesModels;

    public $timeOff;
    public $rejectedBy;
    public $rejectionReason;

    /**
     * Create a new event instance.
     */
    public function __construct(EmployeeTimeOff $timeOff, int $rejectedBy, ?string $rejectionReason = null)
    {
        $this->timeOff = $timeOff;
        $this->rejectedBy = $rejectedBy;
        $this->rejectionReason = $rejectionReason;
    }
}
