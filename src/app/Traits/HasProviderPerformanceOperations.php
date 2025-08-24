<?php

namespace App\Traits;

use App\Models\ProviderPerformance;
use App\DTOs\ProviderPerformanceDTO;
use App\Enums\PerformanceGrade;
use App\Enums\PeriodType;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Carbon\Carbon;

trait HasProviderPerformanceOperations
{
    /**
     * Get all performance records for this provider
     */
    public function performances(): Collection
    {
        return $this->hasMany(ProviderPerformance::class, 'provider_id')->get();
    }

    /**
     * Get performance records by period type
     */
    public function performancesByPeriodType(string $periodType): Collection
    {
        return $this->hasMany(ProviderPerformance::class, 'provider_id')
            ->where('period_type', $periodType)
            ->orderBy('period_start', 'desc')
            ->get();
    }

    /**
     * Get performance records by grade
     */
    public function performancesByGrade(string $grade): Collection
    {
        return $this->hasMany(ProviderPerformance::class, 'provider_id')
            ->where('performance_grade', $grade)
            ->orderBy('period_start', 'desc')
            ->get();
    }

    /**
     * Get performance records by date range
     */
    public function performancesByDateRange(string $startDate, string $endDate): Collection
    {
        return $this->hasMany(ProviderPerformance::class, 'provider_id')
            ->whereBetween('period_start', [$startDate, $endDate])
            ->orderBy('period_start', 'desc')
            ->get();
    }

    /**
     * Get latest performance record
     */
    public function latestPerformance(): ?ProviderPerformance
    {
        return $this->hasMany(ProviderPerformance::class, 'provider_id')
            ->latest('period_start')
            ->first();
    }

    /**
     * Get performance records for the last N periods
     */
    public function recentPerformances(int $count = 5): Collection
    {
        return $this->hasMany(ProviderPerformance::class, 'provider_id')
            ->orderBy('period_start', 'desc')
            ->limit($count)
            ->get();
    }

    /**
     * Get verified performance records
     */
    public function verifiedPerformances(): Collection
    {
        return $this->hasMany(ProviderPerformance::class, 'provider_id')
            ->where('is_verified', true)
            ->orderBy('verified_at', 'desc')
            ->get();
    }

