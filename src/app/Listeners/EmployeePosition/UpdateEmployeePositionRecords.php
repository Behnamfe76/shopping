<?php

namespace App\Listeners\EmployeePosition;

use App\Events\EmployeePosition\EmployeePositionArchived;
use App\Events\EmployeePosition\EmployeePositionUpdated;
use App\Models\Employee;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class UpdateEmployeePositionRecords implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Handle the event.
     */
    public function handle($event): void
    {
        try {
            if ($event instanceof EmployeePositionUpdated) {
                $this->handlePositionUpdated($event);
            } elseif ($event instanceof EmployeePositionArchived) {
                $this->handlePositionArchived($event);
            }
        } catch (\Exception $e) {
            Log::error('Failed to update employee position records', [
                'event' => get_class($event),
                'position_id' => $event->position->id ?? null,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Handle position updated event
     */
    protected function handlePositionUpdated(EmployeePositionUpdated $event): void
    {
        $position = $event->position;
        $changes = $event->changes;

        // Get all employees currently in this position
        $employees = Employee::where('position_id', $position->id)->get();

        foreach ($employees as $employee) {
            $this->updateEmployeePositionRecord($employee, $position, $changes);
        }

        // If department changed, update employee department records
        if (isset($changes['department_id'])) {
            $this->updateEmployeeDepartmentRecords($position->id, $changes['department_id'], $position->department_id);
        }

        Log::info('Employee position records updated for position update', [
            'position_id' => $position->id,
            'title' => $position->title,
            'affected_employees' => $employees->count(),
            'changes' => $changes,
        ]);
    }

    /**
     * Handle position archived event
     */
    protected function handlePositionArchived(EmployeePositionArchived $event): void
    {
        $position = $event->position;
        $archiveDetails = $event->archiveDetails;

        // Get all employees currently in this position
        $employees = Employee::where('position_id', $position->id)->get();

        foreach ($employees as $employee) {
            $this->handleEmployeePositionArchived($employee, $position, $archiveDetails);
        }

        Log::info('Employee position records updated for position archive', [
            'position_id' => $position->id,
            'title' => $position->title,
            'affected_employees' => $employees->count(),
            'archive_details' => $archiveDetails,
        ]);
    }

    /**
     * Update employee position record
     */
    protected function updateEmployeePositionRecord(Employee $employee, $position, array $changes): void
    {
        try {
            // Update employee's current position information
            $employee->update([
                'position_title' => $position->title,
                'position_level' => $position->level->value,
                'position_status' => $position->status->value,
                'department_id' => $position->department_id,
                'updated_at' => now(),
            ]);

            // Create position history record if it doesn't exist
            $this->createPositionHistoryRecord($employee, $position, 'updated', $changes);

            // Update employee's salary information if salary changed
            if (isset($changes['salary_min']) || isset($changes['salary_max'])) {
                $this->updateEmployeeSalaryInfo($employee, $position);
            }

            // Update employee's remote work status if changed
            if (isset($changes['is_remote'])) {
                $this->updateEmployeeRemoteStatus($employee, $position);
            }

            // Update employee's travel requirements if changed
            if (isset($changes['is_travel_required'])) {
                $this->updateEmployeeTravelStatus($employee, $position);
            }

        } catch (\Exception $e) {
            Log::error('Failed to update employee position record', [
                'employee_id' => $employee->id,
                'position_id' => $position->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Handle employee position archived
     */
    protected function handleEmployeePositionArchived(Employee $employee, $position, array $archiveDetails): void
    {
        try {
            // Check if there's a replacement position
            $replacementPositionId = $archiveDetails['replacement_position_id'] ?? null;

            if ($replacementPositionId) {
                // Move employee to replacement position
                $replacementPosition = \App\Models\EmployeePosition::find($replacementPositionId);

                if ($replacementPosition) {
                    $employee->update([
                        'position_id' => $replacementPosition->id,
                        'position_title' => $replacementPosition->title,
                        'position_level' => $replacementPosition->level->value,
                        'position_status' => $replacementPosition->status->value,
                        'department_id' => $replacementPosition->department_id,
                        'updated_at' => now(),
                    ]);

                    // Create position history record for the transition
                    $this->createPositionHistoryRecord($employee, $replacementPosition, 'transferred_from_archived', [
                        'archived_position_id' => $position->id,
                        'archived_position_title' => $position->title,
                        'archive_reason' => $archiveDetails['reason'] ?? 'No reason specified',
                    ]);

                    Log::info('Employee transferred to replacement position', [
                        'employee_id' => $employee->id,
                        'old_position_id' => $position->id,
                        'new_position_id' => $replacementPosition->id,
                    ]);
                }
            } else {
                // No replacement position, mark employee as positionless
                $employee->update([
                    'position_id' => null,
                    'position_title' => null,
                    'position_level' => null,
                    'position_status' => 'positionless',
                    'updated_at' => now(),
                ]);

                // Create position history record for the archive
                $this->createPositionHistoryRecord($employee, $position, 'archived', [
                    'archive_reason' => $archiveDetails['reason'] ?? 'No reason specified',
                    'no_replacement' => true,
                ]);

                Log::info('Employee marked as positionless due to position archive', [
                    'employee_id' => $employee->id,
                    'position_id' => $position->id,
                ]);
            }

        } catch (\Exception $e) {
            Log::error('Failed to handle employee position archived', [
                'employee_id' => $employee->id,
                'position_id' => $position->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Create position history record
     */
    protected function createPositionHistoryRecord(Employee $employee, $position, string $action, array $details = []): void
    {
        try {
            // Check if position_history table exists
            if (DB::getSchemaBuilder()->hasTable('position_history')) {
                DB::table('position_history')->insert([
                    'employee_id' => $employee->id,
                    'position_id' => $position->id,
                    'action' => $action,
                    'position_title' => $position->title,
                    'position_level' => $position->level->value,
                    'position_status' => $position->status->value,
                    'department_id' => $position->department_id,
                    'details' => json_encode($details),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            } else {
                // Fallback to employee_position_history table if it exists
                if (DB::getSchemaBuilder()->hasTable('employee_position_history')) {
                    DB::table('employee_position_history')->insert([
                        'employee_id' => $employee->id,
                        'position_id' => $position->id,
                        'action' => $action,
                        'position_title' => $position->title,
                        'position_level' => $position->level->value,
                        'position_status' => $position->status->value,
                        'department_id' => $position->department_id,
                        'details' => json_encode($details),
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                } else {
                    // If no history tables exist, just log the action
                    Log::info('Position history record created (no history table): '.$action, [
                        'employee_id' => $employee->id,
                        'position_id' => $position->id,
                        'action' => $action,
                        'details' => $details,
                    ]);
                }
            }
        } catch (\Exception $e) {
            Log::warning('Failed to create position history record, using fallback logging', [
                'error' => $e->getMessage(),
                'employee_id' => $employee->id,
                'position_id' => $position->id,
                'action' => $action,
            ]);

            // Log the action to Laravel log as fallback
            Log::info('Position history (fallback): '.$action, [
                'employee_id' => $employee->id,
                'position_id' => $position->id,
                'action' => $action,
                'details' => $details,
            ]);
        }
    }

    /**
     * Update employee department records
     */
    protected function updateEmployeeDepartmentRecords(int $positionId, int $oldDepartmentId, int $newDepartmentId): void
    {
        try {
            // Update all employees in this position to the new department
            Employee::where('position_id', $positionId)
                ->update([
                    'department_id' => $newDepartmentId,
                    'updated_at' => now(),
                ]);

            Log::info('Employee department records updated for position department change', [
                'position_id' => $positionId,
                'old_department_id' => $oldDepartmentId,
                'new_department_id' => $newDepartmentId,
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to update employee department records', [
                'position_id' => $positionId,
                'old_department_id' => $oldDepartmentId,
                'new_department_id' => $newDepartmentId,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Update employee salary information
     */
    protected function updateEmployeeSalaryInfo(Employee $employee, $position): void
    {
        try {
            // Update employee's salary range information
            $employee->update([
                'position_salary_min' => $position->salary_min,
                'position_salary_max' => $position->salary_max,
                'position_hourly_rate_min' => $position->hourly_rate_min,
                'position_hourly_rate_max' => $position->hourly_rate_max,
                'updated_at' => now(),
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to update employee salary info', [
                'employee_id' => $employee->id,
                'position_id' => $position->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Update employee remote status
     */
    protected function updateEmployeeRemoteStatus(Employee $employee, $position): void
    {
        try {
            $employee->update([
                'is_remote' => $position->is_remote,
                'updated_at' => now(),
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to update employee remote status', [
                'employee_id' => $employee->id,
                'position_id' => $position->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Update employee travel status
     */
    protected function updateEmployeeTravelStatus(Employee $employee, $position): void
    {
        try {
            $employee->update([
                'is_travel_required' => $position->is_travel_required,
                'travel_percentage' => $position->travel_percentage,
                'updated_at' => now(),
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to update employee travel status', [
                'employee_id' => $employee->id,
                'position_id' => $position->id,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
