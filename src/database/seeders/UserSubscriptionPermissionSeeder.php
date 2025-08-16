<?php

namespace Fereydooni\Shopping\database\seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class UserSubscriptionPermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Create permissions
        $permissions = [
            // View permissions
            'user-subscription.view',
            'user-subscription.view.any',
            'user-subscription.view.own',

            // Create permissions
            'user-subscription.create',
            'user-subscription.create.any',
            'user-subscription.create.own',

            // Update permissions
            'user-subscription.update',
            'user-subscription.update.any',
            'user-subscription.update.own',

            // Delete permissions
            'user-subscription.delete',
            'user-subscription.delete.any',
            'user-subscription.delete.own',

            // Lifecycle management permissions
            'user-subscription.activate',
            'user-subscription.activate.any',
            'user-subscription.activate.own',

            'user-subscription.cancel',
            'user-subscription.cancel.any',
            'user-subscription.cancel.own',

            'user-subscription.renew',
            'user-subscription.renew.any',
            'user-subscription.renew.own',

            'user-subscription.pause',
            'user-subscription.pause.any',
            'user-subscription.pause.own',

            'user-subscription.resume',
            'user-subscription.resume.any',
            'user-subscription.resume.own',

            // Search and export permissions
            'user-subscription.search',
            'user-subscription.search.any',
            'user-subscription.search.own',

            'user-subscription.export',
            'user-subscription.import',

            // Validation and calculation permissions
            'user-subscription.validate',
            'user-subscription.calculate.revenue',

            // Analytics and statistics permissions
            'user-subscription.view.statistics',
            'user-subscription.view.analytics',

            // Lifecycle management
            'user-subscription.manage.lifecycle',

            // Billing management
            'user-subscription.manage.billing.any',
            'user-subscription.manage.billing.own',

            // Payment management
            'user-subscription.manage.payments.any',
            'user-subscription.manage.payments.own',

            // Invoice management
            'user-subscription.manage.invoices.any',
            'user-subscription.manage.invoices.own',

            // Refund management
            'user-subscription.manage.refunds.any',
            'user-subscription.manage.refunds.own',

            // Dispute management
            'user-subscription.manage.disputes.any',
            'user-subscription.manage.disputes.own',

            // Notification management
            'user-subscription.manage.notifications',

            // Webhook management
            'user-subscription.manage.webhooks',

            // Integration management
            'user-subscription.manage.integrations',

            // Report management
            'user-subscription.manage.reports',

            // Log management
            'user-subscription.manage.logs',

            // Backup management
            'user-subscription.manage.backups',

            // Security management
            'user-subscription.manage.security',

            // Compliance management
            'user-subscription.manage.compliance',

            // Churn management
            'user-subscription.manage.churn',

            // Retention management
            'user-subscription.manage.retention',

            // Upgrade management
            'user-subscription.manage.upgrades.any',
            'user-subscription.manage.upgrades.own',

            // Downgrade management
            'user-subscription.manage.downgrades.any',
            'user-subscription.manage.downgrades.own',

            // Trial management
            'user-subscription.manage.trials.any',
            'user-subscription.manage.trials.own',

            // Grace period management
            'user-subscription.manage.grace.periods.any',
            'user-subscription.manage.grace.periods.own',

            // Dunning management
            'user-subscription.manage.dunning',

            // Collection management
            'user-subscription.manage.collections',

            // Fraud management
            'user-subscription.manage.fraud',

            // Risk management
            'user-subscription.manage.risk',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Create roles
        $roles = [
            'user-subscription-manager' => [
                // Full user subscription management for all users
                'user-subscription.view.any',
                'user-subscription.create.any',
                'user-subscription.update.any',
                'user-subscription.delete.any',
                'user-subscription.activate.any',
                'user-subscription.cancel.any',
                'user-subscription.renew.any',
                'user-subscription.pause.any',
                'user-subscription.resume.any',
                'user-subscription.search.any',
                'user-subscription.export',
                'user-subscription.import',
                'user-subscription.validate',
                'user-subscription.calculate.revenue',
                'user-subscription.view.statistics',
                'user-subscription.view.analytics',
                'user-subscription.manage.lifecycle',
                'user-subscription.manage.billing.any',
                'user-subscription.manage.payments.any',
                'user-subscription.manage.invoices.any',
                'user-subscription.manage.refunds.any',
                'user-subscription.manage.disputes.any',
                'user-subscription.manage.notifications',
                'user-subscription.manage.webhooks',
                'user-subscription.manage.integrations',
                'user-subscription.manage.reports',
                'user-subscription.manage.logs',
                'user-subscription.manage.backups',
                'user-subscription.manage.security',
                'user-subscription.manage.compliance',
                'user-subscription.manage.churn',
                'user-subscription.manage.retention',
                'user-subscription.manage.upgrades.any',
                'user-subscription.manage.downgrades.any',
                'user-subscription.manage.trials.any',
                'user-subscription.manage.grace.periods.any',
                'user-subscription.manage.dunning',
                'user-subscription.manage.collections',
                'user-subscription.manage.fraud',
                'user-subscription.manage.risk',
            ],

            'user-subscription-processor' => [
                // Can process and manage user subscriptions
                'user-subscription.view.any',
                'user-subscription.create.any',
                'user-subscription.update.any',
                'user-subscription.activate.any',
                'user-subscription.cancel.any',
                'user-subscription.renew.any',
                'user-subscription.pause.any',
                'user-subscription.resume.any',
                'user-subscription.search.any',
                'user-subscription.validate',
                'user-subscription.manage.lifecycle',
                'user-subscription.manage.billing.any',
                'user-subscription.manage.payments.any',
                'user-subscription.manage.invoices.any',
                'user-subscription.manage.notifications',
                'user-subscription.manage.upgrades.any',
                'user-subscription.manage.downgrades.any',
                'user-subscription.manage.trials.any',
                'user-subscription.manage.grace.periods.any',
            ],

            'user-subscription-viewer' => [
                // Read-only access to user subscriptions
                'user-subscription.view.any',
                'user-subscription.search.any',
                'user-subscription.view.statistics',
                'user-subscription.view.analytics',
            ],

            'subscription-analyst' => [
                // Can view analytics and statistics
                'user-subscription.view.any',
                'user-subscription.search.any',
                'user-subscription.view.statistics',
                'user-subscription.view.analytics',
                'user-subscription.calculate.revenue',
                'user-subscription.manage.reports',
                'user-subscription.manage.churn',
                'user-subscription.manage.retention',
            ],

            'customer' => [
                // Can manage own subscriptions only
                'user-subscription.view.own',
                'user-subscription.create.own',
                'user-subscription.update.own',
                'user-subscription.cancel.own',
                'user-subscription.pause.own',
                'user-subscription.resume.own',
                'user-subscription.search.own',
                'user-subscription.manage.billing.own',
                'user-subscription.manage.payments.own',
                'user-subscription.manage.invoices.own',
                'user-subscription.manage.upgrades.own',
                'user-subscription.manage.downgrades.own',
            ],
        ];

        foreach ($roles as $roleName => $rolePermissions) {
            $role = Role::firstOrCreate(['name' => $roleName]);
            $role->syncPermissions($rolePermissions);
        }
    }
}
