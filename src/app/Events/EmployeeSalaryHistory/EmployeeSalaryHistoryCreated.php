<?php

namespace App\Events\EmployeeSalaryHistory;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\Models\EmployeeSalaryHistory;

class EmployeeSalaryHistoryCreated
{
    use Dispatchable, SerializesModels;

    public $salaryHistory;

    public function __construct(EmployeeSalaryHistory $salaryHistory)
    {
        $this->salaryHistory = $salaryHistory;
    }

    public function getEmployeeId(): int
    {
        return $this->salaryHistory->employee_id;
    }

    public function getChangeType(): string
    {
        return $this->salaryHistory->change_type->value;
    }

    public function getChangeAmount(): float
    {
        return $this->salaryHistory->change_amount;
    }

    public function getEffectiveDate(): string
    {
        return $this->salaryHistory->effective_date->format('Y-m-d');
    }

    public function isRetroactive(): bool
    {
        return $this->salaryHistory->is_retroactive;
    }

    public function getRetroactivePeriod(): ?string
    {
        if (!$this->isRetroactive()) {
            return null;
        }

        $start = $this->salaryHistory->retroactive_start_date?->format('Y-m-d');
        $end = $this->salaryHistory->retroactive_end_date?->format('Y-m-d');

        return "{$start} to {$end}";
    }
}
