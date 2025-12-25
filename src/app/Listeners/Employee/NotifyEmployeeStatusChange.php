<?php

namespace Fereydooni\Shopping\app\Listeners\Employee;

use Fereydooni\Shopping\app\Events\Employee\EmployeeActivated;
use Fereydooni\Shopping\app\Events\Employee\EmployeeDeactivated;
use Fereydooni\Shopping\app\Events\Employee\EmployeeTerminated;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

class NotifyEmployeeStatusChange implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle employee activated event.
     */
    public function handleEmployeeActivated(EmployeeActivated $event): void
    {
        $this->sendStatusChangeNotification($event->employee, 'activated');
    }

    /**
     * Handle employee deactivated event.
     */
    public function handleEmployeeDeactivated(EmployeeDeactivated $event): void
    {
        $this->sendStatusChangeNotification($event->employee, 'deactivated');
    }

    /**
     * Handle employee terminated event.
     */
    public function handleEmployeeTerminated(EmployeeTerminated $event): void
    {
        $this->sendStatusChangeNotification($event->employee, 'terminated', $event->reason);
    }

    /**
     * Send status change notification.
     */
    protected function sendStatusChangeNotification($employee, string $status, string $reason = ''): void
    {
        try {
            // Notify HR department
            $this->notifyHR($employee, $status, $reason);

            // Notify employee's manager if exists
            if ($employee->manager_id) {
                $this->notifyManager($employee, $status, $reason);
            }

            // Notify employee
            $this->notifyEmployee($employee, $status, $reason);

            Log::info("Employee status change notification sent for {$status}", [
                'employee_id' => $employee->id,
                'status' => $status,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to send employee status change notification', [
                'employee_id' => $employee->id,
                'status' => $status,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Notify HR department.
     */
    protected function notifyHR($employee, string $status, string $reason = ''): void
    {
        // Implementation for HR notification
        // This would typically send an email to HR department
    }

    /**
     * Notify employee's manager.
     */
    protected function notifyManager($employee, string $status, string $reason = ''): void
    {
        // Implementation for manager notification
        // This would typically send an email to the employee's manager
    }

    /**
     * Notify employee.
     */
    protected function notifyEmployee($employee, string $status, string $reason = ''): void
    {
        // Implementation for employee notification
        // This would typically send an email to the employee
    }

    /**
     * Handle a job failure.
     */
    public function failed($event, \Throwable $exception): void
    {
        Log::error('Failed to notify employee status change', [
            'event' => get_class($event),
            'error' => $exception->getMessage(),
        ]);
    }
}
