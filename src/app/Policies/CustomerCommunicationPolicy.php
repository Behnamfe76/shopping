<?php

namespace Fereydooni\Shopping\app\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Fereydooni\Shopping\app\Models\User;
use Fereydooni\Shopping\app\Models\CustomerCommunication;

class CustomerCommunicationPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any customer communications.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('customer-communications.viewAny');
    }

    /**
     * Determine whether the user can view the customer communication.
     */
    public function view(User $user, CustomerCommunication $communication): bool
    {
        return $user->can('customer-communications.view') || 
               ($user->can('customer-communications.viewOwn') && $communication->user_id === $user->id);
    }

    /**
     * Determine whether the user can create customer communications.
     */
    public function create(User $user): bool
    {
        return $user->can('customer-communications.create');
    }

    /**
     * Determine whether the user can update the customer communication.
     */
    public function update(User $user, CustomerCommunication $communication): bool
    {
        return $user->can('customer-communications.update') || 
               ($user->can('customer-communications.updateOwn') && $communication->user_id === $user->id);
    }

    /**
     * Determine whether the user can delete the customer communication.
     */
    public function delete(User $user, CustomerCommunication $communication): bool
    {
        return $user->can('customer-communications.delete') || 
               ($user->can('customer-communications.deleteOwn') && $communication->user_id === $user->id);
    }

    /**
     * Determine whether the user can restore the customer communication.
     */
    public function restore(User $user, CustomerCommunication $communication): bool
    {
        return $user->can('customer-communications.restore');
    }

    /**
     * Determine whether the user can permanently delete the customer communication.
     */
    public function forceDelete(User $user, CustomerCommunication $communication): bool
    {
        return $user->can('customer-communications.forceDelete');
    }

    /**
     * Determine whether the user can schedule the customer communication.
     */
    public function schedule(User $user, CustomerCommunication $communication): bool
    {
        return $user->can('customer-communications.schedule') || 
               ($user->can('customer-communications.scheduleOwn') && $communication->user_id === $user->id);
    }

    /**
     * Determine whether the user can send the customer communication.
     */
    public function send(User $user, CustomerCommunication $communication): bool
    {
        return $user->can('customer-communications.send') || 
               ($user->can('customer-communications.sendOwn') && $communication->user_id === $user->id);
    }

    /**
     * Determine whether the user can cancel the customer communication.
     */
    public function cancel(User $user, CustomerCommunication $communication): bool
    {
        return $user->can('customer-communications.cancel') || 
               ($user->can('customer-communications.cancelOwn') && $communication->user_id === $user->id);
    }

    /**
     * Determine whether the user can reschedule the customer communication.
     */
    public function reschedule(User $user, CustomerCommunication $communication): bool
    {
        return $user->can('customer-communications.reschedule') || 
               ($user->can('customer-communications.rescheduleOwn') && $communication->user_id === $user->id);
    }

    /**
     * Determine whether the user can mark the customer communication as delivered.
     */
    public function markAsDelivered(User $user, CustomerCommunication $communication): bool
    {
        return $user->can('customer-communications.markAsDelivered') || 
               ($user->can('customer-communications.markAsDeliveredOwn') && $communication->user_id === $user->id);
    }

    /**
     * Determine whether the user can mark the customer communication as opened.
     */
    public function markAsOpened(User $user, CustomerCommunication $communication): bool
    {
        return $user->can('customer-communications.markAsOpened') || 
               ($user->can('customer-communications.markAsOpenedOwn') && $communication->user_id === $user->id);
    }

    /**
     * Determine whether the user can mark the customer communication as clicked.
     */
    public function markAsClicked(User $user, CustomerCommunication $communication): bool
    {
        return $user->can('customer-communications.markAsClicked') || 
               ($user->can('customer-communications.markAsClickedOwn') && $communication->user_id === $user->id);
    }

    /**
     * Determine whether the user can mark the customer communication as bounced.
     */
    public function markAsBounced(User $user, CustomerCommunication $communication): bool
    {
        return $user->can('customer-communications.markAsBounced') || 
               ($user->can('customer-communications.markAsBouncedOwn') && $communication->user_id === $user->id);
    }

    /**
     * Determine whether the user can mark the customer communication as unsubscribed.
     */
    public function markAsUnsubscribed(User $user, CustomerCommunication $communication): bool
    {
        return $user->can('customer-communications.markAsUnsubscribed') || 
               ($user->can('customer-communications.markAsUnsubscribedOwn') && $communication->user_id === $user->id);
    }

    /**
     * Determine whether the user can view customer communication analytics.
     */
    public function viewAnalytics(User $user): bool
    {
        return $user->can('customer-communications.viewAnalytics');
    }

    /**
     * Determine whether the user can export customer communication data.
     */
    public function exportData(User $user): bool
    {
        return $user->can('customer-communications.exportData');
    }

    /**
     * Determine whether the user can import customer communication data.
     */
    public function importData(User $user): bool
    {
        return $user->can('customer-communications.importData');
    }

    /**
     * Determine whether the user can manage customer communication attachments.
     */
    public function manageAttachments(User $user, CustomerCommunication $communication): bool
    {
        return $user->can('customer-communications.manageAttachments') || 
               ($user->can('customer-communications.manageAttachmentsOwn') && $communication->user_id === $user->id);
    }

    /**
     * Determine whether the user can view customer communication tracking data.
     */
    public function viewTrackingData(User $user, CustomerCommunication $communication): bool
    {
        return $user->can('customer-communications.viewTrackingData') || 
               ($user->can('customer-communications.viewTrackingDataOwn') && $communication->user_id === $user->id);
    }
}
