<?php

namespace App\Events\EmployeeSalaryHistory;

use App\Models\EmployeeSalaryHistory;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class EmployeeSalaryHistoryProcessed
{
    use Dispatchable, SerializesModels;

    public $salaryHistory;

    public $processedAt;

    public $processedBy;

    public function __construct(EmployeeSalaryHistory $salaryHistory, string $processedAt, int $processedBy)
    {
        $this->salaryHistory = $salaryHistory;
        $this->processedAt = $processedAt;
        $this->processedBy = $processedBy;
    }

    public function getEmployeeId(): int
    {
        return $this->salaryHistory->employee_id;
    }

    public function getProcessedAt(): string
    {
        return $this->processedAt;
    }

    public function getProcessedBy(): int
    {
        return $this->processedBy;
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
}
