<?php

namespace Fereydooni\Shopping\database\seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class UserPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create users permissions
        $permissions = [
            // Basic CRUD permissions
            'users.viewAny',
            'users.view',
            'users.create',
            'users.update',
            'users.delete',
            'users.restore',
            'users.forceDelete',

            // users status management
            'users.activate',
            'users.deactivate',
            'users.suspend',

            // Loyalty program management
            'users.manageLoyaltyPoints',

            // Analytics and reporting
            'users.viewAnalytics',
            'users.viewStats',
            'users.viewLifetimeValue',

            // Data management
            'users.exportData',
            'users.importData',

            // users notes
            'users.viewNotes',
            'users.addNotes',

            // users preferences
            'users.viewPreferences',
            'users.updatePreferences',

            // users relationships
            'users.viewOrderHistory',
            'users.viewAddresses',
            'users.viewReviews',
            'users.viewWishlist',

            // Marketing
            'users.manageMarketing',

            // Search
            'users.search',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Create users management role
        $usersManagerRole = Role::firstOrCreate(['name' => 'users-manager']);

        // Assign permissions to users manager role
        $usersManagerRole->givePermissionTo([
            'users.viewAny',
            'users.view',
            'users.create',
            'users.update',
            'users.activate',
            'users.deactivate',
            'users.suspend',
            'users.manageLoyaltyPoints',
            'users.viewAnalytics',
            'users.viewStats',
            'users.viewNotes',
            'users.addNotes',
            'users.viewPreferences',
            'users.updatePreferences',
            'users.viewOrderHistory',
            'users.viewAddresses',
            'users.viewReviews',
            'users.viewWishlist',
            'users.manageMarketing',
            'users.search',
        ]);

        // Create users admin role
        $usersAdminRole = Role::firstOrCreate(['name' => 'users-admin']);

        // Assign all permissions to users admin role
        $usersAdminRole->givePermissionTo($permissions);

        // Create users viewer role
        $usersViewerRole = Role::firstOrCreate(['name' => 'users-viewer']);

        // Assign read-only permissions to users viewer role
        $usersViewerRole->givePermissionTo([
            'users.viewAny',
            'users.view',
            'users.viewAnalytics',
            'users.viewStats',
            'users.viewNotes',
            'users.viewPreferences',
            'users.viewOrderHistory',
            'users.viewAddresses',
            'users.viewReviews',
            'users.viewWishlist',
            'users.search',
        ]);

        // Create users support role
        $usersSupportRole = Role::firstOrCreate(['name' => 'users-support']);

        // Assign support-related permissions
        $usersSupportRole->givePermissionTo([
            'users.viewAny',
            'users.view',
            'users.update',
            'users.activate',
            'users.deactivate',
            'users.manageLoyaltyPoints',
            'users.viewNotes',
            'users.addNotes',
            'users.viewPreferences',
            'users.updatePreferences',
            'users.viewOrderHistory',
            'users.viewAddresses',
            'users.viewReviews',
            'users.viewWishlist',
            'users.search',
        ]);
    }
}
