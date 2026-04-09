<?php

namespace Fereydooni\Shopping\app\Policies;

use App\Models\User;
use Fereydooni\Shopping\app\Models\Team;
use Illuminate\Auth\Access\HandlesAuthorization;

class TeamPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('team.view') ||
               $user->can('team.view-all') ||
               $user->can('team.view-own') ||
               $user->can('team.view-department');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Team $team): bool
    {
        // Check if user has general view permission
        if ($user->can('team.view-all')) {
            return true;
        }

        // Check if user can view their own team
        if ($user->can('team.view-own')) {
            // This would typically check if user belongs to this team
            // For now, we'll allow if they have the permission
            return true;
        }

        // Check if user can view department teams
        if ($user->can('team.view-department')) {
            // This would typically check if user belongs to the same department
            // For now, we'll allow if they have the permission
            return true;
        }

        // Check basic view permission
        return $user->can('team.view');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('team.create') ||
               $user->can('team.manage-all');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Team $team): bool
    {
        // Check if user has general edit permission
        if ($user->can('team.manage-all')) {
            return true;
        }

        // Check basic edit permission
        if (! $user->can('team.edit')) {
            return false;
        }

        // Check if user can edit their own team
        if ($user->can('team.view-own')) {
            // This would typically check if user belongs to this team
            // For now, we'll allow if they have the permission
            return true;
        }

        // Check if user can edit department teams
        if ($user->can('team.view-department')) {
            // This would typically check if user belongs to the same department
            // For now, we'll allow if they have the permission
            return true;
        }

        return true;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Team $team): bool
    {
        // Check if user has general delete permission
        if ($user->can('team.manage-all')) {
            return true;
        }

        // Check basic delete permission
        return $user->can('team.delete');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Team $team): bool
    {
        return $user->can('team.edit') ||
               $user->can('team.manage-all');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Team $team): bool
    {
        return $user->can('team.delete') ||
               $user->can('team.manage-all');
    }

    /**
     * Determine whether the user can delete all teams.
     */
    public function deleteAll(User $user): bool
    {
        return $user->can('team.delete-all') ||
               $user->can('team.manage-all');
    }

    /**
     * Determine whether the user can delete some teams.
     */
    public function deleteSome(User $user): bool
    {
        return $user->can('team.delete') ||
               $user->can('team.manage-all');
    }

    /**
     * Determine whether the user can manage team members.
     */
    public function manageMember(User $user, Team $team): bool
    {
        return $user->can('team.manage-members') ||
               $user->can('team.manage-all');
    }

    /**
     * Determine whether the user can manage team managers.
     */
    public function manageManager(User $user, Team $team): bool
    {
        return $user->can('team.manage-managers') ||
               $user->can('team.manage-all');
    }

    /**
     * Determine whether the user can export team data.
     */
    public function export(User $user, ?Team $team = null): bool
    {
        return $user->can('team.export') ||
               $user->can('team.manage-all');
    }

    /**
     * Determine whether the user can import team data.
     */
    public function import(User $user, ?Team $team = null): bool
    {
        return $user->can('team.import') ||
               $user->can('team.manage-all');
    }

    /**
     * Determine whether the user can view team statistics.
     */
    public function viewStats(User $user, ?Team $team = null): bool
    {
        return $user->can('team.statistics') ||
               $user->can('team.view-all') ||
               $user->can('team.manage-all');
    }

    /**
     * Determine whether the user can audit team activities.
     */
    public function audit(User $user, ?Team $team = null): bool
    {
        return $user->can('team.audit') ||
               $user->can('team.manage-all');
    }

    /**
     * Determine whether the user can view sensitive team information.
     */
    public function viewSensitive(User $user, Team $team): bool
    {
        return $user->can('team.view-sensitive') ||
               $user->can('team.manage-all');
    }
}
