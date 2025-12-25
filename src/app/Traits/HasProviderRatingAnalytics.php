<?php

namespace Fereydooni\Shopping\App\Traits;

use App\Repositories\Interfaces\ProviderRatingRepositoryInterface;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

trait HasProviderRatingAnalytics
{
    protected ProviderRatingRepositoryInterface $providerRatingRepository;

    /**
     * Get comprehensive rating statistics
     */
    public function getRatingStatistics(): array
    {
        $cacheKey = 'rating_statistics_global';

        return Cache::remember($cacheKey, 3600, function () {
            return [
                'total_ratings' => $this->providerRatingRepository->getTotalRatingCount(),
                'average_rating' => $this->providerRatingRepository->getAverageRating(),
                'verified_ratings' => $this->providerRatingRepository->getVerifiedRatingCount(),
                'pending_ratings' => $this->providerRatingRepository->getPendingRatingCount(),
                'flagged_ratings' => $this->providerRatingRepository->getFlaggedRatingCount(),
                'recommendation_percentage' => $this->providerRatingRepository->getRecommendationPercentage(),
                'rating_distribution' => $this->getRatingDistribution(),
                'category_distribution' => $this->getCategoryDistribution(),
                'status_distribution' => $this->getStatusDistribution(),
            ];
        });
    }

    /**
     * Get provider-specific rating statistics
     */
    public function getProviderRatingStatistics(int $providerId): array
    {
        $cacheKey = "provider_rating_stats_{$providerId}";

        return Cache::remember($cacheKey, 1800, function () use ($providerId) {
            return [
                'average_rating' => $this->providerRatingRepository->getProviderAverageRating($providerId),
                'total_ratings' => $this->providerRatingRepository->getProviderRatingCount($providerId),
                'rating_breakdown' => $this->providerRatingRepository->getProviderRatingBreakdown($providerId),
                'recommendation_percentage' => $this->providerRatingRepository->getProviderRecommendationPercentage($providerId),
                'category_breakdown' => $this->getProviderCategoryBreakdown($providerId),
                'rating_trends' => $this->getProviderRatingTrends($providerId),
                'vote_statistics' => $this->getProviderVoteStatistics($providerId),
                'quality_metrics' => $this->calculateProviderQualityMetrics($providerId),
            ];
        });
    }

    /**
     * Get rating trends over time
     */
    public function getRatingTrends(?string $startDate = null, ?string $endDate = null): array
    {
        $startDate = $startDate ?: now()->subMonths(6)->toDateString();
        $endDate = $endDate ?: now()->toDateString();

        $cacheKey = "rating_trends_{$startDate}_{$endDate}";

        return Cache::remember($cacheKey, 3600, function () use ($startDate, $endDate) {
            $ratings = $this->providerRatingRepository->findByDateRange($startDate, $endDate);

            $trends = [];
            $currentDate = Carbon::parse($startDate);
            $endDateObj = Carbon::parse($endDate);

            while ($currentDate <= $endDateObj) {
                $dateKey = $currentDate->toDateString();
                $dayRatings = $ratings->filter(function ($rating) use ($dateKey) {
                    return $rating->created_at->toDateString() === $dateKey;
                });

                $trends[$dateKey] = [
                    'count' => $dayRatings->count(),
                    'average_rating' => $dayRatings->count() > 0 ? round($dayRatings->avg('rating_value'), 2) : 0,
                    'verified_count' => $dayRatings->where('is_verified', true)->count(),
                    'approved_count' => $dayRatings->where('status', 'approved')->count(),
                ];

                $currentDate->addDay();
            }

            return $trends;
        });
    }

    /**
     * Calculate rating metrics for a provider
     */
    public function calculateRatingMetrics(int $providerId): array
    {
        $cacheKey = "rating_metrics_{$providerId}";

        return Cache::remember($cacheKey, 1800, function () use ($providerId) {
            $ratings = $this->providerRatingRepository->findByProviderId($providerId);
            $approvedRatings = $ratings->where('status', 'approved');

            if ($approvedRatings->isEmpty()) {
                return [
                    'overall_score' => 0,
                    'quality_score' => 0,
                    'service_score' => 0,
                    'pricing_score' => 0,
                    'communication_score' => 0,
                    'reliability_score' => 0,
                    'confidence_interval' => 0,
                    'sample_size' => 0,
                ];
            }

            $metrics = [];
            $categories = ['overall', 'quality', 'service', 'pricing', 'communication', 'reliability'];

            foreach ($categories as $category) {
                $categoryRatings = $approvedRatings->where('category', $category);
                if ($categoryRatings->isNotEmpty()) {
                    $metrics["{$category}_score"] = round($categoryRatings->avg('rating_value'), 2);
                } else {
                    $metrics["{$category}_score"] = 0;
                }
            }

            $metrics['overall_score'] = round($approvedRatings->avg('rating_value'), 2);
            $metrics['confidence_interval'] = $this->calculateConfidenceInterval($approvedRatings);
            $metrics['sample_size'] = $approvedRatings->count();

            return $metrics;
        });
    }

