<?php

namespace Fereydooni\Shopping\app\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Fereydooni\Shopping\app\Models\CustomerNote;
use Illuminate\Foundation\Auth\User;

class CustomerNotePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any customer notes.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('customer-notes.viewAny');
    }

    /**
     * Determine whether the user can view the customer note.
     */
    public function view(User $user, CustomerNote $customerNote): bool
    {
        // Check if user has general permission
        if (!$user->hasPermissionTo('customer-notes.view')) {
            return false;
        }

        // Check if note is private and user is not the creator or admin
        if ($customerNote->is_private && $customerNote->user_id !== $user->id && !$user->hasRole('admin')) {
            return false;
        }

        return true;
    }

    /**
     * Determine whether the user can create customer notes.
     */
    public function create(User $user): bool
    {
        return $user->hasPermissionTo('customer-notes.create');
    }

    /**
     * Determine whether the user can update the customer note.
     */
    public function update(User $user, CustomerNote $customerNote): bool
    {
        // Check if user has general permission
        if (!$user->hasPermissionTo('customer-notes.update')) {
            return false;
        }

        // Only the creator or admin can edit notes
        return $customerNote->user_id === $user->id || $user->hasRole('admin');
    }

    /**
     * Determine whether the user can delete the customer note.
     */
    public function delete(User $user, CustomerNote $customerNote): bool
    {
        // Check if user has general permission
        if (!$user->hasPermissionTo('customer-notes.delete')) {
            return false;
        }

        // Only the creator or admin can delete notes
        return $customerNote->user_id === $user->id || $user->hasRole('admin');
    }

    /**
     * Determine whether the user can restore the customer note.
     */
    public function restore(User $user, CustomerNote $customerNote): bool
    {
        return $user->hasPermissionTo('customer-notes.restore') && 
               ($customerNote->user_id === $user->id || $user->hasRole('admin'));
    }

    /**
     * Determine whether the user can permanently delete the customer note.
     */
    public function forceDelete(User $user, CustomerNote $customerNote): bool
    {
        return $user->hasPermissionTo('customer-notes.forceDelete') && $user->hasRole('admin');
    }

    /**
     * Determine whether the user can pin the customer note.
     */
    public function pin(User $user, CustomerNote $customerNote): bool
    {
        return $user->hasPermissionTo('customer-notes.pin') && 
               ($customerNote->user_id === $user->id || $user->hasRole('admin'));
    }

    /**
     * Determine whether the user can unpin the customer note.
     */
    public function unpin(User $user, CustomerNote $customerNote): bool
    {
        return $user->hasPermissionTo('customer-notes.unpin') && 
               ($customerNote->user_id === $user->id || $user->hasRole('admin'));
    }

    /**
     * Determine whether the user can make the customer note private.
     */
    public function makePrivate(User $user, CustomerNote $customerNote): bool
    {
        return $user->hasPermissionTo('customer-notes.makePrivate') && 
               ($customerNote->user_id === $user->id || $user->hasRole('admin'));
    }

    /**
     * Determine whether the user can make the customer note public.
     */
    public function makePublic(User $user, CustomerNote $customerNote): bool
    {
        return $user->hasPermissionTo('customer-notes.makePublic') && 
               ($customerNote->user_id === $user->id || $user->hasRole('admin'));
    }

    /**
     * Determine whether the user can manage tags on the customer note.
     */
    public function manageTags(User $user, CustomerNote $customerNote): bool
    {
        return $user->hasPermissionTo('customer-notes.manageTags') && 
               ($customerNote->user_id === $user->id || $user->hasRole('admin'));
    }

    /**
     * Determine whether the user can manage attachments on the customer note.
     */
    public function manageAttachments(User $user, CustomerNote $customerNote): bool
    {
        return $user->hasPermissionTo('customer-notes.manageAttachments') && 
               ($customerNote->user_id === $user->id || $user->hasRole('admin'));
    }

    /**
     * Determine whether the user can view private customer notes.
     */
    public function viewPrivate(User $user): bool
    {
        return $user->hasPermissionTo('customer-notes.viewPrivate') || $user->hasRole('admin');
    }

    /**
     * Determine whether the user can export customer note data.
     */
    public function exportData(User $user): bool
    {
        return $user->hasPermissionTo('customer-notes.exportData');
    }

    /**
     * Determine whether the user can view customer notes for a specific customer.
     */
    public function viewCustomerNotes(User $user, int $customerId): bool
    {
        return $user->hasPermissionTo('customer-notes.view') || 
               $user->hasPermissionTo('customers.view');
    }

    /**
     * Determine whether the user can create customer notes for a specific customer.
     */
    public function createCustomerNotes(User $user, int $customerId): bool
    {
        return $user->hasPermissionTo('customer-notes.create') || 
               $user->hasPermissionTo('customers.update');
    }

    /**
     * Determine whether the user can view customer note statistics.
     */
    public function viewStats(User $user): bool
    {
        return $user->hasPermissionTo('customer-notes.viewStats') || $user->hasRole('admin');
    }

    /**
     * Determine whether the user can manage customer note templates.
     */
    public function manageTemplates(User $user): bool
    {
        return $user->hasPermissionTo('customer-notes.manageTemplates') || $user->hasRole('admin');
    }

    /**
     * Determine whether the user can import customer notes.
     */
    public function importData(User $user): bool
    {
        return $user->hasPermissionTo('customer-notes.importData') || $user->hasRole('admin');
    }
}
