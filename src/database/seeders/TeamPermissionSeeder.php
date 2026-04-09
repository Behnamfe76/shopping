<?php

namespace Fereydooni\Shopping\database\seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class TeamPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create Team permissions
        $permissions = [
            'team.view',
            'team.create',
            'team.edit',
            'team.delete',
            'team.delete-all',
            'team.manage-members',
            'team.manage-managers',
            'team.view-own',
            'team.view-department',
            'team.view-all',
            'team.manage-all',
            'team.export',
            'team.import',
            'team.statistics',
            'team.view-sensitive',
            'team.audit',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate([
                'name' => $permission,
                'guard_name' => 'api',
            ]);
        }

        // Create roles if they don't exist
        $roles = [
            'admin',
            'hr-manager',
            'department-manager',
            'team-manager',
            'finance-manager',
            'employee',
            'auditor',
        ];

        foreach ($roles as $roleName) {
            Role::firstOrCreate([
                'name' => $roleName,
                'guard_name' => 'api',
            ]);
        }

        // Assign permissions to roles
        $this->assignPermissionsToRoles();
    }

    /**
     * Assign permissions to roles
     */
    protected function assignPermissionsToRoles(): void
    {
        // Admin role - all permissions
        $adminRole = Role::where('name', 'admin')->first();
        if ($adminRole) {
            $adminRole->givePermissionTo(Permission::where('name', 'like', 'team.%')->get());
        }

        // HR Manager role
        $hrManagerRole = Role::where('name', 'hr-manager')->first();
        if ($hrManagerRole) {
            $hrManagerRole->givePermissionTo([
                'team.view',
                'team.create',
                'team.edit',
                'team.delete',
                'team.manage-members',
                'team.manage-managers',
                'team.view-all',
                'team.view-department',
                'team.statistics',
                'team.export',
                'team.import',
            ]);
        }

        // Department Manager role
        $deptManagerRole = Role::where('name', 'department-manager')->first();
        if ($deptManagerRole) {
            $deptManagerRole->givePermissionTo([
                'team.view',
                'team.create',
                'team.edit',
                'team.manage-members',
                'team.manage-managers',
                'team.view-own',
                'team.view-department',
                'team.statistics',
            ]);
        }

        // Team Manager role
        $teamManagerRole = Role::where('name', 'team-manager')->first();
        if ($teamManagerRole) {
            $teamManagerRole->givePermissionTo([
                'team.view',
                'team.view-own',
                'team.view-department',
                'team.manage-members',
            ]);
        }

        // Finance Manager role
        $financeManagerRole = Role::where('name', 'finance-manager')->first();
        if ($financeManagerRole) {
            $financeManagerRole->givePermissionTo([
                'team.view',
                'team.view-all',
                'team.statistics',
                'team.export',
            ]);
        }

        // Employee role
        $employeeRole = Role::where('name', 'employee')->first();
        if ($employeeRole) {
            $employeeRole->givePermissionTo([
                'team.view-own',
            ]);
        }

        // Auditor role
        $auditorRole = Role::where('name', 'auditor')->first();
        if ($auditorRole) {
            $auditorRole->givePermissionTo([
                'team.view',
                'team.view-all',
                'team.audit',
                'team.export',
            ]);
        }
    }
}
