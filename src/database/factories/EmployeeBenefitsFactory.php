<?php

namespace Fereydooni\Shopping\database\factories;

use Fereydooni\Shopping\app\Models\EmployeeBenefits;
use Fereydooni\Shopping\app\Models\Employee;
use Fereydooni\Shopping\app\Enums\BenefitType;
use Fereydooni\Shopping\app\Enums\BenefitStatus;
use Fereydooni\Shopping\app\Enums\CoverageLevel;
use Fereydooni\Shopping\app\Enums\NetworkType;
use Illuminate\Database\Eloquent\Factories\Factory;

class EmployeeBenefitsFactory extends Factory
{
    protected $model = EmployeeBenefits::class;

    public function definition(): array
    {
        $benefitType = $this->faker->randomElement(BenefitType::values());
        $coverageLevel = $this->faker->randomElement(CoverageLevel::values());
        $networkType = $this->faker->randomElement(NetworkType::values());
        $status = $this->faker->randomElement(BenefitStatus::values());

        // Calculate realistic costs based on benefit type and coverage level
        $costs = $this->calculateRealisticCosts($benefitType, $coverageLevel, $networkType);

        // Generate realistic dates
        $enrollmentDate = $this->faker->dateTimeBetween('-1 year', 'now');
        $effectiveDate = $this->faker->dateTimeBetween($enrollmentDate, '+6 months');
        $endDate = $status === 'terminated' || $status === 'cancelled'
            ? $this->faker->dateTimeBetween($effectiveDate, '+1 year')
            : null;

        return [
            'employee_id' => Employee::factory(),
            'benefit_type' => $benefitType,
            'benefit_name' => $this->generateBenefitName($benefitType),
            'provider' => $this->generateProviderName($benefitType),
            'plan_id' => $this->faker->regexify('[A-Z]{2}[0-9]{6}'),
            'enrollment_date' => $enrollmentDate->format('Y-m-d'),
            'effective_date' => $effectiveDate->format('Y-m-d'),
            'end_date' => $endDate ? $endDate->format('Y-m-d') : null,
            'status' => $status,
            'coverage_level' => $coverageLevel,
            'premium_amount' => $costs['premium_amount'],
            'employee_contribution' => $costs['employee_contribution'],
            'employer_contribution' => $costs['employer_contribution'],
            'total_cost' => $costs['total_cost'],
            'deductible' => $this->generateDeductible($benefitType, $coverageLevel),
            'co_pay' => $this->generateCoPay($benefitType),
            'co_insurance' => $this->generateCoInsurance($benefitType),
            'max_out_of_pocket' => $this->generateMaxOutOfPocket($benefitType, $coverageLevel),
            'network_type' => $networkType,
            'is_active' => $status === 'enrolled',
            'notes' => $this->faker->optional(0.3)->sentence(),
            'documents' => $this->generateDocuments($benefitType),
        ];
    }

