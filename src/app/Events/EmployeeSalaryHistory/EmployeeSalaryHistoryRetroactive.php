<?php

namespace App\Events\EmployeeSalaryHistory;

use App\Models\EmployeeSalaryHistory;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class EmployeeSalaryHistoryRetroactive
{
    use Dispatchable, SerializesModels;

    public $salaryHistory;

    public $retroactiveAmount;

    public $retroactiveDays;

    public $processedAt;

    public function __construct(EmployeeSalaryHistory $salaryHistory, float $retroactiveAmount, int $retroactiveDays, string $processedAt)
    {
        $this->salaryHistory = $salaryHistory;
        $this->retroactiveAmount = $retroactiveAmount;
        $this->retroactiveDays = $retroactiveDays;
        $this->processedAt = $processedAt;
    }

    public function getEmployeeId(): int
    {
        return $this->salaryHistory->employee_id;
    }

    public function getRetroactiveAmount(): float
    {
        return $this->retroactiveAmount;
    }

    public function getRetroactiveDays(): int
    {
        return $this->retroactiveDays;
    }

    public function getProcessedAt(): string
    {
        return $this->processedAt;
    }

    public function getRetroactivePeriod(): string
    {
        $start = $this->salaryHistory->retroactive_start_date?->format('Y-m-d');
        $end = $this->salaryHistory->retroactive_end_date?->format('Y-m-d');

        return "{$start} to {$end}";
    }

    public function getChangeType(): string
    {
        return $this->salaryHistory->change_type->value;
    }
}
