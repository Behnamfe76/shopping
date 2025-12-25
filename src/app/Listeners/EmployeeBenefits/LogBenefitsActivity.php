<?php

namespace App\Listeners\EmployeeBenefits;

use App\Events\EmployeeBenefits\EmployeeBenefitsCancelled;
use App\Events\EmployeeBenefits\EmployeeBenefitsCreated;
use App\Events\EmployeeBenefits\EmployeeBenefitsEnrolled;
use App\Events\EmployeeBenefits\EmployeeBenefitsExpiring;
use App\Events\EmployeeBenefits\EmployeeBenefitsTerminated;
use App\Events\EmployeeBenefits\EmployeeBenefitsUpdated;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class LogBenefitsActivity implements ShouldQueue
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
            Log::error('Error logging benefits activity', [
                'event' => get_class($event),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
        }
    }

    /**
     * Handle benefits created event.
     */
    protected function handleBenefitsCreated(EmployeeBenefitsCreated $event): void
    {
        $benefit = $event->employeeBenefits;

        $this->logActivity(
            'benefits_created',
            $benefit->employee_id,
            $benefit->id,
            'Employee benefit enrollment created',
            [
                'benefit_type' => $benefit->benefit_type,
                'benefit_name' => $benefit->benefit_name,
                'provider' => $benefit->provider,
                'status' => $benefit->status,
            ]
        );
    }

    /**
     * Handle benefits updated event.
     */
    protected function handleBenefitsUpdated(EmployeeBenefitsUpdated $event): void
    {
        $benefit = $event->employeeBenefits;
        $changes = $event->changes;

        $this->logActivity(
            'benefits_updated',
            $benefit->employee_id,
            $benefit->id,
            'Employee benefit enrollment updated',
            [
                'changes' => $changes,
                'benefit_type' => $benefit->benefit_type,
                'benefit_name' => $benefit->benefit_name,
            ]
        );
    }

    /**
     * Handle benefits enrolled event.
     */
    protected function handleBenefitsEnrolled(EmployeeBenefitsEnrolled $event): void
    {
        $benefit = $event->employeeBenefits;

        $this->logActivity(
            'benefits_enrolled',
            $benefit->employee_id,
            $benefit->id,
            'Employee enrolled in benefits',
            [
                'benefit_type' => $benefit->benefit_type,
                'benefit_name' => $benefit->benefit_name,
                'effective_date' => $benefit->effective_date,
                'provider' => $benefit->provider,
            ]
        );
    }

    /**
     * Handle benefits terminated event.
     */
    protected function handleBenefitsTerminated(EmployeeBenefitsTerminated $event): void
    {
        $benefit = $event->employeeBenefits;

        $this->logActivity(
            'benefits_terminated',
            $benefit->employee_id,
            $benefit->id,
            'Employee benefits terminated',
            [
                'benefit_type' => $benefit->benefit_type,
                'benefit_name' => $benefit->benefit_name,
                'end_date' => $benefit->end_date,
                'reason' => $event->reason,
            ]
        );
    }

    /**
     * Handle benefits cancelled event.
     */
    protected function handleBenefitsCancelled(EmployeeBenefitsCancelled $event): void
    {
        $benefit = $event->employeeBenefits;

        $this->logActivity(
            'benefits_cancelled',
            $benefit->employee_id,
            $benefit->id,
            'Employee benefits cancelled',
            [
                'benefit_type' => $benefit->benefit_type,
                'benefit_name' => $benefit->benefit_name,
                'reason' => $event->reason,
            ]
        );
    }

    /**
     * Handle benefits expiring event.
     */
    protected function handleBenefitsExpiring(EmployeeBenefitsExpiring $event): void
    {
        $benefit = $event->employeeBenefits;

        $this->logActivity(
            'benefits_expiring',
            $benefit->employee_id,
            $benefit->id,
            'Employee benefits expiring soon',
            [
                'benefit_type' => $benefit->benefit_type,
                'benefit_name' => $benefit->benefit_name,
                'end_date' => $benefit->end_date,
                'days_until_expiry' => now()->diffInDays($benefit->end_date),
            ]
        );
    }

    /**
     * Log activity to the database.
     */
    protected function logActivity(string $action, int $employeeId, int $benefitId, string $description, array $metadata = []): void
    {
        try {
            // Check if activity_logs table exists
            if (DB::getSchemaBuilder()->hasTable('activity_logs')) {
                DB::table('activity_logs')->insert([
                    'log_name' => 'employee_benefits',
                    'description' => $description,
                    'subject_type' => 'App\Models\EmployeeBenefits',
                    'subject_id' => $benefitId,
                    'causer_type' => 'App\Models\Employee',
                    'causer_id' => $employeeId,
                    'properties' => json_encode($metadata),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            // Also log to Laravel's built-in log
            Log::info('Employee Benefits Activity', [
                'action' => $action,
                'employee_id' => $employeeId,
                'benefit_id' => $benefitId,
                'description' => $description,
                'metadata' => $metadata,
                'timestamp' => now()->toISOString(),
            ]);

        } catch (\Exception $e) {
            Log::error('Error logging activity to database', [
                'action' => $action,
                'employee_id' => $employeeId,
                'benefit_id' => $benefitId,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
