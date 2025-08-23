<?php

namespace Fereydooni\Shopping\app\Policies;

use Fereydooni\Shopping\app\Models\EmployeeNote;
use Fereydooni\Shopping\app\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class EmployeeNotePolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('view employee notes') ||
               $user->hasRole(['admin', 'hr', 'manager']);
    }

    public function view(User $user, EmployeeNote $employeeNote): bool
    {
        // Public notes can be viewed by anyone with permission
        if (!$employeeNote->is_private) {
            return $user->hasPermissionTo('view employee notes');
        }

        // Private notes can only be viewed by the creator, HR, or admin
        return $user->id === $employeeNote->user_id ||
               $user->hasRole(['admin', 'hr']) ||
               $user->hasPermissionTo('view private employee notes');
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo('create employee notes') ||
               $user->hasRole(['admin', 'hr', 'manager']);
    }

    public function update(User $user, EmployeeNote $employeeNote): bool
    {
        // Only the creator, HR, or admin can update notes
        return $user->id === $employeeNote->user_id ||
               $user->hasRole(['admin', 'hr']) ||
               $user->hasPermissionTo('edit employee notes');
    }

    public function delete(User $user, EmployeeNote $employeeNote): bool
    {
        // Only the creator, HR, or admin can delete notes
        return $user->id === $employeeNote->user_id ||
               $user->hasRole(['admin', 'hr']) ||
               $user->hasPermissionTo('delete employee notes');
    }

    public function archive(User $user, EmployeeNote $employeeNote): bool
    {
        return $user->hasPermissionTo('archive employee notes') ||
               $user->hasRole(['admin', 'hr', 'manager']);
    }

    public function unarchive(User $user, EmployeeNote $employeeNote): bool
    {
        return $user->hasPermissionTo('unarchive employee notes') ||
               $user->hasRole(['admin', 'hr', 'manager']);
    }

    public function makePrivate(User $user, EmployeeNote $employeeNote): bool
    {
        return $user->id === $employeeNote->user_id ||
               $user->hasRole(['admin', 'hr']) ||
               $user->hasPermissionTo('make employee notes private');
    }

    public function makePublic(User $user, EmployeeNote $employeeNote): bool
    {
        return $user->id === $employeeNote->user_id ||
               $user->hasRole(['admin', 'hr']) ||
               $user->hasPermissionTo('make employee notes public');
    }

    public function addTags(User $user, EmployeeNote $employeeNote): bool
    {
        return $user->id === $employeeNote->user_id ||
               $user->hasRole(['admin', 'hr']) ||
               $user->hasPermissionTo('add tags to employee notes');
    }

    public function removeTags(User $user, EmployeeNote $employeeNote): bool
    {
        return $user->id === $employeeNote->user_id ||
               $user->hasRole(['admin', 'hr']) ||
               $user->hasPermissionTo('remove tags from employee notes');
    }

    public function addAttachment(User $user, EmployeeNote $employeeNote): bool
    {
        return $user->id === $employeeNote->user_id ||
               $user->hasRole(['admin', 'hr']) ||
               $user->hasPermissionTo('add attachments to employee notes');
    }

    public function removeAttachment(User $user, EmployeeNote $employeeNote): bool
    {
        return $user->id === $employeeNote->user_id ||
               $user->hasRole(['admin', 'hr']) ||
               $user->hasPermissionTo('remove attachments from employee notes');
    }

    public function export(User $user): bool
    {
        return $user->hasPermissionTo('export employee notes') ||
               $user->hasRole(['admin', 'hr']);
    }

    public function import(User $user): bool
    {
        return $user->hasPermissionTo('import employee notes') ||
               $user->hasRole(['admin', 'hr']);
    }

    public function bulkArchive(User $user): bool
    {
        return $user->hasPermissionTo('bulk archive employee notes') ||
               $user->hasRole(['admin', 'hr']);
    }

    public function bulkDelete(User $user): bool
    {
        return $user->hasPermissionTo('bulk delete employee notes') ||
               $user->hasRole(['admin', 'hr']);
    }

    public function viewEmployeeNotes(User $user, int $employeeId): bool
    {
        // Users can view notes for employees they manage or if they have permission
        return $user->hasPermissionTo('view employee notes') ||
               $user->hasRole(['admin', 'hr']) ||
               $this->isManagingEmployee($user, $employeeId);
    }

    public function createForEmployee(User $user, int $employeeId): bool
    {
        return $user->hasPermissionTo('create employee notes') ||
               $user->hasRole(['admin', 'hr']) ||
               $this->isManagingEmployee($user, $employeeId);
    }

    protected function isManagingEmployee(User $user, int $employeeId): bool
    {
        // Check if the user is a manager of the employee
        // This would need to be implemented based on your employee structure
        return false; // Placeholder implementation
    }
}

