<?php

namespace App\Listeners\EmployeeSalaryHistory;

use App\Events\EmployeeSalaryHistory\EmployeeSalaryHistoryProcessed;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class UpdatePayrollRecords implements ShouldQueue
{
    use InteractsWithQueue;

    public function handle($event): void
    {
        if ($event instanceof EmployeeSalaryHistoryProcessed) {
            $this->updatePayrollRecords($event->salaryHistory);
        }
    }

    private function updatePayrollRecords($salaryHistory): void
    {
        // Update payroll system with new salary information
        $this->updatePayrollSystem($salaryHistory);

        // Update tax calculations if needed
        $this->updateTaxCalculations($salaryHistory);

        // Update benefit calculations
        $this->updateBenefitCalculations($salaryHistory);
    }

    private function updatePayrollSystem($salaryHistory): void
    {
        // Implementation for updating payroll system
        // This would integrate with external payroll providers
    }

    private function updateTaxCalculations($salaryHistory): void
    {
        // Update tax withholding calculations based on new salary
        // This would recalculate federal, state, and local taxes
    }

    private function updateBenefitCalculations($salaryHistory): void
    {
        // Update benefit calculations (401k, health insurance, etc.)
        // based on new salary
    }
}
