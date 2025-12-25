<?php

namespace Fereydooni\Shopping\app\Actions\EmployeeBenefits;

use Fereydooni\Shopping\app\Models\EmployeeBenefits;
use Illuminate\Support\Facades\Log;

class CalculateBenefitsCostAction
{
    public function execute(EmployeeBenefits $benefit): array
    {
        try {
            // Calculate total premium
            $totalPremium = $this->calculateTotalPremium($benefit);

            // Calculate employee contribution
            $employeeContribution = $this->calculateEmployeeContribution($benefit, $totalPremium);

            // Calculate employer contribution
            $employerContribution = $this->calculateEmployerContribution($benefit, $totalPremium);

            // Apply coverage level adjustments
            $adjustedCosts = $this->applyCoverageLevelAdjustments($benefit, [
                'total_premium' => $totalPremium,
                'employee_contribution' => $employeeContribution,
                'employer_contribution' => $employerContribution,
            ]);

            // Apply discounts
            $finalCosts = $this->applyDiscounts($benefit, $adjustedCosts);

            return $finalCosts;

        } catch (\Exception $e) {
            Log::error('Failed to calculate benefits cost', [
                'error' => $e->getMessage(),
                'benefit_id' => $benefit->id,
            ]);
            throw $e;
        }
    }

    private function calculateTotalPremium(EmployeeBenefits $benefit): float
    {
        $basePremium = $benefit->premium_amount;

        // Apply network type adjustments
        $networkMultiplier = $this->getNetworkTypeMultiplier($benefit->network_type->value);
        $adjustedPremium = $basePremium * $networkMultiplier;

        // Apply benefit type adjustments
        $typeMultiplier = $this->getBenefitTypeMultiplier($benefit->benefit_type->value);
        $finalPremium = $adjustedPremium * $typeMultiplier;

        return round($finalPremium, 2);
    }

    private function calculateEmployeeContribution(EmployeeBenefits $benefit, float $totalPremium): float
    {
        // If employee contribution is already set, use it
        if ($benefit->employee_contribution > 0) {
            return $benefit->employee_contribution;
        }

        // Calculate based on coverage level
        $contributionPercentage = $this->getEmployeeContributionPercentage($benefit->coverage_level->value);
        $contribution = $totalPremium * ($contributionPercentage / 100);

        return round($contribution, 2);
    }

    private function calculateEmployerContribution(EmployeeBenefits $benefit, float $totalPremium): float
    {
        // If employer contribution is already set, use it
        if ($benefit->employer_contribution > 0) {
            return $benefit->employer_contribution;
        }

        // Calculate based on coverage level
        $contributionPercentage = $this->getEmployerContributionPercentage($benefit->coverage_level->value);
        $contribution = $totalPremium * ($contributionPercentage / 100);

        return round($contribution, 2);
    }

    private function applyCoverageLevelAdjustments(EmployeeBenefits $benefit, array $costs): array
    {
        $coverageLevel = $benefit->coverage_level->value;
        $multiplier = 1.0;

        switch ($coverageLevel) {
            case 'individual':
                $multiplier = 1.0;
                break;
            case 'employee_plus_spouse':
                $multiplier = 1.8;
                break;
            case 'employee_plus_children':
                $multiplier = 1.6;
                break;
            case 'family':
                $multiplier = 2.2;
                break;
        }

        return [
            'total_premium' => $costs['total_premium'] * $multiplier,
            'employee_contribution' => $costs['employee_contribution'] * $multiplier,
            'employer_contribution' => $costs['employer_contribution'] * $multiplier,
        ];
    }

    private function applyDiscounts(EmployeeBenefits $benefit, array $costs): array
    {
        $discounts = 0;

        // Apply employee tenure discount
        $tenureDiscount = $this->calculateTenureDiscount($benefit);
        $discounts += $tenureDiscount;

        // Apply bulk enrollment discount
        $bulkDiscount = $this->calculateBulkDiscount($benefit);
        $discounts += $bulkDiscount;

        // Apply the discounts
        $discountMultiplier = (100 - $discounts) / 100;

        return [
            'total_premium' => round($costs['total_premium'] * $discountMultiplier, 2),
            'employee_contribution' => round($costs['employee_contribution'] * $discountMultiplier, 2),
            'employer_contribution' => round($costs['employer_contribution'] * $discountMultiplier, 2),
            'discounts_applied' => $discounts,
        ];
    }

    private function getNetworkTypeMultiplier(string $networkType): float
    {
        return match ($networkType) {
            'ppo' => 1.0,
            'hmo' => 0.85,
            'epo' => 0.90,
            'pos' => 0.95,
            'hdhp' => 0.75,
            default => 1.0
        };
    }

    private function getBenefitTypeMultiplier(string $benefitType): float
    {
        return match ($benefitType) {
            'health' => 1.0,
            'dental' => 0.25,
            'vision' => 0.15,
            'life' => 0.10,
            'disability' => 0.20,
            'retirement' => 0.05,
            'other' => 0.30,
            default => 1.0
        };
    }

    private function getEmployeeContributionPercentage(string $coverageLevel): float
    {
        return match ($coverageLevel) {
            'individual' => 25.0,
            'employee_plus_spouse' => 30.0,
            'employee_plus_children' => 35.0,
            'family' => 40.0,
            default => 30.0
        };
    }

    private function getEmployerContributionPercentage(string $coverageLevel): float
    {
        return match ($coverageLevel) {
            'individual' => 75.0,
            'employee_plus_spouse' => 70.0,
            'employee_plus_children' => 65.0,
            'family' => 60.0,
            default => 70.0
        };
    }

    private function calculateTenureDiscount(EmployeeBenefits $benefit): float
    {
        // Calculate based on employee tenure
        $employee = $benefit->employee;
        if (! $employee || ! $employee->hire_date) {
            return 0;
        }

        $tenureYears = \now()->diffInYears($employee->hire_date);

        if ($tenureYears >= 10) {
            return 15.0;
        }
        if ($tenureYears >= 5) {
            return 10.0;
        }
        if ($tenureYears >= 2) {
            return 5.0;
        }

        return 0;
    }

    private function calculateBulkDiscount(EmployeeBenefits $benefit): float
    {
        // Calculate based on company size or enrollment percentage
        // This would typically come from company settings
        return 5.0; // Default 5% bulk discount
    }
}
