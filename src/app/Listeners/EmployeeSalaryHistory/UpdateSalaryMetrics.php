<?php

namespace App\Listeners\EmployeeSalaryHistory;

use App\Events\EmployeeSalaryHistory\EmployeeSalaryHistoryApproved;
use App\Events\EmployeeSalaryHistory\EmployeeSalaryHistoryProcessed;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class UpdateSalaryMetrics implements ShouldQueue
{
    use InteractsWithQueue;

    public function handle($event): void
    {
        if ($event instanceof EmployeeSalaryHistoryApproved || $event instanceof EmployeeSalaryHistoryProcessed) {
            $this->updateMetrics($event->salaryHistory);
        }
    }

    private function updateMetrics($salaryHistory): void
    {
        // Update department salary metrics
        $this->updateDepartmentMetrics($salaryHistory);

        // Update company-wide salary metrics
        $this->updateCompanyMetrics($salaryHistory);

        // Update salary band metrics
        $this->updateSalaryBandMetrics($salaryHistory);

        // Update equity metrics
        $this->updateEquityMetrics($salaryHistory);
    }

    private function updateDepartmentMetrics($salaryHistory): void
    {
        $employee = $salaryHistory->employee;
        if (!$employee || !$employee->department) {
            return;
        }

        $department = $employee->department;

        // Update department average salary
        $avgSalary = $department->employees()->avg('current_salary');
        $department->update(['average_salary' => $avgSalary]);

        // Update department salary range
        $minSalary = $department->employees()->min('current_salary');
        $maxSalary = $department->employees()->max('current_salary');
        $department->update([
            'min_salary' => $minSalary,
            'max_salary' => $maxSalary,
        ]);
    }

    private function updateCompanyMetrics($salaryHistory): void
    {
        // Update company-wide salary statistics
        // This would update cached metrics for reporting
    }

    private function updateSalaryBandMetrics($salaryHistory): void
    {
        // Update salary band distributions
        // This would recalculate salary band statistics
    }

    private function updateEquityMetrics($salaryHistory): void
    {
        // Update pay equity metrics
        // This would recalculate gender pay gap, experience-based equity, etc.
    }
}
