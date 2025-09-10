<?php

namespace Fereydooni\Shopping\database\seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class ProductTagPermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Create permissions
        $permissions = [
            // View permissions
            'product-tag.view',
            'product-tag.view.any',
            'product-tag.view.own',

            // Create permissions
            'product-tag.create',
            'product-tag.create.any',
            'product-tag.create.own',

            // Update permissions
            'product-tag.update',
            'product-tag.update.any',
            'product-tag.update.own',

            // Delete permissions
            'product-tag.delete',
            'product-tag.delete.some',
            'product-tag.delete.all',
            'product-tag.delete.any',
            'product-tag.delete.own',

            // Status toggle permissions
            'product-tag.toggle.active',
            'product-tag.toggle.active.any',
            'product-tag.toggle.active.own',
            'product-tag.toggle.featured',
            'product-tag.toggle.featured.any',
            'product-tag.toggle.featured.own',

            // Search permissions
            'product-tag.search',
            'product-tag.search.any',
            'product-tag.search.own',

            // Export/Import permissions
            'product-tag.export',
            'product-tag.import',

            // Bulk operation permissions
            'product-tag.bulk.manage',
            'product-tag.bulk.manage.any',
            'product-tag.bulk.manage.own',

            // Sync permissions
            'product-tag.sync',
            'product-tag.sync.any',
            'product-tag.sync.own',

            // Merge/Split permissions
            'product-tag.merge',
            'product-tag.merge.any',
            'product-tag.merge.own',
            'product-tag.split',
            'product-tag.split.any',
            'product-tag.split.own',

            // Analytics permissions
            'product-tag.analytics.view',
            'product-tag.analytics.view.any',
            'product-tag.analytics.view.own',

            // Reports permissions
            'product-tag.reports.view',
            'product-tag.reports.generate',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Create roles
        $this->createProductTagManagerRole();
        $this->createProductTagEditorRole();
        $this->createProductTagViewerRole();
        $this->createProductTagCreatorRole();
    }

    private function createProductTagManagerRole(): void
    {
        $role = Role::firstOrCreate(['name' => 'product-tag-manager']);

        $managerPermissions = [
            'product-tag.view.any',
            'product-tag.create.any',
            'product-tag.update.any',
            'product-tag.delete.any',
            'product-tag.toggle.active.any',
            'product-tag.toggle.featured.any',
            'product-tag.search.any',
            'product-tag.export',
            'product-tag.import',
            'product-tag.bulk.manage.any',
            'product-tag.sync.any',
            'product-tag.merge.any',
            'product-tag.split.any',
            'product-tag.analytics.view.any',
            'product-tag.reports.view',
            'product-tag.reports.generate',
        ];

        $role->syncPermissions($managerPermissions);
    }

    private function createProductTagEditorRole(): void
    {
        $role = Role::firstOrCreate(['name' => 'product-tag-editor']);

        $editorPermissions = [
            'product-tag.view.any',
            'product-tag.create.any',
            'product-tag.update.any',
            'product-tag.toggle.active.any',
            'product-tag.toggle.featured.any',
            'product-tag.search.any',
            'product-tag.export',
            'product-tag.bulk.manage.any',
            'product-tag.sync.any',
            'product-tag.analytics.view.any',
            'product-tag.reports.view',
        ];

        $role->syncPermissions($editorPermissions);
    }

    private function createProductTagViewerRole(): void
    {
        $role = Role::firstOrCreate(['name' => 'product-tag-viewer']);

        $viewerPermissions = [
            'product-tag.view.any',
            'product-tag.search.any',
            'product-tag.export',
            'product-tag.analytics.view.any',
            'product-tag.reports.view',
        ];

        $role->syncPermissions($viewerPermissions);
    }

    private function createProductTagCreatorRole(): void
    {
        $role = Role::firstOrCreate(['name' => 'product-tag-creator']);

        $creatorPermissions = [
            'product-tag.view.own',
            'product-tag.create.own',
            'product-tag.update.own',
            'product-tag.toggle.active.own',
            'product-tag.toggle.featured.own',
            'product-tag.search.own',
            'product-tag.export',
            'product-tag.bulk.manage.own',
            'product-tag.sync.own',
            'product-tag.analytics.view.own',
        ];

        $role->syncPermissions($creatorPermissions);
    }
}
