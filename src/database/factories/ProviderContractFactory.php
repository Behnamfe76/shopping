<?php

namespace Database\Factories;

use App\Enums\ContractStatus;
use App\Enums\ContractType;
use App\Models\Provider;
use App\Models\ProviderContract;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ProviderContract>
 */
class ProviderContractFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = ProviderContract::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $startDate = $this->faker->dateTimeBetween('-2 years', '+1 month');
        $endDate = $this->faker->dateTimeBetween($startDate, '+2 years');
        $contractType = $this->faker->randomElement(ContractType::cases());
        $status = $this->faker->randomElement(ContractStatus::cases());

        // Generate contract number
        $contractNumber = 'CONTRACT-'.strtoupper($contractType->value).'-'.
                         $this->faker->numberBetween(1000, 9999).'-'.
                         $startDate->format('Y');

        // Determine if contract should be signed
        $isSigned = in_array($status, [ContractStatus::ACTIVE, ContractStatus::SUSPENDED, ContractStatus::EXPIRED]);
        $signedAt = $isSigned ? $this->faker->dateTimeBetween($startDate, 'now') : null;

        // Determine if contract should be terminated
        $isTerminated = $status === ContractStatus::TERMINATED;
        $terminationDate = $isTerminated ? $this->faker->dateTimeBetween($startDate, 'now') : null;

        // Determine if contract should be expired
        $isExpired = $status === ContractStatus::EXPIRED;

        // Calculate renewal date for active contracts
        $renewalDate = null;
        if ($status === ContractStatus::ACTIVE && $endDate) {
            $renewalDate = $this->faker->dateTimeBetween($endDate, $endDate->modify('+3 months'));
        }

        return [
            'contract_number' => $contractNumber,
            'contract_type' => $contractType->value,
            'title' => $this->generateContractTitle($contractType),
            'description' => $this->faker->paragraph(3),
            'start_date' => $startDate->format('Y-m-d'),
            'end_date' => $endDate->format('Y-m-d'),
            'renewal_date' => $renewalDate?->format('Y-m-d'),
            'termination_date' => $terminationDate?->format('Y-m-d'),
            'terms' => $this->generateContractTerms($contractType),
            'conditions' => $this->generateContractConditions($contractType),
            'renewal_terms' => $this->generateRenewalTerms(),
            'commission_rate' => $this->faker->randomFloat(2, 0, 25),
            'contract_value' => $this->faker->randomFloat(2, 1000, 1000000),
            'currency' => $this->faker->randomElement(['USD', 'EUR', 'GBP', 'JPY', 'CAD']),
            'payment_terms' => $this->generatePaymentTerms(),
            'status' => $status->value,
            'auto_renewal' => $this->faker->boolean(30),
            'signed_by' => $isSigned ? $this->faker->numberBetween(1, 10) : null,
            'signed_at' => $signedAt?->format('Y-m-d H:i:s'),
            'termination_reason' => $isTerminated ? $this->faker->sentence() : null,
            'attachments' => $this->generateAttachments(),
            'notes' => $this->generateNotes(),
            'provider_id' => Provider::factory(),
            'created_by' => $this->faker->numberBetween(1, 10),
            'updated_by' => $this->faker->numberBetween(1, 10),
        ];
    }

    /**
     * Generate contract title based on type
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

        return $this->faker->randomElement($titles[$contractType]);
    }

    /**
     * Generate contract terms
     */
    protected function generateContractTerms(ContractType $contractType): array
    {
        $baseTerms = [
            'scope_of_work' => $this->faker->paragraph(),
            'deliverables' => $this->faker->sentences(3, true),
            'timeline' => $this->faker->sentence(),
            'quality_standards' => $this->faker->sentences(2, true),
        ];

        $typeSpecificTerms = [
            ContractType::SERVICE => [
                'service_levels' => $this->faker->sentences(2, true),
                'response_times' => $this->faker->sentence(),
                'availability' => $this->faker->sentence(),
            ],
            ContractType::SUPPLY => [
                'quality_requirements' => $this->faker->sentences(2, true),
                'inspection_procedures' => $this->faker->sentence(),
                'warranty_terms' => $this->faker->sentence(),
            ],
            ContractType::DISTRIBUTION => [
                'territory' => $this->faker->sentence(),
                'sales_targets' => $this->faker->sentence(),
                'marketing_support' => $this->faker->sentence(),
            ],
            ContractType::PARTNERSHIP => [
                'shared_resources' => $this->faker->sentences(2, true),
                'confidentiality' => $this->faker->sentence(),
                'intellectual_property' => $this->faker->sentence(),
            ],
            ContractType::OTHER => [
                'general_terms' => $this->faker->sentences(2, true),
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
            'force_majeure' => $this->faker->sentence(),
            'termination_clauses' => $this->faker->sentences(2, true),
            'dispute_resolution' => $this->faker->sentence(),
            'governing_law' => $this->faker->sentence(),
            'confidentiality' => $this->faker->sentence(),
            'non_compete' => $this->faker->sentence(),
            'liability_limits' => $this->faker->sentence(),
        ];
    }

    /**
     * Generate renewal terms
     */
    protected function generateRenewalTerms(): array
    {
        return [
            'period' => $this->faker->randomElement([6, 12, 18, 24]),
            'unit' => $this->faker->randomElement(['months', 'years']),
            'notice_period' => $this->faker->randomElement([30, 60, 90]),
            'automatic_renewal' => $this->faker->boolean(70),
            'price_adjustment' => $this->faker->sentence(),
            'terms_modification' => $this->faker->sentence(),
        ];
    }

    /**
     * Generate payment terms
     */
    protected function generatePaymentTerms(): array
    {
        return [
            'payment_schedule' => $this->faker->randomElement(['monthly', 'quarterly', 'annually', 'milestone']),
            'payment_terms' => $this->faker->randomElement(['net 30', 'net 45', 'net 60', 'immediate']),
            'late_fees' => $this->faker->sentence(),
            'advance_payment' => $this->faker->boolean(40),
            'retention' => $this->faker->randomFloat(2, 0, 10),
            'bonus' => $this->faker->boolean(20) ? $this->faker->randomFloat(2, 1000, 50000) : 0,
        ];
    }

    /**
     * Generate attachments
     */
    protected function generateAttachments(): array
    {
        $attachmentTypes = ['pdf', 'doc', 'docx', 'xls', 'xlsx'];
        $attachments = [];

        $numAttachments = $this->faker->numberBetween(0, 5);

        for ($i = 0; $i < $numAttachments; $i++) {
            $attachments[] = [
                'name' => $this->faker->words(3, true).'.'.$this->faker->randomElement($attachmentTypes),
                'type' => $this->faker->randomElement($attachmentTypes),
                'size' => $this->faker->numberBetween(100, 10000),
                'uploaded_at' => $this->faker->dateTimeThisYear()->format('Y-m-d H:i:s'),
                'description' => $this->faker->sentence(),
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
        $numNotes = $this->faker->numberBetween(0, 3);

        for ($i = 0; $i < $numNotes; $i++) {
            $notes[] = [
                'content' => $this->faker->paragraph(),
                'author' => $this->faker->name(),
                'created_at' => $this->faker->dateTimeThisYear()->format('Y-m-d H:i:s'),
                'type' => $this->faker->randomElement(['general', 'important', 'reminder', 'follow_up']),
            ];
        }

        return $notes;
    }

    /**
     * Indicate that the contract is active
     */
    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => ContractStatus::ACTIVE->value,
            'signed_by' => $this->faker->numberBetween(1, 10),
            'signed_at' => $this->faker->dateTimeBetween('-1 year', 'now')->format('Y-m-d H:i:s'),
        ]);
    }

    /**
     * Indicate that the contract is expired
     */
    public function expired(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => ContractStatus::EXPIRED->value,
            'end_date' => $this->faker->dateTimeBetween('-1 year', '-1 month')->format('Y-m-d'),
            'signed_by' => $this->faker->numberBetween(1, 10),
            'signed_at' => $this->faker->dateTimeBetween('-2 years', '-1 year')->format('Y-m-d H:i:s'),
        ]);
    }

    /**
     * Indicate that the contract is terminated
     */
    public function terminated(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => ContractStatus::TERMINATED->value,
            'termination_date' => $this->faker->dateTimeBetween('-6 months', 'now')->format('Y-m-d'),
            'termination_reason' => $this->faker->sentence(),
            'signed_by' => $this->faker->numberBetween(1, 10),
            'signed_at' => $this->faker->dateTimeBetween('-1 year', '-6 months')->format('Y-m-d H:i:s'),
        ]);
    }

    /**
     * Indicate that the contract is for a specific type
     */
    public function ofType(ContractType $contractType): static
    {
        return $this->state(fn (array $attributes) => [
            'contract_type' => $contractType->value,
            'title' => $this->generateContractTitle($contractType),
            'terms' => $this->generateContractTerms($contractType),
        ]);
    }

    /**
     * Indicate that the contract has a high value
     */
    public function highValue(): static
    {
        return $this->state(fn (array $attributes) => [
            'contract_value' => $this->faker->randomFloat(2, 100000, 10000000),
            'commission_rate' => $this->faker->randomFloat(2, 5, 20),
        ]);
    }

    /**
     * Indicate that the contract has auto-renewal
     */
    public function autoRenewable(): static
    {
        return $this->state(fn (array $attributes) => [
            'auto_renewal' => true,
            'renewal_terms' => array_merge($this->generateRenewalTerms(), [
                'automatic_renewal' => true,
            ]),
        ]);
    }
}
