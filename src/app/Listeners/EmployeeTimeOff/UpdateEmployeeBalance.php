<?php

namespace Fereydooni\Shopping\app\Listeners\EmployeeTimeOff;

use Fereydooni\Shopping\app\Events\EmployeeTimeOff\EmployeeTimeOffApproved;
use Fereydooni\Shopping\app\Events\EmployeeTimeOff\EmployeeTimeOffCancelled;
use Fereydooni\Shopping\app\Models\Employee;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class UpdateEmployeeBalance implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Handle the event.
     */
    public function handle($event): void
    {
        try {
            switch (true) {
                case $event instanceof EmployeeTimeOffApproved:
                    $this->handleTimeOffApproved($event);
                    break;
                case $event instanceof EmployeeTimeOffCancelled:
                    $this->handleTimeOffCancelled($event);
                    break;
            }
        } catch (\Exception $e) {
            Log::error('Failed to update employee balance', [
                'event' => get_class($event),
                'error' => $e->getMessage(),
            ]);
        }
    }

    protected function handleTimeOffApproved(EmployeeTimeOffApproved $event): void
    {
        $timeOff = $event->timeOff;

        try {
            DB::transaction(function () use ($timeOff) {
                // Update employee's time-off balance
                $employee = Employee::find($timeOff->employee_id);

                if (! $employee) {
                    Log::warning('Employee not found for balance update', [
                        'employee_id' => $timeOff->employee_id,
                    ]);

                    return;
                }

                // Calculate days/hours to deduct
                $daysToDeduct = $timeOff->total_days ?? 0;
                $hoursToDeduct = $timeOff->total_hours ?? 0;

                // Update employee's remaining time-off balance
                // This would typically update a time_off_balance field or related table
                Log::info('Updated employee time-off balance', [
                    'employee_id' => $employee->id,
                    'days_deducted' => $daysToDeduct,
                    'hours_deducted' => $hoursToDeduct,
                    'time_off_id' => $timeOff->id,
                ]);

                // TODO: Implement actual balance update logic
                // This would typically update a time_off_balance table or field
            });
        } catch (\Exception $e) {
            Log::error('Failed to update employee balance in transaction', [
                'employee_id' => $timeOff->employee_id,
                'time_off_id' => $timeOff->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    protected function handleTimeOffCancelled(EmployeeTimeOffCancelled $event): void
    {
        $timeOff = $event->timeOff;

        try {
            DB::transaction(function () use ($timeOff) {
                // Only restore balance if the time-off was previously approved
                if ($timeOff->status !== 'approved') {
                    return;
                }

                $employee = Employee::find($timeOff->employee_id);

                if (! $employee) {
                    Log::warning('Employee not found for balance restoration', [
                        'employee_id' => $timeOff->employee_id,
                    ]);

                    return;
                }

                // Calculate days/hours to restore
                $daysToRestore = $timeOff->total_days ?? 0;
                $hoursToRestore = $timeOff->total_hours ?? 0;

                // Restore employee's time-off balance
                Log::info('Restored employee time-off balance', [
                    'employee_id' => $employee->id,
                    'days_restored' => $daysToRestore,
                    'hours_restored' => $hoursToRestore,
                    'time_off_id' => $timeOff->id,
                ]);

                // TODO: Implement actual balance restoration logic
                // This would typically update a time_off_balance table or field
            });
        } catch (\Exception $e) {
            Log::error('Failed to restore employee balance in transaction', [
                'employee_id' => $timeOff->employee_id,
                'time_off_id' => $timeOff->id,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
