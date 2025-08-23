<?php

namespace Fereydooni\Shopping\database\seeders;

use Illuminate\Database\Seeder;
use Fereydooni\Shopping\app\Models\EmployeeBenefits;
use Fereydooni\Shopping\app\Models\Employee;
use Fereydooni\Shopping\database\factories\EmployeeBenefitsFactory;
use Fereydooni\Shopping\app\Enums\BenefitType;
use Fereydooni\Shopping\app\Enums\BenefitStatus;
use Fereydooni\Shopping\app\Enums\CoverageLevel;
use Fereydooni\Shopping\app\Enums\NetworkType;

class EmployeeBenefitsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Seeding Employee Benefits...');

        // Get all employees
        $employees = Employee::all();

        if ($employees->isEmpty()) {
            $this->command->warn('No employees found. Please seed employees first.');
            return;
        }

        $this->seedHealthBenefits($employees);
        $this->seedDentalBenefits($employees);
        $this->seedVisionBenefits($employees);
        $this->seedLifeInsuranceBenefits($employees);
        $this->seedDisabilityBenefits($employees);
        $this->seedRetirementBenefits($employees);
        $this->seedOtherBenefits($employees);

        $this->command->info('Employee Benefits seeded successfully!');
    }

    /**
     * Seed health insurance benefits
     */
    private function seedHealthBenefits($employees): void
    {
        $this->command->info('Seeding health insurance benefits...');

        $healthProviders = [
            'Blue Cross Blue Shield' => ['ppo', 'hmo'],
            'Aetna' => ['ppo', 'hmo', 'epo'],
            'UnitedHealthcare' => ['ppo', 'hmo', 'epo'],
            'Cigna' => ['ppo', 'hmo', 'pos'],
            'Kaiser Permanente' => ['hmo'],
        ];

        foreach ($employees as $employee) {
            // 85% of employees have health insurance
            if (rand(1, 100) <= 85) {
                $provider = array_rand($healthProviders);
                $networkTypes = $healthProviders[$provider];
                $networkType = $networkTypes[array_rand($networkTypes)];

                $coverageLevel = $this->getRandomCoverageLevel();
                $status = $this->getRandomStatus();

                $benefit = EmployeeBenefits::factory()->create([
                    'employee_id' => $employee->id,
                    'benefit_type' => 'health',
                    'benefit_name' => $provider . ' ' . strtoupper($networkType),
                    'provider' => $provider,
                    'network_type' => $networkType,
                    'coverage_level' => $coverageLevel,
                    'status' => $status,
                    'is_active' => $status === 'enrolled',
                ]);

                $this->updateBenefitCosts($benefit, 'health', $coverageLevel, $networkType);
            }
        }
    }

    /**
     * Seed dental insurance benefits
     */
    private function seedDentalBenefits($employees): void
    {
        $this->command->info('Seeding dental insurance benefits...');

        $dentalProviders = [
            'Delta Dental',
            'MetLife Dental',
            'Aetna Dental',
            'Cigna Dental',
            'Humana Dental'
        ];

        foreach ($employees as $employee) {
            // 70% of employees have dental insurance
            if (rand(1, 100) <= 70) {
                $provider = $dentalProviders[array_rand($dentalProviders)];
                $coverageLevel = $this->getRandomCoverageLevel();
                $status = $this->getRandomStatus();

                $benefit = EmployeeBenefits::factory()->create([
                    'employee_id' => $employee->id,
                    'benefit_type' => 'dental',
                    'benefit_name' => $provider . ' PPO',
                    'provider' => $provider,
                    'network_type' => 'ppo',
                    'coverage_level' => $coverageLevel,
                    'status' => $status,
                    'is_active' => $status === 'enrolled',
                ]);

                $this->updateBenefitCosts($benefit, 'dental', $coverageLevel, 'ppo');
            }
        }
    }

    /**
     * Seed vision insurance benefits
     */
    private function seedVisionBenefits($employees): void
    {
        $this->command->info('Seeding vision insurance benefits...');

        $visionProviders = [
            'VSP Vision',
            'EyeMed Vision',
            'Davis Vision',
            'Superior Vision',
            'Anthem Vision'
        ];

        foreach ($employees as $employee) {
            // 60% of employees have vision insurance
            if (rand(1, 100) <= 60) {
                $provider = $visionProviders[array_rand($visionProviders)];
                $coverageLevel = $this->getRandomCoverageLevel();
                $status = $this->getRandomStatus();

                $benefit = EmployeeBenefits::factory()->create([
                    'employee_id' => $employee->id,
                    'benefit_type' => 'vision',
                    'benefit_name' => $provider,
                    'provider' => $provider,
                    'network_type' => 'ppo',
                    'coverage_level' => $coverageLevel,
                    'status' => $status,
                    'is_active' => $status === 'enrolled',
                ]);

                $this->updateBenefitCosts($benefit, 'vision', $coverageLevel, 'ppo');
            }
        }
    }

    /**
     * Seed life insurance benefits
     */
    private function seedLifeInsuranceBenefits($employees): void
    {
        $this->command->info('Seeding life insurance benefits...');

        $lifeProviders = [
            'MetLife',
            'Prudential',
            'AIG',
            'Lincoln Financial',
            'Guardian'
        ];

        foreach ($employees as $employee) {
            // 50% of employees have life insurance
            if (rand(1, 100) <= 50) {
                $provider = $lifeProviders[array_rand($lifeProviders)];
                $coverageLevel = $this->getRandomCoverageLevel();
                $status = $this->getRandomStatus();

                $benefit = EmployeeBenefits::factory()->create([
                    'employee_id' => $employee->id,
                    'benefit_type' => 'life',
                    'benefit_name' => $provider . ' Life Insurance',
                    'provider' => $provider,
                    'network_type' => 'ppo',
                    'coverage_level' => $coverageLevel,
                    'status' => $status,
                    'is_active' => $status === 'enrolled',
                ]);

                $this->updateBenefitCosts($benefit, 'life', $coverageLevel, 'ppo');
            }
        }
    }

    /**
     * Seed disability benefits
     */
    private function seedDisabilityBenefits($employees): void
    {
        $this->command->info('Seeding disability benefits...');

        $disabilityProviders = [
            'MetLife',
            'Prudential',
            'Guardian',
            'Unum',
            'Lincoln Financial'
        ];

        foreach ($employees as $employee) {
            // 40% of employees have disability insurance
            if (rand(1, 100) <= 40) {
                $provider = $disabilityProviders[array_rand($disabilityProviders)];
                $coverageLevel = $this->getRandomCoverageLevel();
                $status = $this->getRandomStatus();

                $benefit = EmployeeBenefits::factory()->create([
                    'employee_id' => $employee->id,
                    'benefit_type' => 'disability',
                    'benefit_name' => $provider . ' Disability',
                    'provider' => $provider,
                    'network_type' => 'ppo',
                    'coverage_level' => $coverageLevel,
                    'status' => $status,
                    'is_active' => $status === 'enrolled',
                ]);

                $this->updateBenefitCosts($benefit, 'disability', $coverageLevel, 'ppo');
            }
        }
    }

    /**
     * Seed retirement benefits
     */
    private function seedRetirementBenefits($employees): void
    {
        $this->command->info('Seeding retirement benefits...');

        $retirementProviders = [
            'Fidelity',
            'Vanguard',
            'T. Rowe Price',
            'American Funds',
            'Principal'
        ];

        foreach ($employees as $employee) {
            // 90% of employees have retirement benefits
            if (rand(1, 100) <= 90) {
                $provider = $retirementProviders[array_rand($retirementProviders)];
                $coverageLevel = $this->getRandomCoverageLevel();
                $status = $this->getRandomStatus();

                $benefit = EmployeeBenefits::factory()->create([
                    'employee_id' => $employee->id,
                    'benefit_type' => 'retirement',
                    'benefit_name' => $provider . ' 401(k)',
                    'provider' => $provider,
                    'network_type' => 'ppo',
                    'coverage_level' => $coverageLevel,
                    'status' => $status,
                    'is_active' => $status === 'enrolled',
                ]);

                $this->updateBenefitCosts($benefit, 'retirement', $coverageLevel, 'ppo');
            }
        }
    }

    /**
     * Seed other benefits
     */
    private function seedOtherBenefits($employees): void
    {
        $this->command->info('Seeding other benefits...');

        $otherBenefits = [
            'Flexible Spending Account' => 'Internal',
            'Health Savings Account' => 'Internal',
            'Commuter Benefits' => 'Internal',
            'Legal Services' => 'Internal',
            'Pet Insurance' => 'Various Providers'
        ];

        foreach ($employees as $employee) {
            // 30% of employees have other benefits
            if (rand(1, 100) <= 30) {
                $benefitName = array_rand($otherBenefits);
                $provider = $otherBenefits[$benefitName];
                $coverageLevel = $this->getRandomCoverageLevel();
                $status = $this->getRandomStatus();

                $benefit = EmployeeBenefits::factory()->create([
                    'employee_id' => $employee->id,
                    'benefit_type' => 'other',
                    'benefit_name' => $benefitName,
                    'provider' => $provider,
                    'network_type' => 'ppo',
                    'coverage_level' => $coverageLevel,
                    'status' => $status,
                    'is_active' => $status === 'enrolled',
                ]);

                $this->updateBenefitCosts($benefit, 'other', $coverageLevel, 'ppo');
            }
        }
    }

    /**
     * Get random coverage level with weighted distribution
     */
    private function getRandomCoverageLevel(): string
    {
        $weights = [
            'individual' => 40,
            'employee_plus_spouse' => 25,
            'employee_plus_children' => 20,
            'family' => 15
        ];

        $random = rand(1, 100);
        $cumulative = 0;

        foreach ($weights as $level => $weight) {
            $cumulative += $weight;
            if ($random <= $cumulative) {
                return $level;
            }
        }

        return 'individual';
    }

    /**
     * Get random status with weighted distribution
     */
    private function getRandomStatus(): string
    {
        $weights = [
            'enrolled' => 70,
            'pending' => 20,
            'terminated' => 7,
            'cancelled' => 3
        ];

        $random = rand(1, 100);
        $cumulative = 0;

        foreach ($weights as $status => $weight) {
            $cumulative += $weight;
            if ($random <= $cumulative) {
                return $status;
            }
        }

        return 'enrolled';
    }

    /**
     * Update benefit costs based on type, coverage level, and network
     */
    private function updateBenefitCosts(EmployeeBenefits $benefit, string $benefitType, string $coverageLevel, string $networkType): void
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
        $variation = rand(90, 110) / 100;
        $finalPremium = $adjustedPremium * $variation;
        $finalEmployerContribution = $employerContribution * $variation;
        $finalEmployeeContribution = $employeeContribution * $variation;

        $benefit->update([
            'premium_amount' => round($finalPremium, 2),
            'employee_contribution' => round($finalEmployeeContribution, 2),
            'employer_contribution' => round($finalEmployerContribution, 2),
            'total_cost' => round($finalPremium, 2),
        ]);
    }
}
