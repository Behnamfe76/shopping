<?php

namespace App\Events\EmployeeSalaryHistory;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\Models\EmployeeSalaryHistory;

class EmployeeSalaryHistoryApproved
{
    use Dispatchable, SerializesModels;

    public $salaryHistory;
    public $approvedBy;
    public $approvedAt;

    public function __construct(EmployeeSalaryHistory $salaryHistory, int $approvedBy, string $approvedAt)
    {
        $this->salaryHistory = $salaryHistory;
        $this->approvedBy = $approvedBy;
        $this->approvedAt = $approvedAt;
    }

    public function getEmployeeId(): int
    {
        return $this->salaryHistory->employee_id;
    }

    public function getApprovedBy(): int
    {
        return $this->approvedBy;
    }

    public function getApprovedAt(): string
    {
        return $this->approvedAt;
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
}
