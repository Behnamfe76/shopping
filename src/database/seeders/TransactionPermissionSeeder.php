<?php

namespace Fereydooni\Shopping\database\seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class TransactionPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create permissions
        $permissions = [
            // View permissions
            'transaction.view',
            'transaction.view.any',
            'transaction.view.own',

            // Create permissions
            'transaction.create',
            'transaction.create.any',
            'transaction.create.own',

            // Update permissions
            'transaction.update',
            'transaction.update.any',
            'transaction.update.own',

            // Delete permissions
            'transaction.delete',
            'transaction.delete.any',
            'transaction.delete.own',

            // Process permissions
            'transaction.process',
            'transaction.process.any',
            'transaction.process.own',

            // Refund permissions
            'transaction.refund',
            'transaction.refund.any',
            'transaction.refund.own',

            // Search permissions
            'transaction.search',
            'transaction.search.any',
            'transaction.search.own',

            // Export/Import permissions
            'transaction.export',
            'transaction.import',

            // Validation permissions
            'transaction.validate',

            // Financial permissions
            'transaction.calculate.revenue',
            'transaction.view.statistics',

            // Gateway management permissions
            'transaction.manage.gateway',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        // Create roles
        $roles = [
            'transaction-manager' => [
                'transaction.view.any',
                'transaction.create.any',
                'transaction.update.any',
                'transaction.delete.any',
                'transaction.process.any',
                'transaction.refund.any',
                'transaction.search.any',
                'transaction.export',
                'transaction.import',
                'transaction.validate',
                'transaction.calculate.revenue',
                'transaction.view.statistics',
                'transaction.manage.gateway',
            ],
            'transaction-processor' => [
                'transaction.view.any',
                'transaction.create.any',
                'transaction.update.any',
                'transaction.process.any',
                'transaction.refund.any',
                'transaction.search.any',
                'transaction.validate',
                'transaction.view.statistics',
            ],
            'transaction-viewer' => [
                'transaction.view.any',
                'transaction.search.any',
                'transaction.view.statistics',
            ],
            'finance-manager' => [
                'transaction.view.any',
                'transaction.search.any',
                'transaction.refund.any',
                'transaction.calculate.revenue',
                'transaction.view.statistics',
                'transaction.export',
            ],
            'customer' => [
                'transaction.view.own',
                'transaction.search.own',
            ],
        ];

        foreach ($roles as $roleName => $rolePermissions) {
            $role = Role::firstOrCreate(['name' => $roleName, 'guard_name' => 'web']);
            $role->syncPermissions($rolePermissions);
        }

        // Create API permissions
        $apiPermissions = [
            'api.transaction.view',
            'api.transaction.create',
            'api.transaction.update',
            'api.transaction.delete',
            'api.transaction.process',
            'api.transaction.refund',
            'api.transaction.search',
            'api.transaction.statistics',
        ];

        foreach ($apiPermissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'api']);
        }

        // Create API roles
        $apiRoles = [
            'api-transaction-admin' => [
                'api.transaction.view',
                'api.transaction.create',
                'api.transaction.update',
                'api.transaction.delete',
                'api.transaction.process',
                'api.transaction.refund',
                'api.transaction.search',
                'api.transaction.statistics',
            ],
            'api-transaction-user' => [
                'api.transaction.view',
                'api.transaction.create',
                'api.transaction.search',
            ],
        ];

        foreach ($apiRoles as $roleName => $rolePermissions) {
            $role = Role::firstOrCreate(['name' => $roleName, 'guard_name' => 'api']);
            $role->syncPermissions($rolePermissions);
        }
    }
}
