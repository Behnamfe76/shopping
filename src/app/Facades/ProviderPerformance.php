<?php

namespace App\Facades;

use App\Services\ProviderPerformanceService;
use Illuminate\Support\Facades\Facade;

/**
 * @method static \Illuminate\Database\Eloquent\Collection getAllPerformances()
 * @method static \Illuminate\Pagination\LengthAwarePaginator getPaginatedPerformances(int $perPage = 15)
 * @method static \Illuminate\Pagination\Paginator getSimplePaginatedPerformances(int $perPage = 15)
 * @method static \Illuminate\Pagination\CursorPaginator getCursorPaginatedPerformances(int $perPage = 15, string $cursor = null)
 * @method static \App\Models\ProviderPerformance|null getPerformanceById(int $id)
 * @method static \App\DTOs\ProviderPerformanceDTO|null getPerformanceDTOById(int $id)
 * @method static \App\Models\ProviderPerformance createPerformance(array $data)
 * @method static \App\DTOs\ProviderPerformanceDTO createPerformanceAndReturnDTO(array $data)
 * @method static bool updatePerformance(\App\Models\ProviderPerformance $performance, array $data)
 * @method static \App\DTOs\ProviderPerformanceDTO|null updatePerformanceAndReturnDTO(\App\Models\ProviderPerformance $performance, array $data)
 * @method static bool deletePerformance(\App\Models\ProviderPerformance $performance)
 * @method static \Illuminate\Database\Eloquent\Collection getPerformancesByProvider(int $providerId)
 * @method static \Illuminate\Database\Eloquent\Collection getPerformancesByProviderDTO(int $providerId)
 * @method static \Illuminate\Database\Eloquent\Collection getPerformancesByPeriod(string $periodStart, string $periodEnd)
 * @method static \Illuminate\Database\Eloquent\Collection getPerformancesByPeriodDTO(string $periodStart, string $periodEnd)
 * @method static \Illuminate\Database\Eloquent\Collection getPerformancesByGrade(string $grade)
 * @method static \Illuminate\Database\Eloquent\Collection getPerformancesByGradeDTO(string $grade)
 * @method static \Illuminate\Database\Eloquent\Collection getPerformancesByPeriodType(string $periodType)
 * @method static \Illuminate\Database\Eloquent\Collection getPerformancesByPeriodTypeDTO(string $periodType)
 * @method static \App\Models\ProviderPerformance|null getPerformanceByProviderAndPeriod(int $providerId, string $periodStart, string $periodEnd)
 * @method static \App\DTOs\ProviderPerformanceDTO|null getPerformanceByProviderAndPeriodDTO(int $providerId, string $periodStart, string $periodEnd)
 * @method static \Illuminate\Database\Eloquent\Collection getPerformancesByProviderAndGrade(int $providerId, string $grade)
 * @method static \Illuminate\Database\Eloquent\Collection getPerformancesByProviderAndGradeDTO(int $providerId, string $grade)
 * @method static \Illuminate\Database\Eloquent\Collection getVerifiedPerformances()
 * @method static \Illuminate\Database\Eloquent\Collection getVerifiedPerformancesDTO()
 * @method static \Illuminate\Database\Eloquent\Collection getUnverifiedPerformances()
 * @method static \Illuminate\Database\Eloquent\Collection getUnverifiedPerformancesDTO()
 * @method static bool verifyPerformance(\App\Models\ProviderPerformance $performance, int $verifiedBy, string $notes = null)
 * @method static bool unverifyPerformance(\App\Models\ProviderPerformance $performance)
 * @method static \Illuminate\Database\Eloquent\Collection getTopPerformers(int $limit = 10)
 * @method static \Illuminate\Database\Eloquent\Collection getTopPerformersDTO(int $limit = 10)
 * @method static \Illuminate\Database\Eloquent\Collection getBottomPerformers(int $limit = 10)
 * @method static \Illuminate\Database\Eloquent\Collection getBottomPerformersDTO(int $limit = 10)
 * @method static bool calculatePerformance(\App\Models\ProviderPerformance $performance)
 * @method static bool updateMetrics(\App\Models\ProviderPerformance $performance, array $metrics)
 * @method static bool recalculateScore(\App\Models\ProviderPerformance $performance)
 * @method static bool updateGrade(\App\Models\ProviderPerformance $performance)
 * @method static \Illuminate\Database\Eloquent\Collection getPerformancesByProviderId(int $providerId)
 * @method static \Illuminate\Database\Eloquent\Collection getPerformancesByProviderIdDTO(int $providerId)
 * @method static \Illuminate\Database\Eloquent\Collection getPerformancesByPeriodType(string $periodType)
 * @method static \Illuminate\Database\Eloquent\Collection getPerformancesByPeriodTypeDTO(string $periodType)
 * @method static \Illuminate\Database\Eloquent\Collection getPerformancesByPerformanceGrade(string $grade)
 * @method static \Illuminate\Database\Eloquent\Collection getPerformancesByPerformanceGradeDTO(string $grade)
 * @method static \App\Models\ProviderPerformance|null getPerformanceByProviderAndPeriod(int $providerId, string $periodStart, string $periodEnd)
 * @method static \App\DTOs\ProviderPerformanceDTO|null getPerformanceByProviderAndPeriodDTO(int $providerId, string $periodStart, string $periodEnd)
 * @method static \Illuminate\Database\Eloquent\Collection getPerformancesByProviderAndGrade(int $providerId, string $grade)
 * @method static \Illuminate\Database\Eloquent\Collection getPerformancesByProviderAndGradeDTO(int $providerId, string $grade)
 * @method static \Illuminate\Database\Eloquent\Collection getVerifiedPerformances()
 * @method static \Illuminate\Database\Eloquent\Collection getVerifiedPerformancesDTO()
 * @method static \Illuminate\Database\Eloquent\Collection getUnverifiedPerformances()
 * @method static \Illuminate\Database\Eloquent\Collection getUnverifiedPerformancesDTO()
 * @method static bool verifyPerformance(\App\Models\ProviderPerformance $performance, int $verifiedBy, string $notes = null)
 * @method static bool unverifyPerformance(\App\Models\ProviderPerformance $performance)
 * @method static \Illuminate\Database\Eloquent\Collection getTopPerformers(int $limit = 10)
 * @method static \Illuminate\Database\Eloquent\Collection getTopPerformersDTO(int $limit = 10)
 * @method static \Illuminate\Database\Eloquent\Collection getBottomPerformers(int $limit = 10)
 * @method static \Illuminate\Database\Eloquent\Collection getBottomPerformersDTO(int $limit = 10)
 * @method static \Illuminate\Database\Eloquent\Collection getPerformancesByRevenueRange(float $minRevenue, float $maxRevenue)
 * @method static \Illuminate\Database\Eloquent\Collection getPerformancesByRevenueRangeDTO(float $minRevenue, float $maxRevenue)
 * @method static \Illuminate\Database\Eloquent\Collection getPerformancesBySatisfactionRange(float $minScore, float $maxScore)
 * @method static \Illuminate\Database\Eloquent\Collection getPerformancesBySatisfactionRangeDTO(float $minScore, float $maxScore)
 * @method static \Illuminate\Database\Eloquent\Collection getPerformancesByDeliveryRateRange(float $minRate, float $maxRate)
 * @method static \Illuminate\Database\Eloquent\Collection getPerformancesByDeliveryRateRangeDTO(float $minRate, float $maxRate)
 * @method static \Illuminate\Database\Eloquent\Collection getPerformancesByReturnRateRange(float $minRate, float $maxRate)
 * @method static \Illuminate\Database\Eloquent\Collection getPerformancesByReturnRateRangeDTO(float $minRate, float $maxRate)
 * @method static \Illuminate\Database\Eloquent\Collection getPerformancesByDefectRateRange(float $minRate, float $maxRate)
 * @method static \Illuminate\Database\Eloquent\Collection getPerformancesByDefectRateRangeDTO(float $minRate, float $maxRate)
 * @method static \Illuminate\Database\Eloquent\Collection getPerformancesByPerformanceScoreRange(float $minScore, float $maxScore)
 * @method static \Illuminate\Database\Eloquent\Collection getPerformancesByPerformanceScoreRangeDTO(float $minScore, float $maxScore)
 * @method static bool calculatePerformance(\App\Models\ProviderPerformance $performance)
 * @method static bool updateMetrics(\App\Models\ProviderPerformance $performance, array $metrics)
 * @method static bool recalculateScore(\App\Models\ProviderPerformance $performance)
 * @method static bool updateGrade(\App\Models\ProviderPerformance $performance)
 * @method static int getPerformanceCount(int $providerId)
 * @method static int getPerformanceCountByGrade(int $providerId, string $grade)
 * @method static int getPerformanceCountByPeriod(int $providerId, string $periodType)
 * @method static int getVerifiedPerformanceCount(int $providerId)
 * @method static int getUnverifiedPerformanceCount(int $providerId)
 * @method static int getTotalPerformanceCount()
 * @method static int getTotalPerformanceCountByGrade(string $grade)
 * @method static int getTotalPerformanceCountByPeriod(string $periodType)
 * @method static int getTotalVerifiedPerformanceCount()
 * @method static int getTotalUnverifiedPerformanceCount()
 * @method static float getAveragePerformanceScore()
 * @method static float getAveragePerformanceScoreByProvider(int $providerId)
 * @method static float getAveragePerformanceScoreByGrade(string $grade)
 * @method static float getAveragePerformanceScoreByPeriod(string $periodType)
 * @method static float getAverageRevenue()
 * @method static float getAverageRevenueByProvider(int $providerId)
 * @method static float getAverageRevenueByGrade(string $grade)
 * @method static float getAverageRevenueByPeriod(string $periodType)
 * @method static float getAverageSatisfactionScore()
 * @method static float getAverageSatisfactionScoreByProvider(int $providerId)
 * @method static float getAverageSatisfactionScoreByGrade(string $grade)
 * @method static float getAverageSatisfactionScoreByPeriod(string $periodType)
 * @method static float getAverageDeliveryRate()
 * @method static float getAverageDeliveryRateByProvider(int $providerId)
 * @method static float getAverageDeliveryRateByGrade(string $grade)
 * @method static float getAverageDeliveryRateByPeriod(string $periodType)
 * @method static float getAverageReturnRate()
 * @method static float getAverageReturnRateByProvider(int $providerId)
 * @method static float getAverageReturnRateByGrade(string $grade)
 * @method static float getAverageReturnRateByPeriod(string $periodType)
 * @method static float getAverageDefectRate()
 * @method static float getAverageDefectRateByProvider(int $providerId)
 * @method static float getAverageDefectRateByGrade(string $grade)
 * @method static float getAverageDefectRateByPeriod(string $periodType)
 * @method static \Illuminate\Database\Eloquent\Collection getPerformanceHistory(int $providerId, int $limit = 10)
 * @method static \Illuminate\Database\Eloquent\Collection getPerformanceHistoryDTO(int $providerId, int $limit = 10)
 * @method static \Illuminate\Database\Eloquent\Collection getPerformanceTrend(int $providerId, string $periodType, int $periods = 12)
 * @method static \Illuminate\Database\Eloquent\Collection getPerformanceTrendDTO(int $providerId, string $periodType, int $periods = 12)
 * @method static array getPerformanceComparison(int $providerId, string $periodType)
 * @method static array getPerformanceComparisonDTO(int $providerId, string $periodType)
 * @method static array getPerformanceBenchmarks(string $periodType)
 * @method static array getPerformanceBenchmarksDTO(string $periodType)
 * @method static \Illuminate\Database\Eloquent\Collection searchPerformance(string $query)
 * @method static \Illuminate\Database\Eloquent\Collection searchPerformanceDTO(string $query)
 * @method static \Illuminate\Database\Eloquent\Collection searchPerformanceByProvider(int $providerId, string $query)
 * @method static \Illuminate\Database\Eloquent\Collection searchPerformanceByProviderDTO(int $providerId, string $query)
 * @method static array getPerformanceAnalytics(int $providerId)
 * @method static array getPerformanceAnalyticsByGrade(int $providerId, string $grade)
 * @method static array getPerformanceAnalyticsByPeriod(int $providerId, string $periodType)
 * @method static array getGlobalPerformanceAnalytics()
 * @method static array getGlobalPerformanceAnalyticsByGrade(string $grade)
 * @method static array getGlobalPerformanceAnalyticsByPeriod(string $periodType)
 * @method static array getPerformanceDistribution(int $providerId)
 * @method static array getGlobalPerformanceDistribution()
 * @method static array getPerformanceHeatmap(int $providerId)
 * @method static array getGlobalPerformanceHeatmap()
 * @method static array getPerformanceAlerts(int $providerId)
 * @method static array getGlobalPerformanceAlerts()
 * @method static array getPerformanceReports(int $providerId, string $reportType)
 * @method static array getGlobalPerformanceReports(string $reportType)
 *
 * @see \App\Services\ProviderPerformanceService
 */
