<?php

namespace Fereydooni\Shopping\database\seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class OrderItemPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create permissions
        $permissions = [
            // View permissions
            'order-item.view',
            'order-item.view.any',
            'order-item.view.own',

            // Create permissions
            'order-item.create',
            'order-item.create.any',
            'order-item.create.own',

            // Update permissions
            'order-item.update',
            'order-item.update.any',
            'order-item.update.own',

            // Delete permissions
            'order-item.delete',
            'order-item.delete.any',
            'order-item.delete.own',

            // Shipping permissions
            'order-item.mark.shipped',
            'order-item.mark.shipped.any',
            'order-item.mark.shipped.own',

            // Return permissions
            'order-item.mark.returned',
            'order-item.mark.returned.any',
            'order-item.mark.returned.own',

            // Refund permissions
            'order-item.process.refund',
            'order-item.process.refund.any',
            'order-item.process.refund.own',

            // Search permissions
            'order-item.search',
            'order-item.search.any',
            'order-item.search.own',

            // Import/Export permissions
            'order-item.export',
            'order-item.import',

            // Validation permissions
            'order-item.validate',

            // Inventory permissions
            'order-item.inventory.manage',
            'order-item.inventory.view',

            // Report permissions
            'order-item.reports.view',
            'order-item.reports.generate',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        // Create roles
        $this->createOrderItemManagerRole();
        $this->createOrderItemProcessorRole();
        $this->createOrderItemViewerRole();
        $this->createInventoryManagerRole();
    }

    /**
     * Create order item manager role with full permissions
     */
    private function createOrderItemManagerRole(): void
    {
        $role = Role::firstOrCreate(['name' => 'order-item-manager', 'guard_name' => 'web']);

        $permissions = [
            'order-item.view.any',
            'order-item.create.any',
            'order-item.update.any',
            'order-item.delete.any',
            'order-item.mark.shipped.any',
            'order-item.mark.returned.any',
            'order-item.process.refund.any',
            'order-item.search.any',
            'order-item.export',
            'order-item.import',
            'order-item.validate',
            'order-item.inventory.manage',
            'order-item.inventory.view',
            'order-item.reports.view',
            'order-item.reports.generate',
        ];

        $role->syncPermissions($permissions);
    }

    /**
     * Create order item processor role
     */
    private function createOrderItemProcessorRole(): void
    {
        $role = Role::firstOrCreate(['name' => 'order-item-processor', 'guard_name' => 'web']);

        $permissions = [
            'order-item.view.any',
            'order-item.create.any',
            'order-item.update.any',
            'order-item.mark.shipped.any',
            'order-item.mark.returned.any',
            'order-item.process.refund.any',
            'order-item.search.any',
            'order-item.validate',
            'order-item.inventory.view',
            'order-item.reports.view',
        ];

        $role->syncPermissions($permissions);
    }

    /**
     * Create order item viewer role (read-only)
     */
    private function createOrderItemViewerRole(): void
    {
        $role = Role::firstOrCreate(['name' => 'order-item-viewer', 'guard_name' => 'web']);

        $permissions = [
            'order-item.view.any',
            'order-item.search.any',
            'order-item.inventory.view',
            'order-item.reports.view',
        ];

        $role->syncPermissions($permissions);
    }

    /**
     * Create inventory manager role
     */
    private function createInventoryManagerRole(): void
    {
        $role = Role::firstOrCreate(['name' => 'inventory-manager', 'guard_name' => 'web']);

        $permissions = [
            'order-item.view.any',
            'order-item.update.any',
            'order-item.mark.shipped.any',
            'order-item.mark.returned.any',
            'order-item.search.any',
            'order-item.validate',
            'order-item.inventory.manage',
            'order-item.inventory.view',
            'order-item.reports.view',
            'order-item.reports.generate',
        ];

        $role->syncPermissions($permissions);
    }
}
