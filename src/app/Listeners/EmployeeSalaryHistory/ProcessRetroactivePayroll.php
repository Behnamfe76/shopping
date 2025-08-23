<?php

namespace App\Listeners\EmployeeSalaryHistory;

use App\Events\EmployeeSalaryHistory\EmployeeSalaryHistoryProcessed;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class ProcessRetroactivePayroll implements ShouldQueue
{
    use InteractsWithQueue;

    public function handle($event): void
    {
        if ($event instanceof EmployeeSalaryHistoryProcessed) {
            $this->processRetroactiveAdjustment($event->salaryHistory);
        }
    }

    private function processRetroactiveAdjustment($salaryHistory): void
    {
        if (!$salaryHistory->is_retroactive) {
            return;
        }

        // Calculate retroactive amount
        $retroactiveAmount = $this->calculateRetroactiveAmount($salaryHistory);

        // Create retroactive payroll entry
        $this->createRetroactivePayrollEntry($salaryHistory, $retroactiveAmount);

        // Update employee records
        $this->updateEmployeeRetroactiveRecords($salaryHistory, $retroactiveAmount);

        // Send retroactive notification
        $this->sendRetroactiveNotification($salaryHistory, $retroactiveAmount);
    }

    private function calculateRetroactiveAmount($salaryHistory): float
    {
        if (!$salaryHistory->retroactive_start_date || !$salaryHistory->retroactive_end_date) {
            return 0;
        }

        $days = $salaryHistory->retroactive_start_date->diffInDays($salaryHistory->retroactive_end_date) + 1;
        $dailyRate = $salaryHistory->change_amount / 365; // Assuming 365 days per year

        return round($dailyRate * $days, 2);
    }

    private function createRetroactivePayrollEntry($salaryHistory, $retroactiveAmount): void
    {
        // Create payroll entry for retroactive payment
        // This would integrate with the payroll system
    }

    private function updateEmployeeRetroactiveRecords($salaryHistory, $retroactiveAmount): void
    {
        // Update employee records to reflect retroactive adjustment
        $employee = $salaryHistory->employee;

        if ($employee) {
            $employee->update([
                'retroactive_adjustments_total' => ($employee->retroactive_adjustments_total ?? 0) + $retroactiveAmount,
                'last_retroactive_adjustment' => now(),
            ]);
        }
    }

    private function sendRetroactiveNotification($salaryHistory, $retroactiveAmount): void
    {
        // Send notification to employee about retroactive adjustment
        // This would trigger the retroactive notification
    }
}
