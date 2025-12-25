<?php

namespace Fereydooni\Shopping\App\Traits;

use Fereydooni\Shopping\App\Models\Provider;
use Fereydooni\Shopping\App\Repositories\Interfaces\ProviderRepositoryInterface;

trait HasProviderAnalytics
{
    protected ProviderRepositoryInterface $providerRepository;

    /**
     * Get provider statistics
     */
    public function getProviderStats(): array
    {
        return $this->providerRepository->getProviderStats();
    }

    /**
     * Get provider statistics by status
     */
    public function getProviderStatsByStatus(): array
    {
        return $this->providerRepository->getProviderStatsByStatus();
    }

    /**
     * Get provider statistics by type
     */
    public function getProviderStatsByType(): array
    {
        return $this->providerRepository->getProviderStatsByType();
    }

    /**
     * Get provider growth statistics
     */
    public function getProviderGrowthStats(string $period = 'monthly'): array
    {
        return $this->providerRepository->getProviderGrowthStats($period);
    }

    /**
     * Get provider performance statistics
     */
    public function getProviderPerformanceStats(): array
    {
        return $this->providerRepository->getProviderPerformanceStats();
    }

    /**
     * Get provider quality statistics
     */
    public function getProviderQualityStats(): array
    {
        return $this->providerRepository->getProviderQualityStats();
    }

    /**
     * Get provider financial statistics
     */
    public function getProviderFinancialStats(): array
    {
        return $this->providerRepository->getProviderFinancialStats();
    }

    /**
     * Get provider contract statistics
     */
    public function getProviderContractStats(): array
    {
        return $this->providerRepository->getProviderContractStats();
    }

    /**
     * Get provider lifetime value
     */
    public function getProviderLifetimeValue(int $providerId): float
    {
        return $this->providerRepository->getProviderLifetimeValue($providerId);
    }

    /**
     * Get provider order history
     */
    public function getProviderOrderHistory(int $providerId): array
    {
        $orders = $this->providerRepository->getProviderOrderHistory($providerId);

        return $orders->toArray();
    }

    /**
     * Get provider products
     */
    public function getProviderProducts(int $providerId): array
    {
        $products = $this->providerRepository->getProviderProducts($providerId);

        return $products->toArray();
    }

    /**
     * Get provider invoices
     */
    public function getProviderInvoices(int $providerId): array
    {
        $invoices = $this->providerRepository->getProviderInvoices($providerId);

        return $invoices->toArray();
    }

    /**
     * Get provider payments
     */
    public function getProviderPayments(int $providerId): array
    {
        $payments = $this->providerRepository->getProviderPayments($providerId);

        return $payments->toArray();
    }

    /**
     * Get provider analytics
     */
    public function getProviderAnalytics(int $providerId): array
    {
        return $this->providerRepository->getProviderAnalytics($providerId);
    }

    /**
     * Get provider performance metrics
     */
    public function getProviderPerformanceMetrics(int $providerId): array
    {
        return $this->providerRepository->getProviderPerformanceMetrics($providerId);
    }

    /**
     * Calculate provider score
     */
    public function calculateProviderScore(int $providerId): float
    {
        return $this->providerRepository->calculateProviderScore($providerId);
    }

    /**
     * Get top performing providers
     */
    public function getTopPerformingProviders(int $limit = 10): array
    {
        $providers = $this->providerRepository->all();

        $scoredProviders = [];
        foreach ($providers as $provider) {
            $score = $this->calculateProviderScore($provider->id);
            $scoredProviders[] = [
                'provider' => $provider,
                'score' => $score,
            ];
        }

        usort($scoredProviders, function ($a, $b) {
            return $b['score'] <=> $a['score'];
        });

        return array_slice($scoredProviders, 0, $limit);
    }

    /**
     * Get provider performance comparison
     */
    public function getProviderPerformanceComparison(array $providerIds): array
    {
        $comparison = [];

        foreach ($providerIds as $providerId) {
            $provider = $this->providerRepository->find($providerId);
            if ($provider) {
                $comparison[] = [
                    'id' => $providerId,
                    'name' => $provider->company_name,
                    'rating' => $provider->rating ?? 0,
                    'total_orders' => $provider->total_orders ?? 0,
                    'total_spent' => $provider->total_spent ?? 0,
                    'average_order_value' => $provider->average_order_value ?? 0,
                    'quality_rating' => $provider->quality_rating ?? 0,
                    'delivery_rating' => $provider->delivery_rating ?? 0,
                    'communication_rating' => $provider->communication_rating ?? 0,
                    'score' => $this->calculateProviderScore($providerId),
                ];
            }
        }

        return $comparison;
    }

    /**
     * Get provider trend analysis
     */
    public function getProviderTrendAnalysis(int $providerId, int $months = 6): array
    {
        // This would typically analyze historical data
        // For now, return basic structure
        return [
            'provider_id' => $providerId,
            'period_months' => $months,
            'order_trend' => [],
            'spending_trend' => [],
            'rating_trend' => [],
            'performance_trend' => [],
        ];
    }

    /**
     * Get market share analysis
     */
    public function getMarketShareAnalysis(): array
    {
        $providers = $this->providerRepository->all();
        $totalSpending = $providers->sum('total_spent') ?: 1;

        $marketShare = [];
        foreach ($providers as $provider) {
            $share = ($provider->total_spent ?? 0) / $totalSpending * 100;
            $marketShare[] = [
                'provider_id' => $provider->id,
                'company_name' => $provider->company_name,
                'total_spent' => $provider->total_spent ?? 0,
                'market_share_percentage' => round($share, 2),
            ];
        }

        usort($marketShare, function ($a, $b) {
            return $b['market_share_percentage'] <=> $a['market_share_percentage'];
        });

        return $marketShare;
    }

    /**
     * Get provider efficiency metrics
     */
    public function getProviderEfficiencyMetrics(int $providerId): array
    {
        $provider = $this->providerRepository->find($providerId);

        if (! $provider) {
            return [];
        }

        $totalOrders = $provider->total_orders ?? 0;
        $totalSpent = $provider->total_spent ?? 0;
        $averageOrderValue = $provider->average_order_value ?? 0;

        return [
            'provider_id' => $providerId,
            'total_orders' => $totalOrders,
            'total_spent' => $totalSpent,
            'average_order_value' => $averageOrderValue,
            'orders_per_month' => $totalOrders > 0 ? round($totalOrders / 12, 2) : 0,
            'spending_per_order' => $totalOrders > 0 ? round($totalSpent / $totalOrders, 2) : 0,
            'efficiency_score' => $this->calculateEfficiencyScore($provider),
        ];
    }

    /**
     * Calculate efficiency score for a provider
     */
    private function calculateEfficiencyScore(Provider $provider): float
    {
        $score = 0;

        // Rating component (40%)
        if ($provider->rating) {
            $score += ($provider->rating / 5) * 40;
        }

        // Order volume component (30%)
        if ($provider->total_orders) {
            $score += min(($provider->total_orders / 100) * 30, 30);
        }

        // Quality component (30%)
        if ($provider->quality_rating) {
            $score += ($provider->quality_rating / 5) * 30;
        }

        return round($score, 2);
    }
}
