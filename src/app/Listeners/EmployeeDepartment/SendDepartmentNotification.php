<?php

namespace App\Listeners\EmployeeDepartment;

use App\Events\EmployeeDepartment\EmployeeDepartmentCreated;
use App\Events\EmployeeDepartment\EmployeeDepartmentUpdated;
use App\Events\EmployeeDepartment\EmployeeDepartmentManagerAssigned;
use App\Events\EmployeeDepartment\EmployeeDepartmentMoved;
use App\Events\EmployeeDepartment\EmployeeDepartmentArchived;
use App\Notifications\EmployeeDepartment\DepartmentCreated;
use App\Notifications\EmployeeDepartment\ManagerAssigned;
use App\Notifications\EmployeeDepartment\DepartmentMoved;
use App\Notifications\EmployeeDepartment\DepartmentArchived;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class SendDepartmentNotification implements ShouldQueue
{
    use InteractsWithQueue;

    public $queue = 'notifications';
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
                $this->handleDepartmentCreated($event);
            } elseif ($event instanceof EmployeeDepartmentUpdated) {
                $this->handleDepartmentUpdated($event);
            } elseif ($event instanceof EmployeeDepartmentManagerAssigned) {
                $this->handleManagerAssigned($event);
            } elseif ($event instanceof EmployeeDepartmentMoved) {
                $this->handleDepartmentMoved($event);
            } elseif ($event instanceof EmployeeDepartmentArchived) {
                $this->handleDepartmentArchived($event);
            }
        } catch (\Exception $e) {
            Log::error('Error sending department notification', [
                'event' => get_class($event),
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Handle department created event
     */
    protected function handleDepartmentCreated(EmployeeDepartmentCreated $event): void
    {
        try {
            // Notify relevant stakeholders
            $this->notifyStakeholders($event->department, new DepartmentCreated($event->department));

            Log::info('Department creation notification sent', [
                'department_id' => $event->department->id,
                'department_name' => $event->department->name
            ]);
        } catch (\Exception $e) {
            Log::error('Error handling department created notification', [
                'department_id' => $event->department->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Handle department updated event
     */
    protected function handleDepartmentUpdated(EmployeeDepartmentUpdated $event): void
    {
        try {
            // Check if important fields were changed
            $importantChanges = $this->getImportantChanges($event->changes);

            if (!empty($importantChanges)) {
                // Notify relevant stakeholders about important changes
                $this->notifyStakeholders($event->department, new DepartmentCreated($event->department));

                Log::info('Department update notification sent', [
                    'department_id' => $event->department->id,
                    'changes' => $importantChanges
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Error handling department updated notification', [
                'department_id' => $event->department->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Handle manager assigned event
     */
    protected function handleManagerAssigned(EmployeeDepartmentManagerAssigned $event): void
    {
        try {
            // Notify the new manager
            $this->notifyManager($event->managerId, new ManagerAssigned($event->department));

            // Notify the previous manager if exists
            if ($event->previousManagerId) {
                $this->notifyManager($event->previousManagerId, new ManagerAssigned($event->department, 'removed'));
            }

            Log::info('Manager assignment notification sent', [
                'department_id' => $event->department->id,
                'manager_id' => $event->managerId,
                'previous_manager_id' => $event->previousManagerId
            ]);
        } catch (\Exception $e) {
            Log::error('Error handling manager assigned notification', [
                'department_id' => $event->department->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Handle department moved event
     */
    protected function handleDepartmentMoved(EmployeeDepartmentMoved $event): void
    {
        try {
            // Notify relevant stakeholders about the move
            $this->notifyStakeholders($event->department, new DepartmentMoved($event->department, $event->previousParentId, $event->newParentId));

            Log::info('Department moved notification sent', [
                'department_id' => $event->department->id,
                'previous_parent_id' => $event->previousParentId,
                'new_parent_id' => $event->newParentId
            ]);
        } catch (\Exception $e) {
            Log::error('Error handling department moved notification', [
                'department_id' => $event->department->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Handle department archived event
     */
    protected function handleDepartmentArchived(EmployeeDepartmentArchived $event): void
    {
        try {
            // Notify relevant stakeholders about the archive
            $this->notifyStakeholders($event->department, new DepartmentArchived($event->department, $event->reason));

            Log::info('Department archived notification sent', [
                'department_id' => $event->department->id,
                'reason' => $event->reason
            ]);
        } catch (\Exception $e) {
            Log::error('Error handling department archived notification', [
                'department_id' => $event->department->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Notify stakeholders about department changes
     */
    protected function notifyStakeholders($department, $notification): void
    {
        try {
            // Get stakeholders (HR managers, finance managers, etc.)
            $stakeholders = $this->getStakeholders($department);

            foreach ($stakeholders as $stakeholder) {
                $stakeholder->notify($notification);
            }
        } catch (\Exception $e) {
            Log::error('Error notifying stakeholders', [
                'department_id' => $department->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Notify specific manager
     */
    protected function notifyManager(int $managerId, $notification): void
    {
        try {
            // Get manager user
            $manager = $this->getManagerUser($managerId);

            if ($manager) {
                $manager->notify($notification);
            }
        } catch (\Exception $e) {
            Log::error('Error notifying manager', [
                'manager_id' => $managerId,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Get stakeholders for department
     */
    protected function getStakeholders($department): array
    {
        try {
            // This would typically query for users with specific roles
            // For now, return empty array
            return [];
        } catch (\Exception $e) {
            Log::error('Error getting stakeholders', [
                'department_id' => $department->id,
                'error' => $e->getMessage()
            ]);
            return [];
        }
    }

    /**
     * Get manager user
     */
    protected function getManagerUser(int $managerId)
    {
        try {
            // This would typically query for the user
            // For now, return null
            return null;
        } catch (\Exception $e) {
            Log::error('Error getting manager user', [
                'manager_id' => $managerId,
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }

    /**
     * Get important changes from update event
     */
    protected function getImportantChanges(array $changes): array
    {
        $importantFields = ['name', 'code', 'manager_id', 'parent_id', 'budget', 'status'];

        return array_intersect_key($changes, array_flip($importantFields));
    }

    /**
     * Handle a job failure.
     */
    public function failed($event, \Throwable $exception): void
    {
        Log::error('Department notification job failed', [
            'event' => get_class($event),
            'error' => $exception->getMessage()
        ]);
    }
}
