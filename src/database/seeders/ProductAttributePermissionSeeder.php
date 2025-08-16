<?php

namespace Fereydooni\Shopping\database\seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class ProductAttributePermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Create permissions
        $permissions = [
            // View permissions
            'product-attribute.view',
            'product-attribute.view.any',
            'product-attribute.view.own',

            // Create permissions
            'product-attribute.create',
            'product-attribute.create.any',
            'product-attribute.create.own',

            // Update permissions
            'product-attribute.update',
            'product-attribute.update.any',
            'product-attribute.update.own',

            // Delete permissions
            'product-attribute.delete',
            'product-attribute.delete.any',
            'product-attribute.delete.own',

            // Toggle permissions
            'product-attribute.toggle.active',
            'product-attribute.toggle.active.any',
            'product-attribute.toggle.active.own',
            'product-attribute.toggle.required',
            'product-attribute.toggle.required.any',
            'product-attribute.toggle.required.own',
            'product-attribute.toggle.searchable',
            'product-attribute.toggle.searchable.any',
            'product-attribute.toggle.searchable.own',
            'product-attribute.toggle.filterable',
            'product-attribute.toggle.filterable.any',
            'product-attribute.toggle.filterable.own',
            'product-attribute.toggle.comparable',
            'product-attribute.toggle.comparable.any',
            'product-attribute.toggle.comparable.own',
            'product-attribute.toggle.visible',
            'product-attribute.toggle.visible.any',
            'product-attribute.toggle.visible.own',

            // Search permissions
            'product-attribute.search',
            'product-attribute.search.any',
            'product-attribute.search.own',

            // Import/Export permissions
            'product-attribute.export',
            'product-attribute.import',

            // Validation permissions
            'product-attribute.validate',

            // Value management permissions
            'product-attribute.values.manage',
            'product-attribute.values.view',

            // Analytics permissions
            'product-attribute.analytics.view',
            'product-attribute.reports.view',
            'product-attribute.reports.generate',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Create roles
        $this->createProductAttributeManagerRole();
        $this->createProductAttributeEditorRole();
        $this->createProductAttributeViewerRole();
        $this->createProductAttributeCreatorRole();
    }

    private function createProductAttributeManagerRole(): void
    {
        $role = Role::firstOrCreate(['name' => 'product-attribute-manager']);

        $managerPermissions = [
            'product-attribute.view.any',
            'product-attribute.create.any',
            'product-attribute.update.any',
            'product-attribute.delete.any',
            'product-attribute.toggle.active.any',
            'product-attribute.toggle.required.any',
            'product-attribute.toggle.searchable.any',
            'product-attribute.toggle.filterable.any',
            'product-attribute.toggle.comparable.any',
            'product-attribute.toggle.visible.any',
            'product-attribute.search.any',
            'product-attribute.export',
            'product-attribute.import',
            'product-attribute.validate',
            'product-attribute.values.manage',
            'product-attribute.values.view',
            'product-attribute.analytics.view',
            'product-attribute.reports.view',
            'product-attribute.reports.generate',
        ];

        $role->syncPermissions($managerPermissions);
    }

    private function createProductAttributeEditorRole(): void
    {
        $role = Role::firstOrCreate(['name' => 'product-attribute-editor']);

        $editorPermissions = [
            'product-attribute.view.any',
            'product-attribute.update.any',
            'product-attribute.toggle.active.any',
            'product-attribute.toggle.required.any',
            'product-attribute.toggle.searchable.any',
            'product-attribute.toggle.filterable.any',
            'product-attribute.toggle.comparable.any',
            'product-attribute.toggle.visible.any',
            'product-attribute.search.any',
            'product-attribute.validate',
            'product-attribute.values.manage',
            'product-attribute.values.view',
            'product-attribute.analytics.view',
        ];

        $role->syncPermissions($editorPermissions);
    }

    private function createProductAttributeViewerRole(): void
    {
        $role = Role::firstOrCreate(['name' => 'product-attribute-viewer']);

        $viewerPermissions = [
            'product-attribute.view.any',
            'product-attribute.search.any',
            'product-attribute.values.view',
            'product-attribute.analytics.view',
        ];

        $role->syncPermissions($viewerPermissions);
    }

    private function createProductAttributeCreatorRole(): void
    {
        $role = Role::firstOrCreate(['name' => 'product-attribute-creator']);

        $creatorPermissions = [
            'product-attribute.view.own',
            'product-attribute.create.own',
            'product-attribute.update.own',
            'product-attribute.toggle.active.own',
            'product-attribute.toggle.required.own',
            'product-attribute.toggle.searchable.own',
            'product-attribute.toggle.filterable.own',
            'product-attribute.toggle.comparable.own',
            'product-attribute.toggle.visible.own',
            'product-attribute.search.own',
            'product-attribute.validate',
            'product-attribute.values.manage',
            'product-attribute.values.view',
        ];

        $role->syncPermissions($creatorPermissions);
    }
}
