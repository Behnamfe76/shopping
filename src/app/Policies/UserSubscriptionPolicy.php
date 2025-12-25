<?php

namespace Fereydooni\Shopping\app\Policies;

use Fereydooni\Shopping\app\Models\UserSubscription;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User;

class UserSubscriptionPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any user subscriptions.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('user-subscription.view.any');
    }

    /**
     * Determine whether the user can view the user subscription.
     */
    public function view(User $user, UserSubscription $userSubscription): bool
    {
        if ($user->can('user-subscription.view.any')) {
            return true;
        }

        if ($user->can('user-subscription.view.own')) {
            return $this->isOwner($user, $userSubscription);
        }

        return false;
    }

    /**
     * Determine whether the user can create user subscriptions.
     */
    public function create(User $user): bool
    {
        return $user->can('user-subscription.create.any') || $user->can('user-subscription.create.own');
    }

    /**
     * Determine whether the user can update the user subscription.
     */
    public function update(User $user, UserSubscription $userSubscription): bool
    {
        if ($user->can('user-subscription.update.any')) {
            return true;
        }

        if ($user->can('user-subscription.update.own')) {
            return $this->isOwner($user, $userSubscription);
        }

        return false;
    }

    /**
     * Determine whether the user can delete the user subscription.
     */
    public function delete(User $user, UserSubscription $userSubscription): bool
    {
        if ($user->can('user-subscription.delete.any')) {
            return true;
        }

        if ($user->can('user-subscription.delete.own')) {
            return $this->isOwner($user, $userSubscription);
        }

        return false;
    }

    /**
     * Determine whether the user can activate the user subscription.
     */
    public function activate(User $user, UserSubscription $userSubscription): bool
    {
        if ($user->can('user-subscription.activate.any')) {
            return true;
        }

        if ($user->can('user-subscription.activate.own')) {
            return $this->isOwner($user, $userSubscription);
        }

        return false;
    }

    /**
     * Determine whether the user can cancel the user subscription.
     */
    public function cancel(User $user, UserSubscription $userSubscription): bool
    {
        if ($user->can('user-subscription.cancel.any')) {
            return true;
        }

        if ($user->can('user-subscription.cancel.own')) {
            return $this->isOwner($user, $userSubscription);
        }

        return false;
    }

    /**
     * Determine whether the user can expire the user subscription.
     */
    public function expire(User $user, UserSubscription $userSubscription): bool
    {
        if ($user->can('user-subscription.expire.any')) {
            return true;
        }

        if ($user->can('user-subscription.expire.own')) {
            return $this->isOwner($user, $userSubscription);
        }

        return false;
    }

    /**
     * Determine whether the user can renew the user subscription.
     */
    public function renew(User $user, UserSubscription $userSubscription): bool
    {
        if ($user->can('user-subscription.renew.any')) {
            return true;
        }

        if ($user->can('user-subscription.renew.own')) {
            return $this->isOwner($user, $userSubscription);
        }

        return false;
    }

    /**
     * Determine whether the user can pause the user subscription.
     */
    public function pause(User $user, UserSubscription $userSubscription): bool
    {
        if ($user->can('user-subscription.pause.any')) {
            return true;
        }

        if ($user->can('user-subscription.pause.own')) {
            return $this->isOwner($user, $userSubscription);
        }

        return false;
    }

    /**
     * Determine whether the user can resume the user subscription.
     */
    public function resume(User $user, UserSubscription $userSubscription): bool
    {
        if ($user->can('user-subscription.resume.any')) {
            return true;
        }

        if ($user->can('user-subscription.resume.own')) {
            return $this->isOwner($user, $userSubscription);
        }

        return false;
    }

    /**
     * Determine whether the user can view user subscription analytics.
     */
    public function viewAnalytics(User $user): bool
    {
        return $user->can('user-subscription.view.analytics');
    }

    /**
     * Determine whether the user can view user subscription revenue.
     */
    public function viewRevenue(User $user): bool
    {
        return $user->can('user-subscription.view.revenue');
    }

    /**
     * Determine whether the user can export user subscription data.
     */
    public function export(User $user): bool
    {
        return $user->can('user-subscription.export');
    }

    /**
     * Determine whether the user can import user subscription data.
     */
    public function import(User $user): bool
    {
        return $user->can('user-subscription.import');
    }

    /**
     * Determine whether the user can manage user subscription settings.
     */
    public function manageSettings(User $user): bool
    {
        return $user->can('user-subscription.manage.settings');
    }

    /**
     * Determine whether the user can view user subscription statistics.
     */
    public function viewStatistics(User $user): bool
    {
        return $user->can('user-subscription.view.statistics');
    }

    /**
     * Determine whether the user can manage user subscription billing.
     */
    public function manageBilling(User $user, UserSubscription $userSubscription): bool
    {
        if ($user->can('user-subscription.manage.billing.any')) {
            return true;
        }

        if ($user->can('user-subscription.manage.billing.own')) {
            return $this->isOwner($user, $userSubscription);
        }

        return false;
    }

    /**
     * Determine whether the user can manage user subscription payments.
     */
    public function managePayments(User $user, UserSubscription $userSubscription): bool
    {
        if ($user->can('user-subscription.manage.payments.any')) {
            return true;
        }

        if ($user->can('user-subscription.manage.payments.own')) {
            return $this->isOwner($user, $userSubscription);
        }

        return false;
    }

    /**
     * Determine whether the user can manage user subscription invoices.
     */
    public function manageInvoices(User $user, UserSubscription $userSubscription): bool
    {
        if ($user->can('user-subscription.manage.invoices.any')) {
            return true;
        }

        if ($user->can('user-subscription.manage.invoices.own')) {
            return $this->isOwner($user, $userSubscription);
        }

        return false;
    }

    /**
     * Determine whether the user can manage user subscription refunds.
     */
    public function manageRefunds(User $user, UserSubscription $userSubscription): bool
    {
        if ($user->can('user-subscription.manage.refunds.any')) {
            return true;
        }

        if ($user->can('user-subscription.manage.refunds.own')) {
            return $this->isOwner($user, $userSubscription);
        }

        return false;
    }

    /**
     * Determine whether the user can manage user subscription disputes.
     */
    public function manageDisputes(User $user, UserSubscription $userSubscription): bool
    {
        if ($user->can('user-subscription.manage.disputes.any')) {
            return true;
        }

        if ($user->can('user-subscription.manage.disputes.own')) {
            return $this->isOwner($user, $userSubscription);
        }

        return false;
    }

    /**
     * Determine whether the user can manage user subscription notifications.
     */
    public function manageNotifications(User $user): bool
    {
        return $user->can('user-subscription.manage.notifications');
    }

    /**
     * Determine whether the user can manage user subscription webhooks.
     */
    public function manageWebhooks(User $user): bool
    {
        return $user->can('user-subscription.manage.webhooks');
    }

    /**
     * Determine whether the user can manage user subscription integrations.
     */
    public function manageIntegrations(User $user): bool
    {
        return $user->can('user-subscription.manage.integrations');
    }

    /**
     * Determine whether the user can manage user subscription reports.
     */
    public function manageReports(User $user): bool
    {
        return $user->can('user-subscription.manage.reports');
    }

    /**
     * Determine whether the user can manage user subscription logs.
     */
    public function manageLogs(User $user): bool
    {
        return $user->can('user-subscription.manage.logs');
    }

    /**
     * Determine whether the user can manage user subscription backups.
     */
    public function manageBackups(User $user): bool
    {
        return $user->can('user-subscription.manage.backups');
    }

    /**
     * Determine whether the user can manage user subscription security.
     */
    public function manageSecurity(User $user): bool
    {
        return $user->can('user-subscription.manage.security');
    }

    /**
     * Determine whether the user can manage user subscription compliance.
     */
    public function manageCompliance(User $user): bool
    {
        return $user->can('user-subscription.manage.compliance');
    }

    /**
     * Determine whether the user can manage user subscription churn.
     */
    public function manageChurn(User $user): bool
    {
        return $user->can('user-subscription.manage.churn');
    }

    /**
     * Determine whether the user can manage user subscription retention.
     */
    public function manageRetention(User $user): bool
    {
        return $user->can('user-subscription.manage.retention');
    }

    /**
     * Determine whether the user can manage user subscription upgrades.
     */
    public function manageUpgrades(User $user, UserSubscription $userSubscription): bool
    {
        if ($user->can('user-subscription.manage.upgrades.any')) {
            return true;
        }

        if ($user->can('user-subscription.manage.upgrades.own')) {
            return $this->isOwner($user, $userSubscription);
        }

        return false;
    }

    /**
     * Determine whether the user can manage user subscription downgrades.
     */
    public function manageDowngrades(User $user, UserSubscription $userSubscription): bool
    {
        if ($user->can('user-subscription.manage.downgrades.any')) {
            return true;
        }

        if ($user->can('user-subscription.manage.downgrades.own')) {
            return $this->isOwner($user, $userSubscription);
        }

        return false;
    }

    /**
     * Determine whether the user can manage user subscription trials.
     */
    public function manageTrials(User $user, UserSubscription $userSubscription): bool
    {
        if ($user->can('user-subscription.manage.trials.any')) {
            return true;
        }

        if ($user->can('user-subscription.manage.trials.own')) {
            return $this->isOwner($user, $userSubscription);
        }

        return false;
    }

    /**
     * Determine whether the user can manage user subscription grace periods.
     */
    public function manageGracePeriods(User $user, UserSubscription $userSubscription): bool
    {
        if ($user->can('user-subscription.manage.grace.periods.any')) {
            return true;
        }

        if ($user->can('user-subscription.manage.grace.periods.own')) {
            return $this->isOwner($user, $userSubscription);
        }

        return false;
    }

    /**
     * Determine whether the user can manage user subscription dunning.
     */
    public function manageDunning(User $user): bool
    {
        return $user->can('user-subscription.manage.dunning');
    }

    /**
     * Determine whether the user can manage user subscription collections.
     */
    public function manageCollections(User $user): bool
    {
        return $user->can('user-subscription.manage.collections');
    }

    /**
     * Determine whether the user can manage user subscription fraud.
     */
    public function manageFraud(User $user): bool
    {
        return $user->can('user-subscription.manage.fraud');
    }

    /**
     * Determine whether the user can manage user subscription risk.
     */
    public function manageRisk(User $user): bool
    {
        return $user->can('user-subscription.manage.risk');
    }

    /**
     * Check if the user is the owner of the user subscription.
     */
    protected function isOwner(User $user, UserSubscription $userSubscription): bool
    {
        return $userSubscription->user_id === $user->id;
    }
}
