<?php

namespace Fereydooni\Shopping\database\seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class CustomerCommunicationPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create permissions for customer communications
        $permissions = [
            'customer-communications.viewAny',
            'customer-communications.view',
            'customer-communications.create',
            'customer-communications.update',
            'customer-communications.delete',
            'customer-communications.restore',
            'customer-communications.forceDelete',
            'customer-communications.schedule',
            'customer-communications.send',
            'customer-communications.cancel',
            'customer-communications.reschedule',
            'customer-communications.markAsDelivered',
            'customer-communications.markAsOpened',
            'customer-communications.markAsClicked',
            'customer-communications.markAsBounced',
            'customer-communications.markAsUnsubscribed',
            'customer-communications.viewAnalytics',
            'customer-communications.exportData',
            'customer-communications.importData',
            'customer-communications.manageAttachments',
            'customer-communications.viewTrackingData',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Assign permissions to roles
        $this->assignPermissionsToRoles();

        $this->command->info('CustomerCommunication permissions seeded successfully.');
    }

    protected function assignPermissionsToRoles(): void
    {
        // Super Admin - All permissions
        $superAdminRole = Role::where('name', 'super-admin')->first();
        if ($superAdminRole) {
            $superAdminRole->givePermissionTo([
                'customer-communications.viewAny',
                'customer-communications.view',
                'customer-communications.create',
                'customer-communications.update',
                'customer-communications.delete',
                'customer-communications.restore',
                'customer-communications.forceDelete',
                'customer-communications.schedule',
                'customer-communications.send',
                'customer-communications.cancel',
                'customer-communications.reschedule',
                'customer-communications.markAsDelivered',
                'customer-communications.markAsOpened',
                'customer-communications.markAsClicked',
                'customer-communications.markAsBounced',
                'customer-communications.markAsUnsubscribed',
                'customer-communications.viewAnalytics',
                'customer-communications.exportData',
                'customer-communications.importData',
                'customer-communications.manageAttachments',
                'customer-communications.viewTrackingData',
            ]);
        }

        // Admin - Most permissions except force delete
        $adminRole = Role::where('name', 'admin')->first();
        if ($adminRole) {
            $adminRole->givePermissionTo([
                'customer-communications.viewAny',
                'customer-communications.view',
                'customer-communications.create',
                'customer-communications.update',
                'customer-communications.delete',
                'customer-communications.restore',
                'customer-communications.schedule',
                'customer-communications.send',
                'customer-communications.cancel',
                'customer-communications.reschedule',
                'customer-communications.markAsDelivered',
                'customer-communications.markAsOpened',
                'customer-communications.markAsClicked',
                'customer-communications.markAsBounced',
                'customer-communications.markAsUnsubscribed',
                'customer-communications.viewAnalytics',
                'customer-communications.exportData',
                'customer-communications.importData',
                'customer-communications.manageAttachments',
                'customer-communications.viewTrackingData',
            ]);
        }

        // Manager - View, create, update, schedule, send, analytics
        $managerRole = Role::where('name', 'manager')->first();
        if ($managerRole) {
            $managerRole->givePermissionTo([
                'customer-communications.viewAny',
                'customer-communications.view',
                'customer-communications.create',
                'customer-communications.update',
                'customer-communications.schedule',
                'customer-communications.send',
                'customer-communications.cancel',
                'customer-communications.reschedule',
                'customer-communications.markAsDelivered',
                'customer-communications.markAsOpened',
                'customer-communications.markAsClicked',
                'customer-communications.markAsBounced',
                'customer-communications.markAsUnsubscribed',
                'customer-communications.viewAnalytics',
                'customer-communications.exportData',
                'customer-communications.manageAttachments',
                'customer-communications.viewTrackingData',
            ]);
        }

        // Customer Service - View, create, update, basic operations
        $customerServiceRole = Role::where('name', 'customer-service')->first();
        if ($customerServiceRole) {
            $customerServiceRole->givePermissionTo([
                'customer-communications.viewAny',
                'customer-communications.view',
                'customer-communications.create',
                'customer-communications.update',
                'customer-communications.schedule',
                'customer-communications.send',
                'customer-communications.markAsDelivered',
                'customer-communications.markAsOpened',
                'customer-communications.markAsClicked',
                'customer-communications.markAsBounced',
                'customer-communications.markAsUnsubscribed',
                'customer-communications.viewAnalytics',
                'customer-communications.manageAttachments',
                'customer-communications.viewTrackingData',
            ]);
        }

        // Marketing - View, create, update, schedule, analytics
        $marketingRole = Role::where('name', 'marketing')->first();
        if ($marketingRole) {
            $marketingRole->givePermissionTo([
                'customer-communications.viewAny',
                'customer-communications.view',
                'customer-communications.create',
                'customer-communications.update',
                'customer-communications.schedule',
                'customer-communications.send',
                'customer-communications.cancel',
                'customer-communications.reschedule',
                'customer-communications.markAsDelivered',
                'customer-communications.markAsOpened',
                'customer-communications.markAsClicked',
                'customer-communications.markAsBounced',
                'customer-communications.markAsUnsubscribed',
                'customer-communications.viewAnalytics',
                'customer-communications.exportData',
                'customer-communications.importData',
                'customer-communications.manageAttachments',
                'customer-communications.viewTrackingData',
            ]);
        }

        // Viewer - Read-only access
        $viewerRole = Role::where('name', 'viewer')->first();
        if ($viewerRole) {
            $viewerRole->givePermissionTo([
                'customer-communications.viewAny',
                'customer-communications.view',
                'customer-communications.viewAnalytics',
                'customer-communications.viewTrackingData',
            ]);
        }
    }
}
