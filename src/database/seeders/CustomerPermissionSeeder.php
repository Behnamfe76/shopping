<?php

namespace Fereydooni\Shopping\database\seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class CustomerPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create customer permissions
        $permissions = [
            // Basic CRUD permissions
            'customers.viewAny',
            'customers.view',
            'customers.create',
            'customers.update',
            'customers.delete',
            'customers.restore',
            'customers.forceDelete',

            // Customer status management
            'customers.activate',
            'customers.deactivate',
            'customers.suspend',

            // Loyalty program management
            'customers.manageLoyaltyPoints',

            // Analytics and reporting
            'customers.viewAnalytics',
            'customers.viewStats',
            'customers.viewLifetimeValue',

            // Data management
            'customers.exportData',
            'customers.importData',

            // Customer notes
            'customers.viewNotes',
            'customers.addNotes',

            // Customer preferences
            'customers.viewPreferences',
            'customers.updatePreferences',

            // Customer relationships
            'customers.viewOrderHistory',
            'customers.viewAddresses',
            'customers.viewReviews',
            'customers.viewWishlist',

            // Marketing
            'customers.manageMarketing',

            // Search
            'customers.search',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Create customer management role
        $customerManagerRole = Role::firstOrCreate(['name' => 'customer-manager']);

        // Assign permissions to customer manager role
        $customerManagerRole->givePermissionTo([
            'customers.viewAny',
            'customers.view',
            'customers.create',
            'customers.update',
            'customers.activate',
            'customers.deactivate',
            'customers.suspend',
            'customers.manageLoyaltyPoints',
            'customers.viewAnalytics',
            'customers.viewStats',
            'customers.viewNotes',
            'customers.addNotes',
            'customers.viewPreferences',
            'customers.updatePreferences',
            'customers.viewOrderHistory',
            'customers.viewAddresses',
            'customers.viewReviews',
            'customers.viewWishlist',
            'customers.manageMarketing',
            'customers.search',
        ]);

        // Create customer admin role
        $customerAdminRole = Role::firstOrCreate(['name' => 'customer-admin']);

        // Assign all permissions to customer admin role
        $customerAdminRole->givePermissionTo($permissions);

        // Create customer viewer role
        $customerViewerRole = Role::firstOrCreate(['name' => 'customer-viewer']);

        // Assign read-only permissions to customer viewer role
        $customerViewerRole->givePermissionTo([
            'customers.viewAny',
            'customers.view',
            'customers.viewAnalytics',
            'customers.viewStats',
            'customers.viewNotes',
            'customers.viewPreferences',
            'customers.viewOrderHistory',
            'customers.viewAddresses',
            'customers.viewReviews',
            'customers.viewWishlist',
            'customers.search',
        ]);

        // Create customer support role
        $customerSupportRole = Role::firstOrCreate(['name' => 'customer-support']);

        // Assign support-related permissions
        $customerSupportRole->givePermissionTo([
            'customers.viewAny',
            'customers.view',
            'customers.update',
            'customers.activate',
            'customers.deactivate',
            'customers.manageLoyaltyPoints',
            'customers.viewNotes',
            'customers.addNotes',
            'customers.viewPreferences',
            'customers.updatePreferences',
            'customers.viewOrderHistory',
            'customers.viewAddresses',
            'customers.viewReviews',
            'customers.viewWishlist',
            'customers.search',
        ]);
    }
}
