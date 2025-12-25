<?php

namespace Fereydooni\Shopping\app\Permissions;

class ProviderNotePermissions
{
    // Basic CRUD permissions
    public const VIEW = 'provider-note.view';

    public const CREATE = 'provider-note.create';

    public const EDIT = 'provider-note.edit';

    public const DELETE = 'provider-note.delete';

    public const ARCHIVE = 'provider-note.archive';

    // Ownership-based permissions
    public const VIEW_OWN = 'provider-note.view-own';

    public const CREATE_OWN = 'provider-note.create-own';

    public const EDIT_OWN = 'provider-note.edit-own';

    // Team and department permissions
    public const VIEW_TEAM = 'provider-note.view-team';

    public const VIEW_DEPARTMENT = 'provider-note.view-department';

    public const VIEW_ALL = 'provider-note.view-all';

    // Administrative permissions
    public const MANAGE_ALL = 'provider-note.manage-all';

    public const EXPORT = 'provider-note.export';

    public const IMPORT = 'provider-note.import';

    public const STATISTICS = 'provider-note.statistics';

    /**
     * Get all provider note permissions
     */
    public static function getAllPermissions(): array
    {
        return [
            self::VIEW,
            self::CREATE,
            self::EDIT,
            self::DELETE,
            self::ARCHIVE,
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
        ];
    }

    /**
     * Get basic CRUD permissions
     */
    public static function getBasicPermissions(): array
    {
        return [
            self::VIEW,
            self::CREATE,
            self::EDIT,
            self::DELETE,
            self::ARCHIVE,
        ];
    }

    /**
     * Get ownership-based permissions
     */
    public static function getOwnershipPermissions(): array
    {
        return [
            self::VIEW_OWN,
            self::CREATE_OWN,
            self::EDIT_OWN,
        ];
    }

    /**
     * Get team and department permissions
     */
    public static function getTeamPermissions(): array
    {
        return [
            self::VIEW_TEAM,
            self::VIEW_DEPARTMENT,
            self::VIEW_ALL,
        ];
    }

    /**
     * Get administrative permissions
     */
    public static function getAdminPermissions(): array
    {
        return [
            self::MANAGE_ALL,
            self::EXPORT,
            self::IMPORT,
            self::STATISTICS,
        ];
    }

    /**
     * Get permissions for regular users
     */
    public static function getRegularUserPermissions(): array
    {
        return [
            self::VIEW_OWN,
            self::CREATE_OWN,
            self::EDIT_OWN,
        ];
    }

    /**
     * Get permissions for team leaders
     */
    public static function getTeamLeaderPermissions(): array
    {
        return array_merge(
            self::getRegularUserPermissions(),
            [
                self::VIEW_TEAM,
                self::VIEW_DEPARTMENT,
            ]
        );
    }

    /**
     * Get permissions for managers
     */
    public static function getManagerPermissions(): array
    {
        return array_merge(
            self::getTeamLeaderPermissions(),
            [
                self::VIEW_ALL,
                self::EXPORT,
                self::STATISTICS,
            ]
        );
    }

    /**
     * Get permissions for administrators
     */
    public static function getAdminUserPermissions(): array
    {
        return self::getAllPermissions();
    }

    /**
     * Check if a permission is a basic CRUD permission
     */
    public static function isBasicPermission(string $permission): bool
    {
        return in_array($permission, self::getBasicPermissions());
    }

    /**
     * Check if a permission is an ownership-based permission
     */
    public static function isOwnershipPermission(string $permission): bool
    {
        return in_array($permission, self::getOwnershipPermissions());
    }

    /**
     * Check if a permission is a team permission
     */
    public static function isTeamPermission(string $permission): bool
    {
        return in_array($permission, self::getTeamPermissions());
    }

    /**
     * Check if a permission is an administrative permission
     */
    public static function isAdminPermission(string $permission): bool
    {
        return in_array($permission, self::getAdminPermissions());
    }

    /**
     * Get permission descriptions
     */
    public static function getPermissionDescriptions(): array
    {
        return [
            self::VIEW => 'View provider notes',
            self::CREATE => 'Create new provider notes',
            self::EDIT => 'Edit existing provider notes',
            self::DELETE => 'Delete provider notes',
            self::ARCHIVE => 'Archive provider notes',
            self::VIEW_OWN => 'View own provider notes',
            self::CREATE_OWN => 'Create own provider notes',
            self::EDIT_OWN => 'Edit own provider notes',
            self::VIEW_TEAM => 'View team provider notes',
            self::VIEW_DEPARTMENT => 'View department provider notes',
            self::VIEW_ALL => 'View all provider notes',
            self::MANAGE_ALL => 'Manage all provider notes',
            self::EXPORT => 'Export provider notes',
            self::IMPORT => 'Import provider notes',
            self::STATISTICS => 'View provider note statistics',
        ];
    }

    /**
     * Get permission groups
     */
    public static function getPermissionGroups(): array
    {
        return [
            'Basic Operations' => self::getBasicPermissions(),
            'Ownership' => self::getOwnershipPermissions(),
            'Team Access' => self::getTeamPermissions(),
            'Administration' => self::getAdminPermissions(),
        ];
    }
}
