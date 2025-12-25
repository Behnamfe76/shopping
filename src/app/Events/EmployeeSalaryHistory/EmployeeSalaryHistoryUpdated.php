<?php

namespace App\Events\EmployeeSalaryHistory;

use App\Models\EmployeeSalaryHistory;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class EmployeeSalaryHistoryUpdated
{
    use Dispatchable, SerializesModels;

    public $salaryHistory;

    public $changes;

    public function __construct(EmployeeSalaryHistory $salaryHistory, array $changes = [])
    {
        $this->salaryHistory = $salaryHistory;
        $this->changes = $changes;
    }

    public function getEmployeeId(): int
    {
        return $this->salaryHistory->employee_id;
    }

    public function getChanges(): array
    {
        return $this->changes;
    }

    public function hasChanged(string $field): bool
    {
        return array_key_exists($field, $this->changes);
    }

    public function getOldValue(string $field): mixed
    {
        return $this->changes[$field]['old'] ?? null;
    }

    public function getNewValue(string $field): mixed
    {
        return $this->changes[$field]['new'] ?? null;
    }
}