    /**
     * Generate a health insurance benefit
     */
    public function health(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'benefit_type' => 'health',
                'benefit_name' => $this->faker->randomElement([
                    'Blue Cross Blue Shield PPO',
                    'Aetna HMO',
                    'UnitedHealthcare EPO',
                    'Cigna POS',
                    'Kaiser Permanente HMO'
                ]),
                'deductible' => $this->faker->randomFloat(2, 500, 5000),
                'co_pay' => $this->faker->randomFloat(2, 15, 50),
                'co_insurance' => $this->faker->randomFloat(2, 10, 30),
                'max_out_of_pocket' => $this->faker->randomFloat(2, 2000, 10000),
            ];
        });
    }

    /**
     * Generate a dental insurance benefit
     */
    public function dental(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'benefit_type' => 'dental',
                'benefit_name' => $this->faker->randomElement([
                    'Delta Dental PPO',
                    'MetLife Dental',
                    'Aetna Dental',
                    'Cigna Dental',
                    'Humana Dental'
                ]),
                'deductible' => $this->faker->randomFloat(2, 50, 200),
                'co_pay' => $this->faker->randomFloat(2, 5, 25),
                'co_insurance' => $this->faker->randomFloat(2, 20, 50),
                'max_out_of_pocket' => $this->faker->randomFloat(2, 1000, 3000),
            ];
        });
    }

    /**
     * Generate a vision insurance benefit
     */
    public function vision(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'benefit_type' => 'vision',
                'benefit_name' => $this->faker->randomElement([
                    'VSP Vision',
                    'EyeMed Vision',
                    'Davis Vision',
                    'Superior Vision',
                    'Anthem Vision'
                ]),
                'deductible' => $this->faker->randomFloat(2, 0, 50),
                'co_pay' => $this->faker->randomFloat(2, 10, 35),
                'co_insurance' => $this->faker->randomFloat(2, 80, 100),
                'max_out_of_pocket' => $this->faker->randomFloat(2, 200, 1000),
            ];
        });
    }

    /**
     * Generate an enrolled benefit
     */
    public function enrolled(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'enrolled',
                'is_active' => true,
                'effective_date' => $this->faker->dateTimeBetween('-6 months', 'now')->format('Y-m-d'),
            ];
        });
    }

    /**
     * Generate a pending benefit
     */
    public function pending(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'pending',
                'is_active' => false,
                'effective_date' => $this->faker->dateTimeBetween('now', '+3 months')->format('Y-m-d'),
            ];
        });
    }

    /**
     * Generate a terminated benefit
     */
    public function terminated(): static
    {
        return $this->state(function (array $attributes) {
            $effectiveDate = $this->faker->dateTimeBetween('-1 year', '-3 months');
            return [
                'status' => 'terminated',
                'is_active' => false,
                'effective_date' => $effectiveDate->format('Y-m-d'),
                'end_date' => $this->faker->dateTimeBetween($effectiveDate, 'now')->format('Y-m-d'),
            ];
        });
    }

    /**
     * Calculate realistic costs based on benefit type and coverage level
     */
    private function calculateRealisticCosts(string $benefitType, string $coverageLevel, string $networkType): array
    {
        // Base monthly costs by benefit type
        $baseCosts = [
            'health' => ['individual' => 400, 'family' => 1200, 'employee_plus_spouse' => 800, 'employee_plus_children' => 900],
            'dental' => ['individual' => 25, 'family' => 75, 'employee_plus_spouse' => 50, 'employee_plus_children' => 60],
            'vision' => ['individual' => 15, 'family' => 45, 'employee_plus_spouse' => 30, 'employee_plus_children' => 35],
            'life' => ['individual' => 10, 'family' => 30, 'employee_plus_spouse' => 20, 'employee_plus_children' => 25],
            'disability' => ['individual' => 20, 'family' => 60, 'employee_plus_spouse' => 40, 'employee_plus_children' => 50],
            'retirement' => ['individual' => 5, 'family' => 15, 'employee_plus_spouse' => 10, 'employee_plus_children' => 12],
            'other' => ['individual' => 30, 'family' => 90, 'employee_plus_spouse' => 60, 'employee_plus_children' => 70],
        ];

        $basePremium = $baseCosts[$benefitType][$coverageLevel] ?? $baseCosts[$benefitType]['individual'];

        // Apply network type adjustments
        $networkMultipliers = [
            'ppo' => 1.0,
            'hmo' => 0.85,
            'epo' => 0.90,
            'pos' => 0.95,
            'hdhp' => 0.75,
        ];

        $adjustedPremium = $basePremium * ($networkMultipliers[$networkType] ?? 1.0);

        // Calculate contributions (typical 70/30 employer/employee split)
        $employerContribution = $adjustedPremium * 0.70;
        $employeeContribution = $adjustedPremium * 0.30;

        // Add some variation
        $variation = $this->faker->randomFloat(2, 0.9, 1.1);
        $finalPremium = $adjustedPremium * $variation;
        $finalEmployerContribution = $employerContribution * $variation;
        $finalEmployeeContribution = $employeeContribution * $variation;

        return [
            'premium_amount' => round($finalPremium, 2),
            'employee_contribution' => round($finalEmployeeContribution, 2),
            'employer_contribution' => round($finalEmployerContribution, 2),
            'total_cost' => round($finalPremium, 2),
        ];
    }

    /**
     * Generate benefit name based on type
     */
    private function generateBenefitName(string $benefitType): string
    {
        $names = [
            'health' => ['Blue Cross Blue Shield', 'Aetna', 'UnitedHealthcare', 'Cigna', 'Kaiser Permanente'],
            'dental' => ['Delta Dental', 'MetLife Dental', 'Aetna Dental', 'Cigna Dental', 'Humana Dental'],
            'vision' => ['VSP Vision', 'EyeMed Vision', 'Davis Vision', 'Superior Vision', 'Anthem Vision'],
            'life' => ['MetLife', 'Prudential', 'AIG', 'Lincoln Financial', 'Guardian'],
            'disability' => ['MetLife', 'Prudential', 'Guardian', 'Unum', 'Lincoln Financial'],
            'retirement' => ['Fidelity', 'Vanguard', 'T. Rowe Price', 'American Funds', 'T. Rowe Price'],
            'other' => ['Flexible Spending Account', 'Health Savings Account', 'Commuter Benefits', 'Legal Services'],
        ];

        $typeNames = $names[$benefitType] ?? $names['other'];
        return $this->faker->randomElement($typeNames);
    }

    /**
     * Generate provider name
     */
    private function generateProviderName(string $benefitType): string
    {
        $providers = [
            'health' => ['Blue Cross Blue Shield', 'Aetna', 'UnitedHealthcare', 'Cigna', 'Kaiser Permanente'],
            'dental' => ['Delta Dental', 'MetLife', 'Aetna', 'Cigna', 'Humana'],
            'vision' => ['VSP', 'EyeMed', 'Davis Vision', 'Superior Vision', 'Anthem'],
            'life' => ['MetLife', 'Prudential', 'AIG', 'Lincoln Financial', 'Guardian'],
            'disability' => ['MetLife', 'Prudential', 'Guardian', 'Unum', 'Lincoln Financial'],
            'retirement' => ['Fidelity', 'Vanguard', 'T. Rowe Price', 'American Funds', 'Principal'],
            'other' => ['Various Providers', 'Multiple Vendors', 'Internal Programs'],
        ];

        $typeProviders = $providers[$benefitType] ?? $providers['other'];
        return $this->faker->randomElement($typeProviders);
    }

    /**
     * Generate deductible amount
     */
    private function generateDeductible(string $benefitType, string $coverageLevel): ?float
    {
        if ($benefitType === 'retirement') {
            return null;
        }

        $ranges = [
            'health' => ['individual' => [500, 5000], 'family' => [1000, 10000]],
            'dental' => ['individual' => [50, 200], 'family' => [100, 400]],
            'vision' => ['individual' => [0, 50], 'family' => [0, 100]],
            'life' => ['individual' => [0, 0], 'family' => [0, 0]],
            'disability' => ['individual' => [0, 0], 'family' => [0, 0]],
            'other' => ['individual' => [0, 500], 'family' => [0, 1000]],
        ];

        $range = $ranges[$benefitType][$coverageLevel] ?? $ranges[$benefitType]['individual'] ?? [0, 0];

        if ($range[0] === 0 && $range[1] === 0) {
            return null;
        }

        return $this->faker->randomFloat(2, $range[0], $range[1]);
    }

    /**
     * Generate co-pay amount
     */
    private function generateCoPay(string $benefitType): ?float
    {
        $ranges = [
            'health' => [15, 50],
            'dental' => [5, 25],
            'vision' => [10, 35],
            'life' => [0, 0],
            'disability' => [0, 0],
            'retirement' => [0, 0],
            'other' => [0, 0],
        ];

        $range = $ranges[$benefitType] ?? [0, 0];

        if ($range[0] === 0 && $range[1] === 0) {
            return null;
        }

        return $this->faker->randomFloat(2, $range[0], $range[1]);
    }

    /**
     * Generate co-insurance percentage
     */
    private function generateCoInsurance(string $benefitType): ?float
    {
        $ranges = [
            'health' => [10, 30],
            'dental' => [20, 50],
            'vision' => [80, 100],
            'life' => [0, 0],
            'disability' => [0, 0],
            'retirement' => [0, 0],
            'other' => [0, 0],
        ];

        $range = $ranges[$benefitType] ?? [0, 0];

        if ($range[0] === 0 && $range[1] === 0) {
            return null;
        }

        return $this->faker->randomFloat(2, $range[0], $range[1]);
    }

    /**
     * Generate max out of pocket amount
     */
    private function generateMaxOutOfPocket(string $benefitType, string $coverageLevel): ?float
    {
        if ($benefitType === 'retirement') {
            return null;
        }

        $ranges = [
            'health' => ['individual' => [2000, 10000], 'family' => [4000, 20000]],
            'dental' => ['individual' => [1000, 3000], 'family' => [2000, 6000]],
            'vision' => ['individual' => [200, 1000], 'family' => [400, 2000]],
            'life' => ['individual' => [0, 0], 'family' => [0, 0]],
            'disability' => ['individual' => [0, 0], 'family' => [0, 0]],
            'other' => ['individual' => [500, 2000], 'family' => [1000, 4000]],
        ];

        $range = $ranges[$benefitType][$coverageLevel] ?? $ranges[$benefitType]['individual'] ?? [0, 0];

        if ($range[0] === 0 && $range[1] === 0) {
            return null;
        }

        return $this->faker->randomFloat(2, $range[0], $range[1]);
    }

    /**
     * Generate documents array
     */
    private function generateDocuments(string $benefitType): ?array
    {
        if ($this->faker->boolean(70)) {
            return null;
        }

        $documentTypes = [
            'health' => ['enrollment_form.pdf', 'summary_of_benefits.pdf', 'provider_directory.pdf'],
            'dental' => ['dental_enrollment.pdf', 'coverage_summary.pdf'],
            'vision' => ['vision_enrollment.pdf', 'frame_allowance.pdf'],
            'life' => ['life_insurance_application.pdf', 'beneficiary_form.pdf'],
            'disability' => ['disability_application.pdf', 'claim_form.pdf'],
            'retirement' => ['401k_enrollment.pdf', 'investment_options.pdf'],
            'other' => ['enrollment_form.pdf', 'terms_and_conditions.pdf'],
        ];

        $documents = $documentTypes[$benefitType] ?? $documentTypes['other'];
        $count = $this->faker->numberBetween(1, count($documents));

        return $this->faker->randomElements($documents, $count);
    }
};
