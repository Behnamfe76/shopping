<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class CustomerSegmentPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create permissions for customer segments
        $permissions = [
            'customer-segments.viewAny',
            'customer-segments.view',
            'customer-segments.create',
            'customer-segments.update',
            'customer-segments.delete',
            'customer-segments.restore',
            'customer-segments.forceDelete',
            'customer-segments.activate',
            'customer-segments.deactivate',
            'customer-segments.makeAutomatic',
            'customer-segments.makeManual',
            'customer-segments.makeDynamic',
            'customer-segments.makeStatic',
            'customer-segments.setPriority',
            'customer-segments.calculateCustomers',
            'customer-segments.recalculateAll',
            'customer-segments.addCustomer',
            'customer-segments.removeCustomer',
            'customer-segments.updateCriteria',
            'customer-segments.updateConditions',
            'customer-segments.validateCriteria',
            'customer-segments.validateConditions',
            'customer-segments.viewAnalytics',
            'customer-segments.exportData',
            'customer-segments.importData',
            'customer-segments.duplicate',
            'customer-segments.merge',
            'customer-segments.split',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Assign permissions to roles
        $this->assignPermissionsToRoles();
    }

    /**
     * Assign permissions to roles.
     */
    private function assignPermissionsToRoles(): void
    {
        // Admin role - all permissions
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $adminRole->givePermissionTo(Permission::where('name', 'like', 'customer-segments.%')->get());

        // Manager role - most permissions except delete and force delete
        $managerRole = Role::firstOrCreate(['name' => 'manager']);
        $managerPermissions = Permission::where('name', 'like', 'customer-segments.%')
            ->whereNotIn('name', ['customer-segments.delete', 'customer-segments.forceDelete'])
            ->get();
        $managerRole->givePermissionTo($managerPermissions);

        // Analyst role - view and analytics permissions
        $analystRole = Role::firstOrCreate(['name' => 'analyst']);
        $analystPermissions = Permission::whereIn('name', [
            'customer-segments.viewAny',
            'customer-segments.view',
            'customer-segments.viewAnalytics',
            'customer-segments.calculateCustomers',
            'customer-segments.recalculateAll',
            'customer-segments.validateCriteria',
            'customer-segments.validateConditions',
        ])->get();
        $analystRole->givePermissionTo($analystPermissions);

        // Marketing role - create, update, and marketing-related permissions
        $marketingRole = Role::firstOrCreate(['name' => 'marketing']);
        $marketingPermissions = Permission::whereIn('name', [
            'customer-segments.viewAny',
            'customer-segments.view',
            'customer-segments.create',
            'customer-segments.update',
            'customer-segments.activate',
            'customer-segments.deactivate',
            'customer-segments.makeAutomatic',
            'customer-segments.makeManual',
            'customer-segments.makeDynamic',
            'customer-segments.makeStatic',
            'customer-segments.setPriority',
            'customer-segments.calculateCustomers',
            'customer-segments.addCustomer',
            'customer-segments.removeCustomer',
            'customer-segments.updateCriteria',
            'customer-segments.updateConditions',
            'customer-segments.viewAnalytics',
            'customer-segments.exportData',
            'customer-segments.importData',
            'customer-segments.duplicate',
            'customer-segments.merge',
            'customer-segments.split',
        ])->get();
        $marketingRole->givePermissionTo($marketingPermissions);

        // Support role - view permissions only
        $supportRole = Role::firstOrCreate(['name' => 'support']);
        $supportPermissions = Permission::whereIn('name', [
            'customer-segments.viewAny',
            'customer-segments.view',
        ])->get();
        $supportRole->givePermissionTo($supportPermissions);
    }
}
