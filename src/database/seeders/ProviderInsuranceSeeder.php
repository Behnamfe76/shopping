<?php

namespace Database\Seeders;

use App\Enums\InsuranceStatus;
use App\Enums\InsuranceType;
use App\Enums\VerificationStatus;
use App\Models\Provider;
use App\Models\ProviderInsurance;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProviderInsuranceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->seedInsuranceTypes();
        $this->seedInsuranceStatuses();
        $this->seedVerificationStatuses();
        $this->seedSampleInsuranceData();
    }

    /**
     * Seed insurance types
     */
    private function seedInsuranceTypes(): void
    {
        $types = [
            'general_liability' => 'General Liability Insurance',
            'professional_liability' => 'Professional Liability Insurance',
            'product_liability' => 'Product Liability Insurance',
            'workers_compensation' => 'Workers Compensation Insurance',
            'auto_insurance' => 'Auto Insurance',
            'property_insurance' => 'Property Insurance',
            'cyber_insurance' => 'Cyber Insurance',
            'other' => 'Other Insurance',
        ];

        foreach ($types as $key => $name) {
            DB::table('insurance_types')->updateOrInsert(
                ['key' => $key],
                [
                    'name' => $name,
                    'description' => "Insurance coverage for {$name}",
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
    }

    /**
     * Seed insurance statuses
     */
    private function seedInsuranceStatuses(): void
    {
        $statuses = [
            'active' => 'Active',
            'expired' => 'Expired',
            'cancelled' => 'Cancelled',
            'pending' => 'Pending',
            'suspended' => 'Suspended',
        ];

        foreach ($statuses as $key => $name) {
            DB::table('insurance_statuses')->updateOrInsert(
                ['key' => $key],
                [
                    'name' => $name,
                    'description' => "Insurance status: {$name}",
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
    }

    /**
     * Seed verification statuses
     */
    private function seedVerificationStatuses(): void
    {
        $statuses = [
            'unverified' => 'Unverified',
            'pending' => 'Pending Verification',
            'verified' => 'Verified',
            'rejected' => 'Rejected',
            'requires_update' => 'Requires Update',
        ];

        foreach ($statuses as $key => $name) {
            DB::table('verification_statuses')->updateOrInsert(
                ['key' => $key],
                [
                    'name' => $name,
                    'description' => "Verification status: {$name}",
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
    }

    /**
     * Seed sample insurance data
     */
    private function seedSampleInsuranceData(): void
    {
        $providers = Provider::take(10)->get();

        if ($providers->isEmpty()) {
            $this->command->warn('No providers found. Skipping insurance data seeding.');

            return;
        }

        $insuranceData = [
            [
                'insurance_type' => InsuranceType::GENERAL_LIABILITY,
                'policy_number' => 'GL-001-2024',
                'provider_name' => 'ABC Insurance Co.',
                'coverage_amount' => 1000000.00,
                'start_date' => '2024-01-01',
                'end_date' => '2024-12-31',
                'status' => InsuranceStatus::ACTIVE,
                'verification_status' => VerificationStatus::VERIFIED,
                'notes' => 'General liability coverage for business operations',
            ],
            [
                'insurance_type' => InsuranceType::PROFESSIONAL_LIABILITY,
                'policy_number' => 'PL-002-2024',
                'provider_name' => 'XYZ Insurance Co.',
                'coverage_amount' => 500000.00,
                'start_date' => '2024-01-01',
                'end_date' => '2024-12-31',
                'status' => InsuranceStatus::ACTIVE,
                'verification_status' => VerificationStatus::VERIFIED,
                'notes' => 'Professional liability coverage for services',
            ],
            [
                'insurance_type' => InsuranceType::WORKERS_COMPENSATION,
                'policy_number' => 'WC-003-2024',
                'provider_name' => 'Workers Comp Insurance Co.',
                'coverage_amount' => 250000.00,
                'start_date' => '2024-01-01',
                'end_date' => '2024-12-31',
                'status' => InsuranceStatus::ACTIVE,
                'verification_status' => VerificationStatus::VERIFIED,
                'notes' => 'Workers compensation coverage for employees',
            ],
            [
                'insurance_type' => InsuranceType::AUTO_INSURANCE,
                'policy_number' => 'AUTO-004-2024',
                'provider_name' => 'Auto Insurance Co.',
                'coverage_amount' => 300000.00,
                'start_date' => '2024-01-01',
                'end_date' => '2024-12-31',
                'status' => InsuranceStatus::ACTIVE,
                'verification_status' => VerificationStatus::VERIFIED,
                'notes' => 'Auto insurance coverage for company vehicles',
            ],
            [
                'insurance_type' => InsuranceType::PROPERTY_INSURANCE,
                'policy_number' => 'PROP-005-2024',
                'provider_name' => 'Property Insurance Co.',
                'coverage_amount' => 2000000.00,
                'start_date' => '2024-01-01',
                'end_date' => '2024-12-31',
                'status' => InsuranceStatus::ACTIVE,
                'verification_status' => VerificationStatus::VERIFIED,
                'notes' => 'Property insurance coverage for business premises',
            ],
            [
                'insurance_type' => InsuranceType::CYBER_INSURANCE,
                'policy_number' => 'CYBER-006-2024',
                'provider_name' => 'Cyber Insurance Co.',
                'coverage_amount' => 750000.00,
                'start_date' => '2024-01-01',
                'end_date' => '2024-12-31',
                'status' => InsuranceStatus::PENDING,
                'verification_status' => VerificationStatus::PENDING,
                'notes' => 'Cyber insurance coverage for data protection',
            ],
            [
                'insurance_type' => InsuranceType::PRODUCT_LIABILITY,
                'policy_number' => 'PROD-007-2024',
                'provider_name' => 'Product Insurance Co.',
                'coverage_amount' => 1500000.00,
                'start_date' => '2023-01-01',
                'end_date' => '2023-12-31',
                'status' => InsuranceStatus::EXPIRED,
                'verification_status' => VerificationStatus::REJECTED,
                'notes' => 'Expired product liability coverage',
            ],
        ];

        foreach ($providers as $index => $provider) {
            $insuranceCount = rand(2, 5); // Each provider gets 2-5 insurance policies

            for ($i = 0; $i < $insuranceCount; $i++) {
                $insurance = $insuranceData[array_rand($insuranceData)];

                ProviderInsurance::create([
                    'provider_id' => $provider->id,
                    'insurance_type' => $insurance['insurance_type'],
                    'policy_number' => $insurance['policy_number'].'-'.($index + 1).'-'.($i + 1),
                    'provider_name' => $insurance['provider_name'],
                    'coverage_amount' => $insurance['coverage_amount'],
                    'start_date' => $insurance['start_date'],
                    'end_date' => $insurance['end_date'],
                    'status' => $insurance['status'],
                    'verification_status' => $insurance['verification_status'],
                    'verified_by' => $insurance['verification_status'] === VerificationStatus::VERIFIED ? 1 : null,
                    'verified_at' => $insurance['verification_status'] === VerificationStatus::VERIFIED ? now() : null,
                    'notes' => $insurance['notes'],
                    'documents' => json_encode(['certificate.pdf']),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        $this->command->info('Provider insurance data seeded successfully!');
    }
}
