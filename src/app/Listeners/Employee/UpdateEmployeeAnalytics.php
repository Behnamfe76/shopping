<?php

namespace Fereydooni\Shopping\app\Listeners\Employee;

use Fereydooni\Shopping\app\Events\Employee\EmployeeActivated;
use Fereydooni\Shopping\app\Events\Employee\EmployeeCreated;
use Fereydooni\Shopping\app\Events\Employee\EmployeeDeactivated;
use Fereydooni\Shopping\app\Events\Employee\EmployeeDeleted;
use Fereydooni\Shopping\app\Events\Employee\EmployeeTerminated;
use Fereydooni\Shopping\app\Events\Employee\EmployeeUpdated;
use Fereydooni\Shopping\app\Services\EmployeeService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class UpdateEmployeeAnalytics implements ShouldQueue
{
    use InteractsWithQueue;

    protected EmployeeService $employeeService;

    /**
     * Create the event listener.
     */
    public function __construct(EmployeeService $employeeService)
    {
        $this->employeeService = $employeeService;
    }

    /**
     * Handle employee created event.
     */
    public function handleEmployeeCreated(EmployeeCreated $event): void
    {
        $this->clearAnalyticsCache();
        Log::info('Employee analytics cache cleared after employee creation', [
            'employee_id' => $event->employee->id,
        ]);
    }

    /**
     * Handle employee updated event.
     */
    public function handleEmployeeUpdated(EmployeeUpdated $event): void
    {
        $this->clearAnalyticsCache();
        Log::info('Employee analytics cache cleared after employee update', [
            'employee_id' => $event->employee->id,
            'changes' => $event->changes,
        ]);
    }

    /**
     * Handle employee deleted event.
     */
    public function handleEmployeeDeleted(EmployeeDeleted $event): void
    {
        $this->clearAnalyticsCache();
        Log::info('Employee analytics cache cleared after employee deletion', [
            'employee_id' => $event->employee->id,
        ]);
    }

    /**
     * Handle employee activated event.
     */
    public function handleEmployeeActivated(EmployeeActivated $event): void
    {
        $this->clearAnalyticsCache();
        Log::info('Employee analytics cache cleared after employee activation', [
            'employee_id' => $event->employee->id,
        ]);
    }

    /**
     * Handle employee deactivated event.
     */
    public function handleEmployeeDeactivated(EmployeeDeactivated $event): void
    {
        $this->clearAnalyticsCache();
        Log::info('Employee analytics cache cleared after employee deactivation', [
            'employee_id' => $event->employee->id,
        ]);
    }

    /**
     * Handle employee terminated event.
     */
    public function handleEmployeeTerminated(EmployeeTerminated $event): void
    {
        $this->clearAnalyticsCache();
        Log::info('Employee analytics cache cleared after employee termination', [
            'employee_id' => $event->employee->id,
            'reason' => $event->reason,
        ]);
    }

    /**
     * Clear employee analytics cache.
     */
    protected function clearAnalyticsCache(): void
    {
        try {
            Cache::forget('employee_stats');
            Cache::forget('employee_stats_by_status');
            Cache::forget('employee_stats_by_department');
            Cache::forget('employee_stats_by_employment_type');
            Cache::forget('employee_growth_stats');
            Cache::forget('employee_turnover_stats');
            Cache::forget('employee_performance_stats');
            Cache::forget('employee_salary_stats');
        } catch (\Exception $e) {
            Log::error('Failed to clear employee analytics cache', [
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed($event, \Throwable $exception): void
    {
        Log::error('Failed to update employee analytics', [
            'event' => get_class($event),
            'error' => $exception->getMessage(),
        ]);
    }
}
