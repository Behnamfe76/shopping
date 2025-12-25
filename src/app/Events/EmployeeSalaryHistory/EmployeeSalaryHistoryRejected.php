<?php

namespace App\Events\EmployeeSalaryHistory;

use App\Models\EmployeeSalaryHistory;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class EmployeeSalaryHistoryRejected
{
    use Dispatchable, SerializesModels;

    public $salaryHistory;

    public $rejectedBy;

    public $rejectedAt;

    public $reason;

    public function __construct(EmployeeSalaryHistory $salaryHistory, int $rejectedBy, string $rejectedAt, ?string $reason = null)
    {
        $this->salaryHistory = $salaryHistory;
        $this->rejectedBy = $rejectedBy;
        $this->rejectedAt = $rejectedAt;
        $this->reason = $reason;
    }

    public function getEmployeeId(): int
    {
        return $this->salaryHistory->employee_id;
    }

    public function getRejectedBy(): int
    {
        return $this->rejectedBy;
    }

    public function getRejectedAt(): string
    {
        return $this->rejectedAt;
    }

    public function getReason(): ?string
    {
        return $this->reason;
    }

    public function getChangeType(): string
    {
        return $this->salaryHistory->change_type->value;
    }

    public function getChangeAmount(): float
    {
        return $this->salaryHistory->change_amount;
    }
}
