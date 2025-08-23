<?php

namespace App\Listeners\EmployeeBenefits;

use App\Events\EmployeeBenefits\EmployeeBenefitsCreated;
use App\Events\EmployeeBenefits\EmployeeBenefitsUpdated;
use App\Events\EmployeeBenefits\EmployeeBenefitsEnrolled;
use App\Events\EmployeeBenefits\EmployeeBenefitsTerminated;
use App\Events\EmployeeBenefits\EmployeeBenefitsCancelled;
use App\Events\EmployeeBenefits\EmployeeBenefitsExpiring;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class UpdatePayrollDeductions implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Handle the event.
     */
    public function handle(object $event): void
    {
        try {
            match (true) {
                $event instanceof EmployeeBenefitsCreated => $this->handleBenefitsCreated($event),
                $event instanceof EmployeeBenefitsUpdated => $this->handleBenefitsUpdated($event),
                $event instanceof EmployeeBenefitsEnrolled => $this->handleBenefitsEnrolled($event),
                $event instanceof EmployeeBenefitsTerminated => $this->handleBenefitsTerminated($event),
                $event instanceof EmployeeBenefitsCancelled => $this->handleBenefitsCancelled($event),
                $event instanceof EmployeeBenefitsExpiring => $this->handleBenefitsExpiring($event),
                default => Log::info('Unknown EmployeeBenefits event type', ['event' => get_class($event)])
            };
        } catch (\Exception $e) {
            Log::error('Error updating payroll deductions', [
                'event' => get_class($event),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }

    /**
     * Handle benefits created event.
     */
    protected function handleBenefitsCreated(EmployeeBenefitsCreated $event): void
    {
        $benefit = $event->employeeBenefits;

        // Create payroll deduction record
        $this->createPayrollDeduction($benefit);

        // Update employee total deductions
        $this->updateEmployeeTotalDeductions($benefit->employee_id);

        Log::info('Payroll deductions updated for benefits creation', [
            'benefit_id' => $benefit->id,
            'employee_id' => $benefit->employee_id
        ]);
    }

    /**
     * Handle benefits updated event.
     */
    protected function handleBenefitsUpdated(EmployeeBenefitsUpdated $event): void
    {
        $benefit = $event->employeeBenefits;
        $changes = $event->changes;

        // Update payroll deduction if costs changed
        if (isset($changes['employee_contribution']) || isset($changes['premium_amount'])) {
            $this->updatePayrollDeduction($benefit);
            $this->updateEmployeeTotalDeductions($benefit->employee_id);
        }

        // Update deduction if status changed
        if (isset($changes['status'])) {
            $this->updateDeductionStatus($benefit);
        }

        Log::info('Payroll deductions updated for benefits modification', [
            'benefit_id' => $benefit->id,
            'employee_id' => $benefit->employee_id,
            'changes' => $changes
        ]);
    }

    /**
     * Handle benefits enrolled event.
     */
    protected function handleBenefitsEnrolled(EmployeeBenefitsCreated $event): void
    {
        $benefit = $event->employeeBenefits;

        // Activate payroll deduction
        $this->activatePayrollDeduction($benefit);

        // Update employee total deductions
        $this->updateEmployeeTotalDeductions($benefit->employee_id);

        Log::info('Payroll deductions activated for enrollment', [
            'benefit_id' => $benefit->id,
            'employee_id' => $benefit->employee_id
        ]);
    }

    /**
     * Handle benefits terminated event.
     */
    protected function handleBenefitsTerminated(EmployeeBenefitsTerminated $event): void
    {
        $benefit = $event->employeeBenefits;

        // Deactivate payroll deduction
        $this->deactivatePayrollDeduction($benefit, 'terminated');

        // Update employee total deductions
        $this->updateEmployeeTotalDeductions($benefit->employee_id);

        Log::info('Payroll deductions deactivated for termination', [
            'benefit_id' => $benefit->id,
            'employee_id' => $benefit->employee_id
        ]);
    }

    /**
     * Handle benefits cancelled event.
     */
    protected function handleBenefitsCancelled(EmployeeBenefitsCancelled $event): void
    {
        $benefit = $event->employeeBenefits;

        // Deactivate payroll deduction
        $this->deactivatePayrollDeduction($benefit, 'cancelled');

        // Update employee total deductions
        $this->updateEmployeeTotalDeductions($benefit->employee_id);

        Log::info('Payroll deductions deactivated for cancellation', [
            'benefit_id' => $benefit->id,
            'employee_id' => $benefit->employee_id
        ]);
    }

    /**
     * Handle benefits expiring event.
     */
    protected function handleBenefitsExpiring(EmployeeBenefitsExpiring $event): void
    {
        $benefit = $event->employeeBenefits;

        // Flag deduction for renewal
        $this->flagDeductionForRenewal($benefit);

        Log::info('Payroll deduction flagged for renewal', [
            'benefit_id' => $benefit->id,
            'employee_id' => $benefit->employee_id
        ]);
    }

    /**
     * Create payroll deduction record.
     */
    protected function createPayrollDeduction($benefit): void
    {
        try {
            // Check if payroll_deductions table exists
            if (DB::getSchemaBuilder()->hasTable('payroll_deductions')) {
                DB::table('payroll_deductions')->insert([
                    'employee_id' => $benefit->employee_id,
                    'benefit_id' => $benefit->id,
                    'deduction_type' => 'benefits',
                    'deduction_name' => $benefit->benefit_name,
                    'amount' => $benefit->employee_contribution,
                    'frequency' => 'monthly',
                    'start_date' => $benefit->effective_date,
                    'end_date' => $benefit->end_date,
                    'status' => 'active',
                    'is_percentage' => false,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }

            // Also update employee table if it has deduction fields
            if (DB::getSchemaBuilder()->hasColumn('employees', 'total_benefit_deductions')) {
                DB::table('employees')
                    ->where('id', $benefit->employee_id)
                    ->increment('total_benefit_deductions', $benefit->employee_contribution);
            }

        } catch (\Exception $e) {
            Log::error('Error creating payroll deduction', [
                'benefit_id' => $benefit->id,
                'employee_id' => $benefit->employee_id,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Update payroll deduction.
     */
    protected function updatePayrollDeduction($benefit): void
    {
        try {
            if (DB::getSchemaBuilder()->hasTable('payroll_deductions')) {
                DB::table('payroll_deductions')
                    ->where('benefit_id', $benefit->id)
                    ->update([
                        'amount' => $benefit->employee_contribution,
                        'updated_at' => now()
                    ]);
            }

            // Update employee table if it has deduction fields
            if (DB::getSchemaBuilder()->hasColumn('employees', 'total_benefit_deductions')) {
                $this->recalculateEmployeeDeductions($benefit->employee_id);
            }

        } catch (\Exception $e) {
            Log::error('Error updating payroll deduction', [
                'benefit_id' => $benefit->id,
                'employee_id' => $benefit->employee_id,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Update deduction status.
     */
    protected function updateDeductionStatus($benefit): void
    {
        try {
            if (DB::getSchemaBuilder()->hasTable('payroll_deductions')) {
                $status = match($benefit->status) {
                    'enrolled' => 'active',
                    'pending' => 'pending',
                    'terminated', 'cancelled' => 'inactive',
                    default => 'inactive'
                };

                DB::table('payroll_deductions')
                    ->where('benefit_id', $benefit->id)
                    ->update([
                        'status' => $status,
                        'updated_at' => now()
                    ]);
            }

        } catch (\Exception $e) {
            Log::error('Error updating deduction status', [
                'benefit_id' => $benefit->id,
                'employee_id' => $benefit->employee_id,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Activate payroll deduction.
     */
    protected function activatePayrollDeduction($benefit): void
    {
        try {
            if (DB::getSchemaBuilder()->hasTable('payroll_deductions')) {
                DB::table('payroll_deductions')
                    ->where('benefit_id', $benefit->id)
                    ->update([
                        'status' => 'active',
                        'start_date' => $benefit->effective_date,
                        'updated_at' => now()
                    ]);
            }

        } catch (\Exception $e) {
            Log::error('Error activating payroll deduction', [
                'benefit_id' => $benefit->id,
                'employee_id' => $benefit->employee_id,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Deactivate payroll deduction.
     */
    protected function deactivatePayrollDeduction($benefit, string $reason): void
    {
        try {
            if (DB::getSchemaBuilder()->hasTable('payroll_deductions')) {
                DB::table('payroll_deductions')
                    ->where('benefit_id', $benefit->id)
                    ->update([
                        'status' => 'inactive',
                        'end_date' => now(),
                        'notes' => "Deactivated due to benefits {$reason}",
                        'updated_at' => now()
                    ]);
            }

        } catch (\Exception $e) {
            Log::error('Error deactivating payroll deduction', [
                'benefit_id' => $benefit->id,
                'employee_id' => $benefit->employee_id,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Flag deduction for renewal.
     */
    protected function flagDeductionForRenewal($benefit): void
    {
        try {
            if (DB::getSchemaBuilder()->hasTable('payroll_deductions')) {
                DB::table('payroll_deductions')
                    ->where('benefit_id', $benefit->id)
                    ->update([
                        'status' => 'renewal_required',
                        'notes' => 'Benefits expiring - renewal required',
                        'updated_at' => now()
                    ]);
            }

        } catch (\Exception $e) {
            Log::error('Error flagging deduction for renewal', [
                'benefit_id' => $benefit->id,
                'employee_id' => $benefit->employee_id,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Update employee total deductions.
     */
    protected function updateEmployeeTotalDeductions(int $employeeId): void
    {
        try {
            if (DB::getSchemaBuilder()->hasColumn('employees', 'total_benefit_deductions')) {
                $this->recalculateEmployeeDeductions($employeeId);
            }

        } catch (\Exception $e) {
            Log::error('Error updating employee total deductions', [
                'employee_id' => $employeeId,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Recalculate employee deductions.
     */
    protected function recalculateEmployeeDeductions(int $employeeId): void
    {
        try {
            $totalDeductions = DB::table('employee_benefits')
                ->where('employee_id', $employeeId)
                ->where('status', 'enrolled')
                ->where('deleted_at', null)
                ->sum('employee_contribution');

            DB::table('employees')
                ->where('id', $employeeId)
                ->update([
                    'total_benefit_deductions' => $totalDeductions,
                    'updated_at' => now()
                ]);

        } catch (\Exception $e) {
            Log::error('Error recalculating employee deductions', [
                'employee_id' => $employeeId,
                'error' => $e->getMessage()
            ]);
        }
    }
}
