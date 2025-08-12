<?php

namespace Fereydooni\Shopping\database\seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class AddressPermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            // View permissions
            'address.view',
            'address.view.any',
            'address.view.own',

            // Create permissions
            'address.create',
            'address.create.any',
            'address.create.own',

            // Update permissions
            'address.update',
            'address.update.any',
            'address.update.own',

            // Delete permissions
            'address.delete',
            'address.delete.any',
            'address.delete.own',

            // Default address permissions
            'address.set.default',
            'address.set.default.any',
            'address.set.default.own',
            'address.unset.default',
            'address.unset.default.any',
            'address.unset.default.own',

            // Search permissions
            'address.search',
            'address.search.any',
            'address.search.own',

            // Type-specific permissions
            'address.view.billing',
            'address.view.shipping',
            'address.create.billing',
            'address.create.shipping',
            'address.update.billing',
            'address.update.shipping',
            'address.delete.billing',
            'address.delete.shipping',

            // Bulk operations
            'address.bulk.delete',
            'address.bulk.update',
            'address.bulk.set.default',

            // Import/Export permissions
            'address.export',
            'address.import',

            // Validation permissions
            'address.validate',
            'address.validate.any',
            'address.validate.own',

            // Statistics permissions
            'address.view.stats',
            'address.view.stats.any',
            'address.view.stats.own',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        $this->createRoles();
    }

    private function createRoles(): void
    {
        // Super Admin - Full access to everything
        $superAdmin = Role::firstOrCreate(['name' => 'super-admin', 'guard_name' => 'web']);
        $superAdmin->givePermissionTo(Permission::all());

        // Address Manager - Full address management for all users
        $addressManager = Role::firstOrCreate(['name' => 'address-manager', 'guard_name' => 'web']);
        $addressManager->givePermissionTo([
            'address.view.any',
            'address.create.any',
            'address.update.any',
            'address.delete.any',
            'address.set.default.any',
            'address.unset.default.any',
            'address.search.any',
            'address.view.billing',
            'address.view.shipping',
            'address.create.billing',
            'address.create.shipping',
            'address.update.billing',
            'address.update.shipping',
            'address.delete.billing',
            'address.delete.shipping',
            'address.bulk.delete',
            'address.bulk.update',
            'address.bulk.set.default',
            'address.export',
            'address.import',
            'address.validate.any',
            'address.view.stats.any',
        ]);

        // Address Editor - Can edit addresses but not delete
        $addressEditor = Role::firstOrCreate(['name' => 'address-editor', 'guard_name' => 'web']);
        $addressEditor->givePermissionTo([
            'address.view.any',
            'address.create.any',
            'address.update.any',
            'address.set.default.any',
            'address.unset.default.any',
            'address.search.any',
            'address.view.billing',
            'address.view.shipping',
            'address.create.billing',
            'address.create.shipping',
            'address.update.billing',
            'address.update.shipping',
            'address.bulk.update',
            'address.bulk.set.default',
            'address.export',
            'address.validate.any',
            'address.view.stats.any',
        ]);

        // Address Viewer - Read-only access to addresses
        $addressViewer = Role::firstOrCreate(['name' => 'address-viewer', 'guard_name' => 'web']);
        $addressViewer->givePermissionTo([
            'address.view.any',
            'address.search.any',
            'address.view.billing',
            'address.view.shipping',
            'address.export',
            'address.view.stats.any',
        ]);

        // Customer - Can manage own addresses only
        $customer = Role::firstOrCreate(['name' => 'customer', 'guard_name' => 'web']);
        $customer->givePermissionTo([
            'address.view.own',
            'address.create.own',
            'address.update.own',
            'address.delete.own',
            'address.set.default.own',
            'address.unset.default.own',
            'address.search.own',
            'address.view.billing',
            'address.view.shipping',
            'address.create.billing',
            'address.create.shipping',
            'address.update.billing',
            'address.update.shipping',
            'address.delete.billing',
            'address.delete.shipping',
            'address.validate.own',
            'address.view.stats.own',
        ]);

        // Billing Manager - Can manage billing addresses
        $billingManager = Role::firstOrCreate(['name' => 'billing-manager', 'guard_name' => 'web']);
        $billingManager->givePermissionTo([
            'address.view.any',
            'address.create.billing',
            'address.update.billing',
            'address.delete.billing',
            'address.set.default.any',
            'address.search.any',
            'address.view.billing',
            'address.export',
            'address.validate.any',
            'address.view.stats.any',
        ]);

        // Shipping Manager - Can manage shipping addresses
        $shippingManager = Role::firstOrCreate(['name' => 'shipping-manager', 'guard_name' => 'web']);
        $shippingManager->givePermissionTo([
            'address.view.any',
            'address.create.shipping',
            'address.update.shipping',
            'address.delete.shipping',
            'address.set.default.any',
            'address.search.any',
            'address.view.shipping',
            'address.export',
            'address.validate.any',
            'address.view.stats.any',
        ]);

        // Store Manager - Can manage addresses for their store
        $storeManager = Role::firstOrCreate(['name' => 'store-manager', 'guard_name' => 'web']);
        $storeManager->givePermissionTo([
            'address.view.any',
            'address.create.any',
            'address.update.any',
            'address.delete.any',
            'address.set.default.any',
            'address.unset.default.any',
            'address.search.any',
            'address.view.billing',
            'address.view.shipping',
            'address.create.billing',
            'address.create.shipping',
            'address.update.billing',
            'address.update.shipping',
            'address.delete.billing',
            'address.delete.shipping',
            'address.bulk.update',
            'address.bulk.set.default',
            'address.export',
            'address.validate.any',
            'address.view.stats.any',
        ]);
    }
}
