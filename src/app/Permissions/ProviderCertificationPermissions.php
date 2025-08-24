<?php

namespace Fereydooni\Shopping\App\Permissions;

class ProviderCertificationPermissions
{
    /**
     * Get all provider certification permissions.
     */
    public static function getAllPermissions(): array
    {
        return [
            // Basic CRUD permissions
            'provider-certification.view' => [
                'name' => 'View Provider Certifications',
                'description' => 'Can view provider certification information',
                'guard_name' => 'web',
            ],
            'provider-certification.create' => [
                'name' => 'Create Provider Certifications',
                'description' => 'Can create new provider certifications',
                'guard_name' => 'web',
            ],
            'provider-certification.edit' => [
                'name' => 'Edit Provider Certifications',
                'description' => 'Can edit existing provider certifications',
                'guard_name' => 'web',
            ],
            'provider-certification.delete' => [
                'name' => 'Delete Provider Certifications',
                'description' => 'Can delete provider certifications',
                'guard_name' => 'web',
            ],

            // Verification permissions
            'provider-certification.verify' => [
                'name' => 'Verify Provider Certifications',
                'description' => 'Can verify provider certifications',
                'guard_name' => 'web',
            ],
            'provider-certification.reject' => [
                'name' => 'Reject Provider Certifications',
                'description' => 'Can reject provider certification verification',
                'guard_name' => 'web',
            ],
            'provider-certification.require-update' => [
                'name' => 'Require Provider Certification Updates',
                'description' => 'Can require updates to provider certifications',
                'guard_name' => 'web',
            ],

            // Status management permissions
            'provider-certification.activate' => [
                'name' => 'Activate Provider Certifications',
                'description' => 'Can activate provider certifications',
                'guard_name' => 'web',
            ],
            'provider-certification.suspend' => [
                'name' => 'Suspend Provider Certifications',
                'description' => 'Can suspend provider certifications',
                'guard_name' => 'web',
            ],
            'provider-certification.revoke' => [
                'name' => 'Revoke Provider Certifications',
                'description' => 'Can revoke provider certifications',
                'guard_name' => 'web',
            ],
            'provider-certification.renew' => [
                'name' => 'Renew Provider Certifications',
                'description' => 'Can renew provider certifications',
                'guard_name' => 'web',
            ],
            'provider-certification.expire' => [
                'name' => 'Expire Provider Certifications',
                'description' => 'Can mark provider certifications as expired',
                'guard_name' => 'web',
            ],

            // Ownership-based permissions
            'provider-certification.view-own' => [
                'name' => 'View Own Provider Certifications',
                'description' => 'Can view their own provider certifications',
                'guard_name' => 'web',
            ],
            'provider-certification.create-own' => [
                'name' => 'Create Own Provider Certifications',
                'description' => 'Can create their own provider certifications',
                'guard_name' => 'web',
            ],
            'provider-certification.edit-own' => [
                'name' => 'Edit Own Provider Certifications',
                'description' => 'Can edit their own provider certifications',
                'guard_name' => 'web',
            ],
            'provider-certification.delete-own' => [
                'name' => 'Delete Own Provider Certifications',
                'description' => 'Can delete their own provider certifications',
                'guard_name' => 'web',
            ],

            // Administrative permissions
            'provider-certification.view-all' => [
                'name' => 'View All Provider Certifications',
                'description' => 'Can view all provider certifications in the system',
                'guard_name' => 'web',
            ],
            'provider-certification.manage-all' => [
                'name' => 'Manage All Provider Certifications',
                'description' => 'Can manage all provider certifications in the system',
                'guard_name' => 'web',
            ],

            // Data management permissions
            'provider-certification.export' => [
                'name' => 'Export Provider Certification Data',
                'description' => 'Can export provider certification data',
                'guard_name' => 'web',
            ],
            'provider-certification.import' => [
                'name' => 'Import Provider Certification Data',
                'description' => 'Can import provider certification data',
                'guard_name' => 'web',
            ],

            // Analytics and reporting permissions
            'provider-certification.statistics' => [
                'name' => 'View Provider Certification Statistics',
                'description' => 'Can view provider certification statistics and reports',
                'guard_name' => 'web',
            ],
            'provider-certification.analytics' => [
                'name' => 'Access Provider Certification Analytics',
                'description' => 'Can access advanced provider certification analytics',
                'guard_name' => 'web',
            ],

            // Workflow permissions
            'provider-certification.approve' => [
                'name' => 'Approve Provider Certifications',
                'description' => 'Can approve provider certification workflows',
                'guard_name' => 'web',
            ],
            'provider-certification.review' => [
                'name' => 'Review Provider Certifications',
                'description' => 'Can review provider certifications for approval',
                'guard_name' => 'web',
            ],
            'provider-certification.escalate' => [
                'name' => 'Escalate Provider Certifications',
                'description' => 'Can escalate provider certification issues',
                'guard_name' => 'web',
            ],

            // Notification permissions
            'provider-certification.notify' => [
                'name' => 'Send Provider Certification Notifications',
                'description' => 'Can send notifications related to provider certifications',
                'guard_name' => 'web',
            ],
            'provider-certification.remind' => [
                'name' => 'Send Provider Certification Reminders',
                'description' => 'Can send reminders for expiring certifications',
                'guard_name' => 'web',
            ],

            // Audit and compliance permissions
            'provider-certification.audit' => [
                'name' => 'Audit Provider Certifications',
                'description' => 'Can audit provider certification records',
                'guard_name' => 'web',
            ],
            'provider-certification.compliance' => [
                'name' => 'Manage Provider Certification Compliance',
                'description' => 'Can manage compliance requirements for provider certifications',
                'guard_name' => 'web',
            ],
        ];
    }

