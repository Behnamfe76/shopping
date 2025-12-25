<?php

namespace App\Listeners\EmployeeDepartment;

use App\Events\EmployeeDepartment\EmployeeDepartmentArchived;
use App\Events\EmployeeDepartment\EmployeeDepartmentCreated;
use App\Events\EmployeeDepartment\EmployeeDepartmentManagerAssigned;
use App\Events\EmployeeDepartment\EmployeeDepartmentMoved;
use App\Events\EmployeeDepartment\EmployeeDepartmentUpdated;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class LogDepartmentActivity implements ShouldQueue
{
    use InteractsWithQueue;

    public $queue = 'audit';

    public $tries = 3;

    public $timeout = 30;

    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle($event): void
    {
        try {
            if ($event instanceof EmployeeDepartmentCreated) {
                $this->logDepartmentCreated($event);
            } elseif ($event instanceof EmployeeDepartmentUpdated) {
                $this->logDepartmentUpdated($event);
            } elseif ($event instanceof EmployeeDepartmentManagerAssigned) {
                $this->logManagerAssigned($event);
            } elseif ($event instanceof EmployeeDepartmentMoved) {
                $this->logDepartmentMoved($event);
            } elseif ($event instanceof EmployeeDepartmentArchived) {
                $this->logDepartmentArchived($event);
            }
        } catch (\Exception $e) {
            Log::error('Error logging department activity', [
                'event' => get_class($event),
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Log department created activity
     */
    protected function logDepartmentCreated(EmployeeDepartmentCreated $event): void
    {
        try {
            $this->logActivity([
                'action' => 'department_created',
                'department_id' => $event->department->id,
                'department_name' => $event->department->name,
                'department_code' => $event->department->code,
                'parent_id' => $event->department->parent_id,
                'manager_id' => $event->department->manager_id,
                'created_by' => $event->createdBy,
                'timestamp' => $event->timestamp,
                'details' => [
                    'budget' => $event->department->budget,
                    'headcount_limit' => $event->department->headcount_limit,
                    'location' => $event->department->location,
                    'status' => $event->department->status,
                ],
            ]);
        } catch (\Exception $e) {
            Log::error('Error logging department created activity', [
                'department_id' => $event->department->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Log department updated activity
     */
    protected function logDepartmentUpdated(EmployeeDepartmentUpdated $event): void
    {
        try {
            $this->logActivity([
                'action' => 'department_updated',
                'department_id' => $event->department->id,
                'department_name' => $event->department->name,
                'updated_by' => $event->updatedBy,
                'timestamp' => $event->timestamp,
                'changes' => $event->changes,
                'details' => [
                    'previous_values' => $this->getPreviousValues($event->department, $event->changes),
                    'new_values' => $event->changes,
                ],
            ]);
        } catch (\Exception $e) {
            Log::error('Error logging department updated activity', [
                'department_id' => $event->department->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Log manager assigned activity
     */
    protected function logManagerAssigned(EmployeeDepartmentManagerAssigned $event): void
    {
        try {
            $this->logActivity([
                'action' => 'manager_assigned',
                'department_id' => $event->department->id,
                'department_name' => $event->department->name,
                'manager_id' => $event->managerId,
                'previous_manager_id' => $event->previousManagerId,
                'assigned_by' => $event->assignedBy,
                'timestamp' => $event->timestamp,
                'details' => [
                    'assignment_type' => $event->previousManagerId ? 'reassigned' : 'assigned',
                    'department_code' => $event->department->code,
                ],
            ]);
        } catch (\Exception $e) {
            Log::error('Error logging manager assigned activity', [
                'department_id' => $event->department->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Log department moved activity
     */
    protected function logDepartmentMoved(EmployeeDepartmentMoved $event): void
    {
        try {
            $this->logActivity([
                'action' => 'department_moved',
                'department_id' => $event->department->id,
                'department_name' => $event->department->name,
                'previous_parent_id' => $event->previousParentId,
                'new_parent_id' => $event->newParentId,
                'moved_by' => $event->movedBy,
                'timestamp' => $event->timestamp,
                'details' => [
                    'move_type' => $event->previousParentId ? 'repositioned' : 'positioned',
                    'department_code' => $event->department->code,
                ],
            ]);
        } catch (\Exception $e) {
            Log::error('Error logging department moved activity', [
                'department_id' => $event->department->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Log department archived activity
     */
    protected function logDepartmentArchived(EmployeeDepartmentArchived $event): void
    {
        try {
            $this->logActivity([
                'action' => 'department_archived',
                'department_id' => $event->department->id,
                'department_name' => $event->department->name,
                'archived_by' => $event->archivedBy,
                'reason' => $event->reason,
                'timestamp' => $event->timestamp,
                'details' => [
                    'department_code' => $event->department->code,
                    'final_status' => $event->department->status,
                    'archive_reason' => $event->reason,
                ],
            ]);
        } catch (\Exception $e) {
            Log::error('Error logging department archived activity', [
                'department_id' => $event->department->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Log activity to audit system
     */
    protected function logActivity(array $data): void
    {
        try {
            // This would typically log to an audit table or external audit system
            // For now, we'll use Laravel's logging system

            $logData = [
                'audit_type' => 'department_activity',
                'data' => $data,
            ];

            Log::channel('audit')->info('Department activity logged', $logData);

            // Additional audit logging could go here:
            // - Database audit table
            // - External audit service
            // - Security monitoring system

        } catch (\Exception $e) {
            Log::error('Error logging to audit system', [
                'audit_data' => $data,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Get previous values for changed fields
     */
    protected function getPreviousValues($department, array $changes): array
    {
        try {
            $previousValues = [];

            foreach ($changes as $field => $newValue) {
                if ($department->getOriginal($field) !== null) {
                    $previousValues[$field] = $department->getOriginal($field);
                }
            }

            return $previousValues;
        } catch (\Exception $e) {
            Log::error('Error getting previous values', [
                'error' => $e->getMessage(),
            ]);

            return [];
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed($event, \Throwable $exception): void
    {
        Log::error('Department activity logging job failed', [
            'event' => get_class($event),
            'error' => $exception->getMessage(),
        ]);
    }
}
