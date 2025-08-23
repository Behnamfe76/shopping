<?php

namespace App\Permissions;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class ProviderContractPermissions
{
    /**
     * Register all provider contract permissions
     *
     * @return void
     */
    public static function register(): void
    {
        try {
            $permissions = [
                // Basic CRUD permissions
                'provider-contract.view' => 'View provider contracts',
                'provider-contract.create' => 'Create provider contracts',
                'provider-contract.edit' => 'Edit provider contracts',
                'provider-contract.delete' => 'Delete provider contracts',

                // Contract lifecycle permissions
                'provider-contract.sign' => 'Sign provider contracts',
                'provider-contract.renew' => 'Renew provider contracts',
                'provider-contract.terminate' => 'Terminate provider contracts',
                'provider-contract.activate' => 'Activate provider contracts',
                'provider-contract.suspend' => 'Suspend provider contracts',
                'provider-contract.expire' => 'Expire provider contracts',

                // Ownership permissions
                'provider-contract.view-own' => 'View own provider contracts',
                'provider-contract.create-own' => 'Create own provider contracts',
                'provider-contract.edit-own' => 'Edit own provider contracts',
                'provider-contract.delete-own' => 'Delete own provider contracts',
                'provider-contract.sign-own' => 'Sign own provider contracts',
                'provider-contract.renew-own' => 'Renew own provider contracts',
                'provider-contract.terminate-own' => 'Terminate own provider contracts',

                // Team and department permissions
                'provider-contract.view-team' => 'View team provider contracts',
                'provider-contract.view-department' => 'View department provider contracts',
                'provider-contract.view-all' => 'View all provider contracts',
                'provider-contract.manage-all' => 'Manage all provider contracts',

                // Special permissions
                'provider-contract.export' => 'Export provider contract data',
                'provider-contract.import' => 'Import provider contract data',
                'provider-contract.statistics' => 'View provider contract statistics',
                'provider-contract.approval' => 'Approve provider contracts',
                'provider-contract.audit' => 'Audit provider contracts',

                // Financial permissions
                'provider-contract.view-financial' => 'View contract financial information',
                'provider-contract.edit-financial' => 'Edit contract financial information',
                'provider-contract.approve-payments' => 'Approve contract payments',

                // Legal permissions
                'provider-contract.view-legal' => 'View contract legal information',
                'provider-contract.edit-legal' => 'Edit contract legal information',
                'provider-contract.approve-legal' => 'Approve contract legal terms',
            ];

            foreach ($permissions as $permission => $description) {
                static::createPermission($permission, $description);
            }

            Log::info('Provider contract permissions registered successfully');

        } catch (\Exception $e) {
            Log::error('Failed to register provider contract permissions', [
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Create a permission if it doesn't exist
     *
     * @param string $name
     * @param string $description
     * @return void
     */
    protected static function createPermission(string $name, string $description): void
    {
        try {
            $permission = DB::table('permissions')->where('name', $name)->first();

            if (!$permission) {
                DB::table('permissions')->insert([
                    'name' => $name,
                    'guard_name' => 'web',
                    'description' => $description,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                Log::info("Created permission: {$name}");
            }
        } catch (\Exception $e) {
            Log::warning("Failed to create permission: {$name}", [
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Assign permissions to roles
     *
     * @param array $rolePermissions
     * @return void
     */
    public static function assignToRoles(array $rolePermissions): void
    {
        try {
            foreach ($rolePermissions as $roleName => $permissions) {
                $role = DB::table('roles')->where('name', $roleName)->first();

                if ($role) {
                    foreach ($permissions as $permission) {
                        static::assignPermissionToRole($permission, $role->id);
                    }

                    Log::info("Assigned permissions to role: {$roleName}");
                } else {
                    Log::warning("Role not found: {$roleName}");
                }
            }
        } catch (\Exception $e) {
            Log::error('Failed to assign permissions to roles', [
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Assign a permission to a role
     *
     * @param string $permissionName
     * @param int $roleId
     * @return void
     */
    protected static function assignPermissionToRole(string $permissionName, int $roleId): void
    {
        try {
            $permission = DB::table('permissions')->where('name', $permissionName)->first();

            if ($permission) {
                $exists = DB::table('role_has_permissions')
                    ->where('permission_id', $permission->id)
                    ->where('role_id', $roleId)
                    ->exists();

                if (!$exists) {
                    DB::table('role_has_permissions')->insert([
                        'permission_id' => $permission->id,
                        'role_id' => $roleId,
                    ]);
                }
            }
        } catch (\Exception $e) {
            Log::warning("Failed to assign permission {$permissionName} to role {$roleId}", [
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Get default role permissions mapping
     *
     * @return array
     */
    public static function getDefaultRolePermissions(): array
    {
        return [
            'super-admin' => [
                'provider-contract.view',
                'provider-contract.create',
                'provider-contract.edit',
                'provider-contract.delete',
                'provider-contract.sign',
                'provider-contract.renew',
                'provider-contract.terminate',
                'provider-contract.activate',
                'provider-contract.suspend',
                'provider-contract.expire',
                'provider-contract.view-own',
                'provider-contract.create-own',
                'provider-contract.edit-own',
                'provider-contract.delete-own',
                'provider-contract.sign-own',
                'provider-contract.renew-own',
                'provider-contract.terminate-own',
                'provider-contract.view-team',
                'provider-contract.view-department',
                'provider-contract.view-all',
                'provider-contract.manage-all',
                'provider-contract.export',
                'provider-contract.import',
                'provider-contract.statistics',
                'provider-contract.approval',
                'provider-contract.audit',
                'provider-contract.view-financial',
                'provider-contract.edit-financial',
                'provider-contract.approve-payments',
                'provider-contract.view-legal',
                'provider-contract.edit-legal',
                'provider-contract.approve-legal',
            ],
            'admin' => [
                'provider-contract.view',
                'provider-contract.create',
                'provider-contract.edit',
                'provider-contract.delete',
                'provider-contract.sign',
                'provider-contract.renew',
                'provider-contract.terminate',
                'provider-contract.activate',
                'provider-contract.suspend',
                'provider-contract.expire',
                'provider-contract.view-own',
                'provider-contract.create-own',
                'provider-contract.edit-own',
                'provider-contract.delete-own',
                'provider-contract.sign-own',
                'provider-contract.renew-own',
                'provider-contract.terminate-own',
                'provider-contract.view-team',
                'provider-contract.view-department',
                'provider-contract.view-all',
                'provider-contract.export',
                'provider-contract.statistics',
                'provider-contract.approval',
                'provider-contract.view-financial',
                'provider-contract.view-legal',
            ],
            'manager' => [
                'provider-contract.view',
                'provider-contract.create',
                'provider-contract.edit',
                'provider-contract.sign',
                'provider-contract.renew',
                'provider-contract.activate',
                'provider-contract.suspend',
                'provider-contract.view-own',
                'provider-contract.create-own',
                'provider-contract.edit-own',
                'provider-contract.sign-own',
                'provider-contract.renew-own',
                'provider-contract.view-team',
                'provider-contract.view-department',
                'provider-contract.export',
                'provider-contract.statistics',
                'provider-contract.view-financial',
                'provider-contract.view-legal',
            ],
            'employee' => [
                'provider-contract.view',
                'provider-contract.view-own',
                'provider-contract.create-own',
                'provider-contract.edit-own',
                'provider-contract.statistics',
            ],
            'provider' => [
                'provider-contract.view-own',
                'provider-contract.statistics',
            ],
        ];
    }

    /**
     * Check if user has permission
     *
     * @param string $permission
     * @param int $userId
     * @return bool
     */
    public static function hasPermission(string $permission, int $userId): bool
    {
        try {
            $cacheKey = "user_permission_{$userId}_{$permission}";

            return Cache::remember($cacheKey, 300, function () use ($permission, $userId) {
                return DB::table('users')
                    ->join('model_has_roles', 'users.id', '=', 'model_has_roles.model_id')
                    ->join('role_has_permissions', 'model_has_roles.role_id', '=', 'role_has_permissions.role_id')
                    ->join('permissions', 'role_has_permissions.permission_id', '=', 'permissions.id')
                    ->where('users.id', $userId)
                    ->where('permissions.name', $permission)
                    ->where('model_has_roles.model_type', 'App\\Models\\User')
                    ->exists();
            });
        } catch (\Exception $e) {
            Log::error('Failed to check user permission', [
                'user_id' => $userId,
                'permission' => $permission,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Get user permissions
     *
     * @param int $userId
     * @return array
     */
    public static function getUserPermissions(int $userId): array
    {
        try {
            $cacheKey = "user_permissions_{$userId}";

            return Cache::remember($cacheKey, 300, function () use ($userId) {
                return DB::table('users')
                    ->join('model_has_roles', 'users.id', '=', 'model_has_roles.model_id')
                    ->join('role_has_permissions', 'model_has_roles.role_id', '=', 'role_has_permissions.role_id')
                    ->join('permissions', 'role_has_permissions.permission_id', '=', 'permissions.id')
                    ->where('users.id', $userId)
                    ->where('model_has_roles.model_type', 'App\\Models\\User')
                    ->pluck('permissions.name')
                    ->toArray();
            });
        } catch (\Exception $e) {
            Log::error('Failed to get user permissions', [
                'user_id' => $userId,
                'error' => $e->getMessage()
            ]);
            return [];
        }
    }

    /**
     * Clear user permission cache
     *
     * @param int $userId
     * @return void
     */
    public static function clearUserPermissionCache(int $userId): void
    {
        try {
            Cache::forget("user_permissions_{$userId}");

            // Clear individual permission caches
            $permissions = static::getUserPermissions($userId);
            foreach ($permissions as $permission) {
                Cache::forget("user_permission_{$userId}_{$permission}");
            }
        } catch (\Exception $e) {
            Log::warning('Failed to clear user permission cache', [
                'user_id' => $userId,
                'error' => $e->getMessage()
            ]);
        }
    }
}
