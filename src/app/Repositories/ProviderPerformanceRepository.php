<?php

namespace App\Repositories;

use App\Repositories\Interfaces\ProviderPerformanceRepositoryInterface;
use App\Models\ProviderPerformance;
use App\DTOs\ProviderPerformanceDTO;
use App\Enums\PerformanceGrade;
use App\Enums\PeriodType;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Pagination\CursorPaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class ProviderPerformanceRepository implements ProviderPerformanceRepositoryInterface
{
    protected $model;
    protected $cachePrefix = 'provider_performance_';
    protected $cacheTtl = 3600; // 1 hour

    public function __construct(ProviderPerformance $model)
    {
        $this->model = $model;
    }

    // Basic CRUD operations
    public function all(): Collection
    {
        return Cache::remember($this->cachePrefix . 'all', $this->cacheTtl, function () {
            return $this->model->with(['provider', 'verifier'])->get();
        });
    }

    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        return $this->model->with(['provider', 'verifier'])
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    public function simplePaginate(int $perPage = 15): Paginator
    {
        return $this->model->with(['provider', 'verifier'])
            ->orderBy('created_at', 'desc')
            ->simplePaginate($perPage);
    }

    public function cursorPaginate(int $perPage = 15, string $cursor = null): CursorPaginator
    {
        return $this->model->with(['provider', 'verifier'])
            ->orderBy('id')
            ->cursorPaginate($perPage, ['*'], 'cursor', $cursor);
    }

    public function find(int $id): ?ProviderPerformance
    {
        return Cache::remember($this->cachePrefix . 'find_' . $id, $this->cacheTtl, function () use ($id) {
            return $this->model->with(['provider', 'verifier'])->find($id);
        });
    }

    public function findDTO(int $id): ?ProviderPerformanceDTO
    {
        $model = $this->find($id);
        return $model ? ProviderPerformanceDTO::fromModel($model) : null;
    }

    public function create(array $data): ProviderPerformance
    {
        try {
            DB::beginTransaction();

            $providerPerformance = $this->model->create($data);

            // Calculate performance score and grade
            $this->calculatePerformance($providerPerformance);

            DB::commit();

            // Clear relevant caches
            $this->clearCache();

            return $providerPerformance->load(['provider', 'verifier']);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to create provider performance: ' . $e->getMessage());
            throw $e;
        }
    }

    public function createAndReturnDTO(array $data): ProviderPerformanceDTO
    {
        $model = $this->create($data);
        return ProviderPerformanceDTO::fromModel($model);
    }

    public function update(ProviderPerformance $providerPerformance, array $data): bool
    {
        try {
            DB::beginTransaction();

            $result = $providerPerformance->update($data);

            if ($result) {
                // Recalculate performance if metrics changed
                $this->calculatePerformance($providerPerformance);
            }

            DB::commit();

            // Clear relevant caches
            $this->clearCache();

            return $result;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to update provider performance: ' . $e->getMessage());
            throw $e;
        }
    }

    public function updateAndReturnDTO(ProviderPerformance $providerPerformance, array $data): ?ProviderPerformanceDTO
    {
        $result = $this->update($providerPerformance, $data);
        return $result ? ProviderPerformanceDTO::fromModel($providerPerformance->fresh()) : null;
    }

    public function delete(ProviderPerformance $providerPerformance): bool
    {
        try {
            $result = $providerPerformance->delete();

            if ($result) {
                $this->clearCache();
            }

            return $result;
        } catch (\Exception $e) {
            Log::error('Failed to delete provider performance: ' . $e->getMessage());
            throw $e;
        }
    }

    // Find by specific criteria
    public function findByProviderId(int $providerId): Collection
    {
        $cacheKey = $this->cachePrefix . 'provider_' . $providerId;

        return Cache::remember($cacheKey, $this->cacheTtl, function () use ($providerId) {
            return $this->model->with(['provider', 'verifier'])
                ->where('provider_id', $providerId)
                ->orderBy('period_start', 'desc')
                ->get();
        });
    }

    public function findByProviderIdDTO(int $providerId): Collection
    {
        $models = $this->findByProviderId($providerId);
        return $models->map(fn($model) => ProviderPerformanceDTO::fromModel($model));
    }

    public function findByPeriod(string $periodStart, string $periodEnd): Collection
    {
        $cacheKey = $this->cachePrefix . 'period_' . $periodStart . '_' . $periodEnd;

        return Cache::remember($cacheKey, $this->cacheTtl, function () use ($periodStart, $periodEnd) {
            return $this->model->with(['provider', 'verifier'])
                ->whereBetween('period_start', [$periodStart, $periodEnd])
                ->orderBy('period_start', 'desc')
                ->get();
        });
    }

    public function findByPeriodDTO(string $periodStart, string $periodEnd): Collection
    {
        $models = $this->findByPeriod($periodStart, $periodEnd);
        return $models->map(fn($model) => ProviderPerformanceDTO::fromModel($model));
    }

    public function findByPerformanceGrade(string $grade): Collection
    {
        $cacheKey = $this->cachePrefix . 'grade_' . $grade;

        return Cache::remember($cacheKey, $this->cacheTtl, function () use ($grade) {
            return $this->model->with(['provider', 'verifier'])
                ->where('performance_grade', $grade)
                ->orderBy('performance_score', 'desc')
                ->get();
        });
    }

    public function findByPerformanceGradeDTO(string $grade): Collection
    {
        $models = $this->findByPerformanceGrade($grade);
        return $models->map(fn($model) => ProviderPerformanceDTO::fromModel($model));
    }

    public function findByPeriodType(string $periodType): Collection
    {
        $cacheKey = $this->cachePrefix . 'period_type_' . $periodType;

        return Cache::remember($cacheKey, $this->cacheTtl, function () use ($periodType) {
            return $this->model->with(['provider', 'verifier'])
                ->where('period_type', $periodType)
                ->orderBy('period_start', 'desc')
                ->get();
        });
    }

    public function findByPeriodTypeDTO(string $periodType): Collection
    {
        $models = $this->findByPeriodType($periodType);
        return $models->map(fn($model) => ProviderPerformanceDTO::fromModel($model));
    }

    public function findByProviderAndPeriod(int $providerId, string $periodStart, string $periodEnd): ?ProviderPerformance
    {
        $cacheKey = $this->cachePrefix . 'provider_period_' . $providerId . '_' . $periodStart . '_' . $periodEnd;

        return Cache::remember($cacheKey, $this->cacheTtl, function () use ($providerId, $periodStart, $periodEnd) {
            return $this->model->with(['provider', 'verifier'])
                ->where('provider_id', $providerId)
                ->whereBetween('period_start', [$periodStart, $periodEnd])
                ->first();
        });
    }

    public function findByProviderAndPeriodDTO(int $providerId, string $periodStart, string $periodEnd): ?ProviderPerformanceDTO
    {
        $model = $this->findByProviderAndPeriod($providerId, $periodStart, $periodEnd);
        return $model ? ProviderPerformanceDTO::fromModel($model) : null;
    }

    public function findByProviderAndGrade(int $providerId, string $grade): Collection
    {
        $cacheKey = $this->cachePrefix . 'provider_grade_' . $providerId . '_' . $grade;

        return Cache::remember($cacheKey, $this->cacheTtl, function () use ($providerId, $grade) {
            return $this->model->with(['provider', 'verifier'])
                ->where('provider_id', $providerId)
                ->where('performance_grade', $grade)
                ->orderBy('period_start', 'desc')
                ->get();
        });
    }

    public function findByProviderAndGradeDTO(int $providerId, string $grade): Collection
    {
        $models = $this->findByProviderAndGrade($providerId, $grade);
        return $models->map(fn($model) => ProviderPerformanceDTO::fromModel($model));
    }

    // Verification operations
    public function findVerified(): Collection
    {
        $cacheKey = $this->cachePrefix . 'verified';

        return Cache::remember($cacheKey, $this->cacheTtl, function () {
            return $this->model->with(['provider', 'verifier'])
                ->where('is_verified', true)
                ->orderBy('verified_at', 'desc')
                ->get();
        });
    }

    public function findVerifiedDTO(): Collection
    {
        $models = $this->findVerified();
        return $models->map(fn($model) => ProviderPerformanceDTO::fromModel($model));
    }

    public function findUnverified(): Collection
    {
        $cacheKey = $this->cachePrefix . 'unverified';

        return Cache::remember($cacheKey, $this->cacheTtl, function () {
            return $this->model->with(['provider', 'verifier'])
                ->where('is_verified', false)
                ->orderBy('created_at', 'desc')
                ->get();
        });
    }

    public function findUnverifiedDTO(): Collection
    {
        $models = $this->findUnverified();
        return $models->map(fn($model) => ProviderPerformanceDTO::fromModel($model));
    }

    public function verify(ProviderPerformance $providerPerformance, int $verifiedBy, string $notes = null): bool
    {
        try {
            $result = $providerPerformance->verify($verifiedBy, $notes);

            if ($result) {
                $this->clearCache();
            }

            return $result;
        } catch (\Exception $e) {
            Log::error('Failed to verify provider performance: ' . $e->getMessage());
            throw $e;
        }
    }

    public function unverify(ProviderPerformance $providerPerformance): bool
    {
        try {
            $result = $providerPerformance->unverify();

            if ($result) {
                $this->clearCache();
            }

            return $result;
        } catch (\Exception $e) {
            Log::error('Failed to unverify provider performance: ' . $e->getMessage());
            throw $e;
        }
    }

    // Performance analysis
    public function findTopPerformers(int $limit = 10): Collection
    {
        $cacheKey = $this->cachePrefix . 'top_performers_' . $limit;

        return Cache::remember($cacheKey, $this->cacheTtl, function () use ($limit) {
            return $this->model->with(['provider', 'verifier'])
                ->orderBy('performance_score', 'desc')
                ->limit($limit)
                ->get();
        });
    }

    public function findTopPerformersDTO(int $limit = 10): Collection
    {
        $models = $this->findTopPerformers($limit);
        return $models->map(fn($model) => ProviderPerformanceDTO::fromModel($model));
    }

    public function findBottomPerformers(int $limit = 10): Collection
    {
        $cacheKey = $this->cachePrefix . 'bottom_performers_' . $limit;

        return Cache::remember($cacheKey, $this->cacheTtl, function () use ($limit) {
            return $this->model->with(['provider', 'verifier'])
                ->orderBy('performance_score', 'asc')
                ->limit($limit)
                ->get();
        });
    }

    public function findBottomPerformersDTO(int $limit = 10): Collection
    {
        $models = $this->findBottomPerformers($limit);
        return $models->map(fn($model) => ProviderPerformanceDTO::fromModel($model));
    }

    // Performance calculations and updates
    public function calculatePerformance(ProviderPerformance $providerPerformance): bool
    {
        try {
            $providerPerformance->calculatePerformanceScore();
            $providerPerformance->updatePerformanceGrade();

            return $providerPerformance->save();
        } catch (\Exception $e) {
            Log::error('Failed to calculate performance: ' . $e->getMessage());
            throw $e;
        }
    }

    public function updateMetrics(ProviderPerformance $providerPerformance, array $metrics): bool
    {
        try {
            $providerPerformance->fill($metrics);
            $this->calculatePerformance($providerPerformance);

            return $providerPerformance->save();
        } catch (\Exception $e) {
            Log::error('Failed to update metrics: ' . $e->getMessage());
            throw $e;
        }
    }

    public function recalculateScore(ProviderPerformance $providerPerformance): bool
    {
        return $this->calculatePerformance($providerPerformance);
    }

    public function updateGrade(ProviderPerformance $providerPerformance): bool
    {
        try {
            $providerPerformance->updatePerformanceGrade();
            return $providerPerformance->save();
        } catch (\Exception $e) {
            Log::error('Failed to update grade: ' . $e->getMessage());
            throw $e;
        }
    }

    // Cache management
    protected function clearCache(): void
    {
        Cache::flush();
    }

    // Additional methods will be implemented in the next part due to length constraints
}
