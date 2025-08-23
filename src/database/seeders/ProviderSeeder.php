<?php

namespace Fereydooni\Shopping\app\Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Fereydooni\Shopping\app\Models\Provider;
use Fereydooni\Shopping\app\Models\User;
use Fereydooni\Shopping\app\Enums\ProviderStatus;
use Fereydooni\Shopping\app\Enums\ProviderType;

class ProviderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get or create a test user
        $user = User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Admin User',
                'password' => bcrypt('password'),
            ]
        );

        $providers = [
            [
                'user_id' => $user->id,
                'provider_number' => 'PROV-001',
                'company_name' => 'Tech Solutions Inc.',
                'contact_person' => 'John Smith',
                'email' => 'john@techsolutions.com',
                'phone' => '+1-555-0101',
                'website' => 'https://techsolutions.com',
                'tax_id' => 'TAX-001-001',
                'business_license' => 'BL-001-001',
                'provider_type' => ProviderType::MANUFACTURER,
                'status' => ProviderStatus::ACTIVE,
                'rating' => 4.5,
                'total_orders' => 150,
                'total_spent' => 75000.00,
                'average_order_value' => 500.00,
                'last_order_date' => now()->subDays(5),
                'first_order_date' => now()->subMonths(6),
                'payment_terms' => 'Net 30',
                'credit_limit' => 100000.00,
                'current_balance' => 25000.00,
                'address' => '123 Tech Street',
                'city' => 'San Francisco',
                'state' => 'CA',
                'postal_code' => '94105',
                'country' => 'USA',
                'bank_name' => 'Tech Bank',
                'bank_account_number' => '1234567890',
                'bank_routing_number' => '987654321',
                'contact_notes' => 'Excellent quality products, fast delivery',
                'specializations' => ['Electronics', 'Software', 'Hardware'],
                'certifications' => ['ISO 9001', 'CE Mark'],
                'insurance_info' => ['General Liability', 'Product Liability'],
                'contract_start_date' => now()->subMonths(6),
                'contract_end_date' => now()->addMonths(18),
                'commission_rate' => 0.15,
                'discount_rate' => 0.10,
                'shipping_methods' => ['Express', 'Standard', 'Economy'],
                'payment_methods' => ['Credit Card', 'Bank Transfer', 'Check'],
                'quality_rating' => 4.6,
                'delivery_rating' => 4.4,
                'communication_rating' => 4.7,
                'response_time' => 2,
                'on_time_delivery_rate' => 95.5,
                'return_rate' => 2.1,
                'defect_rate' => 1.8,
            ],
            [
                'user_id' => $user->id,
                'provider_number' => 'PROV-002',
                'company_name' => 'Global Distributors Ltd.',
                'contact_person' => 'Sarah Johnson',
                'email' => 'sarah@globaldist.com',
                'phone' => '+1-555-0102',
                'website' => 'https://globaldist.com',
                'tax_id' => 'TAX-002-002',
                'business_license' => 'BL-002-002',
                'provider_type' => ProviderType::DISTRIBUTOR,
                'status' => ProviderStatus::ACTIVE,
                'rating' => 4.2,
                'total_orders' => 89,
                'total_spent' => 45000.00,
                'average_order_value' => 505.62,
                'last_order_date' => now()->subDays(12),
                'first_order_date' => now()->subMonths(4),
                'payment_terms' => 'Net 45',
                'credit_limit' => 75000.00,
                'current_balance' => 15000.00,
                'address' => '456 Distribution Ave',
                'city' => 'New York',
                'state' => 'NY',
                'postal_code' => '10001',
                'country' => 'USA',
                'bank_name' => 'Global Bank',
                'bank_account_number' => '0987654321',
                'bank_routing_number' => '123456789',
                'contact_notes' => 'Good variety, competitive pricing',
                'specializations' => ['Consumer Goods', 'Industrial Supplies'],
                'certifications' => ['ISO 14001'],
                'insurance_info' => ['General Liability'],
                'contract_start_date' => now()->subMonths(4),
                'contract_end_date' => now()->addMonths(20),
                'commission_rate' => 0.12,
                'discount_rate' => 0.08,
                'shipping_methods' => ['Standard', 'Economy'],
                'payment_methods' => ['Credit Card', 'Bank Transfer'],
                'quality_rating' => 4.1,
                'delivery_rating' => 4.3,
                'communication_rating' => 4.2,
                'response_time' => 4,
                'on_time_delivery_rate' => 92.0,
                'return_rate' => 3.5,
                'defect_rate' => 2.8,
            ],
            [
                'user_id' => $user->id,
                'provider_number' => 'PROV-003',
                'company_name' => 'Quality Wholesale Co.',
                'contact_person' => 'Mike Davis',
                'email' => 'mike@qualitywholesale.com',
                'phone' => '+1-555-0103',
                'website' => 'https://qualitywholesale.com',
                'provider_type' => ProviderType::WHOLESALER,
                'status' => ProviderStatus::PENDING,
                'rating' => null,
                'total_orders' => 0,
                'total_spent' => 0.00,
                'average_order_value' => 0.00,
                'payment_terms' => 'Net 30',
                'credit_limit' => 50000.00,
                'current_balance' => 0.00,
                'address' => '789 Wholesale Blvd',
                'city' => 'Chicago',
                'state' => 'IL',
                'postal_code' => '60601',
                'country' => 'USA',
                'contact_notes' => 'New provider, pending approval',
                'specializations' => ['Bulk Materials', 'Raw Materials'],
                'certifications' => ['ISO 9001'],
                'insurance_info' => ['General Liability'],
                'contract_start_date' => null,
                'contract_end_date' => null,
                'commission_rate' => 0.10,
                'discount_rate' => 0.05,
                'shipping_methods' => ['Bulk', 'Standard'],
                'payment_methods' => ['Bank Transfer', 'Check'],
                'quality_rating' => null,
                'delivery_rating' => null,
                'communication_rating' => null,
                'response_time' => null,
                'on_time_delivery_rate' => null,
                'return_rate' => null,
                'defect_rate' => null,
            ],
        ];

        foreach ($providers as $providerData) {
            Provider::create($providerData);
        }

        $this->command->info('Providers seeded successfully!');
    }
}
