<?php

namespace Fereydooni\Shopping\database\seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class CustomerPreferencePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create permissions for customer preferences
        $permissions = [
            // Basic CRUD permissions
            'customer-preferences.viewAny',
            'customer-preferences.view',
            'customer-preferences.create',
            'customer-preferences.update',
            'customer-preferences.delete',
            'customer-preferences.restore',
            'customer-preferences.forceDelete',

            // Status management permissions
            'customer-preferences.activate',
            'customer-preferences.deactivate',

            // Preference-specific permissions
            'customer-preferences.setPreference',
            'customer-preferences.getPreference',
            'customer-preferences.removePreference',
            'customer-preferences.resetPreferences',
            'customer-preferences.importPreferences',
            'customer-preferences.exportPreferences',
            'customer-preferences.syncPreferences',

            // Analytics and reporting permissions
            'customer-preferences.viewAnalytics',
            'customer-preferences.viewStats',
            'customer-preferences.generateReports',

            // Template and management permissions
            'customer-preferences.manageTemplates',
            'customer-preferences.applyTemplate',
            'customer-preferences.backupRestore',
            'customer-preferences.initialize',

            // Customer-specific permissions
            'customers.preferences.view',
            'customers.preferences.manage',
            'customers.preferences.export',
            'customers.preferences.import',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        // Create roles and assign permissions
        $this->createRolesAndAssignPermissions();

        $this->command->info('Customer preference permissions seeded successfully.');
    }

    /**
     * Create roles and assign permissions.
     */
    private function createRolesAndAssignPermissions(): void
    {
        // Admin role - all permissions
        $adminRole = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $adminRole->givePermissionTo(Permission::where('guard_name', 'web')->get());

        // Customer preference manager role
        $preferenceManagerRole = Role::firstOrCreate(['name' => 'customer-preference-manager', 'guard_name' => 'web']);
        $preferenceManagerRole->givePermissionTo([
            'customer-preferences.viewAny',
            'customer-preferences.view',
            'customer-preferences.create',
            'customer-preferences.update',
            'customer-preferences.activate',
            'customer-preferences.deactivate',
            'customer-preferences.setPreference',
            'customer-preferences.getPreference',
            'customer-preferences.removePreference',
            'customer-preferences.resetPreferences',
            'customer-preferences.importPreferences',
            'customer-preferences.exportPreferences',
            'customer-preferences.syncPreferences',
            'customer-preferences.viewAnalytics',
            'customer-preferences.viewStats',
            'customer-preferences.manageTemplates',
            'customer-preferences.applyTemplate',
            'customers.preferences.view',
            'customers.preferences.manage',
            'customers.preferences.export',
            'customers.preferences.import',
        ]);

        // Customer service role
        $customerServiceRole = Role::firstOrCreate(['name' => 'customer-service', 'guard_name' => 'web']);
        $customerServiceRole->givePermissionTo([
            'customer-preferences.viewAny',
            'customer-preferences.view',
            'customer-preferences.setPreference',
            'customer-preferences.getPreference',
            'customer-preferences.removePreference',
            'customer-preferences.resetPreferences',
            'customers.preferences.view',
            'customers.preferences.manage',
        ]);

        // Customer role - limited permissions
        $customerRole = Role::firstOrCreate(['name' => 'customer', 'guard_name' => 'web']);
        $customerRole->givePermissionTo([
            'customer-preferences.view',
            'customer-preferences.setPreference',
            'customer-preferences.getPreference',
            'customer-preferences.removePreference',
            'customers.preferences.view',
        ]);

        // Analytics role
        $analyticsRole = Role::firstOrCreate(['name' => 'analytics', 'guard_name' => 'web']);
        $analyticsRole->givePermissionTo([
            'customer-preferences.viewAny',
            'customer-preferences.view',
            'customer-preferences.viewAnalytics',
            'customer-preferences.viewStats',
            'customer-preferences.generateReports',
        ]);

        $this->command->info('Customer preference roles created and permissions assigned.');
    }
}
