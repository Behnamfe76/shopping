<?php

namespace Fereydooni\Shopping\database\seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class ShipmentItemPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create permissions for shipment items
        $permissions = [
            // View permissions
            'shipment-item.view',
            'shipment-item.view.any',
            'shipment-item.view.own',

            // Create permissions
            'shipment-item.create',
            'shipment-item.create.any',
            'shipment-item.create.own',

            // Update permissions
            'shipment-item.update',
            'shipment-item.update.any',
            'shipment-item.update.own',

            // Delete permissions
            'shipment-item.delete',
            'shipment-item.delete.any',
            'shipment-item.delete.own',

            // Quantity management permissions
            'shipment-item.manage.quantity',
            'shipment-item.manage.quantity.any',
            'shipment-item.manage.quantity.own',

            // Search permissions
            'shipment-item.search',
            'shipment-item.search.any',
            'shipment-item.search.own',

            // Export/Import permissions
            'shipment-item.export',
            'shipment-item.import',

            // Validation permissions
            'shipment-item.validate',

            // Calculation permissions
            'shipment-item.calculate.weight',
            'shipment-item.calculate.volume',
        ];

        // Create permissions
        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        // Create roles and assign permissions
        $this->createShipmentItemManagerRole();
        $this->createShipmentItemEditorRole();
        $this->createShipmentItemViewerRole();
        $this->createWarehouseManagerRole();
    }

    /**
     * Create shipment item manager role with full permissions
     */
    private function createShipmentItemManagerRole(): void
    {
        $role = Role::firstOrCreate(['name' => 'shipment-item-manager', 'guard_name' => 'web']);

        $permissions = [
            'shipment-item.view.any',
            'shipment-item.create.any',
            'shipment-item.update.any',
            'shipment-item.delete.any',
            'shipment-item.manage.quantity.any',
            'shipment-item.search.any',
            'shipment-item.export',
            'shipment-item.import',
            'shipment-item.validate',
            'shipment-item.calculate.weight',
            'shipment-item.calculate.volume',
        ];

        $role->syncPermissions($permissions);
    }

    /**
     * Create shipment item editor role with limited permissions
     */
    private function createShipmentItemEditorRole(): void
    {
        $role = Role::firstOrCreate(['name' => 'shipment-item-editor', 'guard_name' => 'web']);

        $permissions = [
            'shipment-item.view.any',
            'shipment-item.create.any',
            'shipment-item.update.any',
            'shipment-item.manage.quantity.any',
            'shipment-item.search.any',
            'shipment-item.validate',
            'shipment-item.calculate.weight',
            'shipment-item.calculate.volume',
        ];

        $role->syncPermissions($permissions);
    }

    /**
     * Create shipment item viewer role with read-only permissions
     */
    private function createShipmentItemViewerRole(): void
    {
        $role = Role::firstOrCreate(['name' => 'shipment-item-viewer', 'guard_name' => 'web']);

        $permissions = [
            'shipment-item.view.any',
            'shipment-item.search.any',
            'shipment-item.calculate.weight',
            'shipment-item.calculate.volume',
        ];

        $role->syncPermissions($permissions);
    }

    /**
     * Create warehouse manager role with shipment-specific permissions
     */
    private function createWarehouseManagerRole(): void
    {
        $role = Role::firstOrCreate(['name' => 'warehouse-manager', 'guard_name' => 'web']);

        $permissions = [
            'shipment-item.view.own',
            'shipment-item.create.own',
            'shipment-item.update.own',
            'shipment-item.manage.quantity.own',
            'shipment-item.search.own',
            'shipment-item.validate',
            'shipment-item.calculate.weight',
            'shipment-item.calculate.volume',
        ];

        $role->syncPermissions($permissions);
    }
}
