<?php

namespace App\Policies;

use App\Models\ProviderCommunication;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ProviderCommunicationPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        // Users can view communications if they have the appropriate permissions
        return $user->hasPermissionTo('view provider communications') ||
               $user->hasRole(['admin', 'manager', 'support']);
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, ProviderCommunication $providerCommunication): bool
    {
        // Users can view communications they created or are involved with
        return $user->id === $providerCommunication->user_id ||
               $user->id === $providerCommunication->provider_id ||
               $user->hasPermissionTo('view provider communications') ||
               $user->hasRole(['admin', 'manager', 'support']);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        // Users can create communications if they have the appropriate permissions
        return $user->hasPermissionTo('create provider communications') ||
               $user->hasRole(['admin', 'manager', 'support', 'user']);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, ProviderCommunication $providerCommunication): bool
    {
        // Users can update communications they created or have appropriate permissions
        return $user->id === $providerCommunication->user_id ||
               $user->hasPermissionTo('update provider communications') ||
               $user->hasRole(['admin', 'manager', 'support']);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, ProviderCommunication $providerCommunication): bool
    {
        // Only admins and managers can delete communications
        return $user->hasPermissionTo('delete provider communications') ||
               $user->hasRole(['admin', 'manager']);
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, ProviderCommunication $providerCommunication): bool
    {
        // Only admins and managers can restore deleted communications
        return $user->hasPermissionTo('restore provider communications') ||
               $user->hasRole(['admin', 'manager']);
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, ProviderCommunication $providerCommunication): bool
    {
        // Only admins can permanently delete communications
        return $user->hasPermissionTo('force delete provider communications') ||
               $user->hasRole(['admin']);
    }

    /**
     * Determine whether the user can send communications.
     */
    public function send(User $user): bool
    {
        return $user->hasPermissionTo('send provider communications') ||
               $user->hasRole(['admin', 'manager', 'support', 'user']);
    }

    /**
     * Determine whether the user can reply to communications.
     */
    public function reply(User $user, ProviderCommunication $providerCommunication): bool
    {
        // Users can reply to communications they're involved with or have appropriate permissions
        return $user->id === $providerCommunication->user_id ||
               $user->id === $providerCommunication->provider_id ||
               $user->hasPermissionTo('reply to provider communications') ||
               $user->hasRole(['admin', 'manager', 'support']);
    }

    /**
     * Determine whether the user can mark communications as read.
     */
    public function markAsRead(User $user, ProviderCommunication $providerCommunication): bool
    {
        // Users can mark communications as read if they're involved with them
        return $user->id === $providerCommunication->user_id ||
               $user->id === $providerCommunication->provider_id ||
               $user->hasPermissionTo('mark provider communications as read') ||
               $user->hasRole(['admin', 'manager', 'support']);
    }

    /**
     * Determine whether the user can archive communications.
     */
    public function archive(User $user, ProviderCommunication $providerCommunication): bool
    {
        // Users can archive communications they created or have appropriate permissions
        return $user->id === $providerCommunication->user_id ||
               $user->hasPermissionTo('archive provider communications') ||
               $user->hasRole(['admin', 'manager', 'support']);
    }

    /**
     * Determine whether the user can search communications.
     */
    public function search(User $user): bool
    {
        return $user->hasPermissionTo('search provider communications') ||
               $user->hasRole(['admin', 'manager', 'support', 'user']);
    }

    /**
     * Determine whether the user can export communications.
     */
    public function export(User $user): bool
    {
        return $user->hasPermissionTo('export provider communications') ||
               $user->hasRole(['admin', 'manager']);
    }

    /**
     * Determine whether the user can import communications.
     */
    public function import(User $user): bool
    {
        return $user->hasPermissionTo('import provider communications') ||
               $user->hasRole(['admin', 'manager']);
    }

    /**
     * Determine whether the user can view analytics.
     */
    public function viewAnalytics(User $user): bool
    {
        return $user->hasPermissionTo('view provider communication analytics') ||
               $user->hasRole(['admin', 'manager']);
    }

    /**
     * Determine whether the user can perform bulk operations.
     */
    public function bulkOperations(User $user): bool
    {
        return $user->hasPermissionTo('bulk operations on provider communications') ||
               $user->hasRole(['admin', 'manager']);
    }

    /**
     * Determine whether the user can manage threads.
     */
    public function manageThreads(User $user): bool
    {
        return $user->hasPermissionTo('manage provider communication threads') ||
               $user->hasRole(['admin', 'manager', 'support']);
    }

    /**
     * Determine whether the user can manage attachments.
     */
    public function manageAttachments(User $user, ProviderCommunication $providerCommunication): bool
    {
        // Users can manage attachments for communications they created or have appropriate permissions
        return $user->id === $providerCommunication->user_id ||
               $user->hasPermissionTo('manage provider communication attachments') ||
               $user->hasRole(['admin', 'manager', 'support']);
    }

    /**
     * Determine whether the user can view provider-specific communications.
     */
    public function viewByProvider(User $user, int $providerId): bool
    {
        // Users can view communications for providers they're associated with or have appropriate permissions
        return $user->provider_id === $providerId ||
               $user->hasPermissionTo('view provider communications') ||
               $user->hasRole(['admin', 'manager', 'support']);
    }

    /**
     * Determine whether the user can view user-specific communications.
     */
    public function viewByUser(User $user, int $userId): bool
    {
        // Users can view their own communications or have appropriate permissions
        return $user->id === $userId ||
               $user->hasPermissionTo('view provider communications') ||
               $user->hasRole(['admin', 'manager', 'support']);
    }

    /**
     * Determine whether the user can view conversation between provider and user.
     */
    public function viewConversation(User $user, int $providerId, int $userId): bool
    {
        // Users can view conversations they're involved with or have appropriate permissions
        return $user->id === $userId ||
               $user->provider_id === $providerId ||
               $user->hasPermissionTo('view provider communications') ||
               $user->hasRole(['admin', 'manager', 'support']);
    }

    /**
     * Determine whether the user can view communication thread.
     */
    public function viewThread(User $user, string $threadId): bool
    {
        // Users can view threads if they have appropriate permissions
        return $user->hasPermissionTo('view provider communication threads') ||
               $user->hasRole(['admin', 'manager', 'support']);
    }

    /**
     * Determine whether the user can set communications as urgent.
     */
    public function setUrgent(User $user, ProviderCommunication $providerCommunication): bool
    {
        // Users can set communications as urgent if they created them or have appropriate permissions
        return $user->id === $providerCommunication->user_id ||
               $user->hasPermissionTo('set provider communications as urgent') ||
               $user->hasRole(['admin', 'manager', 'support']);
    }

    /**
     * Determine whether the user can update satisfaction ratings.
     */
    public function updateSatisfactionRating(User $user, ProviderCommunication $providerCommunication): bool
    {
        // Users can update satisfaction ratings for communications they're involved with
        return $user->id === $providerCommunication->user_id ||
               $user->id === $providerCommunication->provider_id ||
               $user->hasPermissionTo('update provider communication satisfaction ratings') ||
               $user->hasRole(['admin', 'manager', 'support']);
    }
}
