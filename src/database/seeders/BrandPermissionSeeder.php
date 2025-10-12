<?php

namespace Fereydooni\Shopping\database\seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class BrandPermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Create permissions
        $permissions = [
            // View permissions
            'brand.view',
            'brand.view.any',
            'brand.view.own',

            // Create permissions
            'brand.create',
            'brand.create.any',
            'brand.create.own',

            // Update permissions
            'brand.update',
            'brand.update.any',
            'brand.update.own',

            // Delete permissions
            'brand.delete',
            'brand.delete.any',
            'brand.delete.own',
            'brand.delete.some',
            'brand.delete.all',

            // Status toggle permissions
            'brand.toggle.active',
            'brand.toggle.active.any',
            'brand.toggle.active.own',
            'brand.toggle.featured',
            'brand.toggle.featured.any',
            'brand.toggle.featured.own',

            // Search permissions
            'brand.search',
            'brand.search.any',
            'brand.search.own',

            // Import/Export permissions
            'brand.export',
            'brand.import',

            // Validation permissions
            'brand.validate',

            // Media permissions
            'brand.media.upload',
            'brand.media.delete',

            // SEO permissions
            'brand.seo.manage',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        // Create roles
        $this->createBrandManagerRole();
        $this->createBrandEditorRole();
        $this->createBrandViewerRole();
        $this->createBrandCreatorRole();
    }

    /**
     * Create brand manager role with full permissions
     */
    private function createBrandManagerRole(): void
    {
        $role = Role::firstOrCreate(['name' => 'brand-manager', 'guard_name' => 'web']);

        $permissions = Permission::where('name', 'like', 'brand.%')->pluck('id');
        $role->syncPermissions($permissions);
    }

    /**
     * Create brand editor role with limited permissions
     */
    private function createBrandEditorRole(): void
    {
        $role = Role::firstOrCreate(['name' => 'brand-editor', 'guard_name' => 'web']);

        $permissions = Permission::whereIn('name', [
            'brand.view',
            'brand.view.any',
            'brand.view.own',
            'brand.create',
            'brand.create.any',
            'brand.create.own',
            'brand.update',
            'brand.update.any',
            'brand.update.own',
            'brand.toggle.active',
            'brand.toggle.active.any',
            'brand.toggle.active.own',
            'brand.toggle.featured',
            'brand.toggle.featured.any',
            'brand.toggle.featured.own',
            'brand.search',
            'brand.search.any',
            'brand.search.own',
            'brand.validate',
            'brand.media.upload',
            'brand.media.delete',
            'brand.seo.manage',
        ])->pluck('id');

        $role->syncPermissions($permissions);
    }

    /**
     * Create brand viewer role with read-only permissions
     */
    private function createBrandViewerRole(): void
    {
        $role = Role::firstOrCreate(['name' => 'brand-viewer', 'guard_name' => 'web']);

        $permissions = Permission::whereIn('name', [
            'brand.view',
            'brand.view.any',
            'brand.view.own',
            'brand.search',
            'brand.search.any',
            'brand.search.own',
        ])->pluck('id');

        $role->syncPermissions($permissions);
    }

    /**
     * Create brand creator role with create and manage own permissions
     */
    private function createBrandCreatorRole(): void
    {
        $role = Role::firstOrCreate(['name' => 'brand-creator', 'guard_name' => 'web']);

        $permissions = Permission::whereIn('name', [
            'brand.view',
            'brand.view.any',
            'brand.view.own',
            'brand.create',
            'brand.create.own',
            'brand.update',
            'brand.update.own',
            'brand.delete',
            'brand.delete.own',
            'brand.toggle.active',
            'brand.toggle.active.own',
            'brand.toggle.featured',
            'brand.toggle.featured.own',
            'brand.search',
            'brand.search.own',
            'brand.validate',
            'brand.media.upload',
            'brand.media.delete',
            'brand.seo.manage',
        ])->pluck('id');

        $role->syncPermissions($permissions);
    }
}
