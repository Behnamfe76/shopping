<?php

namespace Fereydooni\Shopping\database\seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class DepartmentPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create Department permissions
        $permissions = [
            'department.view',
            'department.create',
            'department.edit',
            'department.delete',
            'department.assign-manager',
            'department.move',
            'department.view-own',
            'department.view-team',
            'department.view-all',
            'department.manage-all',
            'department.export',
            'department.import',
            'department.statistics',
            'department.budget-management',
            'department.hierarchy-management',
            'department.view-sensitive',
            'department.manage-budget',
            'department.manage-hierarchy',
            'department.audit',
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
            $adminRole->givePermissionTo(Permission::all());
        }

        // HR Manager role
        $hrManagerRole = Role::where('name', 'hr-manager')->first();
        if ($hrManagerRole) {
            $hrManagerRole->givePermissionTo([
                'department.view',
                'department.create',
                'department.edit',
                'department.assign-manager',
                'department.move',
                'department.view-all',
                'department.hierarchy-management',
                'department.statistics',
                'department.export',
                'department.import',
            ]);
        }

        // Department Manager role
        $deptManagerRole = Role::where('name', 'department-manager')->first();
        if ($deptManagerRole) {
            $deptManagerRole->givePermissionTo([
                'department.view',
                'department.view-own',
                'department.view-team',
                'department.statistics',
            ]);
        }

        // Finance Manager role
        $financeManagerRole = Role::where('name', 'finance-manager')->first();
        if ($financeManagerRole) {
            $financeManagerRole->givePermissionTo([
                'department.view',
                'department.view-all',
                'department.budget-management',
                'department.statistics',
                'department.export',
            ]);
        }

        // Employee role
        $employeeRole = Role::where('name', 'employee')->first();
        if ($employeeRole) {
            $employeeRole->givePermissionTo([
                'department.view-own',
            ]);
        }

        // Auditor role
        $auditorRole = Role::where('name', 'auditor')->first();
        if ($auditorRole) {
            $auditorRole->givePermissionTo([
                'department.view',
                'department.view-all',
                'department.audit',
                'department.export',
            ]);
        }
    }
}
