<?php

namespace Database\Seeders;

use App\Models\Provider;
use App\Models\ProviderContract;
use App\Enums\ContractType;
use App\Enums\ContractStatus;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProviderContractSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        try {
            DB::beginTransaction();

            $this->seedProviderContracts();

            DB::commit();

            $this->command->info('Provider contracts seeded successfully!');

        } catch (\Exception $e) {
            DB::rollBack();

            $this->command->error('Failed to seed provider contracts: ' . $e->getMessage());

            throw $e;
        }
    }

    /**
     * Seed provider contracts
     */
    protected function seedProviderContracts(): void
    {
        $providers = Provider::all();

        if ($providers->isEmpty()) {
            $this->command->warn('No providers found. Creating sample contracts with factory providers.');
            $this->createContractsWithFactoryProviders();
            return;
        }

        $this->createContractsForExistingProviders($providers);
    }

    /**
     * Create contracts for existing providers
     */
    protected function createContractsForExistingProviders($providers): void
    {
        $contractTypes = ContractType::cases();
        $statuses = ContractStatus::cases();

        foreach ($providers as $provider) {
            $numContracts = rand(1, 5); // 1-5 contracts per provider

            for ($i = 0; $i < $numContracts; $i++) {
                $contractType = $contractTypes[array_rand($contractTypes)];
                $status = $statuses[array_rand($statuses)];

                $this->createContract($provider, $contractType, $status);
            }
        }
    }

    /**
     * Create contracts with factory providers
     */
    protected function createContractsWithFactoryProviders(): void
    {
        $contractTypes = ContractType::cases();
        $statuses = ContractStatus::cases();

        for ($i = 0; $i < 20; $i++) {
            $contractType = $contractTypes[array_rand($contractTypes)];
            $status = $statuses[array_rand($statuses)];

            $this->createContract(null, $contractType, $status);
        }
    }

    /**
     * Create a single contract
     */
    protected function createContract($provider, ContractType $contractType, ContractStatus $status): void
    {
        $startDate = $this->generateStartDate();
        $endDate = $this->generateEndDate($startDate);

        $contractData = [
            'contract_number' => $this->generateContractNumber($contractType, $startDate),
            'contract_type' => $contractType->value,
            'title' => $this->generateContractTitle($contractType),
            'description' => $this->generateContractDescription($contractType),
            'start_date' => $startDate->format('Y-m-d'),
            'end_date' => $endDate->format('Y-m-d'),
            'renewal_date' => $this->generateRenewalDate($endDate, $status),
            'termination_date' => $this->generateTerminationDate($startDate, $endDate, $status),
            'terms' => $this->generateContractTerms($contractType),
            'conditions' => $this->generateContractConditions($contractType),
            'renewal_terms' => $this->generateRenewalTerms(),
            'commission_rate' => $this->generateCommissionRate($contractType),
            'contract_value' => $this->generateContractValue($contractType),
            'currency' => $this->generateCurrency(),
            'payment_terms' => $this->generatePaymentTerms($contractType),
            'status' => $status->value,
            'auto_renewal' => $this->generateAutoRenewal($status),
            'signed_by' => $this->generateSignedBy($status),
            'signed_at' => $this->generateSignedAt($startDate, $status),
            'termination_reason' => $this->generateTerminationReason($status),
            'attachments' => $this->generateAttachments(),
            'notes' => $this->generateNotes(),
            'provider_id' => $provider ? $provider->id : Provider::factory()->create()->id,
            'created_by' => rand(1, 10),
            'updated_by' => rand(1, 10),
        ];

        ProviderContract::create($contractData);
    }

    /**
     * Generate start date
     */
    protected function generateStartDate(): \DateTime
    {
        $startDate = new \DateTime();
        $startDate->modify('-' . rand(0, 24) . ' months');
        return $startDate;
    }

    /**
     * Generate end date
     */
    protected function generateEndDate(\DateTime $startDate): \DateTime
    {
        $endDate = clone $startDate;
        $duration = rand(6, 60); // 6 months to 5 years
        $endDate->modify('+' . $duration . ' months');
        return $endDate;
    }

    /**
     * Generate contract number
     */
    protected function generateContractNumber(ContractType $contractType, \DateTime $startDate): string
    {
        $prefix = 'CONTRACT-' . strtoupper($contractType->value);
        $number = str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);
        $year = $startDate->format('Y');

        return "{$prefix}-{$number}-{$year}";
    }

    /**
     * Generate contract title
     */
    protected function generateContractTitle(ContractType $contractType): string
    {
        $titles = [
            ContractType::SERVICE => [
                'Professional Services Agreement',
                'Consulting Services Contract',
                'Maintenance Services Agreement',
                'Technical Support Contract',
                'Training Services Agreement',
            ],
            ContractType::SUPPLY => [
                'Product Supply Agreement',
                'Raw Materials Contract',
                'Equipment Supply Agreement',
                'Component Supply Contract',
                'Material Procurement Agreement',
            ],
            ContractType::DISTRIBUTION => [
                'Distribution Agreement',
                'Sales Representative Contract',
                'Channel Partner Agreement',
                'Reseller Contract',
                'Franchise Agreement',
            ],
            ContractType::PARTNERSHIP => [
                'Strategic Partnership Agreement',
                'Joint Venture Contract',
                'Collaboration Agreement',
                'Alliance Contract',
                'Co-Marketing Agreement',
            ],
            ContractType::OTHER => [
                'General Agreement',
                'Memorandum of Understanding',
                'Letter of Intent',
                'Framework Agreement',
                'Master Services Agreement',
            ],
        ];

        return $titles[$contractType][array_rand($titles[$contractType])];
    }

    /**
     * Generate contract description
     */
    protected function generateContractDescription(ContractType $contractType): string
    {
        $descriptions = [
            ContractType::SERVICE => 'This agreement outlines the terms and conditions for the provision of professional services.',
            ContractType::SUPPLY => 'This contract establishes the terms for the supply and delivery of products and materials.',
            ContractType::DISTRIBUTION => 'This agreement defines the distribution relationship and sales representation terms.',
            ContractType::PARTNERSHIP => 'This contract establishes a strategic partnership for mutual business growth.',
            ContractType::OTHER => 'This agreement covers the general terms and conditions for business collaboration.',
        ];

        return $descriptions[$contractType];
    }

    /**
     * Generate renewal date
     */
    protected function generateRenewalDate(\DateTime $endDate, ContractStatus $status): ?string
    {
        if ($status === ContractStatus::ACTIVE) {
            $renewalDate = clone $endDate;
            $renewalDate->modify('+' . rand(1, 3) . ' months');
            return $renewalDate->format('Y-m-d');
        }

        return null;
    }

    /**
     * Generate termination date
     */
    protected function generateTerminationDate(\DateTime $startDate, \DateTime $endDate, ContractStatus $status): ?string
    {
        if ($status === ContractStatus::TERMINATED) {
            $terminationDate = clone $startDate;
            $terminationDate->modify('+' . rand(1, 12) . ' months');
            return $terminationDate->format('Y-m-d');
        }

        return null;
    }

    /**
     * Generate contract terms
     */
    protected function generateContractTerms(ContractType $contractType): array
    {
        $baseTerms = [
            'scope_of_work' => 'Comprehensive scope of work as outlined in the agreement.',
            'deliverables' => 'Specific deliverables and milestones to be achieved.',
            'timeline' => 'Project timeline and key milestones.',
            'quality_standards' => 'Quality standards and performance metrics.',
        ];

        $typeSpecificTerms = [
            ContractType::SERVICE => [
                'service_levels' => 'Service level agreements and response times.',
                'availability' => 'Service availability and support hours.',
            ],
            ContractType::SUPPLY => [
                'quality_requirements' => 'Quality specifications and inspection procedures.',
                'warranty_terms' => 'Warranty coverage and terms.',
            ],
            ContractType::DISTRIBUTION => [
                'territory' => 'Geographic territory and market coverage.',
                'sales_targets' => 'Sales targets and performance expectations.',
            ],
            ContractType::PARTNERSHIP => [
                'shared_resources' => 'Resources to be shared between partners.',
                'confidentiality' => 'Confidentiality and non-disclosure terms.',
            ],
            ContractType::OTHER => [
                'general_terms' => 'General terms and conditions.',
            ],
        ];

        return array_merge($baseTerms, $typeSpecificTerms[$contractType] ?? []);
    }

    /**
     * Generate contract conditions
     */
    protected function generateContractConditions(ContractType $contractType): array
    {
        return [
            'force_majeure' => 'Force majeure clauses and exceptions.',
            'termination_clauses' => 'Conditions for contract termination.',
            'dispute_resolution' => 'Dispute resolution procedures and arbitration.',
            'governing_law' => 'Governing law and jurisdiction.',
            'confidentiality' => 'Confidentiality and non-disclosure obligations.',
            'non_compete' => 'Non-compete and exclusivity terms.',
            'liability_limits' => 'Limitation of liability and indemnification.',
        ];
    }

    /**
     * Generate renewal terms
     */
    protected function generateRenewalTerms(): array
    {
        return [
            'period' => rand(6, 24),
            'unit' => ['months', 'years'][array_rand(['months', 'years'])],
            'notice_period' => [30, 60, 90][array_rand([30, 60, 90])],
            'automatic_renewal' => (bool) rand(0, 1),
            'price_adjustment' => 'Price adjustment terms for renewal.',
            'terms_modification' => 'Process for modifying terms upon renewal.',
        ];
    }

    /**
     * Generate commission rate
     */
    protected function generateCommissionRate(ContractType $contractType): float
    {
        $rates = [
            ContractType::SERVICE => [5, 15],
            ContractType::SUPPLY => [2, 8],
            ContractType::DISTRIBUTION => [10, 25],
            ContractType::PARTNERSHIP => [0, 5],
            ContractType::OTHER => [3, 12],
        ];

        [$min, $max] = $rates[$contractType];
        return round(rand($min * 100, $max * 100) / 100, 2);
    }

    /**
     * Generate contract value
     */
    protected function generateContractValue(ContractType $contractType): float
    {
        $ranges = [
            ContractType::SERVICE => [5000, 500000],
            ContractType::SUPPLY => [10000, 1000000],
            ContractType::DISTRIBUTION => [25000, 2500000],
            ContractType::PARTNERSHIP => [50000, 5000000],
            ContractType::OTHER => [10000, 1000000],
        ];

        [$min, $max] = $ranges[$contractType];
        return round(rand($min, $max), 2);
    }

    /**
     * Generate currency
     */
    protected function generateCurrency(): string
    {
        $currencies = ['USD', 'EUR', 'GBP', 'JPY', 'CAD', 'AUD', 'CHF'];
        return $currencies[array_rand($currencies)];
    }

    /**
     * Generate payment terms
     */
    protected function generatePaymentTerms(ContractType $contractType): array
    {
        return [
            'payment_schedule' => ['monthly', 'quarterly', 'annually', 'milestone'][array_rand(['monthly', 'quarterly', 'annually', 'milestone'])],
            'payment_terms' => ['net 30', 'net 45', 'net 60', 'immediate'][array_rand(['net 30', 'net 45', 'net 60', 'immediate'])],
            'late_fees' => 'Late payment fees and penalties.',
            'advance_payment' => (bool) rand(0, 1),
            'retention' => round(rand(0, 1000) / 100, 2),
            'bonus' => rand(0, 1) ? round(rand(1000, 50000), 2) : 0,
        ];
    }

    /**
     * Generate auto renewal
     */
    protected function generateAutoRenewal(ContractStatus $status): bool
    {
        if ($status === ContractStatus::ACTIVE) {
            return (bool) rand(0, 1);
        }

        return false;
    }

    /**
     * Generate signed by
     */
    protected function generateSignedBy(ContractStatus $status): ?int
    {
        if (in_array($status, [ContractStatus::ACTIVE, ContractStatus::SUSPENDED, ContractStatus::EXPIRED, ContractStatus::TERMINATED])) {
            return rand(1, 10);
        }

        return null;
    }

    /**
     * Generate signed at
     */
    protected function generateSignedAt(\DateTime $startDate, ContractStatus $status): ?string
    {
        if (in_array($status, [ContractStatus::ACTIVE, ContractStatus::SUSPENDED, ContractStatus::EXPIRED, ContractStatus::TERMINATED])) {
            $signedAt = clone $startDate;
            $signedAt->modify('+' . rand(0, 30) . ' days');
            return $signedAt->format('Y-m-d H:i:s');
        }

        return null;
    }

    /**
     * Generate termination reason
     */
    protected function generateTerminationReason(ContractStatus $status): ?string
    {
        if ($status === ContractStatus::TERMINATED) {
            $reasons = [
                'Mutual agreement to terminate',
                'Breach of contract terms',
                'Financial considerations',
                'Strategic business decision',
                'Performance issues',
                'Market conditions',
            ];

            return $reasons[array_rand($reasons)];
        }

        return null;
    }

    /**
     * Generate attachments
     */
    protected function generateAttachments(): array
    {
        $attachments = [];
        $numAttachments = rand(0, 3);

        for ($i = 0; $i < $numAttachments; $i++) {
            $attachments[] = [
                'name' => 'contract_attachment_' . ($i + 1) . '.pdf',
                'type' => 'pdf',
                'size' => rand(100, 5000),
                'uploaded_at' => now()->subDays(rand(1, 30))->format('Y-m-d H:i:s'),
                'description' => 'Contract attachment ' . ($i + 1),
            ];
        }

        return $attachments;
    }

    /**
     * Generate notes
     */
    protected function generateNotes(): array
    {
        $notes = [];
        $numNotes = rand(0, 2);

        for ($i = 0; $i < $numNotes; $i++) {
            $notes[] = [
                'content' => 'Contract note ' . ($i + 1) . ': Important information about the contract.',
                'author' => 'System',
                'created_at' => now()->subDays(rand(1, 15))->format('Y-m-d H:i:s'),
                'type' => ['general', 'important', 'reminder'][array_rand(['general', 'important', 'reminder'])],
            ];
        }

        return $notes;
    }
}
