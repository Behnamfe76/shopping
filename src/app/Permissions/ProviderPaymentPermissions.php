<?php

namespace Fereydooni\Shopping\App\Permissions;

class ProviderPaymentPermissions
{
    /**
     * View provider payments.
     */
    public const VIEW = 'provider-payment.view';

    /**
     * Create provider payments.
     */
    public const CREATE = 'provider-payment.create';

    /**
     * Edit provider payments.
     */
    public const EDIT = 'provider-payment.edit';

    /**
     * Delete provider payments.
     */
    public const DELETE = 'provider-payment.delete';

    /**
     * Process provider payments.
     */
    public const PROCESS = 'provider-payment.process';

    /**
     * Complete provider payments.
     */
    public const COMPLETE = 'provider-payment.complete';

    /**
     * Reconcile provider payments.
     */
    public const RECONCILE = 'provider-payment.reconcile';

    /**
     * View own provider payments.
     */
    public const VIEW_OWN = 'provider-payment.view-own';

    /**
     * Create own provider payments.
     */
    public const CREATE_OWN = 'provider-payment.create-own';

    /**
     * Edit own provider payments.
     */
    public const EDIT_OWN = 'provider-payment.edit-own';

    /**
     * View team provider payments.
     */
    public const VIEW_TEAM = 'provider-payment.view-team';

    /**
     * View department provider payments.
     */
    public const VIEW_DEPARTMENT = 'provider-payment.view-department';

    /**
     * View all provider payments.
     */
    public const VIEW_ALL = 'provider-payment.view-all';

    /**
     * Manage all provider payments.
     */
    public const MANAGE_ALL = 'provider-payment.manage-all';

    /**
     * Export provider payment data.
     */
    public const EXPORT = 'provider-payment.export';

    /**
     * Import provider payment data.
     */
    public const IMPORT = 'provider-payment.import';

    /**
     * View provider payment statistics.
     */
    public const STATISTICS = 'provider-payment.statistics';

    /**
     * Approve provider payments.
     */
    public const APPROVAL = 'provider-payment.approval';

    /**
     * Get all provider payment permissions.
     */
    public static function getAllPermissions(): array
    {
        return [
            self::VIEW,
            self::CREATE,
            self::EDIT,
            self::DELETE,
            self::PROCESS,
            self::COMPLETE,
            self::RECONCILE,
            self::VIEW_OWN,
            self::CREATE_OWN,
            self::EDIT_OWN,
            self::VIEW_TEAM,
            self::VIEW_DEPARTMENT,
            self::VIEW_ALL,
            self::MANAGE_ALL,
            self::EXPORT,
            self::IMPORT,
            self::STATISTICS,
            self::APPROVAL,
        ];
    }

    /**
     * Get basic permissions for regular users.
     */
    public static function getBasicPermissions(): array
    {
        return [
            self::VIEW_OWN,
            self::CREATE_OWN,
            self::EDIT_OWN,
        ];
    }

    /**
     * Get manager permissions.
     */
    public static function getManagerPermissions(): array
    {
        return [
            self::VIEW,
            self::VIEW_TEAM,
            self::VIEW_DEPARTMENT,
            self::PROCESS,
            self::COMPLETE,
            self::RECONCILE,
            self::STATISTICS,
        ];
    }

    /**
     * Get admin permissions.
     */
    public static function getAdminPermissions(): array
    {
        return [
            self::VIEW_ALL,
            self::MANAGE_ALL,
            self::EXPORT,
            self::IMPORT,
            self::APPROVAL,
        ];
    }

    /**
     * Get permissions by role.
     */
    public static function getPermissionsByRole(string $role): array
    {
        return match ($role) {
            'user' => self::getBasicPermissions(),
            'manager' => array_merge(self::getBasicPermissions(), self::getManagerPermissions()),
            'admin' => self::getAllPermissions(),
            default => [],
        };
    }

    /**
     * Check if a permission is a basic permission.
     */
    public static function isBasicPermission(string $permission): bool
    {
        return in_array($permission, self::getBasicPermissions());
    }

    /**
     * Check if a permission is a manager permission.
     */
    public static function isManagerPermission(string $permission): bool
    {
        return in_array($permission, self::getManagerPermissions());
    }

    /**
     * Check if a permission is an admin permission.
     */
    public static function isAdminPermission(string $permission): bool
    {
        return in_array($permission, self::getAdminPermissions());
    }

    /**
     * Get permission descriptions.
     */
    public static function getPermissionDescriptions(): array
    {
        return [
            self::VIEW => 'View provider payments',
            self::CREATE => 'Create new provider payments',
            self::EDIT => 'Edit existing provider payments',
            self::DELETE => 'Delete provider payments',
            self::PROCESS => 'Process provider payments',
            self::COMPLETE => 'Complete provider payments',
            self::RECONCILE => 'Reconcile provider payments',
            self::VIEW_OWN => 'View own provider payments',
            self::CREATE_OWN => 'Create own provider payments',
            self::EDIT_OWN => 'Edit own provider payments',
            self::VIEW_TEAM => 'View team provider payments',
            self::VIEW_DEPARTMENT => 'View department provider payments',
            self::VIEW_ALL => 'View all provider payments',
            self::MANAGE_ALL => 'Manage all provider payments',
            self::EXPORT => 'Export provider payment data',
            self::IMPORT => 'Import provider payment data',
            self::STATISTICS => 'View provider payment statistics',
            self::APPROVAL => 'Approve provider payments',
        ];
    }

    /**
     * Get permission description.
     */
    public static function getPermissionDescription(string $permission): string
    {
        $descriptions = self::getPermissionDescriptions();
        return $descriptions[$permission] ?? 'Unknown permission';
    }

    /**
     * Get permissions grouped by category.
     */
    public static function getPermissionsByCategory(): array
    {
        return [
            'Basic Operations' => [
                self::VIEW_OWN => 'View own provider payments',
                self::CREATE_OWN => 'Create own provider payments',
                self::EDIT_OWN => 'Edit own provider payments',
            ],
            'Payment Management' => [
                self::VIEW => 'View provider payments',
                self::CREATE => 'Create new provider payments',
                self::EDIT => 'Edit existing provider payments',
                self::DELETE => 'Delete provider payments',
            ],
            'Payment Processing' => [
                self::PROCESS => 'Process provider payments',
                self::COMPLETE => 'Complete provider payments',
                self::RECONCILE => 'Reconcile provider payments',
            ],
            'Team Management' => [
                self::VIEW_TEAM => 'View team provider payments',
                self::VIEW_DEPARTMENT => 'View department provider payments',
            ],
            'Administration' => [
                self::VIEW_ALL => 'View all provider payments',
                self::MANAGE_ALL => 'Manage all provider payments',
                self::APPROVAL => 'Approve provider payments',
            ],
            'Data Operations' => [
                self::EXPORT => 'Export provider payment data',
                self::IMPORT => 'Import provider payment data',
                self::STATISTICS => 'View provider payment statistics',
            ],
        ];
    }
}
