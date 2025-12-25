<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class EmployeeDepartmentPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create EmployeeDepartment permissions
        $permissions = [
            'employee-department.view',
            'employee-department.create',
            'employee-department.edit',
            'employee-department.delete',
            'employee-department.assign-manager',
            'employee-department.move',
            'employee-department.view-own',
            'employee-department.view-team',
            'employee-department.view-all',
            'employee-department.manage-all',
            'employee-department.export',
            'employee-department.import',
            'employee-department.statistics',
            'employee-department.budget-management',
            'employee-department.hierarchy-management',
            'employee-department.view-sensitive',
            'employee-department.manage-budget',
            'employee-department.manage-hierarchy',
            'employee-department.audit',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
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
            Role::firstOrCreate(['name' => $roleName]);
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
                'employee-department.view',
                'employee-department.create',
                'employee-department.edit',
                'employee-department.assign-manager',
                'employee-department.move',
                'employee-department.view-all',
                'employee-department.hierarchy-management',
                'employee-department.statistics',
                'employee-department.export',
                'employee-department.import',
            ]);
        }

        // Department Manager role
        $deptManagerRole = Role::where('name', 'department-manager')->first();
        if ($deptManagerRole) {
            $deptManagerRole->givePermissionTo([
                'employee-department.view',
                'employee-department.view-own',
                'employee-department.view-team',
                'employee-department.statistics',
            ]);
        }

        // Finance Manager role
        $financeManagerRole = Role::where('name', 'finance-manager')->first();
        if ($financeManagerRole) {
            $financeManagerRole->givePermissionTo([
                'employee-department.view',
                'employee-department.view-all',
                'employee-department.budget-management',
                'employee-department.statistics',
                'employee-department.export',
            ]);
        }

        // Employee role
        $employeeRole = Role::where('name', 'employee')->first();
        if ($employeeRole) {
            $employeeRole->givePermissionTo([
                'employee-department.view-own',
            ]);
        }

        // Auditor role
        $auditorRole = Role::where('name', 'auditor')->first();
        if ($auditorRole) {
            $auditorRole->givePermissionTo([
                'employee-department.view',
                'employee-department.view-all',
                'employee-department.audit',
                'employee-department.export',
            ]);
        }
    }
}
