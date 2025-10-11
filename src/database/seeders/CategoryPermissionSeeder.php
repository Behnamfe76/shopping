<?php

namespace Fereydooni\Shopping\database\seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class CategoryPermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Create category permissions
        $permissions = [
            // View permissions
            'category.view',
            'category.view.any',
            'category.view.own',

            // Create permissions
            'category.create',
            'category.create.any',

            // Update permissions
            'category.update',
            'category.update.any',
            'category.update.own',

            // Delete permissions
            'category.delete',
            'category.delete.any',
            'category.delete.own',
            'category.delete.some',
            'category.delete.all',

            // Move permissions
            'category.move',
            'category.move.any',

            // Tree permissions
            'category.tree.view',
            'category.tree.manage',

            // Search permissions
            'category.search',

            // Bulk operations
            'category.bulk.delete',
            'category.bulk.move',
            'category.bulk.update',

            // Advanced permissions
            'category.restore',
            'category.force.delete',
            'category.export',
            'category.import',
        ];

        // Create permissions
        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        // Create roles and assign permissions
        $this->createRoles();
    }

    private function createRoles(): void
    {
        // Super Admin Role - All permissions
        $superAdmin = Role::firstOrCreate(['name' => 'super-admin', 'guard_name' => 'web']);
        $superAdmin->givePermissionTo(Permission::where('guard_name', 'web')->get());

        // Category Manager Role - Full category management
        $categoryManager = Role::firstOrCreate(['name' => 'category-manager', 'guard_name' => 'web']);
        $categoryManager->givePermissionTo([
            'category.view.any',
            'category.create.any',
            'category.update.any',
            'category.delete.any',
            'category.move.any',
            'category.tree.view',
            'category.tree.manage',
            'category.search',
            'category.bulk.delete',
            'category.bulk.move',
            'category.bulk.update',
            'category.export',
            'category.import',
        ]);

        // Category Editor Role - Can edit but not delete
        $categoryEditor = Role::firstOrCreate(['name' => 'category-editor', 'guard_name' => 'web']);
        $categoryEditor->givePermissionTo([
            'category.view.any',
            'category.create.any',
            'category.update.any',
            'category.move.any',
            'category.tree.view',
            'category.search',
            'category.bulk.move',
            'category.bulk.update',
        ]);

        // Category Viewer Role - Read-only access
        $categoryViewer = Role::firstOrCreate(['name' => 'category-viewer', 'guard_name' => 'web']);
        $categoryViewer->givePermissionTo([
            'category.view.any',
            'category.tree.view',
            'category.search',
        ]);

        // Store Manager Role - Limited category management
        $storeManager = Role::firstOrCreate(['name' => 'store-manager', 'guard_name' => 'web']);
        $storeManager->givePermissionTo([
            'category.view.any',
            'category.create.any',
            'category.update.any',
            'category.move.any',
            'category.tree.view',
            'category.search',
            'category.bulk.move',
            'category.bulk.update',
        ]);
    }
}
