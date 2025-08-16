<?php

namespace Fereydooni\Shopping\database\seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class ProductAttributeValuePermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Create permissions
        $permissions = [
            // View permissions
            'product-attribute-value.view',
            'product-attribute-value.view.any',
            'product-attribute-value.view.own',

            // Create permissions
            'product-attribute-value.create',
            'product-attribute-value.create.any',
            'product-attribute-value.create.own',

            // Update permissions
            'product-attribute-value.update',
            'product-attribute-value.update.any',
            'product-attribute-value.update.own',

            // Delete permissions
            'product-attribute-value.delete',
            'product-attribute-value.delete.any',
            'product-attribute-value.delete.own',

            // Status toggle permissions
            'product-attribute-value.toggle.active',
            'product-attribute-value.toggle.active.any',
            'product-attribute-value.toggle.active.own',
            'product-attribute-value.toggle.default',
            'product-attribute-value.toggle.default.any',
            'product-attribute-value.toggle.default.own',

            // Search permissions
            'product-attribute-value.search',
            'product-attribute-value.search.any',
            'product-attribute-value.search.own',

            // Import/Export permissions
            'product-attribute-value.export',
            'product-attribute-value.import',

            // Validation permissions
            'product-attribute-value.validate',

            // Relationship management permissions
            'product-attribute-value.relationships.manage',
            'product-attribute-value.relationships.view',

            // Usage and analytics permissions
            'product-attribute-value.usage.view',
            'product-attribute-value.analytics.view',
            'product-attribute-value.reports.view',
            'product-attribute-value.reports.generate',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        // Create roles
        $this->createProductAttributeValueManagerRole();
        $this->createProductAttributeValueEditorRole();
        $this->createProductAttributeValueViewerRole();
        $this->createProductAttributeValueCreatorRole();
    }

    private function createProductAttributeValueManagerRole(): void
    {
        $role = Role::firstOrCreate(['name' => 'product-attribute-value-manager', 'guard_name' => 'web']);

        $permissions = [
            'product-attribute-value.view.any',
            'product-attribute-value.create.any',
            'product-attribute-value.update.any',
            'product-attribute-value.delete.any',
            'product-attribute-value.toggle.active.any',
            'product-attribute-value.toggle.default.any',
            'product-attribute-value.search.any',
            'product-attribute-value.export',
            'product-attribute-value.import',
            'product-attribute-value.validate',
            'product-attribute-value.relationships.manage',
            'product-attribute-value.relationships.view',
            'product-attribute-value.usage.view',
            'product-attribute-value.analytics.view',
            'product-attribute-value.reports.view',
            'product-attribute-value.reports.generate',
        ];

        $role->syncPermissions($permissions);
    }

    private function createProductAttributeValueEditorRole(): void
    {
        $role = Role::firstOrCreate(['name' => 'product-attribute-value-editor', 'guard_name' => 'web']);

        $permissions = [
            'product-attribute-value.view.any',
            'product-attribute-value.create.any',
            'product-attribute-value.update.any',
            'product-attribute-value.toggle.active.any',
            'product-attribute-value.toggle.default.any',
            'product-attribute-value.search.any',
            'product-attribute-value.export',
            'product-attribute-value.validate',
            'product-attribute-value.relationships.view',
            'product-attribute-value.usage.view',
            'product-attribute-value.analytics.view',
            'product-attribute-value.reports.view',
        ];

        $role->syncPermissions($permissions);
    }

    private function createProductAttributeValueViewerRole(): void
    {
        $role = Role::firstOrCreate(['name' => 'product-attribute-value-viewer', 'guard_name' => 'web']);

        $permissions = [
            'product-attribute-value.view.any',
            'product-attribute-value.search.any',
            'product-attribute-value.export',
            'product-attribute-value.relationships.view',
            'product-attribute-value.usage.view',
            'product-attribute-value.analytics.view',
            'product-attribute-value.reports.view',
        ];

        $role->syncPermissions($permissions);
    }

    private function createProductAttributeValueCreatorRole(): void
    {
        $role = Role::firstOrCreate(['name' => 'product-attribute-value-creator', 'guard_name' => 'web']);

        $permissions = [
            'product-attribute-value.view.own',
            'product-attribute-value.create.own',
            'product-attribute-value.update.own',
            'product-attribute-value.delete.own',
            'product-attribute-value.toggle.active.own',
            'product-attribute-value.toggle.default.own',
            'product-attribute-value.search.own',
            'product-attribute-value.validate',
            'product-attribute-value.relationships.view',
            'product-attribute-value.usage.view',
        ];

        $role->syncPermissions($permissions);
    }
}
