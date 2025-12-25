<?php

namespace App\Services;

use App\DTOs\ProviderPerformanceDTO;
use App\Models\ProviderPerformance;
use App\Repositories\Interfaces\ProviderPerformanceRepositoryInterface;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\CursorPaginator;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;

class ProviderPerformanceService
{
    protected $repository;

    public function __construct(ProviderPerformanceRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    // Basic CRUD operations
    public function getAllPerformances(): Collection
    {
        return $this->repository->all();
    }

    public function getPaginatedPerformances(int $perPage = 15): LengthAwarePaginator
    {
        return $this->repository->paginate($perPage);
    }

    public function getSimplePaginatedPerformances(int $perPage = 15): Paginator
    {
        return $this->repository->simplePaginate($perPage);
    }

    public function getCursorPaginatedPerformances(int $perPage = 15, ?string $cursor = null): CursorPaginator
    {
        return $this->repository->cursorPaginate($perPage, $cursor);
    }

    public function getPerformanceById(int $id): ?ProviderPerformance
    {
        return $this->repository->find($id);
    }

    public function getPerformanceDTOById(int $id): ?ProviderPerformanceDTO
    {
        return $this->repository->findDTO($id);
    }

    public function createPerformance(array $data): ProviderPerformance
    {
        try {
            // Validate business rules
            $this->validatePerformanceData($data);

            // Create performance record
            $performance = $this->repository->create($data);

            // Dispatch events
            Event::dispatch('provider.performance.created', $performance);

            // Generate alerts if needed
            $this->generatePerformanceAlerts($performance);

            return $performance;
        } catch (\Exception $e) {
            Log::error('Failed to create provider performance: '.$e->getMessage());
            throw $e;
        }
    }

    public function createPerformanceAndReturnDTO(array $data): ProviderPerformanceDTO
    {
        $performance = $this->createPerformance($data);

        return ProviderPerformanceDTO::fromModel($performance);
    }

    public function updatePerformance(ProviderPerformance $performance, array $data): bool
    {
        try {
            // Validate business rules
            $this->validatePerformanceData($data, $performance);

            // Update performance record
            $result = $this->repository->update($performance, $data);

            if ($result) {
                // Dispatch events
                Event::dispatch('provider.performance.updated', $performance);

                // Generate alerts if needed
                $this->generatePerformanceAlerts($performance);
            }

            return $result;
        } catch (\Exception $e) {
            Log::error('Failed to update provider performance: '.$e->getMessage());
            throw $e;
        }
    }

    public function updatePerformanceAndReturnDTO(ProviderPerformance $performance, array $data): ?ProviderPerformanceDTO
    {
        $result = $this->updatePerformance($performance, $data);

        return $result ? ProviderPerformanceDTO::fromModel($performance->fresh()) : null;
    }

    public function deletePerformance(ProviderPerformance $performance): bool
    {
        try {
            $result = $this->repository->delete($performance);

            if ($result) {
                Event::dispatch('provider.performance.deleted', $performance);
            }

            return $result;
        } catch (\Exception $e) {
            Log::error('Failed to delete provider performance: '.$e->getMessage());
            throw $e;
        }
    }

    // Find by specific criteria
    public function getPerformancesByProvider(int $providerId): Collection
    {
        return $this->repository->findByProviderId($providerId);
    }

    public function getPerformancesByProviderDTO(int $providerId): Collection
    {
        return $this->repository->findByProviderIdDTO($providerId);
    }

    public function getPerformancesByPeriod(string $periodStart, string $periodEnd): Collection
    {
        return $this->repository->findByPeriod($periodStart, $periodEnd);
    }

    public function getPerformancesByPeriodDTO(string $periodStart, string $periodEnd): Collection
    {
        return $this->repository->findByPeriodDTO($periodStart, $periodEnd);
    }

    public function getPerformancesByGrade(string $grade): Collection
    {
        return $this->repository->findByPerformanceGrade($grade);
    }

    public function getPerformancesByGradeDTO(string $grade): Collection
    {
        return $this->repository->findByPerformanceGradeDTO($grade);
    }

    public function getPerformancesByPeriodType(string $periodType): Collection
    {
        return $this->repository->findByPeriodType($periodType);
    }

    public function getPerformancesByPeriodTypeDTO(string $periodType): Collection
    {
        return $this->repository->findByPeriodTypeDTO($periodType);
    }

    public function getPerformanceByProviderAndPeriod(int $providerId, string $periodStart, string $periodEnd): ?ProviderPerformance
    {
        return $this->repository->findByProviderAndPeriod($providerId, $periodStart, $periodEnd);
    }

    public function getPerformanceByProviderAndPeriodDTO(int $providerId, string $periodStart, string $periodEnd): ?ProviderPerformanceDTO
    {
        return $this->repository->findByProviderAndPeriodDTO($providerId, $periodStart, $periodEnd);
    }

    public function getPerformancesByProviderAndGrade(int $providerId, string $grade): Collection
    {
        return $this->repository->findByProviderAndGrade($providerId, $grade);
    }

    public function getPerformancesByProviderAndGradeDTO(int $providerId, string $grade): Collection
    {
        return $this->repository->findByProviderAndGradeDTO($providerId, $grade);
    }

    // Verification operations
    public function getVerifiedPerformances(): Collection
    {
        return $this->repository->findVerified();
    }

    public function getVerifiedPerformancesDTO(): Collection
    {
        return $this->repository->findVerifiedDTO();
    }

    public function getUnverifiedPerformances(): Collection
    {
        return $this->repository->findUnverified();
    }

    public function getUnverifiedPerformancesDTO(): Collection
    {
        return $this->repository->findUnverifiedDTO();
    }

    public function verifyPerformance(ProviderPerformance $performance, int $verifiedBy, ?string $notes = null): bool
    {
        try {
            $result = $this->repository->verify($performance, $verifiedBy, $notes);

            if ($result) {
                Event::dispatch('provider.performance.verified', $performance);
            }

            return $result;
        } catch (\Exception $e) {
            Log::error('Failed to verify provider performance: '.$e->getMessage());
            throw $e;
        }
    }

    public function unverifyPerformance(ProviderPerformance $performance): bool
    {
        try {
            $result = $this->repository->unverify($performance);

            if ($result) {
                Event::dispatch('provider.performance.unverified', $performance);
            }

            return $result;
        } catch (\Exception $e) {
            Log::error('Failed to unverify provider performance: '.$e->getMessage());
            throw $e;
        }
    }

    // Performance analysis
    public function getTopPerformers(int $limit = 10): Collection
    {
        return $this->repository->findTopPerformers($limit);
    }

    public function getTopPerformersDTO(int $limit = 10): Collection
    {
        return $this->repository->findTopPerformersDTO($limit);
    }

    public function getBottomPerformers(int $limit = 10): Collection
    {
        return $this->repository->findBottomPerformers($limit);
    }

    public function getBottomPerformersDTO(int $limit = 10): Collection
    {
        return $this->repository->findBottomPerformersDTO($limit);
    }

    // Performance calculations and updates
    public function calculatePerformance(ProviderPerformance $performance): bool
    {
        try {
            $result = $this->repository->calculatePerformance($performance);

            if ($result) {
                Event::dispatch('provider.performance.calculated', $performance);
            }

            return $result;
        } catch (\Exception $e) {
            Log::error('Failed to calculate performance: '.$e->getMessage());
            throw $e;
        }
    }

    public function updateMetrics(ProviderPerformance $performance, array $metrics): bool
    {
        try {
            $result = $this->repository->updateMetrics($performance, $metrics);

            if ($result) {
                Event::dispatch('provider.performance.metrics_updated', $performance);
            }

            return $result;
        } catch (\Exception $e) {
            Log::error('Failed to update metrics: '.$e->getMessage());
            throw $e;
        }
    }

    public function recalculateScore(ProviderPerformance $performance): bool
    {
        return $this->calculatePerformance($performance);
    }

    public function updateGrade(ProviderPerformance $performance): bool
    {
        try {
            $oldGrade = $performance->performance_grade;
            $result = $this->repository->updateGrade($performance);

            if ($result && $oldGrade !== $performance->performance_grade) {
                Event::dispatch('provider.performance.grade_changed', [
                    'performance' => $performance,
                    'old_grade' => $oldGrade,
                    'new_grade' => $performance->performance_grade,
                ]);
            }

            return $result;
        } catch (\Exception $e) {
            Log::error('Failed to update grade: '.$e->getMessage());
            throw $e;
        }
    }

    // Business logic validation
    protected function validatePerformanceData(array $data, ?ProviderPerformance $existingPerformance = null): void
    {
        // Validate period dates
        if (isset($data['period_start']) && isset($data['period_end'])) {
            $startDate = Carbon::parse($data['period_start']);
            $endDate = Carbon::parse($data['period_end']);

            if ($startDate->gte($endDate)) {
                throw new \InvalidArgumentException('Period start date must be before period end date');
            }
        }

        // Validate performance metrics ranges
        $this->validateMetricRanges($data);

        // Validate business rules
        $this->validateBusinessRules($data, $existingPerformance);
    }

    protected function validateMetricRanges(array $data): void
    {
        $ranges = [
            'on_time_delivery_rate' => [0, 100],
            'return_rate' => [0, 100],
            'defect_rate' => [0, 100],
            'customer_satisfaction_score' => [1, 10],
            'quality_rating' => [1, 10],
            'delivery_rating' => [1, 10],
            'communication_rating' => [1, 10],
            'cost_efficiency_score' => [0, 100],
            'fill_rate' => [0, 100],
            'accuracy_rate' => [0, 100],
            'performance_score' => [0, 100],
        ];

        foreach ($ranges as $metric => $range) {
            if (isset($data[$metric])) {
                $value = $data[$metric];
                if ($value < $range[0] || $value > $range[1]) {
                    throw new \InvalidArgumentException("$metric must be between {$range[0]} and {$range[1]}");
                }
            }
        }
    }

    protected function validateBusinessRules(array $data, ?ProviderPerformance $existingPerformance = null): void
    {
        // Check for duplicate performance records for the same provider and period
        if (isset($data['provider_id']) && isset($data['period_start']) && isset($data['period_end'])) {
            $existing = $this->repository->findByProviderAndPeriod(
                $data['provider_id'],
                $data['period_start'],
                $data['period_end']
            );

            if ($existing && (! $existingPerformance || $existing->id !== $existingPerformance->id)) {
                throw new \InvalidArgumentException('Performance record already exists for this provider and period');
            }
        }
    }

    // Performance alerts and monitoring
    protected function generatePerformanceAlerts(ProviderPerformance $performance): void
    {
        $alerts = $performance->getPerformanceAlerts();

        if (! empty($alerts)) {
            Event::dispatch('provider.performance.alerts_generated', [
                'performance' => $performance,
                'alerts' => $alerts,
            ]);
        }
    }

    // Analytics and reporting methods will be implemented in the next part
}
