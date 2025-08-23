<?php

namespace App\Actions\ProviderContract;

use App\Models\ProviderContract;
use Illuminate\Support\Facades\Log;

class CalculateContractMetricsAction
{
    /**
     * Execute the action to calculate contract metrics
     *
     * @param ProviderContract $contract
     * @return array
     */
    public function execute(ProviderContract $contract): array
    {
        try {
            $metrics = [
                'contract_value' => $this->calculateContractValue($contract),
                'commission_amount' => $this->calculateCommissionAmount($contract),
                'days_remaining' => $this->calculateDaysRemaining($contract),
                'days_elapsed' => $this->calculateDaysElapsed($contract),
                'completion_percentage' => $this->calculateCompletionPercentage($contract),
                'renewal_probability' => $this->calculateRenewalProbability($contract),
                'performance_score' => $this->calculatePerformanceScore($contract),
                'financial_impact' => $this->calculateFinancialImpact($contract),
            ];

            Log::info('Contract metrics calculated successfully', [
                'contract_id' => $contract->id,
                'metrics' => $metrics
            ]);

            return $metrics;

        } catch (\Exception $e) {
            Log::error('Failed to calculate contract metrics', [
                'contract_id' => $contract->id,
                'error' => $e->getMessage()
            ]);

            return [];
        }
    }

    /**
     * Calculate contract value
     *
     * @param ProviderContract $contract
     * @return float
     */
    protected function calculateContractValue(ProviderContract $contract): float
    {
        $baseValue = $contract->contract_value ?? 0;

        // Add any additional value from terms or conditions
        $additionalValue = $this->calculateAdditionalValue($contract);

        return $baseValue + $additionalValue;
    }

    /**
     * Calculate additional value from contract terms
     *
     * @param ProviderContract $contract
     * @return float
     */
    protected function calculateAdditionalValue(ProviderContract $contract): float
    {
        $additionalValue = 0;

        // Calculate value from commission structure
        if ($contract->commission_rate && $contract->contract_value) {
            $additionalValue += ($contract->contract_value * $contract->commission_rate / 100);
        }

        // Add value from payment terms
        if ($contract->payment_terms && isset($contract->payment_terms['bonus'])) {
            $additionalValue += $contract->payment_terms['bonus'];
        }

        return $additionalValue;
    }

    /**
     * Calculate commission amount
     *
     * @param ProviderContract $contract
     * @return float
     */
    protected function calculateCommissionAmount(ProviderContract $contract): float
    {
        if (!$contract->commission_rate || !$contract->contract_value) {
            return 0;
        }

        return ($contract->contract_value * $contract->commission_rate) / 100;
    }

    /**
     * Calculate days remaining in contract
     *
     * @param ProviderContract $contract
     * @return int
     */
    protected function calculateDaysRemaining(ProviderContract $contract): int
    {
        if (!$contract->end_date) {
            return 0;
        }

        $endDate = \Carbon\Carbon::parse($contract->end_date);
        $currentDate = now();

        if ($endDate->isPast()) {
            return 0;
        }

        return $currentDate->diffInDays($endDate, false);
    }

    /**
     * Calculate days elapsed since contract start
     *
     * @param ProviderContract $contract
     * @return int
     */
    protected function calculateDaysElapsed(ProviderContract $contract): int
    {
        if (!$contract->start_date) {
            return 0;
        }

        $startDate = \Carbon\Carbon::parse($contract->start_date);
        $currentDate = now();

        if ($startDate->isFuture()) {
            return 0;
        }

        return $currentDate->diffInDays($startDate, false);
    }

    /**
     * Calculate completion percentage
     *
     * @param ProviderContract $contract
     * @return float
     */
    protected function calculateCompletionPercentage(ProviderContract $contract): float
    {
        if (!$contract->start_date || !$contract->end_date) {
            return 0;
        }

        $startDate = \Carbon\Carbon::parse($contract->start_date);
        $endDate = \Carbon\Carbon::parse($contract->end_date);
        $currentDate = now();

        if ($startDate->isFuture()) {
            return 0;
        }

        if ($endDate->isPast()) {
            return 100;
        }

        $totalDays = $startDate->diffInDays($endDate, false);
        $elapsedDays = $currentDate->diffInDays($startDate, false);

        if ($totalDays <= 0) {
            return 0;
        }

        return min(100, ($elapsedDays / $totalDays) * 100);
    }

    /**
     * Calculate renewal probability
     *
     * @param ProviderContract $contract
     * @return float
     */
    protected function calculateRenewalProbability(ProviderContract $contract): float
    {
        $probability = 50; // Base probability

        // Adjust based on contract status
        switch ($contract->status) {
            case 'active':
                $probability += 20;
                break;
            case 'suspended':
                $probability -= 30;
                break;
            case 'expired':
                $probability -= 20;
                break;
            case 'terminated':
                $probability = 0;
                break;
        }

        // Adjust based on auto-renewal setting
        if ($contract->auto_renewal) {
            $probability += 25;
        }

        // Adjust based on performance (if available)
        if (isset($contract->performance_metrics)) {
            $performanceScore = $contract->performance_metrics['score'] ?? 0;
            $probability += ($performanceScore - 50) * 0.5;
        }

        return max(0, min(100, $probability));
    }

    /**
     * Calculate performance score
     *
     * @param ProviderContract $contract
     * @return float
     */
    protected function calculatePerformanceScore(ProviderContract $contract): float
    {
        $score = 50; // Base score

        // Adjust based on contract completion
        $completionPercentage = $this->calculateCompletionPercentage($contract);
        $score += ($completionPercentage - 50) * 0.3;

        // Adjust based on days remaining vs elapsed
        $daysRemaining = $this->calculateDaysRemaining($contract);
        $daysElapsed = $this->calculateDaysElapsed($contract);

        if ($daysElapsed > 0) {
            $timeRatio = $daysRemaining / $daysElapsed;
            if ($timeRatio > 1) {
                $score += 10; // Ahead of schedule
            } elseif ($timeRatio < 0.8) {
                $score -= 15; // Behind schedule
            }
        }

        // Adjust based on contract value performance
        if ($contract->contract_value && isset($contract->actual_value)) {
            $valueRatio = $contract->actual_value / $contract->contract_value;
            if ($valueRatio > 1) {
                $score += 15; // Exceeding value
            } elseif ($valueRatio < 0.8) {
                $score -= 10; // Below value
            }
        }

        return max(0, min(100, $score));
    }

    /**
     * Calculate financial impact
     *
     * @param ProviderContract $contract
     * @return array
     */
    protected function calculateFinancialImpact(ProviderContract $contract): array
    {
        $contractValue = $this->calculateContractValue($contract);
        $commissionAmount = $this->calculateCommissionAmount($contract);

        return [
            'total_value' => $contractValue,
            'commission_amount' => $commissionAmount,
            'net_value' => $contractValue - $commissionAmount,
            'monthly_value' => $contractValue / 12, // Assuming 12-month contracts
            'daily_value' => $contractValue / 365, // Assuming 365-day contracts
            'roi_percentage' => $this->calculateROI($contract),
        ];
    }

    /**
     * Calculate ROI percentage
     *
     * @param ProviderContract $contract
     * @return float
     */
    protected function calculateROI(ProviderContract $contract): float
    {
        if (!$contract->contract_value || !$contract->investment_amount) {
            return 0;
        }

        $profit = $contract->contract_value - $contract->investment_amount;
        return ($profit / $contract->investment_amount) * 100;
    }
}
