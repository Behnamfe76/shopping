<?php

namespace Fereydooni\Shopping\database\seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class ProductPermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Create permissions
        $permissions = [
            // View permissions
            'product.view',
            'product.view.any',
            'product.view.own',

            // Create permissions
            'product.create',
            'product.create.any',
            'product.create.own',

            // Update permissions
            'product.update',
            'product.update.any',
            'product.update.own',

            // Delete permissions
            'product.delete',
            'product.delete.any',
            'product.delete.own',

            // Status toggle permissions
            'product.toggle.active',
            'product.toggle.active.any',
            'product.toggle.active.own',
            'product.toggle.featured',
            'product.toggle.featured.any',
            'product.toggle.featured.own',

            // Publication permissions
            'product.publish',
            'product.publish.any',
            'product.publish.own',
            'product.unpublish',
            'product.unpublish.any',
            'product.unpublish.own',
            'product.archive',
            'product.archive.any',
            'product.archive.own',

            // Search permissions
            'product.search',
            'product.search.any',
            'product.search.own',

            // Import/Export permissions
            'product.export',
            'product.import',

            // Validation permissions
            'product.validate',

            // Media permissions
            'product.media.upload',
            'product.media.delete',

            // Inventory permissions
            'product.inventory.manage',
            'product.inventory.view',

            // SEO permissions
            'product.seo.manage',

            // Analytics permissions
            'product.analytics.view',

            // Report permissions
            'product.reports.view',
            'product.reports.generate',

            // Bulk operation permissions
            'product.bulk.operations',

            // Duplicate permissions
            'product.duplicate',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Create roles
        $this->createProductManagerRole();
        $this->createProductEditorRole();
        $this->createProductViewerRole();
        $this->createProductCreatorRole();
        $this->createInventoryManagerRole();
    }

    private function createProductManagerRole(): void
    {
        $role = Role::firstOrCreate(['name' => 'product-manager']);

        // Full product management for all products
        $permissions = [
            'product.view.any',
            'product.create.any',
            'product.update.any',
            'product.delete.any',
            'product.toggle.active.any',
            'product.toggle.featured.any',
            'product.publish.any',
            'product.unpublish.any',
            'product.archive.any',
            'product.search.any',
            'product.export',
            'product.import',
            'product.validate',
            'product.media.upload',
            'product.media.delete',
            'product.inventory.manage',
            'product.inventory.view',
            'product.seo.manage',
            'product.analytics.view',
            'product.reports.view',
            'product.reports.generate',
            'product.bulk.operations',
            'product.duplicate',
        ];

        $role->syncPermissions($permissions);
    }

    private function createProductEditorRole(): void
    {
        $role = Role::firstOrCreate(['name' => 'product-editor']);

        // Can edit products but not delete
        $permissions = [
            'product.view.any',
            'product.update.any',
            'product.toggle.active.any',
            'product.toggle.featured.any',
            'product.publish.any',
            'product.unpublish.any',
            'product.search.any',
            'product.media.upload',
            'product.media.delete',
            'product.inventory.view',
            'product.seo.manage',
            'product.analytics.view',
        ];

        $role->syncPermissions($permissions);
    }

    private function createProductViewerRole(): void
    {
        $role = Role::firstOrCreate(['name' => 'product-viewer']);

        // Read-only access to products
        $permissions = [
            'product.view.any',
            'product.search.any',
            'product.inventory.view',
            'product.analytics.view',
            'product.reports.view',
        ];

        $role->syncPermissions($permissions);
    }

    private function createProductCreatorRole(): void
    {
        $role = Role::firstOrCreate(['name' => 'product-creator']);

        // Can create and manage own products
        $permissions = [
            'product.view.own',
            'product.create.own',
            'product.update.own',
            'product.toggle.active.own',
            'product.toggle.featured.own',
            'product.publish.own',
            'product.unpublish.own',
            'product.search.own',
            'product.media.upload',
            'product.media.delete',
            'product.inventory.view',
            'product.seo.manage',
            'product.analytics.view',
        ];

        $role->syncPermissions($permissions);
    }

    private function createInventoryManagerRole(): void
    {
        $role = Role::firstOrCreate(['name' => 'inventory-manager']);

        // Can manage product inventory and stock
        $permissions = [
            'product.view.any',
            'product.inventory.manage',
            'product.inventory.view',
            'product.update.any',
            'product.analytics.view',
            'product.reports.view',
            'product.reports.generate',
        ];

        $role->syncPermissions($permissions);
    }
}
