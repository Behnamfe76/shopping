<?php

namespace Fereydooni\Shopping\database\seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class CustomerWishlistPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create permissions for customer wishlist operations
        $permissions = [
            'customer-wishlists.viewAny' => 'View all customer wishlists',
            'customer-wishlists.view' => 'View customer wishlist details',
            'customer-wishlists.create' => 'Create customer wishlist',
            'customer-wishlists.update' => 'Update customer wishlist',
            'customer-wishlists.delete' => 'Delete customer wishlist',
            'customer-wishlists.restore' => 'Restore deleted customer wishlist',
            'customer-wishlists.forceDelete' => 'Permanently delete customer wishlist',
            'customer-wishlists.addToWishlist' => 'Add product to customer wishlist',
            'customer-wishlists.removeFromWishlist' => 'Remove product from customer wishlist',
            'customer-wishlists.makePublic' => 'Make customer wishlist public',
            'customer-wishlists.makePrivate' => 'Make customer wishlist private',
            'customer-wishlists.setPriority' => 'Set customer wishlist priority',
            'customer-wishlists.markNotified' => 'Mark customer wishlist as notified',
            'customer-wishlists.updatePrice' => 'Update customer wishlist price',
            'customer-wishlists.checkPriceDrop' => 'Check customer wishlist price drop',
            'customer-wishlists.clearWishlist' => 'Clear customer wishlist',
            'customer-wishlists.exportWishlist' => 'Export customer wishlist',
            'customer-wishlists.importWishlist' => 'Import customer wishlist',
            'customer-wishlists.duplicateWishlist' => 'Duplicate customer wishlist',
            'customer-wishlists.viewAnalytics' => 'View customer wishlist analytics',
        ];

        foreach ($permissions as $permission => $description) {
            Permission::firstOrCreate([
                'name' => $permission,
                'guard_name' => 'web',
            ], [
                // 'description' => $description,
            ]);
        }

        // Assign permissions to roles
        $this->assignPermissionsToRoles();

        $this->command->info('Customer wishlist permissions seeded successfully.');
    }

    /**
     * Assign permissions to roles
     */
    private function assignPermissionsToRoles(): void
    {
        // Super Admin - All permissions
        $superAdminRole = Role::where('name', 'super-admin')->first();
        if ($superAdminRole) {
            $superAdminRole->givePermissionTo(Permission::where('name', 'like', 'customer-wishlists.%')->get());
        }

        // Admin - Most permissions except force delete
        $adminRole = Role::where('name', 'admin')->first();
        if ($adminRole) {
            $adminPermissions = Permission::where('name', 'like', 'customer-wishlists.%')
                ->where('name', '!=', 'customer-wishlists.forceDelete')
                ->get();
            $adminRole->givePermissionTo($adminPermissions);
        }

        // Manager - View, create, update, basic operations
        $managerRole = Role::where('name', 'manager')->first();
        if ($managerRole) {
            $managerPermissions = Permission::whereIn('name', [
                'customer-wishlists.viewAny',
                'customer-wishlists.view',
                'customer-wishlists.create',
                'customer-wishlists.update',
                'customer-wishlists.addToWishlist',
                'customer-wishlists.removeFromWishlist',
                'customer-wishlists.makePublic',
                'customer-wishlists.makePrivate',
                'customer-wishlists.setPriority',
                'customer-wishlists.markNotified',
                'customer-wishlists.updatePrice',
                'customer-wishlists.checkPriceDrop',
                'customer-wishlists.exportWishlist',
                'customer-wishlists.viewAnalytics',
            ])->get();
            $managerRole->givePermissionTo($managerPermissions);
        }

        // Customer Service - View and basic operations
        $customerServiceRole = Role::where('name', 'customer-service')->first();
        if ($customerServiceRole) {
            $customerServicePermissions = Permission::whereIn('name', [
                'customer-wishlists.viewAny',
                'customer-wishlists.view',
                'customer-wishlists.addToWishlist',
                'customer-wishlists.removeFromWishlist',
                'customer-wishlists.makePublic',
                'customer-wishlists.makePrivate',
                'customer-wishlists.setPriority',
                'customer-wishlists.markNotified',
                'customer-wishlists.updatePrice',
                'customer-wishlists.checkPriceDrop',
                'customer-wishlists.exportWishlist',
            ])->get();
            $customerServiceRole->givePermissionTo($customerServicePermissions);
        }

        // Customer - Own wishlist operations
        $customerRole = Role::where('name', 'customer')->first();
        if ($customerRole) {
            $customerPermissions = Permission::whereIn('name', [
                'customer-wishlists.view',
                'customer-wishlists.create',
                'customer-wishlists.update',
                'customer-wishlists.delete',
                'customer-wishlists.addToWishlist',
                'customer-wishlists.removeFromWishlist',
                'customer-wishlists.makePublic',
                'customer-wishlists.makePrivate',
                'customer-wishlists.setPriority',
                'customer-wishlists.markNotified',
                'customer-wishlists.updatePrice',
                'customer-wishlists.checkPriceDrop',
                'customer-wishlists.clearWishlist',
                'customer-wishlists.exportWishlist',
                'customer-wishlists.importWishlist',
                'customer-wishlists.duplicateWishlist',
                'customer-wishlists.viewAnalytics',
            ])->get();
            $customerRole->givePermissionTo($customerPermissions);
        }
    }
}
