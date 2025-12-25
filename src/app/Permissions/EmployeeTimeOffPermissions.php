<?php

namespace Fereydooni\Shopping\app\Permissions;

use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class EmployeeTimeOffPermissions
{
    public static function createPermissions(): void
    {
        $permissions = [
            // Basic CRUD permissions
            'employee-time-off.view',
            'employee-time-off.create',
            'employee-time-off.edit',
            'employee-time-off.delete',

            // Workflow permissions
            'employee-time-off.approve',
            'employee-time-off.reject',
            'employee-time-off.cancel',

            // Own permissions
            'employee-time-off.view-own',
            'employee-time-off.create-own',
            'employee-time-off.edit-own',
            'employee-time-off.cancel-own',

            // Team permissions
            'employee-time-off.view-team',
            'employee-time-off.approve-team',

            // Department permissions
            'employee-time-off.view-department',
            'employee-time-off.approve-department',

            // Company-wide permissions
            'employee-time-off.view-all',
            'employee-time-off.approve-all',

            // Data management permissions
            'employee-time-off.export',
            'employee-time-off.import',
            'employee-time-off.statistics',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }
    }

    public static function assignPermissionsToRoles(): void
    {
        // Employee role - can only manage their own time-off
        $employeeRole = Role::firstOrCreate(['name' => 'employee']);
        $employeeRole->givePermissionTo([
            'employee-time-off.view-own',
            'employee-time-off.create-own',
            'employee-time-off.edit-own',
            'employee-time-off.cancel-own',
        ]);

        // Team Lead role - can manage their team's time-off
        $teamLeadRole = Role::firstOrCreate(['name' => 'team-lead']);
        $teamLeadRole->givePermissionTo([
            'employee-time-off.view-own',
            'employee-time-off.create-own',
            'employee-time-off.edit-own',
            'employee-time-off.cancel-own',
            'employee-time-off.view-team',
            'employee-time-off.approve-team',
        ]);

        // Manager role - can manage their department's time-off
        $managerRole = Role::firstOrCreate(['name' => 'manager']);
        $managerRole->givePermissionTo([
            'employee-time-off.view-own',
            'employee-time-off.create-own',
            'employee-time-off.edit-own',
            'employee-time-off.cancel-own',
            'employee-time-off.view-team',
            'employee-time-off.approve-team',
            'employee-time-off.view-department',
            'employee-time-off.approve-department',
        ]);

        // HR role - can manage all time-off requests
        $hrRole = Role::firstOrCreate(['name' => 'hr']);
        $hrRole->givePermissionTo([
            'employee-time-off.view-all',
            'employee-time-off.create',
            'employee-time-off.edit',
            'employee-time-off.delete',
            'employee-time-off.approve-all',
            'employee-time-off.reject',
            'employee-time-off.cancel',
            'employee-time-off.export',
            'employee-time-off.import',
            'employee-time-off.statistics',
        ]);

        // Admin role - has all permissions
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $adminRole->givePermissionTo(Permission::all());
    }

    public static function getPermissionDescriptions(): array
    {
        return [
            'employee-time-off.view' => 'View all time-off requests',
            'employee-time-off.create' => 'Create time-off requests for any employee',
            'employee-time-off.edit' => 'Edit any time-off request',
            'employee-time-off.delete' => 'Delete any time-off request',
            'employee-time-off.approve' => 'Approve time-off requests',
            'employee-time-off.reject' => 'Reject time-off requests',
            'employee-time-off.cancel' => 'Cancel time-off requests',
            'employee-time-off.view-own' => 'View own time-off requests',
            'employee-time-off.create-own' => 'Create own time-off requests',
            'employee-time-off.edit-own' => 'Edit own time-off requests',
            'employee-time-off.cancel-own' => 'Cancel own time-off requests',
            'employee-time-off.view-team' => 'View team members\' time-off requests',
            'employee-time-off.approve-team' => 'Approve team members\' time-off requests',
            'employee-time-off.view-department' => 'View department time-off requests',
            'employee-time-off.approve-department' => 'Approve department time-off requests',
            'employee-time-off.view-all' => 'View all company time-off requests',
            'employee-time-off.approve-all' => 'Approve any time-off request',
            'employee-time-off.export' => 'Export time-off data',
            'employee-time-off.import' => 'Import time-off data',
            'employee-time-off.statistics' => 'View time-off statistics',
        ];
    }

    public static function getRolePermissions(): array
    {
        return [
            'employee' => [
                'employee-time-off.view-own',
                'employee-time-off.create-own',
                'employee-time-off.edit-own',
                'employee-time-off.cancel-own',
            ],
            'team-lead' => [
                'employee-time-off.view-own',
                'employee-time-off.create-own',
                'employee-time-off.edit-own',
                'employee-time-off.cancel-own',
                'employee-time-off.view-team',
                'employee-time-off.approve-team',
            ],
            'manager' => [
                'employee-time-off.view-own',
                'employee-time-off.create-own',
                'employee-time-off.edit-own',
                'employee-time-off.cancel-own',
                'employee-time-off.view-team',
                'employee-time-off.approve-team',
                'employee-time-off.view-department',
                'employee-time-off.approve-department',
            ],
            'hr' => [
                'employee-time-off.view-all',
                'employee-time-off.create',
                'employee-time-off.edit',
                'employee-time-off.delete',
                'employee-time-off.approve-all',
                'employee-time-off.reject',
                'employee-time-off.cancel',
                'employee-time-off.export',
                'employee-time-off.import',
                'employee-time-off.statistics',
            ],
            'admin' => [
                'employee-time-off.view',
                'employee-time-off.create',
                'employee-time-off.edit',
                'employee-time-off.delete',
                'employee-time-off.approve',
                'employee-time-off.reject',
                'employee-time-off.cancel',
                'employee-time-off.view-own',
                'employee-time-off.create-own',
                'employee-time-off.edit-own',
                'employee-time-off.cancel-own',
                'employee-time-off.view-team',
                'employee-time-off.approve-team',
                'employee-time-off.view-department',
                'employee-time-off.approve-department',
                'employee-time-off.view-all',
                'employee-time-off.approve-all',
                'employee-time-off.export',
                'employee-time-off.import',
                'employee-time-off.statistics',
            ],
        ];
    }

    public static function setup(): void
    {
        try {
            DB::beginTransaction();

            self::createPermissions();
            self::assignPermissionsToRoles();

            DB::commit();

        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public static function reset(): void
    {
        try {
            DB::beginTransaction();

            // Remove all permissions
            Permission::where('name', 'like', 'employee-time-off.%')->delete();

            // Remove roles (optional - be careful with this)
            // Role::whereIn('name', ['employee', 'team-lead', 'manager', 'hr', 'admin'])->delete();

            DB::commit();

        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
