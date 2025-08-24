<?php

namespace Fereydooni\Shopping\database\seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class EmployeePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Employee permissions
        $permissions = [
            // Basic CRUD permissions
            'employees.view' => 'View employees',
            'employees.create' => 'Create employees',
            'employees.update' => 'Update employees',
            'employees.delete' => 'Delete employees',
            'employees.restore' => 'Restore deleted employees',
            'employees.force-delete' => 'Permanently delete employees',

            // Employee status management
            'employees.activate' => 'Activate employees',
            'employees.deactivate' => 'Deactivate employees',
            'employees.terminate' => 'Terminate employees',
            'employees.rehire' => 'Rehire employees',

            // Employee management permissions
            'employees.manage-salary' => 'Manage employee salaries',
            'employees.manage-performance' => 'Manage employee performance',
            'employees.manage-time-off' => 'Manage employee time off',
            'employees.manage-benefits' => 'Manage employee benefits',
            'employees.manage-training' => 'Manage employee training',

            // Analytics and reporting
            'employees.view-analytics' => 'View employee analytics',
            'employees.export-data' => 'Export employee data',
            'employees.import-data' => 'Import employee data',

            // Sensitive data access
            'employees.view-sensitive-data' => 'View sensitive employee data',
            'employees.manage-hierarchy' => 'Manage employee hierarchy',
            'employees.manage-emergency-contacts' => 'Manage employee emergency contacts',
            'employees.manage-banking-info' => 'Manage employee banking information',
            'employees.manage-tax-info' => 'Manage employee tax information',
        ];

        // Create permissions
        foreach ($permissions as $permission => $description) {
            Permission::firstOrCreate([
                'name' => $permission,
                'guard_name' => 'web',
            ], [
                // 'description' => $description,
            ]);
        }

        // Create employee management roles
        $roles = [
            'Employee Manager' => [
                'employees.view',
                'employees.create',
                'employees.update',
                'employees.activate',
                'employees.deactivate',
                'employees.manage-performance',
                'employees.manage-time-off',
                'employees.view-analytics',
            ],
            'HR Manager' => [
                'employees.view',
                'employees.create',
                'employees.update',
                'employees.delete',
                'employees.restore',
                'employees.activate',
                'employees.deactivate',
                'employees.terminate',
                'employees.rehire',
                'employees.manage-salary',
                'employees.manage-benefits',
                'employees.manage-training',
                'employees.view-analytics',
                'employees.export-data',
                'employees.import-data',
                'employees.view-sensitive-data',
                'employees.manage-hierarchy',
                'employees.manage-emergency-contacts',
                'employees.manage-banking-info',
                'employees.manage-tax-info',
            ],
            'Employee Viewer' => [
                'employees.view',
                'employees.view-analytics',
            ],
        ];

        // Create roles and assign permissions
        foreach ($roles as $roleName => $rolePermissions) {
            $role = Role::firstOrCreate([
                'name' => $roleName,
                'guard_name' => 'web',
            ]);

            $role->syncPermissions($rolePermissions);
        }

        $this->command->info('Employee permissions and roles seeded successfully!');
    }
}
