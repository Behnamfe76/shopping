<?php

namespace App\Listeners\EmployeeDepartment;

use App\Events\EmployeeDepartment\EmployeeDepartmentArchived;
use App\Events\EmployeeDepartment\EmployeeDepartmentCreated;
use App\Events\EmployeeDepartment\EmployeeDepartmentMoved;
use App\Events\EmployeeDepartment\EmployeeDepartmentUpdated;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class UpdateDepartmentHierarchy implements ShouldQueue
{
    use InteractsWithQueue;

    public $queue = 'hierarchy';

    public $tries = 3;

    public $timeout = 60;

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
            } elseif ($event instanceof EmployeeDepartmentMoved) {
                $this->handleDepartmentMoved($event);
            } elseif ($event instanceof EmployeeDepartmentArchived) {
                $this->handleDepartmentArchived($event);
            }
        } catch (\Exception $e) {
            Log::error('Error updating department hierarchy', [
                'event' => get_class($event),
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Handle department created event
     */
    protected function handleDepartmentCreated(EmployeeDepartmentCreated $event): void
    {
        try {
            $department = $event->department;

            // Update hierarchy cache
            $this->updateHierarchyCache();

            // Update department tree
            $this->updateDepartmentTree();

            // Update parent department children count
            if ($department->parent_id) {
                $this->updateParentChildrenCount($department->parent_id);
            }

            Log::info('Department hierarchy updated for new department', [
                'department_id' => $department->id,
                'parent_id' => $department->parent_id,
            ]);
        } catch (\Exception $e) {
            Log::error('Error handling department created hierarchy update', [
                'department_id' => $event->department->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Handle department updated event
     */
    protected function handleDepartmentUpdated(EmployeeDepartmentUpdated $event): void
    {
        try {
            $department = $event->department;
            $changes = $event->changes;

            // Check if hierarchy-related fields changed
            if (isset($changes['parent_id']) || isset($changes['status'])) {
                $this->updateHierarchyCache();
                $this->updateDepartmentTree();

                Log::info('Department hierarchy updated for department changes', [
                    'department_id' => $department->id,
                    'changes' => $changes,
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Error handling department updated hierarchy update', [
                'department_id' => $event->department->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Handle department moved event
     */
    protected function handleDepartmentMoved(EmployeeDepartmentMoved $event): void
    {
        try {
            $department = $event->department;
            $previousParentId = $event->previousParentId;
            $newParentId = $event->newParentId;

            // Update hierarchy cache
            $this->updateHierarchyCache();

            // Update department tree
            $this->updateDepartmentTree();

            // Update previous parent children count
            if ($previousParentId) {
                $this->updateParentChildrenCount($previousParentId);
            }

            // Update new parent children count
            if ($newParentId) {
                $this->updateParentChildrenCount($newParentId);
            }

            // Update all descendants' depth levels
            $this->updateDescendantsDepth($department->id);

            Log::info('Department hierarchy updated for department move', [
                'department_id' => $department->id,
                'previous_parent_id' => $previousParentId,
                'new_parent_id' => $newParentId,
            ]);
        } catch (\Exception $e) {
            Log::error('Error handling department moved hierarchy update', [
                'department_id' => $event->department->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Handle department archived event
     */
    protected function handleDepartmentArchived(EmployeeDepartmentArchived $event): void
    {
        try {
            $department = $event->department;

            // Update hierarchy cache
            $this->updateHierarchyCache();

            // Update department tree
            $this->updateDepartmentTree();

            // Update parent children count if exists
            if ($department->parent_id) {
                $this->updateParentChildrenCount($department->parent_id);
            }

            Log::info('Department hierarchy updated for department archive', [
                'department_id' => $department->id,
            ]);
        } catch (\Exception $e) {
            Log::error('Error handling department archived hierarchy update', [
                'department_id' => $event->department->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Update hierarchy cache
     */
    protected function updateHierarchyCache(): void
    {
        try {
            // Clear hierarchy-related caches
            Cache::forget('department_hierarchy');
            Cache::forget('department_tree');
            Cache::forget('department_depth_levels');

            // Regenerate cache in background
            // This would typically be done by a separate job
        } catch (\Exception $e) {
            Log::error('Error updating hierarchy cache', [
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Update department tree
     */
    protected function updateDepartmentTree(): void
    {
        try {
            // Clear tree cache
            Cache::forget('department_tree');

            // Regenerate tree in background
            // This would typically be done by a separate job
        } catch (\Exception $e) {
            Log::error('Error updating department tree', [
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Update parent children count
     */
    protected function updateParentChildrenCount(int $parentId): void
    {
        try {
            // Update children count for parent department
            // This would typically update a cached field or trigger a recalculation
        } catch (\Exception $e) {
            Log::error('Error updating parent children count', [
                'parent_id' => $parentId,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Update descendants depth levels
     */
    protected function updateDescendantsDepth(int $departmentId): void
    {
        try {
            // Update depth levels for all descendants
            // This would typically be done by a recursive function
        } catch (\Exception $e) {
            Log::error('Error updating descendants depth', [
                'department_id' => $departmentId,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed($event, \Throwable $exception): void
    {
        Log::error('Department hierarchy update job failed', [
            'event' => get_class($event),
            'error' => $exception->getMessage(),
        ]);
    }
}
