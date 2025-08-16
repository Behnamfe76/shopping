<?php

namespace Fereydooni\Shopping\database\seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class ProductDiscountPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create permissions
        $permissions = [
            // View permissions
            'product-discount.view',
            'product-discount.view.any',
            'product-discount.view.own',

            // Create permissions
            'product-discount.create',
            'product-discount.create.any',
            'product-discount.create.own',

            // Update permissions
            'product-discount.update',
            'product-discount.update.any',
            'product-discount.update.own',

            // Delete permissions
            'product-discount.delete',
            'product-discount.delete.any',
            'product-discount.delete.own',

            // Status management permissions
            'product-discount.toggle.active',
            'product-discount.toggle.active.any',
            'product-discount.toggle.active.own',

            // Date management permissions
            'product-discount.extend',
            'product-discount.extend.any',
            'product-discount.extend.own',
            'product-discount.shorten',
            'product-discount.shorten.any',
            'product-discount.shorten.own',

            // Calculation and application permissions
            'product-discount.calculate',
            'product-discount.calculate.any',
            'product-discount.calculate.own',
            'product-discount.apply',
            'product-discount.apply.any',
            'product-discount.apply.own',
            'product-discount.validate',
            'product-discount.validate.any',
            'product-discount.validate.own',

            // Search permissions
            'product-discount.search',
            'product-discount.search.any',
            'product-discount.search.own',

            // Import/Export permissions
            'product-discount.export',
            'product-discount.import',

            // Analytics and reporting permissions
            'product-discount.analytics.view',
            'product-discount.analytics.view.any',
            'product-discount.analytics.view.own',
            'product-discount.reports.view',
            'product-discount.reports.generate',
            'product-discount.forecast.view',
            'product-discount.recommendations.view',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        // Create roles
        $this->createProductDiscountManagerRole();
        $this->createProductDiscountEditorRole();
        $this->createProductDiscountViewerRole();
        $this->createProductDiscountCreatorRole();
    }

    /**
     * Create product discount manager role with full permissions
     */
    private function createProductDiscountManagerRole(): void
    {
        $role = Role::firstOrCreate(['name' => 'product-discount-manager', 'guard_name' => 'web']);

        $permissions = Permission::where('name', 'like', 'product-discount.%')->pluck('id');
        $role->syncPermissions($permissions);
    }

    /**
     * Create product discount editor role with limited permissions
     */
    private function createProductDiscountEditorRole(): void
    {
        $role = Role::firstOrCreate(['name' => 'product-discount-editor', 'guard_name' => 'web']);

        $permissions = Permission::whereIn('name', [
            'product-discount.view',
            'product-discount.view.any',
            'product-discount.view.own',
            'product-discount.create',
            'product-discount.create.any',
            'product-discount.create.own',
            'product-discount.update',
            'product-discount.update.any',
            'product-discount.update.own',
            'product-discount.toggle.active',
            'product-discount.toggle.active.any',
            'product-discount.toggle.active.own',
            'product-discount.extend',
            'product-discount.extend.any',
            'product-discount.extend.own',
            'product-discount.shorten',
            'product-discount.shorten.any',
            'product-discount.shorten.own',
            'product-discount.calculate',
            'product-discount.calculate.any',
            'product-discount.calculate.own',
            'product-discount.apply',
            'product-discount.apply.any',
            'product-discount.apply.own',
            'product-discount.validate',
            'product-discount.validate.any',
            'product-discount.validate.own',
            'product-discount.search',
            'product-discount.search.any',
            'product-discount.search.own',
            'product-discount.analytics.view',
            'product-discount.analytics.view.any',
            'product-discount.analytics.view.own',
        ])->pluck('id');

        $role->syncPermissions($permissions);
    }

    /**
     * Create product discount viewer role with read-only permissions
     */
    private function createProductDiscountViewerRole(): void
    {
        $role = Role::firstOrCreate(['name' => 'product-discount-viewer', 'guard_name' => 'web']);

        $permissions = Permission::whereIn('name', [
            'product-discount.view',
            'product-discount.view.any',
            'product-discount.view.own',
            'product-discount.search',
            'product-discount.search.any',
            'product-discount.search.own',
            'product-discount.calculate',
            'product-discount.calculate.any',
            'product-discount.calculate.own',
            'product-discount.validate',
            'product-discount.validate.any',
            'product-discount.validate.own',
            'product-discount.analytics.view',
            'product-discount.analytics.view.any',
            'product-discount.analytics.view.own',
        ])->pluck('id');

        $role->syncPermissions($permissions);
    }

    /**
     * Create product discount creator role for managing own discounts
     */
    private function createProductDiscountCreatorRole(): void
    {
        $role = Role::firstOrCreate(['name' => 'product-discount-creator', 'guard_name' => 'web']);

        $permissions = Permission::whereIn('name', [
            'product-discount.view',
            'product-discount.view.own',
            'product-discount.create',
            'product-discount.create.own',
            'product-discount.update',
            'product-discount.update.own',
            'product-discount.delete',
            'product-discount.delete.own',
            'product-discount.toggle.active',
            'product-discount.toggle.active.own',
            'product-discount.extend',
            'product-discount.extend.own',
            'product-discount.shorten',
            'product-discount.shorten.own',
            'product-discount.calculate',
            'product-discount.calculate.own',
            'product-discount.apply',
            'product-discount.apply.own',
            'product-discount.validate',
            'product-discount.validate.own',
            'product-discount.search',
            'product-discount.search.own',
            'product-discount.analytics.view',
            'product-discount.analytics.view.own',
        ])->pluck('id');

        $role->syncPermissions($permissions);
    }
}
