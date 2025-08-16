<?php

namespace Fereydooni\Shopping\database\seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class OrderStatusHistoryPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create permissions
        $permissions = [
            // Basic CRUD permissions
            'order-status-history.view',
            'order-status-history.view.any',
            'order-status-history.view.own',
            'order-status-history.create',
            'order-status-history.create.any',
            'order-status-history.create.own',
            'order-status-history.update',
            'order-status-history.update.any',
            'order-status-history.update.own',
            'order-status-history.delete',
            'order-status-history.delete.any',
            'order-status-history.delete.own',

            // Search and export permissions
            'order-status-history.search',
            'order-status-history.search.any',
            'order-status-history.search.own',
            'order-status-history.export',
            'order-status-history.import',
            'order-status-history.validate',

            // Timeline permissions
            'order-status-history.timeline.view',
            'order-status-history.timeline.view.any',
            'order-status-history.timeline.view.own',

            // Analytics permissions
            'order-status-history.analytics.view',
            'order-status-history.analytics.view.any',
            'order-status-history.analytics.view.own',

            // Reports permissions
            'order-status-history.reports.view',
            'order-status-history.reports.generate',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Create roles
        $this->createOrderStatusHistoryManagerRole();
        $this->createOrderStatusHistoryViewerRole();
        $this->createOrderStatusHistoryAnalystRole();
        $this->createOrderManagerRole();
    }

    /**
     * Create order status history manager role with full permissions
     */
    private function createOrderStatusHistoryManagerRole(): void
    {
        $role = Role::firstOrCreate(['name' => 'order-status-history-manager']);

        $permissions = [
            'order-status-history.view',
            'order-status-history.view.any',
            'order-status-history.view.own',
            'order-status-history.create',
            'order-status-history.create.any',
            'order-status-history.create.own',
            'order-status-history.update',
            'order-status-history.update.any',
            'order-status-history.update.own',
            'order-status-history.delete',
            'order-status-history.delete.any',
            'order-status-history.delete.own',
            'order-status-history.search',
            'order-status-history.search.any',
            'order-status-history.search.own',
            'order-status-history.export',
            'order-status-history.import',
            'order-status-history.validate',
            'order-status-history.timeline.view',
            'order-status-history.timeline.view.any',
            'order-status-history.timeline.view.own',
            'order-status-history.analytics.view',
            'order-status-history.analytics.view.any',
            'order-status-history.analytics.view.own',
            'order-status-history.reports.view',
            'order-status-history.reports.generate',
        ];

        $role->syncPermissions($permissions);
    }

    /**
     * Create order status history viewer role with read-only permissions
     */
    private function createOrderStatusHistoryViewerRole(): void
    {
        $role = Role::firstOrCreate(['name' => 'order-status-history-viewer']);

        $permissions = [
            'order-status-history.view',
            'order-status-history.view.any',
            'order-status-history.view.own',
            'order-status-history.search',
            'order-status-history.search.any',
            'order-status-history.search.own',
            'order-status-history.timeline.view',
            'order-status-history.timeline.view.any',
            'order-status-history.timeline.view.own',
        ];

        $role->syncPermissions($permissions);
    }

    /**
     * Create order status history analyst role with analytics permissions
     */
    private function createOrderStatusHistoryAnalystRole(): void
    {
        $role = Role::firstOrCreate(['name' => 'order-status-history-analyst']);

        $permissions = [
            'order-status-history.view',
            'order-status-history.view.any',
            'order-status-history.view.own',
            'order-status-history.search',
            'order-status-history.search.any',
            'order-status-history.search.own',
            'order-status-history.timeline.view',
            'order-status-history.timeline.view.any',
            'order-status-history.timeline.view.own',
            'order-status-history.analytics.view',
            'order-status-history.analytics.view.any',
            'order-status-history.analytics.view.own',
            'order-status-history.reports.view',
            'order-status-history.reports.generate',
        ];

        $role->syncPermissions($permissions);
    }

    /**
     * Create order manager role with limited status history permissions
     */
    private function createOrderManagerRole(): void
    {
        $role = Role::firstOrCreate(['name' => 'order-manager']);

        $permissions = [
            'order-status-history.view',
            'order-status-history.view.any',
            'order-status-history.view.own',
            'order-status-history.create',
            'order-status-history.create.any',
            'order-status-history.create.own',
            'order-status-history.search',
            'order-status-history.search.any',
            'order-status-history.search.own',
            'order-status-history.timeline.view',
            'order-status-history.timeline.view.any',
            'order-status-history.timeline.view.own',
        ];

        $role->syncPermissions($permissions);
    }
}
