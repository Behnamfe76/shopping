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

class UpdateEmployeeBenefitsRecord implements ShouldQueue
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
            Log::error('Error updating employee benefits record', [
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

        // Update employee benefits count
        $this->updateEmployeeBenefitsCount($benefit->employee_id);

        // Update company benefits statistics
        $this->updateCompanyBenefitsStatistics();

        Log::info('Employee benefits record updated for creation', [
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

        // Update employee benefits record if status changed
        if (isset($changes['status'])) {
            $this->updateEmployeeBenefitsStatus($benefit->employee_id, $changes['status']);
        }

        // Update cost records if costs changed
        if (isset($changes['premium_amount']) || isset($changes['employee_contribution']) || isset($changes['employer_contribution'])) {
            $this->updateEmployeeBenefitsCosts($benefit->employee_id);
        }

        Log::info('Employee benefits record updated for modification', [
            'benefit_id' => $benefit->id,
            'employee_id' => $benefit->employee_id,
            'changes' => $changes
        ]);
    }

    /**
     * Handle benefits enrolled event.
     */
    protected function handleBenefitsEnrolled(EmployeeBenefitsEnrolled $event): void
    {
        $benefit = $event->employeeBenefits;

        // Update employee enrollment status
        $this->updateEmployeeEnrollmentStatus($benefit->employee_id, 'enrolled');

        // Update active benefits count
        $this->updateActiveBenefitsCount();

        Log::info('Employee benefits record updated for enrollment', [
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

        // Update employee enrollment status
        $this->updateEmployeeEnrollmentStatus($benefit->employee_id, 'terminated');

        // Update active benefits count
        $this->updateActiveBenefitsCount();

        Log::info('Employee benefits record updated for termination', [
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

        // Update employee enrollment status
        $this->updateEmployeeEnrollmentStatus($benefit->employee_id, 'cancelled');

        // Update active benefits count
        $this->updateActiveBenefitsCount();

        Log::info('Employee benefits record updated for cancellation', [
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

        // Update expiring benefits count
        $this->updateExpiringBenefitsCount();

        Log::info('Employee benefits record updated for expiring', [
            'benefit_id' => $benefit->id,
            'employee_id' => $benefit->employee_id
        ]);
    }

    /**
     * Update employee benefits count.
     */
    protected function updateEmployeeBenefitsCount(int $employeeId): void
    {
        try {
            DB::table('employees')
                ->where('id', $employeeId)
                ->update([
                    'benefits_count' => DB::raw('(SELECT COUNT(*) FROM employee_benefits WHERE employee_id = ? AND deleted_at IS NULL)'),
                    'updated_at' => now()
                ]);
        } catch (\Exception $e) {
            Log::error('Error updating employee benefits count', [
                'employee_id' => $employeeId,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Update employee benefits status.
     */
    protected function updateEmployeeEnrollmentStatus(int $employeeId, string $status): void
    {
        try {
            DB::table('employees')
                ->where('id', $employeeId)
                ->update([
                    'benefits_status' => $status,
                    'updated_at' => now()
                ]);
        } catch (\Exception $e) {
            Log::error('Error updating employee benefits status', [
                'employee_id' => $employeeId,
                'status' => $status,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Update employee benefits costs.
     */
    protected function updateEmployeeBenefitsCosts(int $employeeId): void
    {
        try {
            $costs = DB::table('employee_benefits')
                ->where('employee_id', $employeeId)
                ->where('deleted_at', null)
                ->selectRaw('
                    SUM(premium_amount) as total_premium,
                    SUM(employee_contribution) as total_employee_contribution,
                    SUM(employer_contribution) as total_employer_contribution
                ')
                ->first();

            if ($costs) {
                DB::table('employees')
                    ->where('id', $employeeId)
                    ->update([
                        'total_benefits_cost' => $costs->total_premium ?? 0,
                        'total_employee_contribution' => $costs->total_employee_contribution ?? 0,
                        'total_employer_contribution' => $costs->total_employer_contribution ?? 0,
                        'updated_at' => now()
                    ]);
            }
        } catch (\Exception $e) {
            Log::error('Error updating employee benefits costs', [
                'employee_id' => $employeeId,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Update active benefits count.
     */
    protected function updateActiveBenefitsCount(): void
    {
        try {
            $activeCount = DB::table('employee_benefits')
                ->where('status', 'enrolled')
                ->where('deleted_at', null)
                ->count();

            // Update company statistics table or cache
            cache()->put('company_active_benefits_count', $activeCount, now()->addHours(24));
        } catch (\Exception $e) {
            Log::error('Error updating active benefits count', [
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Update expiring benefits count.
     */
    protected function updateExpiringBenefitsCount(): void
    {
        try {
            $expiringCount = DB::table('employee_benefits')
                ->where('end_date', '<=', now()->addDays(30))
                ->where('status', 'enrolled')
                ->where('deleted_at', null)
                ->count();

            // Update company statistics table or cache
            cache()->put('company_expiring_benefits_count', $expiringCount, now()->addHours(24));
        } catch (\Exception $e) {
            Log::error('Error updating expiring benefits count', [
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Update company benefits statistics.
     */
    protected function updateCompanyBenefitsStatistics(): void
    {
        try {
            $stats = DB::table('employee_benefits')
                ->where('deleted_at', null)
                ->selectRaw('
                    COUNT(*) as total_benefits,
                    COUNT(CASE WHEN status = "enrolled" THEN 1 END) as active_benefits,
                    COUNT(CASE WHEN status = "pending" THEN 1 END) as pending_benefits,
                    SUM(premium_amount) as total_premium,
                    SUM(employee_contribution) as total_employee_contribution,
                    SUM(employer_contribution) as total_employer_contribution
                ')
                ->first();

            if ($stats) {
                cache()->put('company_benefits_statistics', $stats, now()->addHours(24));
            }
        } catch (\Exception $e) {
            Log::error('Error updating company benefits statistics', [
                'error' => $e->getMessage()
            ]);
        }
    }
}
