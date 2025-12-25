<?php

namespace Fereydooni\Shopping\App\Permissions;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;

class ProviderInvoicePermissions
{
    /**
     * Register all provider invoice permissions
     */
    public static function register(): void
    {
        // Basic CRUD permissions
        Gate::define('provider-invoice.view', function ($user) {
            return $user->hasPermissionTo('provider-invoice.view');
        });

        Gate::define('provider-invoice.create', function ($user) {
            return $user->hasPermissionTo('provider-invoice.create');
        });

        Gate::define('provider-invoice.edit', function ($user) {
            return $user->hasPermissionTo('provider-invoice.edit');
        });

        Gate::define('provider-invoice.delete', function ($user) {
            return $user->hasPermissionTo('provider-invoice.delete');
        });

        // Workflow permissions
        Gate::define('provider-invoice.send', function ($user) {
            return $user->hasPermissionTo('provider-invoice.send');
        });

        Gate::define('provider-invoice.mark-paid', function ($user) {
            return $user->hasPermissionTo('provider-invoice.mark-paid');
        });

        Gate::define('provider-invoice.cancel', function ($user) {
            return $user->hasPermissionTo('provider-invoice.cancel');
        });

        // Ownership permissions
        Gate::define('provider-invoice.view-own', function ($user, $invoice = null) {
            if (! $user->hasPermissionTo('provider-invoice.view-own')) {
                return false;
            }

            if ($invoice) {
                return $user->id === $invoice->created_by;
            }

            return true;
        });

        Gate::define('provider-invoice.create-own', function ($user) {
            return $user->hasPermissionTo('provider-invoice.create-own');
        });

        Gate::define('provider-invoice.edit-own', function ($user, $invoice = null) {
            if (! $user->hasPermissionTo('provider-invoice.edit-own')) {
                return false;
            }

            if ($invoice) {
                return $user->id === $invoice->created_by;
            }

            return true;
        });

        // Team and department permissions
        Gate::define('provider-invoice.view-team', function ($user, $invoice = null) {
            if (! $user->hasPermissionTo('provider-invoice.view-team')) {
                return false;
            }

            if ($invoice && $user->team_id) {
                return $user->team_id === $invoice->team_id;
            }

            return true;
        });

        Gate::define('provider-invoice.view-department', function ($user, $invoice = null) {
            if (! $user->hasPermissionTo('provider-invoice.view-department')) {
                return false;
            }

            if ($invoice && $user->department_id) {
                return $user->department_id === $invoice->department_id;
            }

            return true;
        });

        // Global permissions
        Gate::define('provider-invoice.view-all', function ($user) {
            return $user->hasPermissionTo('provider-invoice.view-all');
        });

        Gate::define('provider-invoice.manage-all', function ($user) {
            return $user->hasPermissionTo('provider-invoice.manage-all');
        });

        // Data management permissions
        Gate::define('provider-invoice.export', function ($user) {
            return $user->hasPermissionTo('provider-invoice.export');
        });

        Gate::define('provider-invoice.import', function ($user) {
            return $user->hasPermissionTo('provider-invoice.import');
        });

        // Analytics permissions
        Gate::define('provider-invoice.statistics', function ($user) {
            return $user->hasPermissionTo('provider-invoice.statistics');
        });

        // Approval permissions
        Gate::define('provider-invoice.approval', function ($user) {
            return $user->hasPermissionTo('provider-invoice.approval');
        });

        // Composite permissions
        Gate::define('provider-invoice.full-access', function ($user) {
            return $user->hasAnyPermission([
                'provider-invoice.manage-all',
                'provider-invoice.view-all',
                'provider-invoice.create',
                'provider-invoice.edit',
                'provider-invoice.delete',
                'provider-invoice.send',
                'provider-invoice.mark-paid',
                'provider-invoice.cancel',
                'provider-invoice.export',
                'provider-invoice.import',
                'provider-invoice.statistics',
                'provider-invoice.approval',
            ]);
        });

        Gate::define('provider-invoice.read-only', function ($user) {
            return $user->hasAnyPermission([
                'provider-invoice.view',
                'provider-invoice.view-own',
                'provider-invoice.view-team',
                'provider-invoice.view-department',
                'provider-invoice.view-all',
            ]);
        });

        Gate::define('provider-invoice.workflow', function ($user) {
            return $user->hasAnyPermission([
                'provider-invoice.send',
                'provider-invoice.mark-paid',
                'provider-invoice.cancel',
            ]);
        });

        Log::info('Provider invoice permissions registered successfully');
    }

    /**
     * Get all available permissions
     */
    public static function getAllPermissions(): array
    {
        return [
            // Basic CRUD
            'provider-invoice.view',
            'provider-invoice.create',
            'provider-invoice.edit',
            'provider-invoice.delete',

            // Workflow
            'provider-invoice.send',
            'provider-invoice.mark-paid',
            'provider-invoice.cancel',

            // Ownership
            'provider-invoice.view-own',
            'provider-invoice.create-own',
            'provider-invoice.edit-own',

            // Team and department
            'provider-invoice.view-team',
            'provider-invoice.view-department',

            // Global
            'provider-invoice.view-all',
            'provider-invoice.manage-all',

            // Data management
            'provider-invoice.export',
            'provider-invoice.import',

            // Analytics
            'provider-invoice.statistics',

            // Approval
            'provider-invoice.approval',

            // Composite
            'provider-invoice.full-access',
            'provider-invoice.read-only',
            'provider-invoice.workflow',
        ];
    }

    /**
     * Get permissions by category
     */
    public static function getPermissionsByCategory(): array
    {
        return [
            'Basic Operations' => [
                'provider-invoice.view',
                'provider-invoice.create',
                'provider-invoice.edit',
                'provider-invoice.delete',
            ],
            'Workflow Management' => [
                'provider-invoice.send',
                'provider-invoice.mark-paid',
                'provider-invoice.cancel',
            ],
            'Access Control' => [
                'provider-invoice.view-own',
                'provider-invoice.create-own',
                'provider-invoice.edit-own',
                'provider-invoice.view-team',
                'provider-invoice.view-department',
                'provider-invoice.view-all',
                'provider-invoice.manage-all',
            ],
            'Data Management' => [
                'provider-invoice.export',
                'provider-invoice.import',
            ],
            'Analytics' => [
                'provider-invoice.statistics',
            ],
            'Approval' => [
                'provider-invoice.approval',
            ],
            'Composite Permissions' => [
                'provider-invoice.full-access',
                'provider-invoice.read-only',
                'provider-invoice.workflow',
            ],
        ];
    }

    /**
     * Check if user has any provider invoice permission
     */
    public static function hasAnyProviderInvoicePermission($user): bool
    {
        return $user->hasAnyPermission(self::getAllPermissions());
    }

    /**
     * Check if user has provider invoice access
     */
    public static function hasProviderInvoiceAccess($user, $invoice = null): bool
    {
        // Check global permissions first
        if ($user->can('provider-invoice.view-all') || $user->can('provider-invoice.manage-all')) {
            return true;
        }

        // Check team permissions
        if ($user->can('provider-invoice.view-team')) {
            if ($invoice && $user->team_id === $invoice->team_id) {
                return true;
            }
        }

        // Check department permissions
        if ($user->can('provider-invoice.view-department')) {
            if ($invoice && $user->department_id === $invoice->department_id) {
                return true;
            }
        }

        // Check own permissions
        if ($user->can('provider-invoice.view-own')) {
            if ($invoice && $user->id === $invoice->created_by) {
                return true;
            }
        }

        // Check basic view permission
        return $user->can('provider-invoice.view');
    }
}
