<?php

namespace Fereydooni\Shopping\app\Events\EmployeeBenefits;

use Fereydooni\Shopping\app\Models\EmployeeBenefits;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class EmployeeBenefitsExpiring
{
    use Dispatchable, SerializesModels;

    public $benefit;
    public $renewalData;

    public function __construct(EmployeeBenefits $benefit, array $renewalData)
    {
        $this->benefit = $benefit;
        $this->renewalData = $renewalData;
    }
}
