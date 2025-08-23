<?php

namespace Fereydooni\Shopping\Permissions;

use Illuminate\Support\Collection;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class EmployeeTrainingPermissions
{
    /**
     * All training-related permissions
     */
    public const PERMISSIONS = [
        // Basic CRUD permissions
        'employee-training.view' => 'View employee training records',
        'employee-training.create' => 'Create new employee training',
        'employee-training.edit' => 'Edit employee training records',
        'employee-training.delete' => 'Delete employee training records',
        
        // Training workflow permissions
        'employee-training.start' => 'Start employee training',
        'employee-training.complete' => 'Complete employee training',
        'employee-training.fail' => 'Mark training as failed',
        'employee-training.renew' => 'Renew training certification',
        'employee-training.update-progress' => 'Update training progress',
        
        // View permissions by scope
        'employee-training.view-own' => 'View own training records',
        'employee-training.view-team' => 'View team member training records',
        'employee-training.view-department' => 'View department training records',
        'employee-training.view-all' => 'View all training records',
        
        // Management permissions
        'employee-training.manage-own' => 'Manage own training records',
        'employee-training.manage-team' => 'Manage team member training records',
        'employee-training.manage-department' => 'Manage department training records',
        'employee-training.manage-all' => 'Manage all training records',
        
        // Special permissions
        'employee-training.assign' => 'Assign training to employees',
        'employee-training.approve' => 'Approve training requests',
        'employee-training.reject' => 'Reject training requests',
        'employee-training.override' => 'Override training requirements',
        
        // Data management permissions
        'employee-training.export' => 'Export training data',
        'employee-training.import' => 'Import training data',
        'employee-training.bulk-actions' => 'Perform bulk actions on training records',
        
        // Reporting and analytics permissions
        'employee-training.statistics' => 'View training statistics',
        'employee-training.reports' => 'Generate training reports',
        'employee-training.analytics' => 'Access training analytics',
        
        // Certification management permissions
        'employee-training.certification-management' => 'Manage training certifications',
        'employee-training.certification-approve' => 'Approve training certifications',
        'employee-training.certification-revoke' => 'Revoke training certifications',
        'employee-training.certification-renew' => 'Renew training certifications',
        
        // Mandatory training permissions
        'employee-training.mandatory-training-management' => 'Manage mandatory training requirements',
        'employee-training.mandatory-training-assign' => 'Assign mandatory training',
        'employee-training.mandatory-training-exempt' => 'Exempt from mandatory training',
        
        // Compliance permissions
        'employee-training.compliance-view' => 'View training compliance reports',
        'employee-training.compliance-manage' => 'Manage training compliance',
        'employee-training.compliance-override' => 'Override compliance requirements',
        
        // Cost management permissions
        'employee-training.cost-view' => 'View training costs',
        'employee-training.cost-manage' => 'Manage training budgets',
        'employee-training.cost-approve' => 'Approve training expenses',
        
        // Provider management permissions
        'employee-training.provider-manage' => 'Manage training providers',
        'employee-training.provider-approve' => 'Approve training providers',
        'employee-training.provider-evaluate' => 'Evaluate training providers',
        
        // Content management permissions
        'employee-training.content-manage' => 'Manage training content',
        'employee-training.content-approve' => 'Approve training content',
        'employee-training.content-publish' => 'Publish training content',
        
        // Assessment permissions
        'employee-training.assessment-create' => 'Create training assessments',
        'employee-training.assessment-grade' => 'Grade training assessments',
        'employee-training.assessment-review' => 'Review training assessments',
        
        // Notification permissions
        'employee-training.notifications-send' => 'Send training notifications',
        'employee-training.notifications-manage' => 'Manage training notifications',
        'employee-training.reminders-send' => 'Send training reminders',
        
        // System administration permissions
        'employee-training.system-admin' => 'Full system administration for training',
        'employee-training.settings-manage' => 'Manage training system settings',
        'employee-training.audit-view' => 'View training audit logs'
    ];

    /**
     * Permission groups for easier management
     */
    public const PERMISSION_GROUPS = [
        'basic' => [
            'employee-training.view',
            'employee-training.create',
            'employee-training.edit',
            'employee-training.delete'
        ],
        'workflow' => [
            'employee-training.start',
            'employee-training.complete',
            'employee-training.fail',
            'employee-training.renew',
            'employee-training.update-progress'
        ],
        'view_scopes' => [
            'employee-training.view-own',
            'employee-training.view-team',
            'employee-training.view-department',
            'employee-training.view-all'
        ],
        'management' => [
            'employee-training.manage-own',
            'employee-training.manage-team',
            'employee-training.manage-department',
            'employee-training.manage-all'
        ],
        'special' => [
            'employee-training.assign',
            'employee-training.approve',
            'employee-training.reject',
            'employee-training.override'
        ],
        'data' => [
            'employee-training.export',
            'employee-training.import',
            'employee-training.bulk-actions'
        ],
        'reporting' => [
            'employee-training.statistics',
            'employee-training.reports',
            'employee-training.analytics'
        ],
        'certification' => [
            'employee-training.certification-management',
            'employee-training.certification-approve',
            'employee-training.certification-revoke',
            'employee-training.certification-renew'
        ],
        'mandatory' => [
            'employee-training.mandatory-training-management',
            'employee-training.mandatory-training-assign',
            'employee-training.mandatory-training-exempt'
        ],
        'compliance' => [
            'employee-training.compliance-view',
            'employee-training.compliance-manage',
            'employee-training.compliance-override'
        ],
        'cost' => [
            'employee-training.cost-view',
            'employee-training.cost-manage',
            'employee-training.cost-approve'
        ],
        'provider' => [
            'employee-training.provider-manage',
            'employee-training.provider-approve',
            'employee-training.provider-evaluate'
        ],
        'content' => [
            'employee-training.content-manage',
            'employee-training.content-approve',
            'employee-training.content-publish'
        ],
        'assessment' => [
            'employee-training.assessment-create',
            'employee-training.assessment-grade',
            'employee-training.assessment-review'
        ],
        'notifications' => [
            'employee-training.notifications-send',
            'employee-training.notifications-manage',
            'employee-training.reminders-send'
        ],
        'admin' => [
            'employee-training.system-admin',
            'employee-training.settings-manage',
            'employee-training.audit-view'
        ]
    ];

    /**
     * Role-based permission assignments
     */
    public const ROLE_PERMISSIONS = [
        'employee' => [
            'employee-training.view-own',
            'employee-training.view-team'
        ],
        'manager' => [
            'employee-training.view-own',
            'employee-training.view-team',
            'employee-training.view-department',
            'employee-training.manage-team',
            'employee-training.assign',
            'employee-training.approve',
            'employee-training.statistics',
            'employee-training.reports',
            'employee-training.notifications-send',
            'employee-training.reminders-send'
        ],
        'hr' => [
            'employee-training.view-all',
            'employee-training.create',
            'employee-training.edit',
            'employee-training.delete',
            'employee-training.assign',
            'employee-training.approve',
            'employee-training.reject',
            'employee-training.override',
            'employee-training.export',
            'employee-training.import',
            'employee-training.statistics',
            'employee-training.reports',
            'employee-training.analytics',
            'employee-training.certification-management',
            'employee-training.mandatory-training-management',
            'employee-training.compliance-view',
            'employee-training.cost-view',
            'employee-training.notifications-manage',
            'employee-training.reminders-send'
        ],
        'training-admin' => [
            'employee-training.view-all',
            'employee-training.create',
            'employee-training.edit',
            'employee-training.delete',
            'employee-training.start',
            'employee-training.complete',
            'employee-training.fail',
            'employee-training.renew',
            'employee-training.update-progress',
            'employee-training.assign',
            'employee-training.approve',
            'employee-training.reject',
            'employee-training.override',
            'employee-training.export',
            'employee-training.import',
            'employee-training.bulk-actions',
            'employee-training.statistics',
            'employee-training.reports',
            'employee-training.analytics',
            'employee-training.certification-management',
            'employee-training.certification-approve',
            'employee-training.certification-revoke',
            'employee-training.certification-renew',
            'employee-training.mandatory-training-management',
            'employee-training.mandatory-training-assign',
            'employee-training.mandatory-training-exempt',
            'employee-training.compliance-view',
            'employee-training.compliance-manage',
            'employee-training.cost-view',
            'employee-training.cost-manage',
            'employee-training.provider-manage',
            'employee-training.provider-approve',
            'employee-training.content-manage',
            'employee-training.content-approve',
            'employee-training.assessment-create',
            'employee-training.assessment-grade',
            'employee-training.notifications-manage',
            'employee-training.reminders-send'
        ],
        'compliance' => [
            'employee-training.view-all',
            'employee-training.compliance-view',
            'employee-training.compliance-manage',
            'employee-training.compliance-override',
            'employee-training.mandatory-training-management',
            'employee-training.mandatory-training-assign',
            'employee-training.mandatory-training-exempt',
            'employee-training.certification-revoke',
            'employee-training.statistics',
            'employee-training.reports',
            'employee-training.audit-view'
        ],
        'finance' => [
            'employee-training.view-all',
            'employee-training.cost-view',
            'employee-training.cost-manage',
            'employee-training.cost-approve',
            'employee-training.provider-approve',
            'employee-training.statistics',
            'employee-training.reports'
        ],
        'admin' => [
            'employee-training.system-admin',
            'employee-training.settings-manage',
            'employee-training.audit-view'
        ]
    ];

    /**
     * Create all training permissions
     */
    public static function createPermissions(): void
    {
        foreach (self::PERMISSIONS as $permission => $description) {
            Permission::firstOrCreate([
                'name' => $permission,
                'guard_name' => 'web'
            ], [
                'description' => $description
            ]);
        }
    }

    /**
     * Assign permissions to roles
     */
    public static function assignPermissionsToRoles(): void
    {
        foreach (self::ROLE_PERMISSIONS as $roleName => $permissions) {
            $role = Role::firstOrCreate(['name' => $roleName]);
            
            foreach ($permissions as $permission) {
                $permissionModel = Permission::where('name', $permission)->first();
                if ($permissionModel && !$role->hasPermissionTo($permission)) {
                    $role->givePermissionTo($permission);
                }
            }
        }
    }

    /**
     * Get permissions by group
     */
    public static function getPermissionsByGroup(string $group): array
    {
        return self::PERMISSION_GROUPS[$group] ?? [];
    }

    /**
     * Get all permissions for a specific role
     */
    public static function getPermissionsForRole(string $roleName): array
    {
        return self::ROLE_PERMISSIONS[$roleName] ?? [];
    }

    /**
     * Check if user has training permission
     */
    public static function hasPermission(string $permission): bool
    {
        return auth()->user()?->hasPermissionTo($permission) ?? false;
    }

    /**
     * Check if user has any training permission
     */
    public static function hasAnyTrainingPermission(): bool
    {
        $user = auth()->user();
        if (!$user) {
            return false;
        }

        return $user->hasAnyPermission(array_keys(self::PERMISSIONS));
    }

    /**
     * Get user's training permissions
     */
    public static function getUserPermissions(): Collection
    {
        $user = auth()->user();
        if (!$user) {
            return collect();
        }

        return $user->getAllPermissions()->filter(function ($permission) {
            return str_starts_with($permission->name, 'employee-training.');
        });
    }

    /**
     * Get user's training permission names
     */
    public static function getUserPermissionNames(): array
    {
        return self::getUserPermissions()->pluck('name')->toArray();
    }

    /**
     * Check if user can view training records
     */
    public static function canViewTraining(int $employeeId = null): bool
    {
        $user = auth()->user();
        if (!$user) {
            return false;
        }

        // Check specific permissions
        if ($user->hasPermissionTo('employee-training.view-all')) {
            return true;
        }

        if ($user->hasPermissionTo('employee-training.view-department')) {
            // Check if user is in same department as employee
            return self::isInSameDepartment($user, $employeeId);
        }

        if ($user->hasPermissionTo('employee-training.view-team')) {
            // Check if user manages the employee
            return self::isTeamMember($user, $employeeId);
        }

        if ($user->hasPermissionTo('employee-training.view-own')) {
            // Check if user is viewing their own training
            return $user->employee_id == $employeeId;
        }

        return false;
    }

    /**
     * Check if user can manage training records
     */
    public static function canManageTraining(int $employeeId = null): bool
    {
        $user = auth()->user();
        if (!$user) {
            return false;
        }

        // Check specific permissions
        if ($user->hasPermissionTo('employee-training.manage-all')) {
            return true;
        }

        if ($user->hasPermissionTo('employee-training.manage-department')) {
            return self::isInSameDepartment($user, $employeeId);
        }

        if ($user->hasPermissionTo('employee-training.manage-team')) {
            return self::isTeamMember($user, $employeeId);
        }

        if ($user->hasPermissionTo('employee-training.manage-own')) {
            return $user->employee_id == $employeeId;
        }

        return false;
    }

    /**
     * Check if user is in same department as employee
     */
    private static function isInSameDepartment($user, int $employeeId): bool
    {
        // Implementation depends on your department structure
        // For now, return false
        return false;
    }

    /**
     * Check if user manages the employee
     */
    private static function isTeamMember($user, int $employeeId): bool
    {
        // Implementation depends on your team structure
        // For now, return false
        return false;
    }
}
