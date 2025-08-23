<?php

namespace Fereydooni\Shopping\App\Traits;

use Fereydooni\Shopping\App\Models\Provider;
use Fereydooni\Shopping\App\Repositories\Interfaces\ProviderRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

trait HasProviderRatingManagement
{
    protected ProviderRepositoryInterface $providerRepository;

    /**
     * Update provider overall rating
     */
    public function updateProviderRating(Provider $provider, float $rating): bool
    {
        return $this->providerRepository->update($provider, ['rating' => $rating]);
    }

    /**
     * Update provider quality rating
     */
    public function updateProviderQualityRating(Provider $provider, float $rating): bool
    {
        return $this->providerRepository->update($provider, ['quality_rating' => $rating]);
    }

    /**
     * Update provider delivery rating
     */
    public function updateProviderDeliveryRating(Provider $provider, float $rating): bool
    {
        return $this->providerRepository->update($provider, ['delivery_rating' => $rating]);
    }

    /**
     * Update provider communication rating
     */
    public function updateProviderCommunicationRating(Provider $provider, float $rating): bool
    {
        return $this->providerRepository->update($provider, ['communication_rating' => $rating]);
    }

    /**
     * Update provider response time
     */
    public function updateProviderResponseTime(Provider $provider, int $responseTime): bool
    {
        return $this->providerRepository->update($provider, ['response_time' => $responseTime]);
    }

    /**
     * Update provider on-time delivery rate
     */
    public function updateProviderOnTimeDeliveryRate(Provider $provider, float $rate): bool
    {
        return $this->providerRepository->update($provider, ['on_time_delivery_rate' => $rate]);
    }

    /**
     * Update provider return rate
     */
    public function updateProviderReturnRate(Provider $provider, float $rate): bool
    {
        return $this->providerRepository->update($provider, ['return_rate' => $rate]);
    }

    /**
     * Update provider defect rate
     */
    public function updateProviderDefectRate(Provider $provider, float $rate): bool
    {
        return $this->providerRepository->update($provider, ['defect_rate' => $rate]);
    }

    /**
     * Calculate provider overall rating from individual ratings
     */
    public function calculateProviderOverallRating(Provider $provider): float
    {
        $qualityRating = $provider->quality_rating ?? 0;
        $deliveryRating = $provider->delivery_rating ?? 0;
        $communicationRating = $provider->communication_rating ?? 0;

        if ($qualityRating == 0 && $deliveryRating == 0 && $communicationRating == 0) {
            return 0;
        }

        $totalRating = $qualityRating + $deliveryRating + $communicationRating;
        $count = 0;

        if ($qualityRating > 0) $count++;
        if ($deliveryRating > 0) $count++;
        if ($communicationRating > 0) $count++;

        return $count > 0 ? round($totalRating / $count, 2) : 0;
    }

    /**
     * Get top rated providers
     */
    public function getTopRatedProviders(int $limit = 10): Collection
    {
        return $this->providerRepository->getTopRated($limit);
    }

    /**
     * Get most reliable providers
     */
    public function getMostReliableProviders(int $limit = 10): Collection
    {
        return $this->providerRepository->getMostReliable($limit);
    }

    /**
     * Get providers by rating range
     */
    public function getProvidersByRatingRange(float $minRating, float $maxRating): Collection
    {
        return $this->providerRepository->search('')
            ->where('rating', '>=', $minRating)
            ->where('rating', '<=', $maxRating)
            ->orderBy('rating', 'desc')
            ->get();
    }

    /**
     * Get providers with low ratings
     */
    public function getProvidersWithLowRatings(float $threshold = 3.0): Collection
    {
        return $this->providerRepository->search('')
            ->where('rating', '<', $threshold)
            ->orderBy('rating', 'asc')
            ->get();
    }

    /**
     * Get providers with high ratings
     */
    public function getProvidersWithHighRatings(float $threshold = 4.5): Collection
    {
        return $this->providerRepository->search('')
            ->where('rating', '>=', $threshold)
            ->orderBy('rating', 'desc')
            ->get();
    }

    /**
     * Get average provider rating
     */
    public function getAverageProviderRating(): float
    {
        return $this->providerRepository->getAverageProviderRating();
    }

    /**
     * Get provider rating statistics
     */
    public function getProviderRatingStats(): array
    {
        $providers = $this->providerRepository->all();

        $ratings = $providers->pluck('rating')->filter()->values();

        if ($ratings->isEmpty()) {
            return [
                'average' => 0,
                'min' => 0,
                'max' => 0,
                'count' => 0,
                'distribution' => []
            ];
        }

        $distribution = [
            '5.0' => $ratings->filter(fn($r) => $r >= 4.5)->count(),
            '4.0' => $ratings->filter(fn($r) => $r >= 3.5 && $r < 4.5)->count(),
            '3.0' => $ratings->filter(fn($r) => $r >= 2.5 && $r < 3.5)->count(),
            '2.0' => $ratings->filter(fn($r) => $r >= 1.5 && $r < 2.5)->count(),
            '1.0' => $ratings->filter(fn($r) => $r < 1.5)->count(),
        ];

        return [
            'average' => round($ratings->avg(), 2),
            'min' => $ratings->min(),
            'max' => $ratings->max(),
            'count' => $ratings->count(),
            'distribution' => $distribution
        ];
    }

    /**
     * Get providers needing rating review
     */
    public function getProvidersNeedingRatingReview(): Collection
    {
        return $this->providerRepository->search('')
            ->where(function($query) {
                $query->where('rating', '<', 3.0)
                      ->orWhereNull('rating');
            })
            ->get();
    }

    /**
     * Get providers with rating improvements
     */
    public function getProvidersWithRatingImprovements(): Collection
    {
        // This would typically compare current vs historical ratings
        // For now, return empty collection
        return collect();
    }

    /**
     * Get rating trend for a provider
     */
    public function getProviderRatingTrend(int $providerId, int $months = 6): array
    {
        // This would typically query a rating_history table
        // For now, return empty array
        return [];
    }
}
