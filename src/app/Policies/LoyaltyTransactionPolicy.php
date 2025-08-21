<?php

namespace Fereydooni\Shopping\app\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User;
use Fereydooni\Shopping\app\Models\LoyaltyTransaction;

class LoyaltyTransactionPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any loyalty transactions.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('loyalty-transactions.viewAny');
    }

    /**
     * Determine whether the user can view the loyalty transaction.
     */
    public function view(User $user, LoyaltyTransaction $loyaltyTransaction): bool
    {
        return $user->can('loyalty-transactions.view') || 
               $user->id === $loyaltyTransaction->user_id ||
               $user->can('loyalty-transactions.viewAll');
    }

    /**
     * Determine whether the user can create loyalty transactions.
     */
    public function create(User $user): bool
    {
        return $user->can('loyalty-transactions.create');
    }

    /**
     * Determine whether the user can update the loyalty transaction.
     */
    public function update(User $user, LoyaltyTransaction $loyaltyTransaction): bool
    {
        return $user->can('loyalty-transactions.update') || 
               $user->id === $loyaltyTransaction->user_id ||
               $user->can('loyalty-transactions.updateAll');
    }

    /**
     * Determine whether the user can delete the loyalty transaction.
     */
    public function delete(User $user, LoyaltyTransaction $loyaltyTransaction): bool
    {
        return $user->can('loyalty-transactions.delete') || 
               $user->can('loyalty-transactions.deleteAll');
    }

    /**
     * Determine whether the user can restore the loyalty transaction.
     */
    public function restore(User $user, LoyaltyTransaction $loyaltyTransaction): bool
    {
        return $user->can('loyalty-transactions.restore') || 
               $user->can('loyalty-transactions.restoreAll');
    }

    /**
     * Determine whether the user can permanently delete the loyalty transaction.
     */
    public function forceDelete(User $user, LoyaltyTransaction $loyaltyTransaction): bool
    {
        return $user->can('loyalty-transactions.forceDelete') || 
               $user->can('loyalty-transactions.forceDeleteAll');
    }

    /**
     * Determine whether the user can reverse the loyalty transaction.
     */
    public function reverse(User $user, LoyaltyTransaction $loyaltyTransaction): bool
    {
        return $user->can('loyalty-transactions.reverse') || 
               $user->can('loyalty-transactions.reverseAll');
    }

    /**
     * Determine whether the user can add loyalty points.
     */
    public function addPoints(User $user): bool
    {
        return $user->can('loyalty-transactions.addPoints') || 
               $user->can('loyalty-transactions.managePoints');
    }

    /**
     * Determine whether the user can deduct loyalty points.
     */
    public function deductPoints(User $user): bool
    {
        return $user->can('loyalty-transactions.deductPoints') || 
               $user->can('loyalty-transactions.managePoints');
    }

    /**
     * Determine whether the user can calculate loyalty balance.
     */
    public function calculateBalance(User $user): bool
    {
        return $user->can('loyalty-transactions.calculateBalance') || 
               $user->can('loyalty-transactions.view');
    }

    /**
     * Determine whether the user can check loyalty expiration.
     */
    public function checkExpiration(User $user): bool
    {
        return $user->can('loyalty-transactions.checkExpiration') || 
               $user->can('loyalty-transactions.view');
    }

    /**
     * Determine whether the user can calculate loyalty tier.
     */
    public function calculateTier(User $user): bool
    {
        return $user->can('loyalty-transactions.calculateTier') || 
               $user->can('loyalty-transactions.view');
    }

    /**
     * Determine whether the user can validate loyalty transactions.
     */
    public function validateTransaction(User $user): bool
    {
        return $user->can('loyalty-transactions.validateTransaction') || 
               $user->can('loyalty-transactions.create');
    }

    /**
     * Determine whether the user can view loyalty analytics.
     */
    public function viewAnalytics(User $user): bool
    {
        return $user->can('loyalty-transactions.viewAnalytics') || 
               $user->can('loyalty-transactions.viewAll');
    }

    /**
     * Determine whether the user can export loyalty data.
     */
    public function exportData(User $user): bool
    {
        return $user->can('loyalty-transactions.exportData') || 
               $user->can('loyalty-transactions.viewAll');
    }

    /**
     * Determine whether the user can import loyalty data.
     */
    public function importData(User $user): bool
    {
        return $user->can('loyalty-transactions.importData') || 
               $user->can('loyalty-transactions.create');
    }

    /**
     * Determine whether the user can view customer loyalty transactions.
     */
    public function viewCustomerTransactions(User $user, int $customerId): bool
    {
        return $user->can('loyalty-transactions.viewCustomerTransactions') || 
               $user->can('loyalty-transactions.viewAll') ||
               $user->can('customers.view');
    }

    /**
     * Determine whether the user can manage customer loyalty points.
     */
    public function manageCustomerPoints(User $user, int $customerId): bool
    {
        return $user->can('loyalty-transactions.manageCustomerPoints') || 
               $user->can('loyalty-transactions.managePoints') ||
               $user->can('customers.update');
    }

    /**
     * Determine whether the user can view loyalty transaction history.
     */
    public function viewHistory(User $user): bool
    {
        return $user->can('loyalty-transactions.viewHistory') || 
               $user->can('loyalty-transactions.view');
    }

    /**
     * Determine whether the user can view loyalty transaction reports.
     */
    public function viewReports(User $user): bool
    {
        return $user->can('loyalty-transactions.viewReports') || 
               $user->can('loyalty-transactions.viewAnalytics');
    }

    /**
     * Determine whether the user can manage loyalty transaction settings.
     */
    public function manageSettings(User $user): bool
    {
        return $user->can('loyalty-transactions.manageSettings') || 
               $user->can('system.settings');
    }

    /**
     * Determine whether the user can approve loyalty transactions.
     */
    public function approve(User $user, LoyaltyTransaction $loyaltyTransaction): bool
    {
        return $user->can('loyalty-transactions.approve') || 
               $user->can('loyalty-transactions.manageAll');
    }

    /**
     * Determine whether the user can reject loyalty transactions.
     */
    public function reject(User $user, LoyaltyTransaction $loyaltyTransaction): bool
    {
        return $user->can('loyalty-transactions.reject') || 
               $user->can('loyalty-transactions.manageAll');
    }

    /**
     * Determine whether the user can process loyalty transactions.
     */
    public function process(User $user, LoyaltyTransaction $loyaltyTransaction): bool
    {
        return $user->can('loyalty-transactions.process') || 
               $user->can('loyalty-transactions.manageAll');
    }

    /**
     * Determine whether the user can view loyalty transaction audit logs.
     */
    public function viewAuditLogs(User $user): bool
    {
        return $user->can('loyalty-transactions.viewAuditLogs') || 
               $user->can('system.audit');
    }
}
