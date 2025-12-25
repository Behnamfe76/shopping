<?php

namespace Fereydooni\Shopping\app\Permissions;

use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class EmployeeEmergencyContactPermissions
{
    /**
     * All permissions for employee emergency contacts.
     */
    public const PERMISSIONS = [
        // Basic CRUD permissions
        'employee-emergency-contact.view' => 'View emergency contacts',
        'employee-emergency-contact.create' => 'Create emergency contacts',
        'employee-emergency-contact.edit' => 'Edit emergency contacts',
        'employee-emergency-contact.delete' => 'Delete emergency contacts',

        // Special permissions
        'employee-emergency-contact.set-primary' => 'Set emergency contact as primary',
        'employee-emergency-contact.activate' => 'Activate emergency contacts',
        'employee-emergency-contact.deactivate' => 'Deactivate emergency contacts',

        // Scope-based permissions
        'employee-emergency-contact.view-own' => 'View own emergency contacts',
        'employee-emergency-contact.create-own' => 'Create own emergency contacts',
        'employee-emergency-contact.edit-own' => 'Edit own emergency contacts',
        'employee-emergency-contact.delete-own' => 'Delete own emergency contacts',
        'employee-emergency-contact.set-primary-own' => 'Set own emergency contact as primary',

        'employee-emergency-contact.view-team' => 'View team emergency contacts',
        'employee-emergency-contact.view-department' => 'View department emergency contacts',
        'employee-emergency-contact.view-all' => 'View all emergency contacts',
        'employee-emergency-contact.manage-all' => 'Manage all emergency contacts',

        // Advanced permissions
        'employee-emergency-contact.export' => 'Export emergency contact data',
        'employee-emergency-contact.import' => 'Import emergency contact data',
        'employee-emergency-contact.statistics' => 'View emergency contact statistics',
        'employee-emergency-contact.validation' => 'Validate emergency contact information',
        'employee-emergency-contact.bulk-operations' => 'Perform bulk operations on emergency contacts',
        'employee-emergency-contact.audit' => 'View emergency contact audit trail',
    ];

    /**
     * Permission groups for better organization.
     */
    public const PERMISSION_GROUPS = [
        'Basic Operations' => [
            'employee-emergency-contact.view',
            'employee-emergency-contact.create',
            'employee-emergency-contact.edit',
            'employee-emergency-contact.delete',
        ],
        'Status Management' => [
            'employee-emergency-contact.set-primary',
            'employee-emergency-contact.activate',
            'employee-emergency-contact.deactivate',
        ],
        'Own Contacts' => [
            'employee-emergency-contact.view-own',
            'employee-emergency-contact.create-own',
            'employee-emergency-contact.edit-own',
            'employee-emergency-contact.delete-own',
            'employee-emergency-contact.set-primary-own',
        ],
        'Team & Department' => [
            'employee-emergency-contact.view-team',
            'employee-emergency-contact.view-department',
        ],
        'Administrative' => [
            'employee-emergency-contact.view-all',
            'employee-emergency-contact.manage-all',
            'employee-emergency-contact.export',
            'employee-emergency-contact.import',
            'employee-emergency-contact.statistics',
            'employee-emergency-contact.validation',
            'employee-emergency-contact.bulk-operations',
            'employee-emergency-contact.audit',
        ],
    ];

    /**
     * Default role permissions mapping.
     */
    public const ROLE_PERMISSIONS = [
        'employee' => [
            'employee-emergency-contact.view-own',
            'employee-emergency-contact.create-own',
            'employee-emergency-contact.edit-own',
            'employee-emergency-contact.delete-own',
            'employee-emergency-contact.set-primary-own',
        ],
        'manager' => [
            'employee-emergency-contact.view',
            'employee-emergency-contact.create',
            'employee-emergency-contact.edit',
            'employee-emergency-contact.delete',
            'employee-emergency-contact.set-primary',
            'employee-emergency-contact.activate',
            'employee-emergency-contact.deactivate',
            'employee-emergency-contact.view-team',
            'employee-emergency-contact.view-department',
            'employee-emergency-contact.export',
            'employee-emergency-contact.statistics',
            'employee-emergency-contact.validation',
        ],
        'hr-manager' => [
            'employee-emergency-contact.view',
            'employee-emergency-contact.create',
            'employee-emergency-contact.edit',
            'employee-emergency-contact.delete',
            'employee-emergency-contact.set-primary',
            'employee-emergency-contact.activate',
            'employee-emergency-contact.deactivate',
            'employee-emergency-contact.view-team',
            'employee-emergency-contact.view-department',
            'employee-emergency-contact.view-all',
            'employee-emergency-contact.export',
            'employee-emergency-contact.import',
            'employee-emergency-contact.statistics',
            'employee-emergency-contact.validation',
            'employee-emergency-contact.bulk-operations',
            'employee-emergency-contact.audit',
        ],
        'admin' => [
            'employee-emergency-contact.view',
            'employee-emergency-contact.create',
            'employee-emergency-contact.edit',
            'employee-emergency-contact.delete',
            'employee-emergency-contact.set-primary',
            'employee-emergency-contact.activate',
            'employee-emergency-contact.deactivate',
            'employee-emergency-contact.view-team',
            'employee-emergency-contact.view-department',
            'employee-emergency-contact.view-all',
            'employee-emergency-contact.manage-all',
            'employee-emergency-contact.export',
            'employee-emergency-contact.import',
            'employee-emergency-contact.statistics',
            'employee-emergency-contact.validation',
            'employee-emergency-contact.bulk-operations',
            'employee-emergency-contact.audit',
        ],
        'super-admin' => [
            'employee-emergency-contact.view',
            'employee-emergency-contact.create',
            'employee-emergency-contact.edit',
            'employee-emergency-contact.delete',
            'employee-emergency-contact.set-primary',
            'employee-emergency-contact.activate',
            'employee-emergency-contact.deactivate',
            'employee-emergency-contact.view-team',
            'employee-emergency-contact.view-department',
            'employee-emergency-contact.view-all',
            'employee-emergency-contact.manage-all',
            'employee-emergency-contact.export',
            'employee-emergency-contact.import',
            'employee-emergency-contact.statistics',
            'employee-emergency-contact.validation',
            'employee-emergency-contact.bulk-operations',
            'employee-emergency-contact.audit',
        ],
    ];

    /**
     * Create all permissions.
     */
    public static function createPermissions(): void
    {
        foreach (self::PERMISSIONS as $permission => $description) {
            Permission::firstOrCreate([
                'name' => $permission,
                'guard_name' => 'web',
            ], [
                'display_name' => $description,
                'description' => $description,
            ]);
        }
    }

    /**
     * Assign permissions to roles.
     */
    public static function assignPermissionsToRoles(): void
    {
        foreach (self::ROLE_PERMISSIONS as $roleName => $permissions) {
            $role = Role::firstOrCreate(['name' => $roleName, 'guard_name' => 'web']);

            foreach ($permissions as $permission) {
                $permissionModel = Permission::where('name', $permission)->first();
                if ($permissionModel && ! $role->hasPermissionTo($permissionModel)) {
                    $role->givePermissionTo($permissionModel);
                }
            }
        }
    }

    /**
     * Get all permissions as array.
     */
    public static function getAllPermissions(): array
    {
        return self::PERMISSIONS;
    }

    /**
     * Get permissions by group.
     */
    public static function getPermissionsByGroup(): array
    {
        return self::PERMISSION_GROUPS;
    }

    /**
     * Get permissions for a specific role.
     */
    public static function getPermissionsForRole(string $roleName): array
    {
        return self::ROLE_PERMISSIONS[$roleName] ?? [];
    }

    /**
     * Check if a permission exists.
     */
    public static function permissionExists(string $permission): bool
    {
        return array_key_exists($permission, self::PERMISSIONS);
    }

    /**
     * Get permission description.
     */
    public static function getPermissionDescription(string $permission): ?string
    {
        return self::PERMISSIONS[$permission] ?? null;
    }

    /**
     * Get permissions that allow viewing emergency contacts.
     */
    public static function getViewPermissions(): array
    {
        return [
            'employee-emergency-contact.view',
            'employee-emergency-contact.view-own',
            'employee-emergency-contact.view-team',
            'employee-emergency-contact.view-department',
            'employee-emergency-contact.view-all',
        ];
    }

    /**
     * Get permissions that allow creating emergency contacts.
     */
    public static function getCreatePermissions(): array
    {
        return [
            'employee-emergency-contact.create',
            'employee-emergency-contact.create-own',
        ];
    }

    /**
     * Get permissions that allow editing emergency contacts.
     */
    public static function getEditPermissions(): array
    {
        return [
            'employee-emergency-contact.edit',
            'employee-emergency-contact.edit-own',
        ];
    }

    /**
     * Get permissions that allow deleting emergency contacts.
     */
    public static function getDeletePermissions(): array
    {
        return [
            'employee-emergency-contact.delete',
            'employee-emergency-contact.delete-own',
        ];
    }

    /**
     * Get permissions that allow managing emergency contacts.
     */
    public static function getManagePermissions(): array
    {
        return [
            'employee-emergency-contact.manage-all',
        ];
    }

    /**
     * Get permissions that allow administrative operations.
     */
    public static function getAdminPermissions(): array
    {
        return [
            'employee-emergency-contact.export',
            'employee-emergency-contact.import',
            'employee-emergency-contact.statistics',
            'employee-emergency-contact.validation',
            'employee-emergency-contact.bulk-operations',
            'employee-emergency-contact.audit',
        ];
    }

    /**
     * Check if a permission allows viewing own contacts.
     */
    public static function allowsViewOwn(string $permission): bool
    {
        return in_array($permission, [
            'employee-emergency-contact.view-own',
            'employee-emergency-contact.view-team',
            'employee-emergency-contact.view-department',
            'employee-emergency-contact.view-all',
        ]);
    }

    /**
     * Check if a permission allows viewing team contacts.
     */
    public static function allowsViewTeam(string $permission): bool
    {
        return in_array($permission, [
            'employee-emergency-contact.view-team',
            'employee-emergency-contact.view-department',
            'employee-emergency-contact.view-all',
        ]);
    }

    /**
     * Check if a permission allows viewing department contacts.
     */
    public static function allowsViewDepartment(string $permission): bool
    {
        return in_array($permission, [
            'employee-emergency-contact.view-department',
            'employee-emergency-contact.view-all',
        ]);
    }

    /**
     * Check if a permission allows viewing all contacts.
     */
    public static function allowsViewAll(string $permission): bool
    {
        return $permission === 'employee-emergency-contact.view-all';
    }

    /**
     * Check if a permission allows managing all contacts.
     */
    public static function allowsManageAll(string $permission): bool
    {
        return $permission === 'employee-emergency-contact.manage-all';
    }

    /**
     * Get the highest level view permission from a list.
     */
    public static function getHighestViewPermission(array $permissions): string
    {
        if (in_array('employee-emergency-contact.view-all', $permissions)) {
            return 'employee-emergency-contact.view-all';
        }

        if (in_array('employee-emergency-contact.view-department', $permissions)) {
            return 'employee-emergency-contact.view-department';
        }

        if (in_array('employee-emergency-contact.view-team', $permissions)) {
            return 'employee-emergency-contact.view-team';
        }

        if (in_array('employee-emergency-contact.view-own', $permissions)) {
            return 'employee-emergency-contact.view-own';
        }

        return 'employee-emergency-contact.view';
    }

    /**
     * Get the scope for a view permission.
     */
    public static function getViewScope(string $permission): string
    {
        switch ($permission) {
            case 'employee-emergency-contact.view-all':
                return 'all';
            case 'employee-emergency-contact.view-department':
                return 'department';
            case 'employee-emergency-contact.view-team':
                return 'team';
            case 'employee-emergency-contact.view-own':
                return 'own';
            default:
                return 'none';
        }
    }

    /**
     * Get required permissions for a specific action.
     */
    public static function getRequiredPermissions(string $action): array
    {
        $requirements = [
            'view' => ['employee-emergency-contact.view'],
            'create' => ['employee-emergency-contact.create'],
            'edit' => ['employee-emergency-contact.edit'],
            'delete' => ['employee-emergency-contact.delete'],
            'set-primary' => ['employee-emergency-contact.set-primary'],
            'activate' => ['employee-emergency-contact.activate'],
            'deactivate' => ['employee-emergency-contact.deactivate'],
            'export' => ['employee-emergency-contact.export'],
            'import' => ['employee-emergency-contact.import'],
            'statistics' => ['employee-emergency-contact.statistics'],
            'validation' => ['employee-emergency-contact.validation'],
            'bulk-operations' => ['employee-emergency-contact.bulk-operations'],
            'audit' => ['employee-emergency-contact.audit'],
        ];

        return $requirements[$action] ?? [];
    }

    /**
     * Check if permissions are sufficient for an action.
     */
    public static function hasSufficientPermissions(array $userPermissions, string $action): bool
    {
        $required = self::getRequiredPermissions($action);

        if (empty($required)) {
            return false;
        }

        foreach ($required as $permission) {
            if (! in_array($permission, $userPermissions)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Get all permissions as a flat list with descriptions.
     */
    public static function getPermissionsList(): array
    {
        $list = [];

        foreach (self::PERMISSION_GROUPS as $groupName => $permissions) {
            foreach ($permissions as $permission) {
                $list[] = [
                    'name' => $permission,
                    'description' => self::PERMISSIONS[$permission],
                    'group' => $groupName,
                ];
            }
        }

        return $list;
    }

    /**
     * Clean up permissions (remove unused ones).
     */
    public static function cleanupPermissions(): void
    {
        $existingPermissions = Permission::where('name', 'like', 'employee-emergency-contact.%')->get();

        foreach ($existingPermissions as $permission) {
            if (! array_key_exists($permission->name, self::PERMISSIONS)) {
                $permission->delete();
            }
        }
    }

    /**
     * Reset all permissions and roles.
     */
    public static function reset(): void
    {
        // Remove all emergency contact permissions from roles
        $roles = Role::all();
        foreach ($roles as $role) {
            $permissions = $role->permissions()->where('name', 'like', 'employee-emergency-contact.%')->get();
            foreach ($permissions as $permission) {
                $role->revokePermissionTo($permission);
            }
        }

        // Delete all emergency contact permissions
        Permission::where('name', 'like', 'employee-emergency-contact.%')->delete();

        // Recreate permissions
        self::createPermissions();

        // Reassign permissions to roles
        self::assignPermissionsToRoles();
    }
}