class ProviderPerformance extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return ProviderPerformanceService::class;
    }

    /**
     * Get the service instance.
     *
     * @return \App\Services\ProviderPerformanceService
     */
    public static function getFacadeRoot()
    {
        return static::resolveFacadeInstance(static::getFacadeAccessor());
    }

    /**
     * Get the service instance.
     *
     * @return \App\Services\ProviderPerformanceService
     */
    public static function service()
    {
        return static::getFacadeRoot();
    }

    /**
     * Get the repository instance.
     *
     * @return \App\Repositories\Interfaces\ProviderPerformanceRepositoryInterface
     */
    public static function repository()
    {
        return static::getFacadeRoot()->repository;
    }

    /**
     * Get the model instance.
     *
     * @return \App\Models\ProviderPerformance
     */
    public static function model()
    {
        return new \App\Models\ProviderPerformance;
    }

    /**
     * Get the DTO instance.
     *
     * @return \App\DTOs\ProviderPerformanceDTO
     */
    public static function dto()
    {
        return new \App\DTOs\ProviderPerformanceDTO(
            id: null,
            provider_id: 0,
            period_start: '',
            period_end: '',
            period_type: \App\Enums\PeriodType::MONTHLY,
            total_orders: 0,
            total_revenue: 0.0,
            average_order_value: 0.0,
            on_time_delivery_rate: 0.0,
            return_rate: 0.0,
            defect_rate: 0.0,
            customer_satisfaction_score: 0.0,
            response_time_avg: 0.0,
            quality_rating: 0.0,
            delivery_rating: 0.0,
            communication_rating: 0.0,
            cost_efficiency_score: 0.0,
            inventory_turnover_rate: 0.0,
            lead_time_avg: 0.0,
            fill_rate: 0.0,
            accuracy_rate: 0.0,
            performance_score: 0.0,
            performance_grade: \App\Enums\PerformanceGrade::C,
            is_verified: false,
            verified_by: null,
            verified_at: null,
            notes: null,
            created_at: null,
            updated_at: null,
            provider: null,
            verifier: null
        );
    }

    /**
     * Get the enum instances.
     *
     * @return array
     */
    public static function enums()
    {
        return [
            'performance_grades' => \App\Enums\PerformanceGrade::cases(),
            'period_types' => \App\Enums\PeriodType::cases(),
        ];
    }

    /**
     * Get the performance grades.
     *
     * @return array
     */
    public static function grades()
    {
        return \App\Enums\PerformanceGrade::cases();
    }

    /**
     * Get the period types.
     *
     * @return array
     */
    public static function periodTypes()
    {
        return \App\Enums\PeriodType::cases();
    }

    /**
     * Get the grade descriptions.
     *
     * @return array
     */
    public static function gradeDescriptions()
    {
        $grades = [];
        foreach (\App\Enums\PerformanceGrade::cases() as $grade) {
            $grades[$grade->value] = $grade->getDescription();
        }

        return $grades;
    }

    /**
     * Get the grade colors.
     *
     * @return array
     */
    public static function gradeColors()
    {
        $colors = [];
        foreach (\App\Enums\PerformanceGrade::cases() as $grade) {
            $colors[$grade->value] = $grade->getColor();
        }

        return $colors;
    }

    /**
     * Get the grade score ranges.
     *
     * @return array
     */
    public static function gradeScoreRanges()
    {
        $ranges = [];
        foreach (\App\Enums\PerformanceGrade::cases() as $grade) {
            $ranges[$grade->value] = $grade->getScoreRange();
        }

        return $ranges;
    }

    /**
     * Get the period type descriptions.
     *
     * @return array
     */
    public static function periodTypeDescriptions()
    {
        $descriptions = [];
        foreach (\App\Enums\PeriodType::cases() as $periodType) {
            $descriptions[$periodType->value] = $periodType->getDescription();
        }

        return $descriptions;
    }

    /**
     * Get the period type labels.
     *
     * @return array
     */
    public static function periodTypeLabels()
    {
        $labels = [];
        foreach (\App\Enums\PeriodType::cases() as $periodType) {
            $labels[$periodType->value] = $periodType->getLabel();
        }

        return $labels;
    }

    /**
     * Get the period type days.
     *
     * @return array
     */
    public static function periodTypeDays()
    {
        $days = [];
        foreach (\App\Enums\PeriodType::cases() as $periodType) {
            $days[$periodType->value] = $periodType->getDays();
        }

        return $days;
    }

    /**
     * Check if a performance score corresponds to a specific grade.
     */
    public static function isScoreInGrade(float $score, string $grade): bool
    {
        $gradeEnum = \App\Enums\PerformanceGrade::from($grade);
        $range = $gradeEnum->getScoreRange();

        return $score >= $range[0] && $score <= $range[1];
    }

    /**
     * Get the grade for a performance score.
     */
    public static function getGradeForScore(float $score): string
    {
        if ($score >= 90) {
            return 'A';
        }
        if ($score >= 80) {
            return 'B';
        }
        if ($score >= 70) {
            return 'C';
        }
        if ($score >= 60) {
            return 'D';
        }

        return 'F';
    }

    /**
     * Get the grade description for a performance score.
     */
    public static function getGradeDescriptionForScore(float $score): string
    {
        $grade = static::getGradeForScore($score);
        $gradeEnum = \App\Enums\PerformanceGrade::from($grade);

        return $gradeEnum->getDescription();
    }

    /**
     * Get the grade color for a performance score.
     */
    public static function getGradeColorForScore(float $score): string
    {
        $grade = static::getGradeForScore($score);
        $gradeEnum = \App\Enums\PerformanceGrade::from($grade);

        return $gradeEnum->getColor();
    }

    /**
     * Calculate the performance score from individual metrics.
     */
    public static function calculateScoreFromMetrics(array $metrics): float
    {
        $weights = [
            'on_time_delivery_rate' => 0.20,
            'customer_satisfaction_score' => 0.25,
            'quality_rating' => 0.20,
            'delivery_rating' => 0.15,
            'communication_rating' => 0.10,
            'cost_efficiency_score' => 0.10,
        ];

        $score = 0;
        foreach ($weights as $metric => $weight) {
            if (isset($metrics[$metric])) {
                $value = $metrics[$metric];
                if ($metric === 'customer_satisfaction_score') {
                    $score += ($value / 10) * 100 * $weight;
                } else {
                    $score += $value * $weight;
                }
            }
        }

        return round($score, 2);
    }

    /**
     * Get the performance trend for a score.
     */
    public static function getTrendForScore(float $score): string
    {
        if ($score >= 80) {
            return 'excellent';
        }
        if ($score >= 60) {
            return 'good';
        }
        if ($score >= 40) {
            return 'fair';
        }

        return 'poor';
    }

    /**
     * Get the performance alerts for metrics.
     */
    public static function getAlertsForMetrics(array $metrics): array
    {
        $alerts = [];

        if (isset($metrics['on_time_delivery_rate']) && $metrics['on_time_delivery_rate'] < 90) {
            $alerts[] = 'On-time delivery rate is below target (90%)';
        }

        if (isset($metrics['customer_satisfaction_score']) && $metrics['customer_satisfaction_score'] < 7.0) {
            $alerts[] = 'Customer satisfaction score is below target (7.0)';
        }

        if (isset($metrics['return_rate']) && $metrics['return_rate'] > 5) {
            $alerts[] = 'Return rate is above acceptable threshold (5%)';
        }

        if (isset($metrics['defect_rate']) && $metrics['defect_rate'] > 2) {
            $alerts[] = 'Defect rate is above acceptable threshold (2%)';
        }

        return $alerts;
    }

    /**
     * Get improvement suggestions for metrics.
     */
    public static function getImprovementSuggestionsForMetrics(array $metrics): array
    {
        $suggestions = [];

        if (isset($metrics['on_time_delivery_rate']) && $metrics['on_time_delivery_rate'] < 90) {
            $suggestions[] = 'Improve logistics and delivery processes';
        }

        if (isset($metrics['customer_satisfaction_score']) && $metrics['customer_satisfaction_score'] < 7.0) {
            $suggestions[] = 'Enhance customer service and communication';
        }

        if (isset($metrics['quality_rating']) && $metrics['quality_rating'] < 8.0) {
            $suggestions[] = 'Implement quality control measures';
        }

        if (isset($metrics['communication_rating']) && $metrics['communication_rating'] < 7.0) {
            $suggestions[] = 'Improve communication channels and response times';
        }

        return $suggestions;
    }
}
