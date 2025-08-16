<?php

namespace Fereydooni\Shopping\app\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User;
use Fereydooni\Shopping\app\Models\Transaction;

class TransactionPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any transactions.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('transaction.view.any');
    }

    /**
     * Determine whether the user can view the transaction.
     */
    public function view(User $user, Transaction $transaction): bool
    {
        // Check if user can view any transactions
        if ($user->can('transaction.view.any')) {
            return true;
        }

        // Check if user can view own transactions
        if ($user->can('transaction.view.own')) {
            return $transaction->user_id === $user->id;
        }

        return false;
    }

    /**
     * Determine whether the user can create transactions.
     */
    public function create(User $user): bool
    {
        return $user->can('transaction.create.any') || $user->can('transaction.create.own');
    }

    /**
     * Determine whether the user can update the transaction.
     */
    public function update(User $user, Transaction $transaction): bool
    {
        // Check if user can update any transactions
        if ($user->can('transaction.update.any')) {
            return true;
        }

        // Check if user can update own transactions
        if ($user->can('transaction.update.own')) {
            return $transaction->user_id === $user->id;
        }

        return false;
    }

    /**
     * Determine whether the user can delete the transaction.
     */
    public function delete(User $user, Transaction $transaction): bool
    {
        // Check if user can delete any transactions
        if ($user->can('transaction.delete.any')) {
            return true;
        }

        // Check if user can delete own transactions
        if ($user->can('transaction.delete.own')) {
            return $transaction->user_id === $user->id;
        }

        return false;
    }

    /**
     * Determine whether the user can process transactions.
     */
    public function process(User $user, Transaction $transaction = null): bool
    {
        // Check if user can process any transactions
        if ($user->can('transaction.process.any')) {
            return true;
        }

        // Check if user can process own transactions
        if ($user->can('transaction.process.own') && $transaction) {
            return $transaction->user_id === $user->id;
        }

        return false;
    }

    /**
     * Determine whether the user can refund transactions.
     */
    public function refund(User $user, Transaction $transaction = null): bool
    {
        // Check if user can refund any transactions
        if ($user->can('transaction.refund.any')) {
            return true;
        }

        // Check if user can refund own transactions
        if ($user->can('transaction.refund.own') && $transaction) {
            return $transaction->user_id === $user->id;
        }

        return false;
    }

    /**
     * Determine whether the user can search transactions.
     */
    public function search(User $user): bool
    {
        return $user->can('transaction.search.any') || $user->can('transaction.search.own');
    }

    /**
     * Determine whether the user can export transactions.
     */
    public function export(User $user): bool
    {
        return $user->can('transaction.export');
    }

    /**
     * Determine whether the user can import transactions.
     */
    public function import(User $user): bool
    {
        return $user->can('transaction.import');
    }

    /**
     * Determine whether the user can validate transactions.
     */
    public function validate(User $user): bool
    {
        return $user->can('transaction.validate');
    }

    /**
     * Determine whether the user can view transaction statistics.
     */
    public function viewStatistics(User $user): bool
    {
        return $user->can('transaction.view.statistics');
    }

    /**
     * Determine whether the user can calculate transaction revenue.
     */
    public function calculateRevenue(User $user): bool
    {
        return $user->can('transaction.calculate.revenue');
    }

    /**
     * Determine whether the user can manage payment gateways.
     */
    public function manageGateway(User $user): bool
    {
        return $user->can('transaction.manage.gateway');
    }

    /**
     * Determine whether the user can mark transaction as success.
     */
    public function markAsSuccess(User $user, Transaction $transaction): bool
    {
        return $this->process($user, $transaction);
    }

    /**
     * Determine whether the user can mark transaction as failed.
     */
    public function markAsFailed(User $user, Transaction $transaction): bool
    {
        return $this->process($user, $transaction);
    }

    /**
     * Determine whether the user can mark transaction as refunded.
     */
    public function markAsRefunded(User $user, Transaction $transaction): bool
    {
        return $this->refund($user, $transaction);
    }

    /**
     * Determine whether the user can view transaction by gateway.
     */
    public function viewByGateway(User $user, string $gateway): bool
    {
        return $user->can('transaction.view.any') || $user->can('transaction.view.own');
    }

    /**
     * Determine whether the user can view transaction by status.
     */
    public function viewByStatus(User $user, string $status): bool
    {
        return $user->can('transaction.view.any') || $user->can('transaction.view.own');
    }

    /**
     * Determine whether the user can view transaction by date range.
     */
    public function viewByDateRange(User $user): bool
    {
        return $user->can('transaction.view.any') || $user->can('transaction.view.own');
    }

    /**
     * Determine whether the user can view transaction by amount range.
     */
    public function viewByAmountRange(User $user): bool
    {
        return $user->can('transaction.view.any') || $user->can('transaction.view.own');
    }

    /**
     * Determine whether the user can view transaction by currency.
     */
    public function viewByCurrency(User $user, string $currency): bool
    {
        return $user->can('transaction.view.any') || $user->can('transaction.view.own');
    }

    /**
     * Determine whether the user can view transaction count.
     */
    public function viewCount(User $user): bool
    {
        return $user->can('transaction.view.any') || $user->can('transaction.view.own');
    }

    /**
     * Determine whether the user can view transaction revenue.
     */
    public function viewRevenue(User $user): bool
    {
        return $user->can('transaction.view.statistics') || $user->can('transaction.calculate.revenue');
    }
}
