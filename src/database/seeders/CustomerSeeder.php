<?php

namespace Fereydooni\Shopping\database\seeders;

use App\Models\User;
use Fereydooni\Shopping\app\Enums\CustomerStatus;
use Fereydooni\Shopping\app\Enums\CustomerType;
use Fereydooni\Shopping\app\Enums\Gender;
use Fereydooni\Shopping\app\Models\Customer;
use Illuminate\Database\Seeder;

class CustomerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create 100 users for customers
        $users = User::factory()->count(100)->create();

        // Create sample customers with predefined data
        $customers = [
            [
                'user_id' => $users[0]->id,
                'first_name' => 'John',
                'last_name' => 'Doe',
                'email' => 'john.doe@example.com',
                'phone' => '+1234567890',
                'date_of_birth' => '1990-01-15',
                'gender' => Gender::MALE,
                'company_name' => null,
                'tax_id' => null,
                'customer_type' => CustomerType::INDIVIDUAL,
                'status' => CustomerStatus::ACTIVE,
                'loyalty_points' => 150,
                'total_orders' => 5,
                'total_spent' => 1250.00,
                'average_order_value' => 250.00,
                'last_order_date' => now()->subDays(30),
                'first_order_date' => now()->subMonths(6),
                'preferred_payment_method' => 'credit_card',
                'preferred_shipping_method' => 'standard',
                'marketing_consent' => true,
                'newsletter_subscription' => true,
                'notes' => 'Regular customer, prefers premium products',
                'tags' => 'premium,regular',
                'address_count' => 2,
                'order_count' => 5,
                'review_count' => 3,
                'wishlist_count' => 8,
            ],
            [
                'user_id' => $users[1]->id,
                'first_name' => 'Jane',
                'last_name' => 'Smith',
                'email' => 'jane.smith@example.com',
                'phone' => '+1234567891',
                'date_of_birth' => '1985-05-20',
                'gender' => Gender::FEMALE,
                'company_name' => 'Smith Enterprises',
                'tax_id' => 'TAX123456',
                'customer_type' => CustomerType::BUSINESS,
                'status' => CustomerStatus::ACTIVE,
                'loyalty_points' => 500,
                'total_orders' => 12,
                'total_spent' => 3500.00,
                'average_order_value' => 291.67,
                'last_order_date' => now()->subDays(7),
                'first_order_date' => now()->subMonths(12),
                'preferred_payment_method' => 'bank_transfer',
                'preferred_shipping_method' => 'express',
                'marketing_consent' => true,
                'newsletter_subscription' => false,
                'notes' => 'Business customer, bulk orders',
                'tags' => 'business,bulk',
                'address_count' => 3,
                'order_count' => 12,
                'review_count' => 8,
                'wishlist_count' => 15,
            ],
            [
                'user_id' => $users[2]->id,
                'first_name' => 'Mike',
                'last_name' => 'Johnson',
                'email' => 'mike.johnson@example.com',
                'phone' => '+1234567892',
                'date_of_birth' => '1992-08-10',
                'gender' => Gender::MALE,
                'company_name' => null,
                'tax_id' => null,
                'customer_type' => CustomerType::INDIVIDUAL,
                'status' => CustomerStatus::PENDING,
                'loyalty_points' => 0,
                'total_orders' => 0,
                'total_spent' => 0.00,
                'average_order_value' => 0.00,
                'last_order_date' => null,
                'first_order_date' => null,
                'preferred_payment_method' => null,
                'preferred_shipping_method' => null,
                'marketing_consent' => false,
                'newsletter_subscription' => false,
                'notes' => 'New customer, needs onboarding',
                'tags' => 'new,onboarding',
                'address_count' => 0,
                'order_count' => 0,
                'review_count' => 0,
                'wishlist_count' => 0,
            ],
            [
                'user_id' => $users[3]->id,
                'first_name' => 'Sarah',
                'last_name' => 'Wilson',
                'email' => 'sarah.wilson@example.com',
                'phone' => '+1234567893',
                'date_of_birth' => '1988-12-03',
                'gender' => Gender::FEMALE,
                'company_name' => 'Wilson Retail',
                'tax_id' => 'TAX789012',
                'customer_type' => CustomerType::WHOLESALE,
                'status' => CustomerStatus::ACTIVE,
                'loyalty_points' => 1200,
                'total_orders' => 25,
                'total_spent' => 15000.00,
                'average_order_value' => 600.00,
                'last_order_date' => now()->subDays(2),
                'first_order_date' => now()->subMonths(18),
                'preferred_payment_method' => 'invoice',
                'preferred_shipping_method' => 'freight',
                'marketing_consent' => true,
                'newsletter_subscription' => true,
                'notes' => 'Wholesale customer, high volume orders',
                'tags' => 'wholesale,high-volume',
                'address_count' => 5,
                'order_count' => 25,
                'review_count' => 12,
                'wishlist_count' => 30,
            ],
            [
                'user_id' => $users[4]->id,
                'first_name' => 'David',
                'last_name' => 'Brown',
                'email' => 'david.brown@example.com',
                'phone' => '+1234567894',
                'date_of_birth' => '1975-03-25',
                'gender' => Gender::MALE,
                'company_name' => null,
                'tax_id' => null,
                'customer_type' => CustomerType::VIP,
                'status' => CustomerStatus::ACTIVE,
                'loyalty_points' => 2500,
                'total_orders' => 50,
                'total_spent' => 25000.00,
                'average_order_value' => 500.00,
                'last_order_date' => now()->subDays(1),
                'first_order_date' => now()->subMonths(24),
                'preferred_payment_method' => 'credit_card',
                'preferred_shipping_method' => 'premium',
                'marketing_consent' => true,
                'newsletter_subscription' => true,
                'notes' => 'VIP customer, premium service required',
                'tags' => 'vip,premium',
                'address_count' => 4,
                'order_count' => 50,
                'review_count' => 25,
                'wishlist_count' => 45,
            ],
        ];

        foreach ($customers as $customerData) {
            Customer::create($customerData);
        }

        // Create additional random customers (95 more to reach 100 total)
        for ($i = 5; $i < 100; $i++) {
            $gender = Gender::cases()[array_rand(Gender::cases())];
            $customerType = CustomerType::cases()[array_rand(CustomerType::cases())];
            $status = CustomerStatus::cases()[array_rand(CustomerStatus::cases())];

            Customer::create([
                'user_id' => $users[$i]->id,
                'first_name' => fake()->firstName(),
                'last_name' => fake()->lastName(),
                'email' => fake()->unique()->safeEmail(),
                'phone' => fake()->phoneNumber(),
                'date_of_birth' => fake()->date(),
                'gender' => $gender,
                'company_name' => $customerType === CustomerType::BUSINESS || $customerType === CustomerType::WHOLESALE ? fake()->company() : null,
                'tax_id' => $customerType === CustomerType::BUSINESS || $customerType === CustomerType::WHOLESALE ? 'TAX'.fake()->numerify('######') : null,
                'customer_type' => $customerType,
                'status' => $status,
                'loyalty_points' => fake()->numberBetween(0, 1000),
                'total_orders' => fake()->numberBetween(0, 20),
                'total_spent' => fake()->randomFloat(2, 0, 5000),
                'average_order_value' => fake()->randomFloat(2, 0, 500),
                'last_order_date' => fake()->optional()->dateTimeBetween('-1 year', 'now'),
                'first_order_date' => fake()->optional()->dateTimeBetween('-2 years', 'now'),
                'preferred_payment_method' => fake()->optional()->randomElement(['credit_card', 'debit_card', 'paypal', 'bank_transfer']),
                'preferred_shipping_method' => fake()->optional()->randomElement(['standard', 'express', 'premium', 'freight']),
                'marketing_consent' => fake()->boolean(),
                'newsletter_subscription' => fake()->boolean(),
                'notes' => fake()->optional()->sentence(),
                'tags' => fake()->optional()->words(3, true),
                'address_count' => fake()->numberBetween(0, 5),
                'order_count' => fake()->numberBetween(0, 20),
                'review_count' => fake()->numberBetween(0, 10),
                'wishlist_count' => fake()->numberBetween(0, 20),
            ]);
        }
    }
}
