<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use App\Permissions\EmployeeSalaryHistoryPermissions;

class EmployeeSalaryHistoryPermissionSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('Seeding Employee Salary History permissions...');

        // Create permissions
        $this->createPermissions();

        // Assign permissions to roles
        $this->assignPermissionsToRoles();

        $this->command->info('Employee Salary History permissions seeded successfully!');
    }

    private function createPermissions(): void
    {
        $permissions = EmployeeSalaryHistoryPermissions::getPermissions();

        foreach ($permissions as $permission => $description) {
            Permission::firstOrCreate([
                'name' => $permission,
                'guard_name' => 'web',
            ], [
                'description' => $description,
            ]);
        }
    }

    private function assignPermissionsToRoles(): void
    {
        $rolePermissions = EmployeeSalaryHistoryPermissions::getRolePermissions();

        foreach ($rolePermissions as $roleName => $permissions) {
            $role = Role::firstOrCreate(['name' => $roleName, 'guard_name' => 'web']);

            foreach ($permissions as $permission) {
                $permissionModel = Permission::where('name', $permission)->first();
                if ($permissionModel) {
                    $role->givePermissionTo($permissionModel);
                }
            }
        }

        // Assign default permissions to all authenticated users
        $defaultPermissions = EmployeeSalaryHistoryPermissions::getDefaultPermissions();
        $authenticatedRole = Role::firstOrCreate(['name' => 'authenticated', 'guard_name' => 'web']);

        foreach ($defaultPermissions as $permission) {
            $permissionModel = Permission::where('name', $permission)->first();
            if ($permissionModel) {
                $authenticatedRole->givePermissionTo($permissionModel);
            }
        }
    }
}
