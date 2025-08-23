<?php

namespace Fereydooni\Shopping\app\Actions\EmployeeBenefits;

use Fereydooni\Shopping\app\DTOs\EmployeeBenefitsDTO;
use Fereydooni\Shopping\app\Models\EmployeeBenefits;
use App\Repositories\EmployeeBenefitsRepository;
use Fereydooni\Shopping\app\Events\EmployeeBenefits\EmployeeBenefitsExpiring;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class ProcessBenefitsRenewalAction
{
    public function __construct(
        private EmployeeBenefitsRepository $repository
    ) {}

    public function execute(int $daysAhead = 30): array
    {
        try {
            // Identify expiring benefits
            $expiringBenefits = $this->repository->findExpiringSoon($daysAhead);

            if ($expiringBenefits->isEmpty()) {
                return [
                    'expiring_count' => 0,
                    'renewals_processed' => 0,
                    'auto_renewals' => 0,
                    'manual_renewals' => 0
                ];
            }

            $renewalsProcessed = 0;
            $autoRenewals = 0;
            $manualRenewals = 0;

            foreach ($expiringBenefits as $benefit) {
                try {
                    $renewalData = $this->processBenefitRenewal($benefit);

                    if ($renewalData['auto_renewed']) {
                        $autoRenewals++;
                    } else {
                        $manualRenewals++;
                    }

                    $renewalsProcessed++;

                    // Send renewal notifications
                    $this->sendRenewalNotifications($benefit, $renewalData);

                    // Fire expiring event
                    \event(new EmployeeBenefitsExpiring($benefit, $renewalData));

                } catch (\Exception $e) {
                    Log::error('Failed to process renewal for benefit', [
                        'benefit_id' => $benefit->id,
                        'error' => $e->getMessage()
                    ]);
                }
            }

            return [
                'expiring_count' => $expiringBenefits->count(),
                'renewals_processed' => $renewalsProcessed,
                'auto_renewals' => $autoRenewals,
                'manual_renewals' => $manualRenewals
            ];

        } catch (\Exception $e) {
            Log::error('Failed to process benefits renewal', [
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    private function processBenefitRenewal(EmployeeBenefits $benefit): array
    {
        // Check if benefit can be auto-renewed
        $canAutoRenew = $this->canAutoRenew($benefit);

        if ($canAutoRenew) {
            return $this->autoRenewBenefit($benefit);
        } else {
            return $this->prepareManualRenewal($benefit);
        }
    }

    private function canAutoRenew(EmployeeBenefits $benefit): bool
    {
        // Check if benefit type supports auto-renewal
        $autoRenewableTypes = ['health', 'dental', 'vision'];
        if (!in_array($benefit->benefit_type->value, $autoRenewableTypes)) {
            return false;
        }

        // Check if employee is still active
        if (!$benefit->employee || !$benefit->employee->is_active) {
            return false;
        }

        // Check if benefit is not terminated
        if ($benefit->status->value === 'terminated') {
            return false;
        }

        return true;
    }

    private function autoRenewBenefit(EmployeeBenefits $benefit): array
    {
        // Calculate new effective date
        $newEffectiveDate = $this->calculateNewEffectiveDate($benefit);

        // Calculate renewal costs
        $renewalCosts = $this->calculateRenewalCosts($benefit);

        // Update the benefit
        $this->repository->update($benefit, [
            'effective_date' => $newEffectiveDate,
            'premium_amount' => $renewalCosts['premium_amount'],
            'employee_contribution' => $renewalCosts['employee_contribution'],
            'employer_contribution' => $renewalCosts['employer_contribution'],
            'total_cost' => $renewalCosts['total_cost'],
            'status' => 'enrolled',
            'is_active' => true
        ]);

        return [
            'auto_renewed' => true,
            'new_effective_date' => $newEffectiveDate,
            'renewal_costs' => $renewalCosts,
            'renewal_type' => 'automatic'
        ];
    }

    private function prepareManualRenewal(EmployeeBenefits $benefit): array
    {
        // Calculate renewal costs
        $renewalCosts = $this->calculateRenewalCosts($benefit);

        // Set status to pending for manual review
        $this->repository->update($benefit, [
            'status' => 'pending',
            'is_active' => false
        ]);

        return [
            'auto_renewed' => false,
            'renewal_costs' => $renewalCosts,
            'renewal_type' => 'manual',
            'requires_approval' => true
        ];
    }

    private function calculateNewEffectiveDate(EmployeeBenefits $benefit): string
    {
        $currentEndDate = $benefit->end_date ?? $benefit->effective_date;
        return \now()->addDays(1)->format('Y-m-d');
    }

    private function calculateRenewalCosts(EmployeeBenefits $benefit): array
    {
        $basePremium = $benefit->premium_amount;

        // Apply annual increase (typically 3-5%)
        $annualIncrease = 0.04; // 4% default
        $newPremium = $basePremium * (1 + $annualIncrease);

        // Recalculate contributions
        $employeeContribution = $benefit->employee_contribution * (1 + $annualIncrease);
        $employerContribution = $benefit->employer_contribution * (1 + $annualIncrease);
        $totalCost = $newPremium;

        return [
            'premium_amount' => round($newPremium, 2),
            'employee_contribution' => round($employeeContribution, 2),
            'employer_contribution' => round($employerContribution, 2),
            'total_cost' => round($totalCost, 2),
            'annual_increase' => $annualIncrease * 100
        ];
    }

    private function sendRenewalNotifications(EmployeeBenefits $benefit, array $renewalData): void
    {
        // Send notification to employee
        // Send notification to HR
        // Send notification to benefits administrator

        if ($renewalData['auto_renewed']) {
            // Send auto-renewal confirmation
        } else {
            // Send manual renewal request
        }
    }
}
