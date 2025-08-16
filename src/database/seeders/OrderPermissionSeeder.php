<?php

namespace Fereydooni\Shopping\database\seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class OrderPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create permissions
        $permissions = [
            // View permissions
            'order.view',
            'order.view.any',
            'order.view.own',

            // Create permissions
            'order.create',
            'order.create.any',
            'order.create.own',

            // Update permissions
            'order.update',
            'order.update.any',
            'order.update.own',

            // Delete permissions
            'order.delete',
            'order.delete.any',
            'order.delete.own',

            // Cancel permissions
            'order.cancel',
            'order.cancel.any',
            'order.cancel.own',

            // Payment permissions
            'order.mark.paid',
            'order.mark.paid.any',
            'order.mark.paid.own',

            // Shipping permissions
            'order.mark.shipped',
            'order.mark.shipped.any',
            'order.mark.shipped.own',

            // Completion permissions
            'order.mark.completed',
            'order.mark.completed.any',
            'order.mark.completed.own',

            // Search permissions
            'order.search',
            'order.search.any',
            'order.search.own',

            // Export/Import permissions
            'order.export',
            'order.import',

            // Validation permissions
            'order.validate',

            // Refund permissions
            'order.refund',
            'order.refund.any',
            'order.refund.own',

            // Notes permissions
            'order.notes.add',
            'order.notes.view',
            'order.notes.delete',

            // Reports permissions
            'order.reports.view',
            'order.reports.generate',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        // Create roles
        $this->createOrderManagerRole();
        $this->createOrderProcessorRole();
        $this->createOrderViewerRole();
        $this->createCustomerRole();
    }

    /**
     * Create order manager role with full permissions
     */
    private function createOrderManagerRole(): void
    {
        $role = Role::firstOrCreate(['name' => 'order-manager', 'guard_name' => 'web']);

        $permissions = Permission::whereIn('name', [
            'order.view.any',
            'order.create.any',
            'order.update.any',
            'order.delete.any',
            'order.cancel.any',
            'order.mark.paid.any',
            'order.mark.shipped.any',
            'order.mark.completed.any',
            'order.search.any',
            'order.export',
            'order.import',
            'order.validate',
            'order.refund.any',
            'order.notes.add',
            'order.notes.view',
            'order.notes.delete',
            'order.reports.view',
            'order.reports.generate',
        ])->get();

        $role->syncPermissions($permissions);
    }

    /**
     * Create order processor role with processing permissions
     */
    private function createOrderProcessorRole(): void
    {
        $role = Role::firstOrCreate(['name' => 'order-processor', 'guard_name' => 'web']);

        $permissions = Permission::whereIn('name', [
            'order.view.any',
            'order.update.any',
            'order.mark.paid.any',
            'order.mark.shipped.any',
            'order.mark.completed.any',
            'order.search.any',
            'order.validate',
            'order.notes.add',
            'order.notes.view',
            'order.reports.view',
        ])->get();

        $role->syncPermissions($permissions);
    }

    /**
     * Create order viewer role with read-only permissions
     */
    private function createOrderViewerRole(): void
    {
        $role = Role::firstOrCreate(['name' => 'order-viewer', 'guard_name' => 'web']);

        $permissions = Permission::whereIn('name', [
            'order.view.any',
            'order.search.any',
            'order.notes.view',
            'order.reports.view',
        ])->get();

        $role->syncPermissions($permissions);
    }

    /**
     * Create customer role with own order permissions
     */
    private function createCustomerRole(): void
    {
        $role = Role::firstOrCreate(['name' => 'customer', 'guard_name' => 'web']);

        $permissions = Permission::whereIn('name', [
            'order.view.own',
            'order.create.own',
            'order.update.own',
            'order.cancel.own',
            'order.search.own',
            'order.notes.view',
        ])->get();

        $role->syncPermissions($permissions);
    }
}