    /**
     * Get top rated providers
     */
    public function getTopRatedProviders(int $limit = 10): Collection
    {
        $cacheKey = "top_rated_providers_{$limit}";

        return Cache::remember($cacheKey, 3600, function () use ($limit) {
            return $this->providerRatingRepository->getTopRatedProviders($limit);
        });
    }

    /**
     * Get top rated providers as DTOs
     */
    public function getTopRatedProvidersDTO(int $limit = 10): Collection
    {
        $cacheKey = "top_rated_providers_dto_{$limit}";

        return Cache::remember($cacheKey, 3600, function () use ($limit) {
            return $this->providerRatingRepository->getTopRatedProvidersDTO($limit);
        });
    }

    /**
     * Get rating distribution by value
     */
    public function getRatingDistribution(): array
    {
        $distribution = [];

        for ($i = 1; $i <= 5; $i++) {
            $distribution[$i] = $this->providerRatingRepository->getRatingCountByValue($i);
        }

        return $distribution;
    }

    /**
     * Get rating distribution by category
     */
    public function getCategoryDistribution(): array
    {
        $categories = ['overall', 'quality', 'service', 'pricing', 'communication', 'reliability'];
        $distribution = [];

        foreach ($categories as $category) {
            $distribution[$category] = $this->providerRatingRepository->getRatingCountByCategory($category);
        }

        return $distribution;
    }

    /**
     * Get rating distribution by status
     */
    public function getStatusDistribution(): array
    {
        return [
            'pending' => $this->providerRatingRepository->getPendingRatingCount(),
            'approved' => $this->providerRatingRepository->findByStatus('approved')->count(),
            'rejected' => $this->providerRatingRepository->findByStatus('rejected')->count(),
            'flagged' => $this->providerRatingRepository->getFlaggedRatingCount(),
        ];
    }

    /**
     * Get provider category breakdown
     */
    public function getProviderCategoryBreakdown(int $providerId): array
    {
        $ratings = $this->providerRatingRepository->findByProviderId($providerId);
        $approvedRatings = $ratings->where('status', 'approved');

        $breakdown = [];
        $categories = ['overall', 'quality', 'service', 'pricing', 'communication', 'reliability'];

        foreach ($categories as $category) {
            $categoryRatings = $approvedRatings->where('category', $category);
            $breakdown[$category] = [
                'count' => $categoryRatings->count(),
                'average' => $categoryRatings->count() > 0 ? round($categoryRatings->avg('rating_value'), 2) : 0,
                'percentage' => $approvedRatings->count() > 0 ? round(($categoryRatings->count() / $approvedRatings->count()) * 100, 2) : 0,
            ];
        }

        return $breakdown;
    }

    /**
     * Get provider rating trends
     */
    public function getProviderRatingTrends(int $providerId, int $months = 6): array
    {
        $startDate = now()->subMonths($months)->toDateString();
        $endDate = now()->toDateString();

        $ratings = $this->providerRatingRepository->findByDateRange($startDate, $endDate);
        $providerRatings = $ratings->where('provider_id', $providerId);

        $trends = [];
        $currentDate = Carbon::parse($startDate);
        $endDateObj = Carbon::parse($endDate);

        while ($currentDate <= $endDateObj) {
            $dateKey = $currentDate->toDateString();
            $monthKey = $currentDate->format('Y-m');

            if (! isset($trends[$monthKey])) {
                $trends[$monthKey] = [
                    'month' => $monthKey,
                    'ratings_count' => 0,
                    'average_rating' => 0,
                    'verified_count' => 0,
                ];
            }

            $dayRatings = $providerRatings->filter(function ($rating) use ($dateKey) {
                return $rating->created_at->toDateString() === $dateKey;
            });

            $trends[$monthKey]['ratings_count'] += $dayRatings->count();
            $trends[$monthKey]['verified_count'] += $dayRatings->where('is_verified', true)->count();

            $currentDate->addDay();
        }

        // Calculate monthly averages
        foreach ($trends as $monthKey => &$monthData) {
            $monthRatings = $providerRatings->filter(function ($rating) use ($monthKey) {
                return $rating->created_at->format('Y-m') === $monthKey;
            });

            $monthData['average_rating'] = $monthRatings->count() > 0 ? round($monthRatings->avg('rating_value'), 2) : 0;
        }

        return array_values($trends);
    }

