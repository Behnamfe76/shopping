<?php

namespace Fereydooni\Shopping\app\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User;
use Fereydooni\Shopping\app\Models\Subscription;

class SubscriptionPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any subscriptions.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('subscription.view.any');
    }

    /**
     * Determine whether the user can view the subscription.
     */
    public function view(User $user, Subscription $subscription): bool
    {
        if ($user->can('subscription.view.any')) {
            return true;
        }

        if ($user->can('subscription.view.own')) {
            return $this->isOwner($user, $subscription);
        }

        return false;
    }

    /**
     * Determine whether the user can create subscriptions.
     */
    public function create(User $user): bool
    {
        return $user->can('subscription.create.any') || $user->can('subscription.create.own');
    }

    /**
     * Determine whether the user can update the subscription.
     */
    public function update(User $user, Subscription $subscription): bool
    {
        if ($user->can('subscription.update.any')) {
            return true;
        }

        if ($user->can('subscription.update.own')) {
            return $this->isOwner($user, $subscription);
        }

        return false;
    }

    /**
     * Determine whether the user can delete the subscription.
     */
    public function delete(User $user, Subscription $subscription): bool
    {
        if ($user->can('subscription.delete.any')) {
            return true;
        }

        if ($user->can('subscription.delete.own')) {
            return $this->isOwner($user, $subscription);
        }

        return false;
    }

    /**
     * Determine whether the user can manage subscription pricing.
     */
    public function managePricing(User $user, Subscription $subscription): bool
    {
        if ($user->can('subscription.manage.pricing.any')) {
            return true;
        }

        if ($user->can('subscription.manage.pricing.own')) {
            return $this->isOwner($user, $subscription);
        }

        return false;
    }

    /**
     * Determine whether the user can manage subscription billing cycles.
     */
    public function manageBillingCycle(User $user, Subscription $subscription): bool
    {
        if ($user->can('subscription.manage.billing.cycle.any')) {
            return true;
        }

        if ($user->can('subscription.manage.billing.cycle.own')) {
            return $this->isOwner($user, $subscription);
        }

        return false;
    }

    /**
     * Determine whether the user can manage trial periods.
     */
    public function manageTrialPeriod(User $user, Subscription $subscription): bool
    {
        if ($user->can('subscription.manage.trial.period.any')) {
            return true;
        }

        if ($user->can('subscription.manage.trial.period.own')) {
            return $this->isOwner($user, $subscription);
        }

        return false;
    }

    /**
     * Determine whether the user can view subscription analytics.
     */
    public function viewAnalytics(User $user): bool
    {
        return $user->can('subscription.view.analytics');
    }

    /**
     * Determine whether the user can view subscription revenue.
     */
    public function viewRevenue(User $user): bool
    {
        return $user->can('subscription.view.revenue');
    }

    /**
     * Determine whether the user can export subscription data.
     */
    public function export(User $user): bool
    {
        return $user->can('subscription.export');
    }

    /**
     * Determine whether the user can import subscription data.
     */
    public function import(User $user): bool
    {
        return $user->can('subscription.import');
    }

    /**
     * Determine whether the user can manage subscription settings.
     */
    public function manageSettings(User $user): bool
    {
        return $user->can('subscription.manage.settings');
    }

    /**
     * Determine whether the user can view subscription statistics.
     */
    public function viewStatistics(User $user): bool
    {
        return $user->can('subscription.view.statistics');
    }

    /**
     * Determine whether the user can manage popular subscriptions.
     */
    public function managePopular(User $user): bool
    {
        return $user->can('subscription.manage.popular');
    }

    /**
     * Determine whether the user can manage subscription categories.
     */
    public function manageCategories(User $user): bool
    {
        return $user->can('subscription.manage.categories');
    }

    /**
     * Determine whether the user can manage subscription tags.
     */
    public function manageTags(User $user): bool
    {
        return $user->can('subscription.manage.tags');
    }

    /**
     * Determine whether the user can manage subscription metadata.
     */
    public function manageMetadata(User $user): bool
    {
        return $user->can('subscription.manage.metadata');
    }

    /**
     * Determine whether the user can manage subscription media.
     */
    public function manageMedia(User $user, Subscription $subscription): bool
    {
        if ($user->can('subscription.manage.media.any')) {
            return true;
        }

        if ($user->can('subscription.manage.media.own')) {
            return $this->isOwner($user, $subscription);
        }

        return false;
    }

    /**
     * Determine whether the user can manage subscription SEO.
     */
    public function manageSeo(User $user, Subscription $subscription): bool
    {
        if ($user->can('subscription.manage.seo.any')) {
            return true;
        }

        if ($user->can('subscription.manage.seo.own')) {
            return $this->isOwner($user, $subscription);
        }

        return false;
    }

    /**
     * Determine whether the user can manage subscription reviews.
     */
    public function manageReviews(User $user, Subscription $subscription): bool
    {
        if ($user->can('subscription.manage.reviews.any')) {
            return true;
        }

        if ($user->can('subscription.manage.reviews.own')) {
            return $this->isOwner($user, $subscription);
        }

        return false;
    }

    /**
     * Determine whether the user can manage subscription variants.
     */
    public function manageVariants(User $user, Subscription $subscription): bool
    {
        if ($user->can('subscription.manage.variants.any')) {
            return true;
        }

        if ($user->can('subscription.manage.variants.own')) {
            return $this->isOwner($user, $subscription);
        }

        return false;
    }

    /**
     * Determine whether the user can manage subscription discounts.
     */
    public function manageDiscounts(User $user, Subscription $subscription): bool
    {
        if ($user->can('subscription.manage.discounts.any')) {
            return true;
        }

        if ($user->can('subscription.manage.discounts.own')) {
            return $this->isOwner($user, $subscription);
        }

        return false;
    }

    /**
     * Determine whether the user can manage subscription inventory.
     */
    public function manageInventory(User $user, Subscription $subscription): bool
    {
        if ($user->can('subscription.manage.inventory.any')) {
            return true;
        }

        if ($user->can('subscription.manage.inventory.own')) {
            return $this->isOwner($user, $subscription);
        }

        return false;
    }

    /**
     * Determine whether the user can manage subscription shipping.
     */
    public function manageShipping(User $user, Subscription $subscription): bool
    {
        if ($user->can('subscription.manage.shipping.any')) {
            return true;
        }

        if ($user->can('subscription.manage.shipping.own')) {
            return $this->isOwner($user, $subscription);
        }

        return false;
    }

    /**
     * Determine whether the user can manage subscription taxes.
     */
    public function manageTaxes(User $user, Subscription $subscription): bool
    {
        if ($user->can('subscription.manage.taxes.any')) {
            return true;
        }

        if ($user->can('subscription.manage.taxes.own')) {
            return $this->isOwner($user, $subscription);
        }

        return false;
    }

    /**
     * Determine whether the user can manage subscription notifications.
     */
    public function manageNotifications(User $user): bool
    {
        return $user->can('subscription.manage.notifications');
    }

    /**
     * Determine whether the user can manage subscription webhooks.
     */
    public function manageWebhooks(User $user): bool
    {
        return $user->can('subscription.manage.webhooks');
    }

    /**
     * Determine whether the user can manage subscription integrations.
     */
    public function manageIntegrations(User $user): bool
    {
        return $user->can('subscription.manage.integrations');
    }

    /**
     * Determine whether the user can manage subscription reports.
     */
    public function manageReports(User $user): bool
    {
        return $user->can('subscription.manage.reports');
    }

    /**
     * Determine whether the user can manage subscription logs.
     */
    public function manageLogs(User $user): bool
    {
        return $user->can('subscription.manage.logs');
    }

    /**
     * Determine whether the user can manage subscription backups.
     */
    public function manageBackups(User $user): bool
    {
        return $user->can('subscription.manage.backups');
    }

    /**
     * Determine whether the user can manage subscription security.
     */
    public function manageSecurity(User $user): bool
    {
        return $user->can('subscription.manage.security');
    }

    /**
     * Determine whether the user can manage subscription compliance.
     */
    public function manageCompliance(User $user): bool
    {
        return $user->can('subscription.manage.compliance');
    }

    /**
     * Check if the user is the owner of the subscription.
     */
    protected function isOwner(User $user, Subscription $subscription): bool
    {
        // For subscriptions, ownership is typically determined by the product owner
        // You may need to adjust this logic based on your specific requirements
        return $subscription->product && $subscription->product->user_id === $user->id;
    }
}
