<?php

namespace App\Listeners\EmployeeTimeOff;

use App\Events\EmployeeTimeOff\EmployeeTimeOffCreated;
use App\Events\EmployeeTimeOff\EmployeeTimeOffUpdated;
use App\Events\EmployeeTimeOff\EmployeeTimeOffApproved;
use App\Events\EmployeeTimeOff\EmployeeTimeOffCancelled;
use App\Events\EmployeeTimeOff\EmployeeTimeOffRejected;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class UpdateCalendar implements ShouldQueue
{
    use InteractsWithQueue;

    public $queue = 'calendar-updates';

    /**
     * Handle the event.
     */
    public function handle($event): void
    {
        try {
            $timeOff = $event->timeOff;

            switch (get_class($event)) {
                case EmployeeTimeOffCreated::class:
                    $this->addToCalendar($timeOff);
                    break;

                case EmployeeTimeOffUpdated::class:
                    $this->updateCalendar($timeOff);
                    break;

                case EmployeeTimeOffApproved::class:
                    $this->confirmCalendarEntry($timeOff);
                    break;

                case EmployeeTimeOffCancelled::class:
                case EmployeeTimeOffRejected::class:
                    $this->removeFromCalendar($timeOff);
                    break;
            }

            // Clear calendar cache
            $this->clearCalendarCache($timeOff);

        } catch (\Exception $e) {
            Log::error('Failed to update calendar for time-off: ' . $e->getMessage(), [
                'time_off_id' => $event->timeOff->id ?? 'unknown',
                'event' => get_class($event),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }

    /**
     * Add time-off to calendar
     */
    protected function addToCalendar($timeOff): void
    {
        // Add to employee's personal calendar
        $this->addToEmployeeCalendar($timeOff);

        // Add to team calendar if approved
        if ($timeOff->status === 'approved') {
            $this->addToTeamCalendar($timeOff);
        }

        // Add to company-wide calendar if needed
        $this->addToCompanyCalendar($timeOff);

        Log::info('Time-off added to calendar', [
            'time_off_id' => $timeOff->id,
            'employee_id' => $timeOff->employee_id,
            'dates' => $timeOff->start_date->toDateString() . ' to ' . $timeOff->end_date->toDateString()
        ]);
    }

    /**
     * Update calendar entry
     */
    protected function updateCalendar($timeOff): void
    {
        // Update employee calendar
        $this->updateEmployeeCalendar($timeOff);

        // Update team calendar if status changed
        $this->updateTeamCalendar($timeOff);

        // Update company calendar
        $this->updateCompanyCalendar($timeOff);

        Log::info('Time-off calendar updated', [
            'time_off_id' => $timeOff->id,
            'employee_id' => $timeOff->employee_id
        ]);
    }

    /**
     * Confirm calendar entry after approval
     */
    protected function confirmCalendarEntry($timeOff): void
    {
        // Ensure entry exists in team calendar
        $this->addToTeamCalendar($timeOff);

        // Update company calendar
        $this->updateCompanyCalendar($timeOff);

        Log::info('Time-off calendar entry confirmed', [
            'time_off_id' => $timeOff->id,
            'employee_id' => $timeOff->employee_id
        ]);
    }

    /**
     * Remove from calendar
     */
    protected function removeFromCalendar($timeOff): void
    {
        // Remove from employee calendar
        $this->removeFromEmployeeCalendar($timeOff);

        // Remove from team calendar
        $this->removeFromTeamCalendar($timeOff);

        // Remove from company calendar
        $this->removeFromCompanyCalendar($timeOff);

        Log::info('Time-off removed from calendar', [
            'time_off_id' => $timeOff->id,
            'employee_id' => $timeOff->employee_id
        ]);
    }

    /**
     * Add to employee's personal calendar
     */
    protected function addToEmployeeCalendar($timeOff): void
    {
        // Implementation for adding to employee's personal calendar
        // This could integrate with Google Calendar, Outlook, or internal calendar system
    }

    /**
     * Add to team calendar
     */
    protected function addToTeamCalendar($timeOff): void
    {
        // Implementation for adding to team calendar
        // This would show the time-off to team members
    }

    /**
     * Add to company calendar
     */
    protected function addToCompanyCalendar($timeOff): void
    {
        // Implementation for adding to company-wide calendar
        // This could be for HR or management visibility
    }

    /**
     * Update employee calendar
     */
    protected function updateEmployeeCalendar($timeOff): void
    {
        // Implementation for updating employee calendar
    }

    /**
     * Update team calendar
     */
    protected function updateTeamCalendar($timeOff): void
    {
        // Implementation for updating team calendar
    }

    /**
     * Update company calendar
     */
    protected function updateCompanyCalendar($timeOff): void
    {
        // Implementation for updating company calendar
    }

    /**
     * Remove from employee calendar
     */
    protected function removeFromEmployeeCalendar($timeOff): void
    {
        // Implementation for removing from employee calendar
    }

    /**
     * Remove from team calendar
     */
    protected function removeFromTeamCalendar($timeOff): void
    {
        // Implementation for removing from team calendar
    }

    /**
     * Remove from company calendar
     */
    protected function removeFromCompanyCalendar($timeOff): void
    {
        // Implementation for removing from company calendar
    }

    /**
     * Clear calendar cache
     */
    protected function clearCalendarCache($timeOff): void
    {
        $employeeId = $timeOff->employee_id;
        $departmentId = $timeOff->employee->department_id ?? null;

        // Clear employee calendar cache
        Cache::forget("employee_calendar_{$employeeId}");

        // Clear team calendar cache
        if ($departmentId) {
            Cache::forget("team_calendar_{$departmentId}");
        }

        // Clear company calendar cache
        Cache::forget('company_calendar');

        // Clear date-specific caches
        $startDate = $timeOff->start_date->format('Y-m-d');
        $endDate = $timeOff->end_date->format('Y-m-d');

        for ($date = $startDate; $date <= $endDate; $date = date('Y-m-d', strtotime($date . ' +1 day'))) {
            Cache::forget("calendar_date_{$date}");
        }
    }
}
