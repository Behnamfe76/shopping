<?php

namespace App\Listeners\EmployeeSalaryHistory;

use App\Events\EmployeeSalaryHistory\EmployeeSalaryHistoryApproved;
use App\Events\EmployeeSalaryHistory\EmployeeSalaryHistoryProcessed;
use App\Models\Employee;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class UpdateEmployeeSalaryRecord implements ShouldQueue
{
    use InteractsWithQueue;

    public function handle($event): void
    {
        if ($event instanceof EmployeeSalaryHistoryApproved || $event instanceof EmployeeSalaryHistoryProcessed) {
            $this->updateEmployeeSalary($event->salaryHistory);
        }
    }

    private function updateEmployeeSalary($salaryHistory): void
    {
        $employee = $salaryHistory->employee;

        if (! $employee) {
            return;
        }

        // Update the employee's current salary
        $employee->update([
            'current_salary' => $salaryHistory->new_salary,
            'salary_updated_at' => now(),
        ]);

        // Update salary history metadata
        $employee->update([
            'salary_history_count' => $employee->salaryHistories()->count(),
            'last_salary_change' => $salaryHistory->effective_date,
            'last_salary_change_type' => $salaryHistory->change_type->value,
        ]);
    }
}