    /**
     * Get basic CRUD permissions.
     */
    public static function getBasicPermissions(): array
    {
        return [
            'provider-certification.view',
            'provider-certification.create',
            'provider-certification.edit',
            'provider-certification.delete',
        ];
    }

    /**
     * Get verification permissions.
     */
    public static function getVerificationPermissions(): array
    {
        return [
            'provider-certification.verify',
            'provider-certification.reject',
            'provider-certification.require-update',
        ];
    }

    /**
     * Get status management permissions.
     */
    public static function getStatusManagementPermissions(): array
    {
        return [
            'provider-certification.activate',
            'provider-certification.suspend',
            'provider-certification.revoke',
            'provider-certification.renew',
            'provider-certification.expire',
        ];
    }

    /**
     * Get ownership-based permissions.
     */
    public static function getOwnershipPermissions(): array
    {
        return [
            'provider-certification.view-own',
            'provider-certification.create-own',
            'provider-certification.edit-own',
            'provider-certification.delete-own',
        ];
    }

    /**
     * Get administrative permissions.
     */
    public static function getAdministrativePermissions(): array
    {
        return [
            'provider-certification.view-all',
            'provider-certification.manage-all',
        ];
    }

    /**
     * Get data management permissions.
     */
    public static function getDataManagementPermissions(): array
    {
        return [
            'provider-certification.export',
            'provider-certification.import',
        ];
    }

    /**
     * Get analytics permissions.
     */
    public static function getAnalyticsPermissions(): array
    {
        return [
            'provider-certification.statistics',
            'provider-certification.analytics',
        ];
    }

    /**
     * Get workflow permissions.
     */
    public static function getWorkflowPermissions(): array
    {
        return [
            'provider-certification.approve',
            'provider-certification.review',
            'provider-certification.escalate',
        ];
    }

    /**
     * Get notification permissions.
     */
    public static function getNotificationPermissions(): array
    {
        return [
            'provider-certification.notify',
            'provider-certification.remind',
        ];
    }

    /**
     * Get audit and compliance permissions.
     */
    public static function getAuditPermissions(): array
    {
        return [
            'provider-certification.audit',
            'provider-certification.compliance',
        ];
    }

    /**
     * Get permissions for provider users.
     */
    public static function getProviderUserPermissions(): array
    {
        return [
            'provider-certification.view-own',
            'provider-certification.create-own',
            'provider-certification.edit-own',
            'provider-certification.delete-own',
        ];
    }