    /**
     * Calculate confidence interval for ratings
     */
    protected function calculateConfidenceInterval(Collection $ratings): float
    {
        if ($ratings->count() < 2) {
            return 0;
        }

        $values = $ratings->pluck('rating_value')->toArray();
        $mean = array_sum($values) / count($values);
        $variance = array_sum(array_map(function ($x) use ($mean) {
            return pow($x - $mean, 2);
        }, $values)) / (count($values) - 1);

        $standardError = sqrt($variance / count($values));
        $confidenceLevel = 1.96; // 95% confidence interval

        return round($confidenceLevel * $standardError, 3);
    }

    /**
     * Get rating quality metrics
     */
    public function calculateProviderQualityMetrics(int $providerId): array
    {
        $ratings = $this->providerRatingRepository->findByProviderId($providerId);
        $approvedRatings = $ratings->where('status', 'approved');

        if ($approvedRatings->isEmpty()) {
            return [
                'consistency_score' => 0,
                'reliability_score' => 0,
                'engagement_score' => 0,
                'overall_quality' => 0,
            ];
        }

        // Consistency score (how consistent ratings are)
        $ratingValues = $approvedRatings->pluck('rating_value')->toArray();
        $variance = $this->calculateVariance($ratingValues);
        $consistencyScore = max(0, 100 - ($variance * 20)); // Lower variance = higher consistency

        // Reliability score (based on verified ratings and helpful votes)
        $verifiedCount = $approvedRatings->where('is_verified', true)->count();
        $totalVotes = $approvedRatings->sum('total_votes');
        $helpfulVotes = $approvedRatings->sum('helpful_votes');

        $verificationRate = $approvedRatings->count() > 0 ? ($verifiedCount / $approvedRatings->count()) * 100 : 0;
        $helpfulRate = $totalVotes > 0 ? ($helpfulVotes / $totalVotes) * 100 : 0;
        $reliabilityScore = ($verificationRate + $helpfulRate) / 2;

        // Engagement score (based on comment length and helpful votes)
        $avgCommentLength = $approvedRatings->avg(function ($rating) {
            return strlen($rating->comment);
        });
        $engagementScore = min(100, ($avgCommentLength / 10) + ($helpfulRate * 0.5));

        // Overall quality score
        $overallQuality = round(($consistencyScore + $reliabilityScore + $engagementScore) / 3, 2);

        return [
            'consistency_score' => round($consistencyScore, 2),
            'reliability_score' => round($reliabilityScore, 2),
            'engagement_score' => round($engagementScore, 2),
            'overall_quality' => $overallQuality,
        ];
    }

    /**
     * Calculate variance of an array of numbers
     */
    protected function calculateVariance(array $numbers): float
    {
        if (count($numbers) < 2) {
            return 0;
        }

        $mean = array_sum($numbers) / count($numbers);
        $variance = array_sum(array_map(function ($x) use ($mean) {
            return pow($x - $mean, 2);
        }, $numbers)) / (count($numbers) - 1);

        return $variance;
    }

    /**
     * Get rating performance comparison
     */
    public function getRatingPerformanceComparison(int $providerId): array
    {
        $providerStats = $this->getProviderRatingStatistics($providerId);
        $globalStats = $this->getRatingStatistics();

        $comparison = [];
        $metrics = ['average_rating', 'total_ratings', 'recommendation_percentage'];

        foreach ($metrics as $metric) {
            if (isset($providerStats[$metric]) && isset($globalStats[$metric])) {
                $providerValue = $providerStats[$metric];
                $globalValue = $globalStats[$metric];

                if ($globalValue > 0) {
                    $percentage = (($providerValue - $globalValue) / $globalValue) * 100;
                    $comparison[$metric] = [
                        'provider_value' => $providerValue,
                        'global_value' => $globalValue,
                        'difference' => round($providerValue - $globalValue, 2),
                        'percentage_difference' => round($percentage, 2),
                        'status' => $percentage > 0 ? 'above_average' : ($percentage < 0 ? 'below_average' : 'average'),
                    ];
                }
            }
        }

        return $comparison;
    }
}
