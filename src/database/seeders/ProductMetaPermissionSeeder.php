<?php

namespace Fereydooni\Shopping\database\seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class ProductMetaPermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Create permissions
        $permissions = [
            // View permissions
            'product-meta.view',
            'product-meta.view.any',
            'product-meta.view.own',
            'product-meta.view.public',
            'product-meta.view.private',

            // Create permissions
            'product-meta.create',
            'product-meta.create.any',
            'product-meta.create.own',

            // Update permissions
            'product-meta.update',
            'product-meta.update.any',
            'product-meta.update.own',

            // Delete permissions
            'product-meta.delete',
            'product-meta.delete.any',
            'product-meta.delete.own',

            // Toggle permissions
            'product-meta.toggle.public',
            'product-meta.toggle.public.any',
            'product-meta.toggle.public.own',
            'product-meta.toggle.searchable',
            'product-meta.toggle.searchable.any',
            'product-meta.toggle.searchable.own',
            'product-meta.toggle.filterable',
            'product-meta.toggle.filterable.any',
            'product-meta.toggle.filterable.own',

            // Search permissions
            'product-meta.search',
            'product-meta.search.any',
            'product-meta.search.own',

            // Import/Export permissions
            'product-meta.export',
            'product-meta.import',

            // Validation permissions
            'product-meta.validate',

            // Bulk operation permissions
            'product-meta.bulk.manage',
            'product-meta.bulk.manage.any',
            'product-meta.bulk.manage.own',

            // Sync permissions
            'product-meta.sync',
            'product-meta.sync.any',
            'product-meta.sync.own',

            // Analytics permissions
            'product-meta.analytics.view',
            'product-meta.analytics.view.any',
            'product-meta.analytics.view.own',

            // Report permissions
            'product-meta.reports.view',
            'product-meta.reports.generate',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Create roles
        $this->createProductMetaManagerRole();
        $this->createProductMetaEditorRole();
        $this->createProductMetaViewerRole();
        $this->createProductMetaCreatorRole();
    }

    private function createProductMetaManagerRole(): void
    {
        $role = Role::firstOrCreate(['name' => 'product-meta-manager']);

        $managerPermissions = [
            'product-meta.view.any',
            'product-meta.create.any',
            'product-meta.update.any',
            'product-meta.delete.any',
            'product-meta.toggle.public.any',
            'product-meta.toggle.searchable.any',
            'product-meta.toggle.filterable.any',
            'product-meta.search.any',
            'product-meta.export',
            'product-meta.import',
            'product-meta.validate',
            'product-meta.bulk.manage.any',
            'product-meta.sync.any',
            'product-meta.analytics.view.any',
            'product-meta.reports.view',
            'product-meta.reports.generate',
        ];

        $role->syncPermissions($managerPermissions);
    }

    private function createProductMetaEditorRole(): void
    {
        $role = Role::firstOrCreate(['name' => 'product-meta-editor']);

        $editorPermissions = [
            'product-meta.view.any',
            'product-meta.create.any',
            'product-meta.update.any',
            'product-meta.toggle.public.any',
            'product-meta.toggle.searchable.any',
            'product-meta.toggle.filterable.any',
            'product-meta.search.any',
            'product-meta.export',
            'product-meta.validate',
            'product-meta.bulk.manage.any',
            'product-meta.analytics.view.any',
            'product-meta.reports.view',
        ];

        $role->syncPermissions($editorPermissions);
    }

    private function createProductMetaViewerRole(): void
    {
        $role = Role::firstOrCreate(['name' => 'product-meta-viewer']);

        $viewerPermissions = [
            'product-meta.view.public',
            'product-meta.view.own',
            'product-meta.search.own',
            'product-meta.export',
            'product-meta.analytics.view.own',
            'product-meta.reports.view',
        ];

        $role->syncPermissions($viewerPermissions);
    }

    private function createProductMetaCreatorRole(): void
    {
        $role = Role::firstOrCreate(['name' => 'product-meta-creator']);

        $creatorPermissions = [
            'product-meta.view.own',
            'product-meta.create.own',
            'product-meta.update.own',
            'product-meta.delete.own',
            'product-meta.toggle.public.own',
            'product-meta.toggle.searchable.own',
            'product-meta.toggle.filterable.own',
            'product-meta.search.own',
            'product-meta.export',
            'product-meta.validate',
            'product-meta.bulk.manage.own',
            'product-meta.sync.own',
            'product-meta.analytics.view.own',
            'product-meta.reports.view',
        ];

        $role->syncPermissions($creatorPermissions);
    }
}
