<?php

namespace Fereydooni\Shopping\app\Events\EmployeeBenefits;

use Fereydooni\Shopping\app\Models\EmployeeBenefits;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class EmployeeBenefitsTerminated
{
    use Dispatchable, SerializesModels;

    public $benefit;
    public $reason;

    public function __construct(EmployeeBenefits $benefit, string $reason = null)
    {
        $this->benefit = $benefit;
        $this->reason = $reason;
    }
}
