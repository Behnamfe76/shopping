<?php

namespace Fereydooni\Shopping\database\seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class ProductVariantPermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Create permissions
        $permissions = [
            // View permissions
            'product-variant.view',
            'product-variant.view.any',
            'product-variant.view.own',

            // Create permissions
            'product-variant.create',
            'product-variant.create.any',
            'product-variant.create.own',

            // Update permissions
            'product-variant.update',
            'product-variant.update.any',
            'product-variant.update.own',

            // Delete permissions
            'product-variant.delete',
            'product-variant.delete.any',
            'product-variant.delete.own',

            // Status toggle permissions
            'product-variant.toggle.active',
            'product-variant.toggle.active.any',
            'product-variant.toggle.active.own',
            'product-variant.toggle.featured',
            'product-variant.toggle.featured.any',
            'product-variant.toggle.featured.own',

            // Inventory management permissions
            'product-variant.manage.inventory',
            'product-variant.manage.inventory.any',
            'product-variant.manage.inventory.own',

            // Pricing management permissions
            'product-variant.manage.pricing',
            'product-variant.manage.pricing.any',
            'product-variant.manage.pricing.own',

            // Search permissions
            'product-variant.search',
            'product-variant.search.any',
            'product-variant.search.own',

            // Export/Import permissions
            'product-variant.export',
            'product-variant.import',

            // Bulk operation permissions
            'product-variant.bulk.manage',
            'product-variant.bulk.manage.any',
            'product-variant.bulk.manage.own',

            // Sync permissions
            'product-variant.sync',
            'product-variant.sync.any',
            'product-variant.sync.own',

            // Analytics permissions
            'product-variant.analytics.view',
            'product-variant.analytics.view.any',
            'product-variant.analytics.view.own',

            // Reports permissions
            'product-variant.reports.view',
            'product-variant.reports.generate',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        // Create roles
        $this->createProductVariantManagerRole();
        $this->createProductVariantEditorRole();
        $this->createProductVariantViewerRole();
        $this->createProductVariantCreatorRole();
    }

    private function createProductVariantManagerRole(): void
    {
        $role = Role::firstOrCreate(['name' => 'product-variant-manager', 'guard_name' => 'web']);

        $permissions = [
            'product-variant.view.any',
            'product-variant.create.any',
            'product-variant.update.any',
            'product-variant.delete.any',
            'product-variant.toggle.active.any',
            'product-variant.toggle.featured.any',
            'product-variant.manage.inventory.any',
            'product-variant.manage.pricing.any',
            'product-variant.search.any',
            'product-variant.export',
            'product-variant.import',
            'product-variant.bulk.manage.any',
            'product-variant.sync.any',
            'product-variant.analytics.view.any',
            'product-variant.reports.view',
            'product-variant.reports.generate',
        ];

        $role->syncPermissions($permissions);
    }

    private function createProductVariantEditorRole(): void
    {
        $role = Role::firstOrCreate(['name' => 'product-variant-editor', 'guard_name' => 'web']);

        $permissions = [
            'product-variant.view.any',
            'product-variant.update.any',
            'product-variant.toggle.active.any',
            'product-variant.toggle.featured.any',
            'product-variant.manage.inventory.any',
            'product-variant.manage.pricing.any',
            'product-variant.search.any',
            'product-variant.export',
            'product-variant.analytics.view.any',
            'product-variant.reports.view',
        ];

        $role->syncPermissions($permissions);
    }

    private function createProductVariantViewerRole(): void
    {
        $role = Role::firstOrCreate(['name' => 'product-variant-viewer', 'guard_name' => 'web']);

        $permissions = [
            'product-variant.view.any',
            'product-variant.search.any',
            'product-variant.export',
            'product-variant.analytics.view.any',
            'product-variant.reports.view',
        ];

        $role->syncPermissions($permissions);
    }

    private function createProductVariantCreatorRole(): void
    {
        $role = Role::firstOrCreate(['name' => 'product-variant-creator', 'guard_name' => 'web']);

        $permissions = [
            'product-variant.view.own',
            'product-variant.create.own',
            'product-variant.update.own',
            'product-variant.toggle.active.own',
            'product-variant.toggle.featured.own',
            'product-variant.manage.inventory.own',
            'product-variant.manage.pricing.own',
            'product-variant.search.own',
            'product-variant.export',
            'product-variant.bulk.manage.own',
            'product-variant.sync.own',
            'product-variant.analytics.view.own',
            'product-variant.reports.view',
        ];

        $role->syncPermissions($permissions);
    }
}
