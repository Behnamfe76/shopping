<?php

namespace Fereydooni\Shopping\app\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Fereydooni\Shopping\app\Models\User;
use Fereydooni\Shopping\app\Models\Employee;
use Fereydooni\Shopping\app\Enums\EmployeeStatus;
use Fereydooni\Shopping\app\Enums\EmploymentType;
use Fereydooni\Shopping\app\Enums\Gender;

class SeedRequiredDataCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'shopping:seed-required
                            {--users : Seed users only}
                            {--roles : Seed roles only}
                            {--permissions : Seed permissions only}
                            {--employees : Seed employees only}
                            {--force : Force the operation without confirmation}
                            {--fresh : Run fresh migrations before seeding}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Seed required data for the shopping package (users, roles, permissions, employees)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $users = $this->option('users');
        $roles = $this->option('roles');
        $permissions = $this->option('permissions');
        $employees = $this->option('employees');
        $force = $this->option('force');
        $fresh = $this->option('fresh');

        // If no specific option is provided, run all
        if (!$users && !$roles && !$permissions && !$employees) {
            $users = $roles = $permissions = $employees = true;
        }

        // If fresh option is provided, run fresh migrations first
        if ($fresh) {
            $this->info('Running fresh migrations...');
            Artisan::call('migrate:fresh', [], $this->getOutput());
        }

        if (!$force) {
            if (!$this->confirm('Are you sure you want to seed required data? This will create users, roles, and permissions.')) {
                $this->info('Seeding cancelled.');
                return;
            }
        }

        $this->info('Starting to seed required data...');

        if ($permissions) {
            $this->seedPermissions();
        }

        if ($roles) {
            $this->seedRoles();
        }

        if ($users) {
            // Ensure roles exist before creating users
            if (!$roles) {
                $this->seedRoles();
            }
            $this->seedUsers();
        }

        if ($employees) {
            // Ensure users exist before creating employees
            if (!$users) {
                $this->seedUsers();
            }
            $this->seedEmployees();
        }

        $this->info('Required data seeding completed successfully!');
    }

    /**
     * Seed permissions for the shopping package.
     */
    protected function seedPermissions(): void
    {
        $this->info('Seeding permissions...');

        // Run all permission seeders
        $permissionSeeders = [
            'CategoryPermissionSeeder',
            'AddressPermissionSeeder',
            'OrderPermissionSeeder',
            'OrderItemPermissionSeeder',
            'OrderStatusHistoryPermissionSeeder',
            'TransactionPermissionSeeder',
            'ShipmentPermissionSeeder',
            'ShipmentItemPermissionSeeder',
            'ProductAttributePermissionSeeder',
            'ProductAttributeValuePermissionSeeder',
            'ProductDiscountPermissionSeeder',
            'ProductPermissionSeeder',
            'ProductMetaPermissionSeeder',
            'ProductReviewPermissionSeeder',
            'ProductTagPermissionSeeder',
            'ProductVariantPermissionSeeder',
            'UserSubscriptionPermissionSeeder',
            'CustomerPermissionSeeder',
            'CustomerPreferencePermissionSeeder',
            'CustomerWishlistPermissionSeeder',
            'CustomerCommunicationPermissionSeeder',
            'EmployeePermissionSeeder',
        ];

        foreach ($permissionSeeders as $seeder) {
            try {
                $this->info("Running {$seeder}...");
                Artisan::call('shopping:seed', [
                    '--class' => $seeder,
                    '--force' => true
                ], $this->getOutput());
            } catch (\Exception $e) {
                $this->warn("Warning: Could not run {$seeder}: " . $e->getMessage());
            }
        }
    }

    /**
     * Seed roles for the shopping package.
     */
    protected function seedRoles(): void
    {
        $this->info('Seeding roles...');

        // Get the guard name from the User model or use default
        $guardName = config('auth.defaults.guard', 'web');

        $roles = [
            'super-admin' => 'Super Administrator - Full access to everything',
            'admin' => 'Administrator - Full access to shopping system',
            'manager' => 'Manager - Manage products, orders, and customers',
            'employee' => 'Employee - Basic access to assigned tasks',
            'customer' => 'Customer - Access to customer features',
            'guest' => 'Guest - Limited access to public features',
        ];

        foreach ($roles as $name => $description) {
            Role::firstOrCreate(['name' => $name], [
                'guard_name' => $guardName,
                'description' => $description,
            ]);
            $this->info("Created role: {$name}");
        }

        // Assign permissions to roles
        $this->assignPermissionsToRoles();
    }

    /**
     * Assign permissions to roles.
     */
    protected function assignPermissionsToRoles(): void
    {
        $this->info('Assigning permissions to roles...');

        // Super Admin gets all permissions
        $superAdmin = Role::where('name', 'super-admin')->first();
        if ($superAdmin) {
            $superAdmin->givePermissionTo(Permission::all());
            $this->info('Assigned all permissions to super-admin');
        }

        // Admin gets most permissions (excluding super-admin specific ones)
        $admin = Role::where('name', 'admin')->first();
        if ($admin) {
            $adminPermissions = Permission::whereNotIn('name', [
                'manage-system-settings',
                'manage-roles-permissions',
            ])->get();
            $admin->givePermissionTo($adminPermissions);
            $this->info('Assigned admin permissions');
        }

        // Manager gets management permissions
        $manager = Role::where('name', 'manager')->first();
        if ($manager) {
            $managerPermissions = Permission::whereIn('name', [
                'view-products', 'create-products', 'edit-products', 'delete-products',
                'view-orders', 'create-orders', 'edit-orders', 'delete-orders',
                'view-customers', 'create-customers', 'edit-customers', 'delete-customers',
                'view-categories', 'create-categories', 'edit-categories', 'delete-categories',
                'view-brands', 'create-brands', 'edit-brands', 'delete-brands',
                'view-reports', 'export-data',
            ])->get();
            $manager->givePermissionTo($managerPermissions);
            $this->info('Assigned manager permissions');
        }

        // Employee gets basic permissions
        $employee = Role::where('name', 'employee')->first();
        if ($employee) {
            $employeePermissions = Permission::whereIn('name', [
                'view-products', 'view-orders', 'view-customers',
                'create-orders', 'edit-orders',
                'view-reports',
            ])->get();
            $employee->givePermissionTo($employeePermissions);
            $this->info('Assigned employee permissions');
        }

        // Customer gets customer-specific permissions
        $customer = Role::where('name', 'customer')->first();
        if ($customer) {
            $customerPermissions = Permission::whereIn('name', [
                'view-own-profile', 'edit-own-profile',
                'view-own-orders', 'create-orders',
                'view-own-wishlist', 'manage-own-wishlist',
                'view-own-preferences', 'manage-own-preferences',
            ])->get();
            $customer->givePermissionTo($customerPermissions);
            $this->info('Assigned customer permissions');
        }
    }

    /**
     * Seed users for the shopping package.
     */
    protected function seedUsers(): void
    {
        $this->info('Seeding users...');

        $users = [
            [
                'name' => 'Super Admin',
                'email' => 'superadmin@shopping.com',
                'password' => 'password',
                'email_verified_at' => now(),
                'role' => 'super-admin',
            ],
            [
                'name' => 'Admin User',
                'email' => 'admin@shopping.com',
                'password' => 'password',
                'email_verified_at' => now(),
                'role' => 'admin',
            ],
            [
                'name' => 'Manager User',
                'email' => 'manager@shopping.com',
                'password' => 'password',
                'email_verified_at' => now(),
                'role' => 'manager',
            ],
            [
                'name' => 'Employee User',
                'email' => 'employee@shopping.com',
                'password' => 'password',
                'email_verified_at' => now(),
                'role' => 'employee',
            ],
            [
                'name' => 'Customer User',
                'email' => 'customer@shopping.com',
                'password' => 'password',
                'email_verified_at' => now(),
                'role' => 'customer',
            ],
        ];

        foreach ($users as $userData) {
            $role = $userData['role'];
            unset($userData['role']);
            $userModel = config('shopping.user_model');
            $user = $userModel::firstOrCreate(
                ['email' => $userData['email']],
                array_merge($userData, [
                    'password' => Hash::make($userData['password']),
                ])
            );

            // Ensure the role exists and assign it
            $roleModel = Role::where('name', $role)->first();
            if ($roleModel) {
                $user->assignRole($roleModel);
                $this->info("Created user: {$user->email} with role: {$role}");
            } else {
                $this->warn("Role '{$role}' not found for user: {$user->email}");
            }
        }
    }

    /**
     * Seed employees for the shopping package.
     */
    protected function seedEmployees(): void
    {
        $this->info('Seeding employees...');

        // First, ensure we have users for employees
        $this->seedUsers();

        $employees = [
            [
                'user_id' => User::where('email', 'superadmin@shopping.com')->first()->id,
                'employee_number' => 'EMP001',
                'first_name' => 'Super',
                'last_name' => 'Admin',
                'email' => 'superadmin@shopping.com',
                'phone' => '+1234567890',
                'date_of_birth' => '1985-01-01',
                'gender' => Gender::MALE,
                'hire_date' => '2020-01-01',
                'position' => 'Super Administrator',
                'department' => 'IT',
                'manager_id' => null,
                'salary' => 120000.00,
                'hourly_rate' => 57.69,
                'employment_type' => EmploymentType::FULL_TIME,
                'status' => EmployeeStatus::ACTIVE,
                'emergency_contact_name' => 'Emergency Contact',
                'emergency_contact_phone' => '+1234567891',
                'emergency_contact_relationship' => 'Spouse',
                'address' => '123 Admin St',
                'city' => 'New York',
                'state' => 'NY',
                'postal_code' => '10001',
                'country' => 'USA',
                'benefits_enrolled' => true,
                'vacation_days_used' => 0,
                'vacation_days_total' => 25,
                'sick_days_used' => 0,
                'sick_days_total' => 15,
                'performance_rating' => 5.0,
                'training_completed' => ['System Administration', 'Security'],
                'certifications' => ['ITIL', 'CISSP'],
                'skills' => ['System Administration', 'Security', 'Management'],
                'notes' => 'Super administrator with full system access.',
            ],
            [
                'user_id' => User::where('email', 'admin@shopping.com')->first()->id,
                'employee_number' => 'EMP002',
                'first_name' => 'Admin',
                'last_name' => 'User',
                'email' => 'admin@shopping.com',
                'phone' => '+1234567892',
                'date_of_birth' => '1990-05-15',
                'gender' => Gender::FEMALE,
                'hire_date' => '2021-03-15',
                'position' => 'System Administrator',
                'department' => 'IT',
                'manager_id' => 1, // Super Admin
                'salary' => 95000.00,
                'hourly_rate' => 45.67,
                'employment_type' => EmploymentType::FULL_TIME,
                'status' => EmployeeStatus::ACTIVE,
                'emergency_contact_name' => 'Emergency Contact',
                'emergency_contact_phone' => '+1234567893',
                'emergency_contact_relationship' => 'Spouse',
                'address' => '456 Admin Ave',
                'city' => 'New York',
                'state' => 'NY',
                'postal_code' => '10002',
                'country' => 'USA',
                'benefits_enrolled' => true,
                'vacation_days_used' => 5,
                'vacation_days_total' => 20,
                'sick_days_used' => 2,
                'sick_days_total' => 10,
                'performance_rating' => 4.5,
                'training_completed' => ['Laravel', 'Vue.js', 'Docker'],
                'certifications' => ['AWS Certified Developer', 'Laravel Certified Developer'],
                'skills' => ['PHP', 'Laravel', 'Vue.js', 'MySQL', 'Docker'],
                'notes' => 'Experienced system administrator with strong technical skills.',
            ],
            [
                'user_id' => User::where('email', 'manager@shopping.com')->first()->id,
                'employee_number' => 'EMP003',
                'first_name' => 'Manager',
                'last_name' => 'User',
                'email' => 'manager@shopping.com',
                'phone' => '+1234567894',
                'date_of_birth' => '1988-08-20',
                'gender' => Gender::MALE,
                'hire_date' => '2022-01-10',
                'position' => 'Operations Manager',
                'department' => 'Operations',
                'manager_id' => 2, // Admin
                'salary' => 75000.00,
                'hourly_rate' => 36.06,
                'employment_type' => EmploymentType::FULL_TIME,
                'status' => EmployeeStatus::ACTIVE,
                'emergency_contact_name' => 'Emergency Contact',
                'emergency_contact_phone' => '+1234567895',
                'emergency_contact_relationship' => 'Spouse',
                'address' => '789 Manager Blvd',
                'city' => 'New York',
                'state' => 'NY',
                'postal_code' => '10003',
                'country' => 'USA',
                'benefits_enrolled' => true,
                'vacation_days_used' => 8,
                'vacation_days_total' => 20,
                'sick_days_used' => 1,
                'sick_days_total' => 10,
                'performance_rating' => 4.2,
                'training_completed' => ['Project Management', 'Team Leadership'],
                'certifications' => ['PMP', 'Scrum Master'],
                'skills' => ['Project Management', 'Team Leadership', 'Operations'],
                'notes' => 'Experienced operations manager with strong leadership skills.',
            ],
            [
                'user_id' => User::where('email', 'employee@shopping.com')->first()->id,
                'employee_number' => 'EMP004',
                'first_name' => 'Employee',
                'last_name' => 'User',
                'email' => 'employee@shopping.com',
                'phone' => '+1234567896',
                'date_of_birth' => '1995-12-10',
                'gender' => Gender::FEMALE,
                'hire_date' => '2023-06-01',
                'position' => 'Customer Support Representative',
                'department' => 'Customer Support',
                'manager_id' => 3, // Manager
                'salary' => 45000.00,
                'hourly_rate' => 21.63,
                'employment_type' => EmploymentType::FULL_TIME,
                'status' => EmployeeStatus::ACTIVE,
                'emergency_contact_name' => 'Emergency Contact',
                'emergency_contact_phone' => '+1234567897',
                'emergency_contact_relationship' => 'Parent',
                'address' => '321 Employee St',
                'city' => 'New York',
                'state' => 'NY',
                'postal_code' => '10004',
                'country' => 'USA',
                'benefits_enrolled' => true,
                'vacation_days_used' => 3,
                'vacation_days_total' => 15,
                'sick_days_used' => 0,
                'sick_days_total' => 10,
                'performance_rating' => 4.0,
                'training_completed' => ['Customer Service', 'Product Knowledge'],
                'certifications' => ['Customer Service Excellence'],
                'skills' => ['Customer Service', 'Communication', 'Problem Solving'],
                'notes' => 'Dedicated customer support representative with excellent communication skills.',
            ],
        ];

        foreach ($employees as $employeeData) {
            $employee = Employee::firstOrCreate(
                ['email' => $employeeData['email']],
                $employeeData
            );
            $this->info("Created employee: {$employee->first_name} {$employee->last_name}");
        }
    }
}
