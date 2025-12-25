<?php

namespace App\Repositories\Interfaces;

use App\DTOs\ProviderPerformanceDTO;
use App\Models\ProviderPerformance;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\CursorPaginator;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;

interface ProviderPerformanceRepositoryInterface
{
    // Basic CRUD operations
    public function all(): Collection;

    public function paginate(int $perPage = 15): LengthAwarePaginator;

    public function simplePaginate(int $perPage = 15): Paginator;

    public function cursorPaginate(int $perPage = 15, ?string $cursor = null): CursorPaginator;

    public function find(int $id): ?ProviderPerformance;

    public function findDTO(int $id): ?ProviderPerformanceDTO;

    public function create(array $data): ProviderPerformance;

    public function createAndReturnDTO(array $data): ProviderPerformanceDTO;

    public function update(ProviderPerformance $providerPerformance, array $data): bool;

    public function updateAndReturnDTO(ProviderPerformance $providerPerformance, array $data): ?ProviderPerformanceDTO;

    public function delete(ProviderPerformance $providerPerformance): bool;

    // Find by specific criteria
    public function findByProviderId(int $providerId): Collection;

    public function findByProviderIdDTO(int $providerId): Collection;

    public function findByPeriod(string $periodStart, string $periodEnd): Collection;

    public function findByPeriodDTO(string $periodStart, string $periodEnd): Collection;

    public function findByPerformanceGrade(string $grade): Collection;

    public function findByPerformanceGradeDTO(string $grade): Collection;

    public function findByPeriodType(string $periodType): Collection;

    public function findByPeriodTypeDTO(string $periodType): Collection;

    public function findByProviderAndPeriod(int $providerId, string $periodStart, string $periodEnd): ?ProviderPerformance;

    public function findByProviderAndPeriodDTO(int $providerId, string $periodStart, string $periodEnd): ?ProviderPerformanceDTO;

    public function findByProviderAndGrade(int $providerId, string $grade): Collection;

    public function findByProviderAndGradeDTO(int $providerId, string $grade): Collection;

    // Verification operations
    public function findVerified(): Collection;

    public function findVerifiedDTO(): Collection;

    public function findUnverified(): Collection;

    public function findUnverifiedDTO(): Collection;

    public function verify(ProviderPerformance $providerPerformance, int $verifiedBy, ?string $notes = null): bool;

    public function unverify(ProviderPerformance $providerPerformance): bool;

    // Performance analysis
    public function findTopPerformers(int $limit = 10): Collection;

    public function findTopPerformersDTO(int $limit = 10): Collection;

    public function findBottomPerformers(int $limit = 10): Collection;

    public function findBottomPerformersDTO(int $limit = 10): Collection;

    // Range-based queries
    public function findByRevenueRange(float $minRevenue, float $maxRevenue): Collection;

    public function findByRevenueRangeDTO(float $minRevenue, float $maxRevenue): Collection;

    public function findBySatisfactionRange(float $minScore, float $maxScore): Collection;

    public function findBySatisfactionRangeDTO(float $minScore, float $maxScore): Collection;

    public function findByDeliveryRateRange(float $minRate, float $maxRate): Collection;

    public function findByDeliveryRateRangeDTO(float $minRate, float $maxRate): Collection;

    public function findByReturnRateRange(float $minRate, float $maxRate): Collection;

    public function findByReturnRateRangeDTO(float $minRate, float $maxRate): Collection;

    public function findByDefectRateRange(float $minRate, float $maxRate): Collection;

    public function findByDefectRateRangeDTO(float $minRate, float $maxRate): Collection;

    public function findByPerformanceScoreRange(float $minScore, float $maxScore): Collection;

    public function findByPerformanceScoreRangeDTO(float $minScore, float $maxScore): Collection;

    // Performance calculations and updates
    public function calculatePerformance(ProviderPerformance $providerPerformance): bool;

    public function updateMetrics(ProviderPerformance $providerPerformance, array $metrics): bool;

    public function recalculateScore(ProviderPerformance $providerPerformance): bool;

    public function updateGrade(ProviderPerformance $providerPerformance): bool;

    // Count operations
    public function getPerformanceCount(int $providerId): int;

