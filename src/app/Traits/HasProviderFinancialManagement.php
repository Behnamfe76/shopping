<?php

namespace Fereydooni\Shopping\App\Traits;

use Fereydooni\Shopping\App\Models\Provider;
use Fereydooni\Shopping\App\Repositories\Interfaces\ProviderRepositoryInterface;

trait HasProviderFinancialManagement
{
    protected ProviderRepositoryInterface $providerRepository;

    /**
     * Update provider credit limit
     */
    public function updateProviderCreditLimit(Provider $provider, float $newLimit): bool
    {
        return $this->providerRepository->update($provider, ['credit_limit' => $newLimit]);
    }

    /**
     * Update provider current balance
     */
    public function updateProviderCurrentBalance(Provider $provider, float $newBalance): bool
    {
        return $this->providerRepository->update($provider, ['current_balance' => $newBalance]);
    }

    /**
     * Update provider commission rate
     */
    public function updateProviderCommissionRate(Provider $provider, float $newRate): bool
    {
        return $this->providerRepository->update($provider, ['commission_rate' => $newRate]);
    }

    /**
     * Update provider discount rate
     */
    public function updateProviderDiscountRate(Provider $provider, float $newRate): bool
    {
        return $this->providerRepository->update($provider, ['discount_rate' => $newRate]);
    }

    /**
     * Update provider payment terms
     */
    public function updateProviderPaymentTerms(Provider $provider, string $terms): bool
    {
        return $this->providerRepository->update($provider, ['payment_terms' => $terms]);
    }

    /**
     * Update provider bank information
     */
    public function updateProviderBankInfo(Provider $provider, array $bankInfo): bool
    {
        $data = [
            'bank_name' => $bankInfo['bank_name'] ?? null,
            'bank_account_number' => $bankInfo['bank_account_number'] ?? null,
            'bank_routing_number' => $bankInfo['bank_routing_number'] ?? null,
        ];

        return $this->providerRepository->update($provider, $data);
    }

    /**
     * Get providers with high credit limits
     */
    public function getProvidersWithHighCreditLimits(float $threshold = 10000): array
    {
        $providers = $this->providerRepository->all();

        return $providers->where('credit_limit', '>=', $threshold)->toArray();
    }

    /**
     * Get providers with low credit limits
     */
    public function getProvidersWithLowCreditLimits(float $threshold = 1000): array
    {
        $providers = $this->providerRepository->all();

        return $providers->where('credit_limit', '<=', $threshold)->toArray();
    }

    /**
     * Get providers with high balances
     */
    public function getProvidersWithHighBalances(float $threshold = 5000): array
    {
        $providers = $this->providerRepository->all();

        return $providers->where('current_balance', '>=', $threshold)->toArray();
    }

    /**
     * Get providers with negative balances
     */
    public function getProvidersWithNegativeBalances(): array
    {
        $providers = $this->providerRepository->all();

        return $providers->where('current_balance', '<', 0)->toArray();
    }

    /**
     * Get total credit limit across all providers
     */
    public function getTotalCreditLimit(): float
    {
        return $this->providerRepository->getTotalCreditLimit();
    }

    /**
     * Get average credit limit across all providers
     */
    public function getAverageCreditLimit(): float
    {
        return $this->providerRepository->getAverageCreditLimit();
    }

    /**
     * Get total current balance across all providers
     */
    public function getTotalCurrentBalance(): float
    {
        return $this->providerRepository->getTotalCurrentBalance();
    }

    /**
     * Get average current balance across all providers
     */
    public function getAverageCurrentBalance(): float
    {
        return $this->providerRepository->getAverageCurrentBalance();
    }

    /**
     * Get top spending providers
     */
    public function getTopSpendingProviders(int $limit = 10): array
    {
        return $this->providerRepository->getTopSpenders($limit)->toArray();
    }

    /**
     * Get providers by spending range
     */
    public function getProvidersBySpendingRange(float $minSpent, float $maxSpent): array
    {
        $providers = $this->providerRepository->all();

        return $providers->where('total_spent', '>=', $minSpent)
            ->where('total_spent', '<=', $maxSpent)
            ->toArray();
    }

    /**
     * Get providers with high commission rates
     */
    public function getProvidersWithHighCommissionRates(float $threshold = 0.15): array
    {
        $providers = $this->providerRepository->all();

        return $providers->where('commission_rate', '>=', $threshold)->toArray();
    }

    /**
     * Get providers with high discount rates
     */
    public function getProvidersWithHighDiscountRates(float $threshold = 0.20): array
    {
        $providers = $this->providerRepository->all();

        return $providers->where('discount_rate', '>=', $threshold)->toArray();
    }

    /**
     * Calculate provider credit utilization
     */
    public function calculateProviderCreditUtilization(Provider $provider): float
    {
        if ($provider->credit_limit <= 0) {
            return 0;
        }

        return round(($provider->current_balance / $provider->credit_limit) * 100, 2);
    }

    /**
     * Get providers with high credit utilization
     */
    public function getProvidersWithHighCreditUtilization(float $threshold = 80): array
    {
        $providers = $this->providerRepository->all();
        $highUtilization = [];

        foreach ($providers as $provider) {
            $utilization = $this->calculateProviderCreditUtilization($provider);
            if ($utilization >= $threshold) {
                $highUtilization[] = $provider;
            }
        }

        return $highUtilization;
    }

    /**
     * Get financial summary for a provider
     */
    public function getProviderFinancialSummary(int $providerId): array
    {
        $provider = $this->providerRepository->find($providerId);

        if (! $provider) {
            return [];
        }

        return [
            'credit_limit' => $provider->credit_limit ?? 0,
            'current_balance' => $provider->current_balance ?? 0,
            'available_credit' => ($provider->credit_limit ?? 0) - ($provider->current_balance ?? 0),
            'credit_utilization' => $this->calculateProviderCreditUtilization($provider),
            'total_spent' => $provider->total_spent ?? 0,
            'average_order_value' => $provider->average_order_value ?? 0,
            'commission_rate' => $provider->commission_rate ?? 0,
            'discount_rate' => $provider->discount_rate ?? 0,
            'payment_terms' => $provider->payment_terms ?? '',
        ];
    }

    /**
     * Get overall financial statistics
     */
    public function getOverallFinancialStats(): array
    {
        return [
            'total_credit_limit' => $this->getTotalCreditLimit(),
            'average_credit_limit' => $this->getAverageCreditLimit(),
            'total_current_balance' => $this->getTotalCurrentBalance(),
            'average_current_balance' => $this->getAverageCurrentBalance(),
            'total_provider_spending' => $this->providerRepository->getTotalProviderSpending(),
            'average_provider_spending' => $this->providerRepository->getAverageProviderSpending(),
        ];
    }
}