    /**
     * Get unverified performance records
     */
    public function unverifiedPerformances(): Collection
    {
        return $this->hasMany(ProviderPerformance::class, 'provider_id')
            ->where('is_verified', false)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Get performance records by year
     */
    public function performancesByYear(int $year): Collection
    {
        return $this->hasMany(ProviderPerformance::class, 'provider_id')
            ->whereYear('period_start', $year)
            ->orderBy('period_start', 'desc')
            ->get();
    }

    /**
     * Get performance records by month
     */
    public function performancesByMonth(int $year, int $month): Collection
    {
        return $this->hasMany(ProviderPerformance::class, 'provider_id')
            ->whereYear('period_start', $year)
            ->whereMonth('period_start', $month)
            ->orderBy('period_start', 'desc')
            ->get();
    }

    /**
     * Get performance records by quarter
     */
    public function performancesByQuarter(int $year, int $quarter): Collection
    {
        $startMonth = ($quarter - 1) * 3 + 1;
        $endMonth = $quarter * 3;

        return $this->hasMany(ProviderPerformance::class, 'provider_id')
            ->whereYear('period_start', $year)
            ->whereMonth('period_start', '>=', $startMonth)
            ->whereMonth('period_start', '<=', $endMonth)
            ->orderBy('period_start', 'desc')
            ->get();
    }

    /**
     * Get performance records by week
     */
    public function performancesByWeek(int $year, int $week): Collection
    {
        $startDate = Carbon::now()->setISODate($year, $week)->startOfWeek();
        $endDate = $startDate->copy()->endOfWeek();

        return $this->hasMany(ProviderPerformance::class, 'provider_id')
            ->whereBetween('period_start', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
            ->orderBy('period_start', 'desc')
            ->get();
    }

    /**
     * Get performance records by day
     */
    public function performancesByDay(string $date): Collection
    {
        return $this->hasMany(ProviderPerformance::class, 'provider_id')
            ->whereDate('period_start', $date)
            ->orderBy('period_start', 'desc')
            ->get();
    }

    /**
     * Get performance records with pagination
     */
    public function paginatedPerformances(int $perPage = 15): LengthAwarePaginator
    {
        return $this->hasMany(ProviderPerformance::class, 'provider_id')
            ->orderBy('period_start', 'desc')
            ->paginate($perPage);
    }

    /**
     * Get performance records by performance score range
     */
    public function performancesByScoreRange(float $minScore, float $maxScore): Collection
    {
        return $this->hasMany(ProviderPerformance::class, 'provider_id')
            ->whereBetween('performance_score', [$minScore, $maxScore])
            ->orderBy('performance_score', 'desc')
            ->get();
    }

    /**
     * Get performance records by revenue range
     */
    public function performancesByRevenueRange(float $minRevenue, float $maxRevenue): Collection
    {
        return $this->hasMany(ProviderPerformance::class, 'provider_id')
            ->whereBetween('total_revenue', [$minRevenue, $maxRevenue])
            ->orderBy('total_revenue', 'desc')
            ->get();
    }

    /**
     * Get performance records by customer satisfaction range
     */
    public function performancesBySatisfactionRange(float $minScore, float $maxScore): Collection
    {
        return $this->hasMany(ProviderPerformance::class, 'provider_id')
            ->whereBetween('customer_satisfaction_score', [$minScore, $maxScore])
            ->orderBy('customer_satisfaction_score', 'desc')
            ->get();
    }

    /**
     * Get performance records by delivery rate range
     */
    public function performancesByDeliveryRateRange(float $minRate, float $maxRate): Collection
    {
        return $this->hasMany(ProviderPerformance::class, 'provider_id')
            ->whereBetween('on_time_delivery_rate', [$minRate, $maxRate])
            ->orderBy('on_time_delivery_rate', 'desc')
            ->get();
    }

    /**
     * Get performance records by return rate range
     */
    public function performancesByReturnRateRange(float $minRate, float $maxRate): Collection
    {
        return $this->hasMany(ProviderPerformance::class, 'provider_id')
            ->whereBetween('return_rate', [$minRate, $maxRate])
            ->orderBy('return_rate', 'asc')
            ->get();
    }

    /**
     * Get performance records by defect rate range
     */
    public function performancesByDefectRateRange(float $minRate, float $maxRate): Collection
    {
        return $this->hasMany(ProviderPerformance::class, 'provider_id')
            ->whereBetween('defect_rate', [$minRate, $maxRate])
            ->orderBy('defect_rate', 'asc')
            ->get();
    }

    /**
     * Get performance records that need attention (low performers or with alerts)
     */
    public function performancesNeedingAttention(): Collection
    {
        return $this->hasMany(ProviderPerformance::class, 'provider_id')
            ->where(function ($query) {
                $query->whereIn('performance_grade', [PerformanceGrade::D, PerformanceGrade::F])
                    ->orWhere('on_time_delivery_rate', '<', 90)
                    ->orWhere('customer_satisfaction_score', '<', 7.0)
                    ->orWhere('return_rate', '>', 5)
                    ->orWhere('defect_rate', '>', 2);
            })
            ->orderBy('performance_score', 'asc')
            ->get();
    }

    /**
     * Get high performing records
     */
    public function highPerformances(): Collection
    {
        return $this->hasMany(ProviderPerformance::class, 'provider_id')
            ->whereIn('performance_grade', [PerformanceGrade::A, PerformanceGrade::B])
            ->orderBy('performance_score', 'desc')
            ->get();
    }

    /**
     * Get low performing records
     */
    public function lowPerformances(): Collection
    {
        return $this->hasMany(ProviderPerformance::class, 'provider_id')
            ->whereIn('performance_grade', [PerformanceGrade::D, PerformanceGrade::F])
            ->orderBy('performance_score', 'asc')
            ->get();
    }

    /**
     * Get average performing records
     */
    public function averagePerformances(): Collection
    {
        return $this->hasMany(ProviderPerformance::class, 'provider_id')
            ->where('performance_grade', PerformanceGrade::C)
            ->orderBy('performance_score', 'desc')
            ->get();
    }

    /**
     * Get performance records as DTOs
     */
    public function performancesAsDTOs(): Collection
    {
        return $this->performances()->map(fn($performance) => ProviderPerformanceDTO::fromModel($performance));
    }

    /**
     * Get performance records by period type as DTOs
     */
    public function performancesByPeriodTypeAsDTOs(string $periodType): Collection
    {
        return $this->performancesByPeriodType($periodType)
            ->map(fn($performance) => ProviderPerformanceDTO::fromModel($performance));
    }

    /**
     * Get performance records by grade as DTOs
     */
    public function performancesByGradeAsDTOs(string $grade): Collection
    {
        return $this->performancesByGrade($grade)
            ->map(fn($performance) => ProviderPerformanceDTO::fromModel($performance));
    }

    /**
     * Get performance records by date range as DTOs
     */
    public function performancesByDateRangeAsDTOs(string $startDate, string $endDate): Collection
    {
        return $this->performancesByDateRange($startDate, $endDate)
            ->map(fn($performance) => ProviderPerformanceDTO::fromModel($performance));
    }

    /**
     * Get latest performance as DTO
     */
    public function latestPerformanceAsDTO(): ?ProviderPerformanceDTO
    {
        $performance = $this->latestPerformance();
        return $performance ? ProviderPerformanceDTO::fromModel($performance) : null;
    }

    /**
     * Get recent performances as DTOs
     */
    public function recentPerformancesAsDTOs(int $count = 5): Collection
    {
        return $this->recentPerformances($count)
            ->map(fn($performance) => ProviderPerformanceDTO::fromModel($performance));
    }

    /**
     * Get verified performances as DTOs
     */
    public function verifiedPerformancesAsDTOs(): Collection
    {
        return $this->verifiedPerformances()
            ->map(fn($performance) => ProviderPerformanceDTO::fromModel($performance));
    }

    /**
     * Get unverified performances as DTOs
     */
    public function unverifiedPerformancesAsDTOs(): Collection
    {
        return $this->unverifiedPerformances()
            ->map(fn($performance) => ProviderPerformanceDTO::fromModel($performance));
    }
}
