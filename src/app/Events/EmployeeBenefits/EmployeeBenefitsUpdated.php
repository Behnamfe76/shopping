<?php

namespace Fereydooni\Shopping\app\Events\EmployeeBenefits;

use Fereydooni\Shopping\app\Models\EmployeeBenefits;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class EmployeeBenefitsUpdated
{
    use Dispatchable, SerializesModels;

    public $benefit;

    public function __construct(EmployeeBenefits $benefit)
    {
        $this->benefit = $benefit;
    }
}
