<?php

namespace Fereydooni\Shopping\database\seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class ShipmentPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create permissions
        $permissions = [
            // View permissions
            'shipment.view',
            'shipment.view.any',
            'shipment.view.own',

            // Create permissions
            'shipment.create',
            'shipment.create.any',
            'shipment.create.own',

            // Update permissions
            'shipment.update',
            'shipment.update.any',
            'shipment.update.own',

            // Delete permissions
            'shipment.delete',
            'shipment.delete.any',
            'shipment.delete.own',

            // Ship permissions
            'shipment.ship',
            'shipment.ship.any',
            'shipment.ship.own',

            // Deliver permissions
            'shipment.deliver',
            'shipment.deliver.any',
            'shipment.deliver.own',

            // Return permissions
            'shipment.return',
            'shipment.return.any',
            'shipment.return.own',

            // Track permissions
            'shipment.track',
            'shipment.track.any',
            'shipment.track.own',

            // Label permissions
            'shipment.label',
            'shipment.label.any',
            'shipment.label.own',

            // Pickup permissions
            'shipment.pickup',
            'shipment.pickup.any',
            'shipment.pickup.own',

            // Search permissions
            'shipment.search',
            'shipment.search.any',
            'shipment.search.own',

            // Export/Import permissions
            'shipment.export',
            'shipment.import',

            // Analytics permissions
            'shipment.analytics.view',
            'shipment.analytics.view.any',
            'shipment.analytics.view.own',

            // Reports permissions
            'shipment.reports.view',
            'shipment.reports.generate',

            // Tracking permissions
            'shipment.tracking.update',
            'shipment.tracking.view',
            'shipment.tracking.validate',

            // Delivery permissions
            'shipment.delivery.update',
            'shipment.delivery.confirm',
            'shipment.delivery.optimize',

            // Carrier permissions
            'shipment.carrier.select',
            'shipment.carrier.rates',
            'shipment.carrier.performance',

            // Cost permissions
            'shipment.cost.calculate',
            'shipment.cost.view',
            'shipment.cost.optimize',

            // Insurance permissions
            'shipment.insurance.add',
            'shipment.insurance.remove',
            'shipment.insurance.view',

            // Package permissions
            'shipment.package.weigh',
            'shipment.package.dimension',
            'shipment.package.count',

            // Status permissions
            'shipment.status.change',
            'shipment.status.view',
            'shipment.status.history',

            // Notes permissions
            'shipment.notes.add',
            'shipment.notes.view',
            'shipment.notes.delete',

            // Alerts permissions
            'shipment.alerts.view',
            'shipment.alerts.create',
            'shipment.alerts.manage',

            // Dashboard permissions
            'shipment.dashboard.view',
            'shipment.dashboard.manage',

            // Bulk operations permissions
            'shipment.bulk.create',
            'shipment.bulk.update',
            'shipment.bulk.delete',
            'shipment.bulk.ship',
            'shipment.bulk.deliver',

            // API permissions
            'shipment.api.access',
            'shipment.api.create',
            'shipment.api.update',
            'shipment.api.delete',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        // Create roles
        $this->createShipmentManagerRole();
        $this->createShipmentDispatcherRole();
        $this->createShipmentViewerRole();
        $this->createShipmentCreatorRole();
        $this->createShipmentTrackerRole();
        $this->createShipmentAnalystRole();
    }

    /**
     * Create shipment manager role with full permissions
     */
    private function createShipmentManagerRole(): void
    {
        $role = Role::firstOrCreate(['name' => 'shipment-manager', 'guard_name' => 'web']);

        $permissions = [
            'shipment.view.any',
            'shipment.create.any',
            'shipment.update.any',
            'shipment.delete.any',
            'shipment.ship.any',
            'shipment.deliver.any',
            'shipment.return.any',
            'shipment.track.any',
            'shipment.label.any',
            'shipment.pickup.any',
            'shipment.search.any',
            'shipment.export',
            'shipment.import',
            'shipment.analytics.view.any',
            'shipment.reports.view',
            'shipment.reports.generate',
            'shipment.tracking.update',
            'shipment.tracking.view',
            'shipment.tracking.validate',
            'shipment.delivery.update',
            'shipment.delivery.confirm',
            'shipment.delivery.optimize',
            'shipment.carrier.select',
            'shipment.carrier.rates',
            'shipment.carrier.performance',
            'shipment.cost.calculate',
            'shipment.cost.view',
            'shipment.cost.optimize',
            'shipment.insurance.add',
            'shipment.insurance.remove',
            'shipment.insurance.view',
            'shipment.package.weigh',
            'shipment.package.dimension',
            'shipment.package.count',
            'shipment.status.change',
            'shipment.status.view',
            'shipment.status.history',
            'shipment.notes.add',
            'shipment.notes.view',
            'shipment.notes.delete',
            'shipment.alerts.view',
            'shipment.alerts.create',
            'shipment.alerts.manage',
            'shipment.dashboard.view',
            'shipment.dashboard.manage',
            'shipment.bulk.create',
            'shipment.bulk.update',
            'shipment.bulk.delete',
            'shipment.bulk.ship',
            'shipment.bulk.deliver',
            'shipment.api.access',
            'shipment.api.create',
            'shipment.api.update',
            'shipment.api.delete',
        ];

        $role->syncPermissions($permissions);
    }

    /**
     * Create shipment dispatcher role
     */
    private function createShipmentDispatcherRole(): void
    {
        $role = Role::firstOrCreate(['name' => 'shipment-dispatcher', 'guard_name' => 'web']);

        $permissions = [
            'shipment.view.any',
            'shipment.create.any',
            'shipment.update.any',
            'shipment.ship.any',
            'shipment.deliver.any',
            'shipment.track.any',
            'shipment.label.any',
            'shipment.pickup.any',
            'shipment.search.any',
            'shipment.tracking.update',
            'shipment.tracking.view',
            'shipment.delivery.update',
            'shipment.delivery.confirm',
            'shipment.carrier.select',
            'shipment.carrier.rates',
            'shipment.cost.calculate',
            'shipment.cost.view',
            'shipment.package.weigh',
            'shipment.package.dimension',
            'shipment.package.count',
            'shipment.status.change',
            'shipment.status.view',
            'shipment.notes.add',
            'shipment.notes.view',
            'shipment.alerts.view',
            'shipment.dashboard.view',
            'shipment.bulk.create',
            'shipment.bulk.ship',
            'shipment.api.access',
            'shipment.api.create',
            'shipment.api.update',
        ];

        $role->syncPermissions($permissions);
    }

    /**
     * Create shipment viewer role (read-only)
     */
    private function createShipmentViewerRole(): void
    {
        $role = Role::firstOrCreate(['name' => 'shipment-viewer', 'guard_name' => 'web']);

        $permissions = [
            'shipment.view.any',
            'shipment.track.any',
            'shipment.search.any',
            'shipment.tracking.view',
            'shipment.status.view',
            'shipment.notes.view',
            'shipment.alerts.view',
            'shipment.dashboard.view',
            'shipment.api.access',
        ];

        $role->syncPermissions($permissions);
    }

    /**
     * Create shipment creator role
     */
    private function createShipmentCreatorRole(): void
    {
        $role = Role::firstOrCreate(['name' => 'shipment-creator', 'guard_name' => 'web']);

        $permissions = [
            'shipment.view.own',
            'shipment.create.own',
            'shipment.update.own',
            'shipment.track.own',
            'shipment.search.own',
            'shipment.tracking.view',
            'shipment.status.view',
            'shipment.notes.add',
            'shipment.notes.view',
            'shipment.alerts.view',
            'shipment.dashboard.view',
            'shipment.api.access',
            'shipment.api.create',
        ];

        $role->syncPermissions($permissions);
    }

    /**
     * Create shipment tracker role
     */
    private function createShipmentTrackerRole(): void
    {
        $role = Role::firstOrCreate(['name' => 'shipment-tracker', 'guard_name' => 'web']);

        $permissions = [
            'shipment.view.any',
            'shipment.track.any',
            'shipment.tracking.update',
            'shipment.tracking.view',
            'shipment.tracking.validate',
            'shipment.delivery.update',
            'shipment.delivery.confirm',
            'shipment.status.change',
            'shipment.status.view',
            'shipment.notes.add',
            'shipment.notes.view',
            'shipment.alerts.view',
            'shipment.alerts.create',
            'shipment.dashboard.view',
            'shipment.api.access',
            'shipment.api.update',
        ];

        $role->syncPermissions($permissions);
    }

    /**
     * Create shipment analyst role
     */
    private function createShipmentAnalystRole(): void
    {
        $role = Role::firstOrCreate(['name' => 'shipment-analyst', 'guard_name' => 'web']);

        $permissions = [
            'shipment.view.any',
            'shipment.search.any',
            'shipment.analytics.view.any',
            'shipment.reports.view',
            'shipment.reports.generate',
            'shipment.carrier.performance',
            'shipment.cost.view',
            'shipment.status.view',
            'shipment.status.history',
            'shipment.delivery.optimize',
            'shipment.dashboard.view',
            'shipment.dashboard.manage',
            'shipment.api.access',
        ];

        $role->syncPermissions($permissions);
    }
}
