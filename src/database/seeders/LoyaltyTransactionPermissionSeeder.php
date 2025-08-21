<?php

namespace Fereydooni\Shopping\database\seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class LoyaltyTransactionPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Seeding loyalty transaction permissions...');

        // Create permissions
        $permissions = [
            // Basic CRUD permissions
            'loyalty-transactions.viewAny',
            'loyalty-transactions.view',
            'loyalty-transactions.create',
            'loyalty-transactions.update',
            'loyalty-transactions.delete',
            'loyalty-transactions.restore',
            'loyalty-transactions.forceDelete',

            // Transaction-specific permissions
            'loyalty-transactions.reverse',
            'loyalty-transactions.addPoints',
            'loyalty-transactions.deductPoints',
            'loyalty-transactions.calculateBalance',
            'loyalty-transactions.checkExpiration',
            'loyalty-transactions.calculateTier',
            'loyalty-transactions.validateTransaction',

            // Analytics and reporting permissions
            'loyalty-transactions.viewAnalytics',
            'loyalty-transactions.viewReports',
            'loyalty-transactions.viewHistory',
            'loyalty-transactions.viewAuditLogs',

            // Import/Export permissions
            'loyalty-transactions.exportData',
            'loyalty-transactions.importData',

            // Customer-specific permissions
            'loyalty-transactions.viewCustomerTransactions',
            'loyalty-transactions.manageCustomerPoints',

            // Management permissions
            'loyalty-transactions.managePoints',
            'loyalty-transactions.manageAll',
            'loyalty-transactions.manageSettings',

            // Status management permissions
            'loyalty-transactions.approve',
            'loyalty-transactions.reject',
            'loyalty-transactions.process',

            // View all permissions
            'loyalty-transactions.viewAll',
            'loyalty-transactions.updateAll',
            'loyalty-transactions.deleteAll',
            'loyalty-transactions.restoreAll',
            'loyalty-transactions.forceDeleteAll',
            'loyalty-transactions.reverseAll',
        ];

        // Create permissions
        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Create roles and assign permissions
        $this->createRolesAndPermissions();

        $this->command->info('Loyalty transaction permissions seeded successfully!');
    }

    /**
     * Create roles and assign permissions
     */
    protected function createRolesAndPermissions(): void
    {
        // Super Admin Role - All permissions
        $superAdmin = Role::firstOrCreate(['name' => 'super-admin']);
        $superAdmin->givePermissionTo(Permission::all());

        // Admin Role - Most permissions
        $admin = Role::firstOrCreate(['name' => 'admin']);
        $adminPermissions = [
            'loyalty-transactions.viewAny',
            'loyalty-transactions.view',
            'loyalty-transactions.create',
            'loyalty-transactions.update',
            'loyalty-transactions.delete',
            'loyalty-transactions.restore',
            'loyalty-transactions.reverse',
            'loyalty-transactions.addPoints',
            'loyalty-transactions.deductPoints',
            'loyalty-transactions.calculateBalance',
            'loyalty-transactions.checkExpiration',
            'loyalty-transactions.calculateTier',
            'loyalty-transactions.validateTransaction',
            'loyalty-transactions.viewAnalytics',
            'loyalty-transactions.viewReports',
            'loyalty-transactions.viewHistory',
            'loyalty-transactions.exportData',
            'loyalty-transactions.importData',
            'loyalty-transactions.viewCustomerTransactions',
            'loyalty-transactions.manageCustomerPoints',
            'loyalty-transactions.managePoints',
            'loyalty-transactions.approve',
            'loyalty-transactions.reject',
            'loyalty-transactions.process',
            'loyalty-transactions.viewAll',
            'loyalty-transactions.updateAll',
            'loyalty-transactions.deleteAll',
            'loyalty-transactions.restoreAll',
            'loyalty-transactions.reverseAll',
        ];
        $admin->givePermissionTo($adminPermissions);

        // Manager Role - Limited management permissions
        $manager = Role::firstOrCreate(['name' => 'manager']);
        $managerPermissions = [
            'loyalty-transactions.viewAny',
            'loyalty-transactions.view',
            'loyalty-transactions.create',
            'loyalty-transactions.update',
            'loyalty-transactions.reverse',
            'loyalty-transactions.addPoints',
            'loyalty-transactions.deductPoints',
            'loyalty-transactions.calculateBalance',
            'loyalty-transactions.checkExpiration',
            'loyalty-transactions.calculateTier',
            'loyalty-transactions.viewAnalytics',
            'loyalty-transactions.viewReports',
            'loyalty-transactions.viewHistory',
            'loyalty-transactions.exportData',
            'loyalty-transactions.viewCustomerTransactions',
            'loyalty-transactions.manageCustomerPoints',
            'loyalty-transactions.approve',
            'loyalty-transactions.reject',
            'loyalty-transactions.process',
        ];
        $manager->givePermissionTo($managerPermissions);

        // Staff Role - Basic permissions
        $staff = Role::firstOrCreate(['name' => 'staff']);
        $staffPermissions = [
            'loyalty-transactions.viewAny',
            'loyalty-transactions.view',
            'loyalty-transactions.create',
            'loyalty-transactions.addPoints',
            'loyalty-transactions.deductPoints',
            'loyalty-transactions.calculateBalance',
            'loyalty-transactions.checkExpiration',
            'loyalty-transactions.calculateTier',
            'loyalty-transactions.viewHistory',
            'loyalty-transactions.viewCustomerTransactions',
        ];
        $staff->givePermissionTo($staffPermissions);

        // Customer Service Role - Customer-focused permissions
        $customerService = Role::firstOrCreate(['name' => 'customer-service']);
        $customerServicePermissions = [
            'loyalty-transactions.viewAny',
            'loyalty-transactions.view',
            'loyalty-transactions.create',
            'loyalty-transactions.update',
            'loyalty-transactions.addPoints',
            'loyalty-transactions.deductPoints',
            'loyalty-transactions.calculateBalance',
            'loyalty-transactions.checkExpiration',
            'loyalty-transactions.calculateTier',
            'loyalty-transactions.viewHistory',
            'loyalty-transactions.viewCustomerTransactions',
            'loyalty-transactions.manageCustomerPoints',
            'loyalty-transactions.reverse',
        ];
        $customerService->givePermissionTo($customerServicePermissions);

        // Analyst Role - Read-only analytics permissions
        $analyst = Role::firstOrCreate(['name' => 'analyst']);
        $analystPermissions = [
            'loyalty-transactions.viewAny',
            'loyalty-transactions.view',
            'loyalty-transactions.viewAnalytics',
            'loyalty-transactions.viewReports',
            'loyalty-transactions.viewHistory',
            'loyalty-transactions.exportData',
            'loyalty-transactions.viewCustomerTransactions',
            'loyalty-transactions.calculateBalance',
            'loyalty-transactions.checkExpiration',
            'loyalty-transactions.calculateTier',
        ];
        $analyst->givePermissionTo($analystPermissions);

        // Customer Role - Limited view permissions
        $customer = Role::firstOrCreate(['name' => 'customer']);
        $customerPermissions = [
            'loyalty-transactions.view', // Only own transactions
            'loyalty-transactions.calculateBalance', // Only own balance
            'loyalty-transactions.checkExpiration', // Only own expiration
            'loyalty-transactions.calculateTier', // Only own tier
            'loyalty-transactions.viewHistory', // Only own history
        ];
        $customer->givePermissionTo($customerPermissions);
    }
}
