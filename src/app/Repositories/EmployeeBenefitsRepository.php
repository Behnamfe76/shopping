<?php

namespace App\Repositories;

use App\DTOs\EmployeeBenefitsDTO;
use App\Models\EmployeeBenefits;
use App\Repositories\Interfaces\EmployeeBenefitsRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\CursorPaginator;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class EmployeeBenefitsRepository implements EmployeeBenefitsRepositoryInterface
{
    protected $model;

    protected $cachePrefix = 'employee_benefits';

    protected $cacheTtl = 3600; // 1 hour

    public function __construct(EmployeeBenefits $model)
    {
        $this->model = $model;
    }

    public function all(): Collection
    {
        return Cache::remember("{$this->cachePrefix}:all", $this->cacheTtl, function () {
            return $this->model->with(['employee'])->get();
        });
    }

    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        $cacheKey = "{$this->cachePrefix}:paginate:{$perPage}:".request()->get('page', 1);

        return Cache::remember($cacheKey, $this->cacheTtl, function () use ($perPage) {
            return $this->model->with(['employee'])
                ->orderBy('created_at', 'desc')
                ->paginate($perPage);
        });
    }

    public function simplePaginate(int $perPage = 15): Paginator
    {
        return $this->model->with(['employee'])
            ->orderBy('created_at', 'desc')
            ->simplePaginate($perPage);
    }

    public function cursorPaginate(int $perPage = 15, ?string $cursor = null): CursorPaginator
    {
        return $this->model->with(['employee'])
            ->orderBy('id', 'desc')
            ->cursorPaginate($perPage, ['*'], 'cursor', $cursor);
    }

    public function find(int $id): ?EmployeeBenefits
    {
        return Cache::remember("{$this->cachePrefix}:find:{$id}", $this->cacheTtl, function () use ($id) {
            return $this->model->with(['employee'])->find($id);
        });
    }

    public function findDTO(int $id): ?EmployeeBenefitsDTO
    {
        $benefit = $this->find($id);

        return $benefit ? EmployeeBenefitsDTO::fromModel($benefit) : null;
    }

    public function findByEmployeeId(int $employeeId): Collection
    {
        $cacheKey = "{$this->cachePrefix}:employee:{$employeeId}";

        return Cache::remember($cacheKey, $this->cacheTtl, function () use ($employeeId) {
            return $this->model->with(['employee'])
                ->where('employee_id', $employeeId)
                ->orderBy('effective_date', 'desc')
                ->get();
        });
    }

    public function findByEmployeeIdDTO(int $employeeId): Collection
    {
        $benefits = $this->findByEmployeeId($employeeId);

        return $benefits->map(fn ($benefit) => EmployeeBenefitsDTO::fromModel($benefit));
    }

    public function findByBenefitType(string $benefitType): Collection
    {
        $cacheKey = "{$this->cachePrefix}:type:{$benefitType}";

        return Cache::remember($cacheKey, $this->cacheTtl, function () use ($benefitType) {
            return $this->model->with(['employee'])
                ->where('benefit_type', $benefitType)
                ->orderBy('effective_date', 'desc')
                ->get();
        });
    }

    public function findByBenefitTypeDTO(string $benefitType): Collection
    {
        $benefits = $this->findByBenefitType($benefitType);

        return $benefits->map(fn ($benefit) => EmployeeBenefitsDTO::fromModel($benefit));
    }

    public function findByStatus(string $status): Collection
    {
        $cacheKey = "{$this->cachePrefix}:status:{$status}";

        return Cache::remember($cacheKey, $this->cacheTtl, function () use ($status) {
            return $this->model->with(['employee'])
                ->where('status', $status)
                ->orderBy('effective_date', 'desc')
                ->get();
        });
    }

    public function findByStatusDTO(string $status): Collection
    {
        $benefits = $this->findByStatus($status);

        return $benefits->map(fn ($benefit) => EmployeeBenefitsDTO::fromModel($benefit));
    }

    public function findByProvider(string $provider): Collection
    {
        $cacheKey = "{$this->cachePrefix}:provider:".md5($provider);

        return Cache::remember($cacheKey, $this->cacheTtl, function () use ($provider) {
            return $this->model->with(['employee'])
                ->where('provider', 'like', "%{$provider}%")
                ->orderBy('effective_date', 'desc')
                ->get();
        });
    }

    public function findByProviderDTO(string $provider): Collection
    {
        $benefits = $this->findByProvider($provider);

        return $benefits->map(fn ($benefit) => EmployeeBenefitsDTO::fromModel($benefit));
    }

    public function findByDateRange(string $startDate, string $endDate): Collection
    {
        $cacheKey = "{$this->cachePrefix}:daterange:".md5($startDate.$endDate);

        return Cache::remember($cacheKey, $this->cacheTtl, function () use ($startDate, $endDate) {
            return $this->model->with(['employee'])
                ->whereBetween('effective_date', [$startDate, $endDate])
                ->orWhereBetween('end_date', [$startDate, $endDate])
                ->orderBy('effective_date', 'desc')
                ->get();
        });
    }

    public function findByDateRangeDTO(string $startDate, string $endDate): Collection
    {
        $benefits = $this->findByDateRange($startDate, $endDate);

        return $benefits->map(fn ($benefit) => EmployeeBenefitsDTO::fromModel($benefit));
    }

    public function findByEmployeeAndType(int $employeeId, string $benefitType): Collection
    {
        $cacheKey = "{$this->cachePrefix}:employee_type:{$employeeId}:{$benefitType}";

        return Cache::remember($cacheKey, $this->cacheTtl, function () use ($employeeId, $benefitType) {
            return $this->model->with(['employee'])
                ->where('employee_id', $employeeId)
                ->where('benefit_type', $benefitType)
                ->orderBy('effective_date', 'desc')
                ->get();
        });
    }

    public function findByEmployeeAndTypeDTO(int $employeeId, string $benefitType): Collection
    {
        $benefits = $this->findByEmployeeAndType($employeeId, $benefitType);

        return $benefits->map(fn ($benefit) => EmployeeBenefitsDTO::fromModel($benefit));
    }

    public function findActive(): Collection
    {
        return Cache::remember("{$this->cachePrefix}:active", $this->cacheTtl, function () {
            return $this->model->with(['employee'])
                ->where('status', 'enrolled')
                ->where('is_active', true)
                ->where('effective_date', '<=', now())
                ->where(function ($query) {
                    $query->whereNull('end_date')
                        ->orWhere('end_date', '>', now());
                })
                ->orderBy('effective_date', 'desc')
                ->get();
        });
    }

    public function findActiveDTO(): Collection
    {
        $benefits = $this->findActive();

        return $benefits->map(fn ($benefit) => EmployeeBenefitsDTO::fromModel($benefit));
    }

    public function findPending(): Collection
    {
        return Cache::remember("{$this->cachePrefix}:pending", $this->cacheTtl, function () {
            return $this->model->with(['employee'])
                ->where('status', 'pending')
                ->orderBy('created_at', 'desc')
                ->get();
        });
    }

    public function findPendingDTO(): Collection
    {
        $benefits = $this->findPending();

        return $benefits->map(fn ($benefit) => EmployeeBenefitsDTO::fromModel($benefit));
    }

    public function findTerminated(): Collection
    {
        return Cache::remember("{$this->cachePrefix}:terminated", $this->cacheTtl, function () {
            return $this->model->with(['employee'])
                ->where('status', 'terminated')
                ->orderBy('end_date', 'desc')
                ->get();
        });
    }

    public function findTerminatedDTO(): Collection
    {
        $benefits = $this->findTerminated();

        return $benefits->map(fn ($benefit) => EmployeeBenefitsDTO::fromModel($benefit));
    }

    public function findByEffectiveDate(string $effectiveDate): Collection
    {
        $cacheKey = "{$this->cachePrefix}:effective_date:".md5($effectiveDate);

        return Cache::remember($cacheKey, $this->cacheTtl, function () use ($effectiveDate) {
            return $this->model->with(['employee'])
                ->where('effective_date', $effectiveDate)
                ->orderBy('created_at', 'desc')
                ->get();
        });
    }

    public function findByEffectiveDateDTO(string $effectiveDate): Collection
    {
        $benefits = $this->findByEffectiveDate($effectiveDate);

        return $benefits->map(fn ($benefit) => EmployeeBenefitsDTO::fromModel($benefit));
    }

    public function findByEnrollmentDate(string $enrollmentDate): Collection
    {
        $cacheKey = "{$this->cachePrefix}:enrollment_date:".md5($enrollmentDate);

        return Cache::remember($cacheKey, $this->cacheTtl, function () use ($enrollmentDate) {
            return $this->model->with(['employee'])
                ->where('enrollment_date', $enrollmentDate)
                ->orderBy('created_at', 'desc')
                ->get();
        });
    }

    public function findByEnrollmentDateDTO(string $enrollmentDate): Collection
    {
        $benefits = $this->findByEnrollmentDate($enrollmentDate);

        return $benefits->map(fn ($benefit) => EmployeeBenefitsDTO::fromModel($benefit));
    }

    public function findExpiringSoon(string $days = 30): Collection
    {
        $expiryDate = now()->addDays($days);

        return Cache::remember("{$this->cachePrefix}:expiring_soon:{$days}", $this->cacheTtl, function () use ($expiryDate) {
            return $this->model->with(['employee'])
                ->where('status', 'enrolled')
                ->where('is_active', true)
                ->whereNotNull('end_date')
                ->where('end_date', '<=', $expiryDate)
                ->where('end_date', '>', now())
                ->orderBy('end_date', 'asc')
                ->get();
        });
    }

    public function findExpiringSoonDTO(string $days = 30): Collection
    {
        $benefits = $this->findExpiringSoon($days);

        return $benefits->map(fn ($benefit) => EmployeeBenefitsDTO::fromModel($benefit));
    }

    public function findByCoverageLevel(string $coverageLevel): Collection
    {
        $cacheKey = "{$this->cachePrefix}:coverage_level:{$coverageLevel}";

        return Cache::remember($cacheKey, $this->cacheTtl, function () use ($coverageLevel) {
            return $this->model->with(['employee'])
                ->where('coverage_level', $coverageLevel)
                ->orderBy('effective_date', 'desc')
                ->get();
        });
    }

    public function findByCoverageLevelDTO(string $coverageLevel): Collection
    {
        $benefits = $this->findByCoverageLevel($coverageLevel);

        return $benefits->map(fn ($benefit) => EmployeeBenefitsDTO::fromModel($benefit));
    }

    public function findByNetworkType(string $networkType): Collection
    {
        $cacheKey = "{$this->cachePrefix}:network_type:{$networkType}";

        return Cache::remember($cacheKey, $this->cacheTtl, function () use ($networkType) {
            return $this->model->with(['employee'])
                ->where('network_type', $networkType)
                ->orderBy('effective_date', 'desc')
                ->get();
        });
    }

    public function findByNetworkTypeDTO(string $networkType): Collection
    {
        $benefits = $this->findByNetworkType($networkType);

        return $benefits->map(fn ($benefit) => EmployeeBenefitsDTO::fromModel($benefit));
    }

    public function create(array $data): EmployeeBenefits
    {
        try {
            DB::beginTransaction();

            $benefit = $this->model->create($data);

            // Clear relevant caches
            $this->clearEmployeeBenefitsCache($benefit->employee_id);
            $this->clearGeneralCache();

            DB::commit();

            Log::info('Employee benefit created', [
                'benefit_id' => $benefit->id,
                'employee_id' => $benefit->employee_id,
                'benefit_type' => $benefit->benefit_type,
            ]);

            return $benefit->load('employee');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to create employee benefit', [
                'data' => $data,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    public function createAndReturnDTO(array $data): EmployeeBenefitsDTO
    {
        $benefit = $this->create($data);

        return EmployeeBenefitsDTO::fromModel($benefit);
    }

    public function update(EmployeeBenefits $benefit, array $data): bool
    {
        try {
            DB::beginTransaction();

            $oldData = $benefit->toArray();
            $updated = $benefit->update($data);

            if ($updated) {
                // Clear relevant caches
                $this->clearEmployeeBenefitsCache($benefit->employee_id);
                $this->clearGeneralCache();

                Log::info('Employee benefit updated', [
                    'benefit_id' => $benefit->id,
                    'employee_id' => $benefit->employee_id,
                    'changes' => array_diff_assoc($data, $oldData),
                ]);
            }

            DB::commit();

            return $updated;

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to update employee benefit', [
                'benefit_id' => $benefit->id,
                'data' => $data,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    public function updateAndReturnDTO(EmployeeBenefits $benefit, array $data): ?EmployeeBenefitsDTO
    {
        $updated = $this->update($benefit, $data);

        return $updated ? EmployeeBenefitsDTO::fromModel($benefit->fresh()) : null;
    }

    public function delete(EmployeeBenefits $benefit): bool
    {
        try {
            DB::beginTransaction();

            $employeeId = $benefit->employee_id;
            $deleted = $benefit->delete();

            if ($deleted) {
                // Clear relevant caches
                $this->clearEmployeeBenefitsCache($employeeId);
                $this->clearGeneralCache();

                Log::info('Employee benefit deleted', [
                    'benefit_id' => $benefit->id,
                    'employee_id' => $employeeId,
                ]);
            }

            DB::commit();

            return $deleted;

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to delete employee benefit', [
                'benefit_id' => $benefit->id,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    public function enroll(EmployeeBenefits $benefit, ?string $effectiveDate = null): bool
    {
        try {
            DB::beginTransaction();

            $data = [
                'status' => 'enrolled',
                'effective_date' => $effectiveDate ?? now(),
                'enrollment_date' => now(),
            ];

            $enrolled = $this->update($benefit, $data);

            if ($enrolled) {
                Log::info('Employee benefit enrolled', [
                    'benefit_id' => $benefit->id,
                    'employee_id' => $benefit->employee_id,
                    'effective_date' => $data['effective_date'],
                ]);
            }

            DB::commit();

            return $enrolled;

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to enroll employee benefit', [
                'benefit_id' => $benefit->id,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    public function terminate(EmployeeBenefits $benefit, ?string $endDate = null, ?string $reason = null): bool
    {
        try {
            DB::beginTransaction();

            $data = [
                'status' => 'terminated',
                'end_date' => $endDate ?? now(),
                'notes' => $benefit->notes."\nTerminated: ".($reason ?? 'No reason provided'),
            ];

            $terminated = $this->update($benefit, $data);

            if ($terminated) {
                Log::info('Employee benefit terminated', [
                    'benefit_id' => $benefit->id,
                    'employee_id' => $benefit->employee_id,
                    'end_date' => $data['end_date'],
                    'reason' => $reason,
                ]);
            }

            DB::commit();

            return $terminated;

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to terminate employee benefit', [
                'benefit_id' => $benefit->id,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    public function cancel(EmployeeBenefits $benefit, ?string $reason = null): bool
    {
        try {
            DB::beginTransaction();

            $data = [
                'status' => 'cancelled',
                'notes' => $benefit->notes."\nCancelled: ".($reason ?? 'No reason provided'),
            ];

            $cancelled = $this->update($benefit, $data);

            if ($cancelled) {
                Log::info('Employee benefit cancelled', [
                    'benefit_id' => $benefit->id,
                    'employee_id' => $benefit->employee_id,
                    'reason' => $reason,
                ]);
            }

            DB::commit();

            return $cancelled;

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to cancel employee benefit', [
                'benefit_id' => $benefit->id,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    public function activate(EmployeeBenefits $benefit): bool
    {
        return $this->update($benefit, ['is_active' => true]);
    }

    public function deactivate(EmployeeBenefits $benefit): bool
    {
        return $this->update($benefit, ['is_active' => false]);
    }

    public function updateCosts(EmployeeBenefits $benefit, array $costData): bool
    {
        $data = array_intersect_key($costData, array_flip([
            'premium_amount', 'employee_contribution', 'employer_contribution',
            'total_cost', 'deductible', 'co_pay', 'co_insurance', 'max_out_of_pocket',
        ]));

        return $this->update($benefit, $data);
    }

    public function getEmployeeBenefitsCount(int $employeeId): int
    {
        $cacheKey = "{$this->cachePrefix}:count:employee:{$employeeId}";

        return Cache::remember($cacheKey, $this->cacheTtl, function () use ($employeeId) {
            return $this->model->where('employee_id', $employeeId)->count();
        });
    }

    public function getEmployeeBenefitsCountByType(int $employeeId, string $benefitType): int
    {
        $cacheKey = "{$this->cachePrefix}:count:employee_type:{$employeeId}:{$benefitType}";

        return Cache::remember($cacheKey, $this->cacheTtl, function () use ($employeeId, $benefitType) {
            return $this->model->where('employee_id', $employeeId)
                ->where('benefit_type', $benefitType)
                ->count();
        });
    }

    public function getEmployeeBenefitsCountByStatus(int $employeeId, string $status): int
    {
        $cacheKey = "{$this->cachePrefix}:count:employee_status:{$employeeId}:{$status}";

        return Cache::remember($cacheKey, $this->cacheTtl, function () use ($employeeId, $status) {
            return $this->model->where('employee_id', $employeeId)
                ->where('status', $status)
                ->count();
        });
    }

    public function getEmployeeTotalMonthlyCost(int $employeeId): float
    {
        $cacheKey = "{$this->cachePrefix}:monthly_cost:{$employeeId}";

        return Cache::remember($cacheKey, $this->cacheTtl, function () use ($employeeId) {
            return $this->model->where('employee_id', $employeeId)
                ->where('status', 'enrolled')
                ->where('is_active', true)
                ->sum('premium_amount');
        });
    }

    public function getEmployeeTotalAnnualCost(int $employeeId): float
    {
        return $this->getEmployeeTotalMonthlyCost($employeeId) * 12;
    }

    public function getEmployeeContribution(int $employeeId): float
    {
        $cacheKey = "{$this->cachePrefix}:employee_contribution:{$employeeId}";

        return Cache::remember($cacheKey, $this->cacheTtl, function () use ($employeeId) {
            return $this->model->where('employee_id', $employeeId)
                ->where('status', 'enrolled')
                ->where('is_active', true)
                ->sum('employee_contribution');
        });
    }

    public function getEmployerContribution(int $employeeId): float
    {
        $cacheKey = "{$this->cachePrefix}:employer_contribution:{$employeeId}";

        return Cache::remember($cacheKey, $this->cacheTtl, function () use ($employeeId) {
            return $this->model->where('employee_id', $employeeId)
                ->where('status', 'enrolled')
                ->where('is_active', true)
                ->sum('employer_contribution');
        });
    }

    public function getTotalBenefitsCount(): int
    {
        return Cache::remember("{$this->cachePrefix}:total_count", $this->cacheTtl, function () {
            return $this->model->count();
        });
    }

    public function getTotalBenefitsCountByType(string $benefitType): int
    {
        $cacheKey = "{$this->cachePrefix}:total_count_type:{$benefitType}";

        return Cache::remember($cacheKey, $this->cacheTtl, function () use ($benefitType) {
            return $this->model->where('benefit_type', $benefitType)->count();
        });
    }

    public function getTotalBenefitsCountByStatus(string $status): int
    {
        $cacheKey = "{$this->cachePrefix}:total_count_status:{$status}";

        return Cache::remember($cacheKey, $this->cacheTtl, function () use ($status) {
            return $this->model->where('status', $status)->count();
        });
    }

    public function getTotalMonthlyCost(): float
    {
        return Cache::remember("{$this->cachePrefix}:total_monthly_cost", $this->cacheTtl, function () {
            return $this->model->where('status', 'enrolled')
                ->where('is_active', true)
                ->sum('premium_amount');
        });
    }

    public function getTotalAnnualCost(): float
    {
        return $this->getTotalMonthlyCost() * 12;
    }

    public function getTotalEmployeeContribution(): float
    {
        return Cache::remember("{$this->cachePrefix}:total_employee_contribution", $this->cacheTtl, function () {
            return $this->model->where('status', 'enrolled')
                ->where('is_active', true)
                ->sum('employee_contribution');
        });
    }

    public function getTotalEmployerContribution(): float
    {
        return Cache::remember("{$this->cachePrefix}:total_employer_contribution", $this->cacheTtl, function () {
            return $this->model->where('status', 'enrolled')
                ->where('is_active', true)
                ->sum('employer_contribution');
        });
    }

    public function getActiveEnrollmentsCount(): int
    {
        return Cache::remember("{$this->cachePrefix}:active_enrollments_count", $this->cacheTtl, function () {
            return $this->model->where('status', 'enrolled')
                ->where('is_active', true)
                ->where('effective_date', '<=', now())
                ->where(function ($query) {
                    $query->whereNull('end_date')
                        ->orWhere('end_date', '>', now());
                })
                ->count();
        });
    }

    public function getPendingEnrollmentsCount(): int
    {
        return Cache::remember("{$this->cachePrefix}:pending_enrollments_count", $this->cacheTtl, function () {
            return $this->model->where('status', 'pending')->count();
        });
    }

    public function getExpiringEnrollmentsCount(string $days = 30): int
    {
        $expiryDate = now()->addDays($days);

        return Cache::remember("{$this->cachePrefix}:expiring_enrollments_count:{$days}", $this->cacheTtl, function () use ($expiryDate) {
            return $this->model->where('status', 'enrolled')
                ->where('is_active', true)
                ->whereNotNull('end_date')
                ->where('end_date', '<=', $expiryDate)
                ->where('end_date', '>', now())
                ->count();
        });
    }

    public function searchBenefits(string $query): Collection
    {
        return $this->model->with(['employee'])
            ->where(function ($q) use ($query) {
                $q->where('benefit_name', 'like', "%{$query}%")
                    ->orWhere('provider', 'like', "%{$query}%")
                    ->orWhere('plan_id', 'like', "%{$query}%")
                    ->orWhereHas('employee', function ($empQuery) use ($query) {
                        $empQuery->where('first_name', 'like', "%{$query}%")
                            ->orWhere('last_name', 'like', "%{$query}%")
                            ->orWhere('employee_id', 'like', "%{$query}%");
                    });
            })
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function searchBenefitsDTO(string $query): Collection
    {
        $benefits = $this->searchBenefits($query);

        return $benefits->map(fn ($benefit) => EmployeeBenefitsDTO::fromModel($benefit));
    }

    public function searchBenefitsByEmployee(int $employeeId, string $query): Collection
    {
        return $this->model->with(['employee'])
            ->where('employee_id', $employeeId)
            ->where(function ($q) use ($query) {
                $q->where('benefit_name', 'like', "%{$query}%")
                    ->orWhere('provider', 'like', "%{$query}%")
                    ->orWhere('plan_id', 'like', "%{$query}%");
            })
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function searchBenefitsByEmployeeDTO(int $employeeId, string $query): Collection
    {
        $benefits = $this->searchBenefitsByEmployee($employeeId, $query);

        return $benefits->map(fn ($benefit) => EmployeeBenefitsDTO::fromModel($benefit));
    }

    public function exportBenefitsData(array $filters = []): string
    {
        $query = $this->model->with(['employee']);

        // Apply filters
        if (isset($filters['employee_id'])) {
            $query->where('employee_id', $filters['employee_id']);
        }

        if (isset($filters['benefit_type'])) {
            $query->where('benefit_type', $filters['benefit_type']);
        }

        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (isset($filters['date_from'])) {
            $query->where('effective_date', '>=', $filters['date_from']);
        }

        if (isset($filters['date_to'])) {
            $query->where('effective_date', '<=', $filters['date_to']);
        }

        $benefits = $query->get();

        // Convert to CSV format
        $csv = "ID,Employee ID,Employee Name,Benefit Type,Benefit Name,Provider,Status,Effective Date,End Date,Total Cost\n";

        foreach ($benefits as $benefit) {
            $csv .= sprintf(
                "%d,%d,%s,%s,%s,%s,%s,%s,%s,%.2f\n",
                $benefit->id,
                $benefit->employee_id,
                $benefit->employee->full_name ?? 'N/A',
                $benefit->benefit_type,
                $benefit->benefit_name,
                $benefit->provider,
                $benefit->status,
                $benefit->effective_date,
                $benefit->end_date ?? 'N/A',
                $benefit->total_cost
            );
        }

        return $csv;
    }

    public function importBenefitsData(string $data): bool
    {
        try {
            DB::beginTransaction();

            $lines = explode("\n", trim($data));
            $headers = str_getcsv(array_shift($lines));

            $imported = 0;
            foreach ($lines as $line) {
                if (empty(trim($line))) {
                    continue;
                }

                $row = array_combine($headers, str_getcsv($line));

                // Validate required fields
                if (empty($row['employee_id']) || empty($row['benefit_type']) || empty($row['benefit_name'])) {
                    continue;
                }

                // Create or update benefit
                $this->model->updateOrCreate(
                    [
                        'employee_id' => $row['employee_id'],
                        'benefit_type' => $row['benefit_type'],
                        'benefit_name' => $row['benefit_name'],
                    ],
                    $row
                );

                $imported++;
            }

            // Clear caches
            $this->clearGeneralCache();

            DB::commit();

            Log::info('Benefits data imported', ['imported_count' => $imported]);

            return true;

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to import benefits data', ['error' => $e->getMessage()]);
            throw $e;
        }
    }

    public function getBenefitsStatistics(?int $employeeId = null): array
    {
        $cacheKey = "{$this->cachePrefix}:statistics".($employeeId ? ":employee:{$employeeId}" : '');

        return Cache::remember($cacheKey, $this->cacheTtl, function () use ($employeeId) {
            $query = $this->model;

            if ($employeeId) {
                $query->where('employee_id', $employeeId);
            }

            $total = $query->count();
            $active = $query->clone()->where('status', 'enrolled')->where('is_active', true)->count();
            $pending = $query->clone()->where('status', 'pending')->count();
            $terminated = $query->clone()->where('status', 'terminated')->count();

            $monthlyCost = $query->clone()
                ->where('status', 'enrolled')
                ->where('is_active', true)
                ->sum('premium_amount');

            $annualCost = $monthlyCost * 12;

            return [
                'total_benefits' => $total,
                'active_benefits' => $active,
                'pending_benefits' => $pending,
                'terminated_benefits' => $terminated,
                'monthly_cost' => $monthlyCost,
                'annual_cost' => $annualCost,
                'active_percentage' => $total > 0 ? round(($active / $total) * 100, 2) : 0,
            ];
        });
    }

    public function getDepartmentBenefitsStatistics(int $departmentId): array
    {
        $cacheKey = "{$this->cachePrefix}:department_statistics:{$departmentId}";

        return Cache::remember($cacheKey, $this->cacheTtl, function () use ($departmentId) {
            $benefits = $this->model->whereHas('employee', function ($query) use ($departmentId) {
                $query->where('department_id', $departmentId);
            })->get();

            $total = $benefits->count();
            $active = $benefits->where('status', 'enrolled')->where('is_active', true)->count();
            $monthlyCost = $benefits->where('status', 'enrolled')->where('is_active', true)->sum('premium_amount');

            return [
                'total_benefits' => $total,
                'active_benefits' => $active,
                'monthly_cost' => $monthlyCost,
                'annual_cost' => $monthlyCost * 12,
                'active_percentage' => $total > 0 ? round(($active / $total) * 100, 2) : 0,
            ];
        });
    }

    public function getCompanyBenefitsStatistics(): array
    {
        return Cache::remember("{$this->cachePrefix}:company_statistics", $this->cacheTtl, function () {
            $total = $this->model->count();
            $active = $this->model->where('status', 'enrolled')->where('is_active', true)->count();
            $monthlyCost = $this->model->where('status', 'enrolled')->where('is_active', true)->sum('premium_amount');

            // Benefits by type
            $byType = $this->model->selectRaw('benefit_type, COUNT(*) as count')
                ->groupBy('benefit_type')
                ->pluck('count', 'benefit_type')
                ->toArray();

            // Benefits by status
            $byStatus = $this->model->selectRaw('status, COUNT(*) as count')
                ->groupBy('status')
                ->pluck('count', 'status')
                ->toArray();

            return [
                'total_benefits' => $total,
                'active_benefits' => $active,
                'monthly_cost' => $monthlyCost,
                'annual_cost' => $monthlyCost * 12,
                'active_percentage' => $total > 0 ? round(($active / $total) * 100, 2) : 0,
                'by_type' => $byType,
                'by_status' => $byStatus,
            ];
        });
    }

    public function getCostAnalysis(?int $employeeId = null): array
    {
        $cacheKey = "{$this->cachePrefix}:cost_analysis".($employeeId ? ":employee:{$employeeId}" : '');

        return Cache::remember($cacheKey, $this->cacheTtl, function () use ($employeeId) {
            $query = $this->model->where('status', 'enrolled')->where('is_active', true);

            if ($employeeId) {
                $query->where('employee_id', $employeeId);
            }

            $totalPremium = $query->sum('premium_amount');
            $totalEmployeeContribution = $query->sum('employee_contribution');
            $totalEmployerContribution = $query->sum('employer_contribution');
            $totalCost = $query->sum('total_cost');

            // Average costs
            $count = $query->count();
            $avgPremium = $count > 0 ? $totalPremium / $count : 0;
            $avgEmployeeContribution = $count > 0 ? $totalEmployeeContribution / $count : 0;
            $avgEmployerContribution = $count > 0 ? $totalEmployerContribution / $count : 0;

            return [
                'total_premium' => $totalPremium,
                'total_employee_contribution' => $totalEmployeeContribution,
                'total_employer_contribution' => $totalEmployerContribution,
                'total_cost' => $totalCost,
                'average_premium' => round($avgPremium, 2),
                'average_employee_contribution' => round($avgEmployeeContribution, 2),
                'average_employer_contribution' => round($avgEmployerContribution, 2),
                'benefits_count' => $count,
            ];
        });
    }

    public function getEnrollmentTrends(?string $startDate = null, ?string $endDate = null): array
    {
        $startDate = $startDate ?? now()->subYear()->format('Y-m-d');
        $endDate = $endDate ?? now()->format('Y-m-d');

        $cacheKey = "{$this->cachePrefix}:enrollment_trends:".md5($startDate.$endDate);

        return Cache::remember($cacheKey, $this->cacheTtl, function () use ($startDate, $endDate) {
            $trends = $this->model->selectRaw('DATE(enrollment_date) as date, COUNT(*) as count')
                ->whereBetween('enrollment_date', [$startDate, $endDate])
                ->groupBy('date')
                ->orderBy('date')
                ->get()
                ->pluck('count', 'date')
                ->toArray();

            // Fill missing dates with 0
            $period = \Carbon\CarbonPeriod::create($startDate, $endDate);
            $filledTrends = [];

            foreach ($period as $date) {
                $dateStr = $date->format('Y-m-d');
                $filledTrends[$dateStr] = $trends[$dateStr] ?? 0;
            }

            return $filledTrends;
        });
    }

    protected function clearEmployeeBenefitsCache(int $employeeId): void
    {
        Cache::forget("{$this->cachePrefix}:employee:{$employeeId}");
        Cache::forget("{$this->cachePrefix}:count:employee:{$employeeId}");
        Cache::forget("{$this->cachePrefix}:monthly_cost:{$employeeId}");
        Cache::forget("{$this->cachePrefix}:employee_contribution:{$employeeId}");
        Cache::forget("{$this->cachePrefix}:employer_contribution:{$employeeId}");
    }

    protected function clearGeneralCache(): void
    {
        Cache::forget("{$this->cachePrefix}:all");
        Cache::forget("{$this->cachePrefix}:active");
        Cache::forget("{$this->cachePrefix}:pending");
        Cache::forget("{$this->cachePrefix}:terminated");
        Cache::forget("{$this->cachePrefix}:total_count");
        Cache::forget("{$this->cachePrefix}:total_monthly_cost");
        Cache::forget("{$this->cachePrefix}:total_employee_contribution");
        Cache::forget("{$this->cachePrefix}:total_employer_contribution");
        Cache::forget("{$this->cachePrefix}:active_enrollments_count");
        Cache::forget("{$this->cachePrefix}:pending_enrollments_count");
        Cache::forget("{$this->cachePrefix}:company_statistics");

        // Clear pagination cache
        Cache::forget("{$this->cachePrefix}:paginate:15:1");
        Cache::forget("{$this->cachePrefix}:paginate:15:2");
        Cache::forget("{$this->cachePrefix}:paginate:15:3");
    }
}