    public function getPerformanceCountByGrade(int $providerId, string $grade): int;

    public function getPerformanceCountByPeriod(int $providerId, string $periodType): int;

    public function getVerifiedPerformanceCount(int $providerId): int;

    public function getUnverifiedPerformanceCount(int $providerId): int;

    public function getTotalPerformanceCount(): int;

    public function getTotalPerformanceCountByGrade(string $grade): int;

    public function getTotalPerformanceCountByPeriod(string $periodType): int;

    public function getTotalVerifiedPerformanceCount(): int;

    public function getTotalUnverifiedPerformanceCount(): int;

    // Average calculations
    public function getAveragePerformanceScore(): float;

    public function getAveragePerformanceScoreByProvider(int $providerId): float;

    public function getAveragePerformanceScoreByGrade(string $grade): float;

    public function getAveragePerformanceScoreByPeriod(string $periodType): float;

    public function getAverageRevenue(): float;

    public function getAverageRevenueByProvider(int $providerId): float;

    public function getAverageRevenueByGrade(string $grade): float;

    public function getAverageRevenueByPeriod(string $periodType): float;

    public function getAverageSatisfactionScore(): float;

    public function getAverageSatisfactionScoreByProvider(int $providerId): float;

    public function getAverageSatisfactionScoreByGrade(string $grade): float;

    public function getAverageSatisfactionScoreByPeriod(string $periodType): float;

    public function getAverageDeliveryRate(): float;

    public function getAverageDeliveryRateByProvider(int $providerId): float;

    public function getAverageDeliveryRateByGrade(string $grade): float;

    public function getAverageDeliveryRateByPeriod(string $periodType): float;

    public function getAverageReturnRate(): float;

    public function getAverageReturnRateByProvider(int $providerId): float;

    public function getAverageReturnRateByGrade(string $grade): float;

    public function getAverageReturnRateByPeriod(string $periodType): float;

    public function getAverageDefectRate(): float;

    public function getAverageDefectRateByProvider(int $providerId): float;

    public function getAverageDefectRateByGrade(string $grade): float;

    public function getAverageDefectRateByPeriod(string $periodType): float;

    // Historical data and trends
    public function getPerformanceHistory(int $providerId, int $limit = 10): Collection;

    public function getPerformanceHistoryDTO(int $providerId, int $limit = 10): Collection;

    public function getPerformanceTrend(int $providerId, string $periodType, int $periods = 12): Collection;

    public function getPerformanceTrendDTO(int $providerId, string $periodType, int $periods = 12): Collection;

    // Performance comparisons and benchmarks
    public function getPerformanceComparison(int $providerId, string $periodType): array;

    public function getPerformanceComparisonDTO(int $providerId, string $periodType): array;

    public function getPerformanceBenchmarks(string $periodType): array;

    public function getPerformanceBenchmarksDTO(string $periodType): array;

    // Search functionality
    public function searchPerformance(string $query): Collection;

    public function searchPerformanceDTO(string $query): Collection;

    public function searchPerformanceByProvider(int $providerId, string $query): Collection;

    public function searchPerformanceByProviderDTO(int $providerId, string $query): Collection;

    // Analytics and reporting
    public function getPerformanceAnalytics(int $providerId): array;

    public function getPerformanceAnalyticsByGrade(int $providerId, string $grade): array;

    public function getPerformanceAnalyticsByPeriod(int $providerId, string $periodType): array;

    public function getGlobalPerformanceAnalytics(): array;

    public function getGlobalPerformanceAnalyticsByGrade(string $grade): array;

    public function getGlobalPerformanceAnalyticsByPeriod(string $periodType): array;

    public function getPerformanceDistribution(int $providerId): array;

    public function getGlobalPerformanceDistribution(): array;

    public function getPerformanceHeatmap(int $providerId): array;

    public function getGlobalPerformanceHeatmap(): array;

    public function getPerformanceAlerts(int $providerId): array;

    public function getGlobalPerformanceAlerts(): array;

    public function getPerformanceReports(int $providerId, string $reportType): array;

    public function getGlobalPerformanceReports(string $reportType): array;
}