    /**
     * Get permissions for certification managers.
     */
    public static function getManagerPermissions(): array
    {
        return [
            'provider-certification.view',
            'provider-certification.create',
            'provider-certification.edit',
            'provider-certification.verify',
            'provider-certification.reject',
            'provider-certification.require-update',
            'provider-certification.activate',
            'provider-certification.suspend',
            'provider-certification.renew',
            'provider-certification.statistics',
            'provider-certification.notify',
            'provider-certification.remind',
        ];
    }

    /**
     * Get permissions for certification administrators.
     */
    public static function getAdministratorPermissions(): array
    {
        return array_merge(
            self::getManagerPermissions(),
            [
                'provider-certification.delete',
                'provider-certification.revoke',
                'provider-certification.expire',
                'provider-certification.view-all',
                'provider-certification.manage-all',
                'provider-certification.export',
                'provider-certification.import',
                'provider-certification.analytics',
                'provider-certification.approve',
                'provider-certification.review',
                'provider-certification.escalate',
                'provider-certification.audit',
                'provider-certification.compliance',
            ]
        );
    }

    /**
     * Get permissions for certification auditors.
     */
    public static function getAuditorPermissions(): array
    {
        return [
            'provider-certification.view-all',
            'provider-certification.audit',
            'provider-certification.compliance',
            'provider-certification.statistics',
            'provider-certification.analytics',
            'provider-certification.export',
        ];
    }

    /**
     * Get permissions for certification reviewers.
     */
    public static function getReviewerPermissions(): array
    {
        return [
            'provider-certification.view',
            'provider-certification.verify',
            'provider-certification.reject',
            'provider-certification.require-update',
            'provider-certification.review',
            'provider-certification.statistics',
        ];
    }

    /**
     * Check if a permission is a basic CRUD permission.
     */
    public static function isBasicPermission(string $permission): bool
    {
        return in_array($permission, self::getBasicPermissions());
    }

    /**
     * Check if a permission is a verification permission.
     */
    public static function isVerificationPermission(string $permission): bool
    {
        return in_array($permission, self::getVerificationPermissions());
    }

    /**
     * Check if a permission is a status management permission.
     */
    public static function isStatusManagementPermission(string $permission): bool
    {
        return in_array($permission, self::getStatusManagementPermissions());
    }

    /**
     * Check if a permission is an ownership permission.
     */
    public static function isOwnershipPermission(string $permission): bool
    {
        return in_array($permission, self::getOwnershipPermissions());
    }

    /**
     * Check if a permission is an administrative permission.
     */
    public static function isAdministrativePermission(string $permission): bool
    {
        return in_array($permission, self::getAdministrativePermissions());
    }

    /**
     * Get permission descriptions for display.
     */
    public static function getPermissionDescriptions(): array
    {
        $permissions = self::getAllPermissions();
        $descriptions = [];

        foreach ($permissions as $key => $permission) {
            $descriptions[$key] = $permission['description'];
        }

        return $descriptions;
    }

    /**
     * Get permission names for display.
     */
    public static function getPermissionNames(): array
    {
        $permissions = self::getAllPermissions();
        $names = [];

        foreach ($permissions as $key => $permission) {
            $names[$key] = $permission['name'];
        }

        return $names;
    }

    /**
     * Get permissions grouped by category.
     */
    public static function getPermissionsByCategory(): array
    {
        return [
            'Basic Operations' => self::getBasicPermissions(),
            'Verification' => self::getVerificationPermissions(),
            'Status Management' => self::getStatusManagementPermissions(),
            'Ownership' => self::getOwnershipPermissions(),
            'Administrative' => self::getAdministrativePermissions(),
            'Data Management' => self::getDataManagementPermissions(),
            'Analytics' => self::getAnalyticsPermissions(),
            'Workflow' => self::getWorkflowPermissions(),
            'Notifications' => self::getNotificationPermissions(),
            'Audit & Compliance' => self::getAuditPermissions(),
        ];
    }
}
