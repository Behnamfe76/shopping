<?php

namespace App\Repositories;

use App\Repositories\Interfaces\EmployeeSalaryHistoryRepositoryInterface;
use App\Models\EmployeeSalaryHistory;
use App\DTOs\EmployeeSalaryHistoryDTO;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Pagination\CursorPaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class EmployeeSalaryHistoryRepository implements EmployeeSalaryHistoryRepositoryInterface
{
    protected $model;
    protected $cachePrefix = 'salary_history_';
    protected $cacheTtl = 3600; // 1 hour

    public function __construct(EmployeeSalaryHistory $model)
    {
        $this->model = $model;
    }

    public function all(): Collection
    {
        return Cache::remember($this->cachePrefix . 'all', $this->cacheTtl, function () {
            return $this->model->with(['employee', 'approver'])->get();
        });
    }

    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        return $this->model->with(['employee', 'approver'])
            ->orderBy('effective_date', 'desc')
            ->paginate($perPage);
    }

    public function simplePaginate(int $perPage = 15): Paginator
    {
        return $this->model->with(['employee', 'approver'])
            ->orderBy('effective_date', 'desc')
            ->simplePaginate($perPage);
    }

    public function cursorPaginate(int $perPage = 15, string $cursor = null): CursorPaginator
    {
        return $this->model->with(['employee', 'approver'])
            ->orderBy('effective_date', 'desc')
            ->cursorPaginate($perPage, ['*'], 'cursor', $cursor);
    }

    public function find(int $id): ?EmployeeSalaryHistory
    {
        return Cache::remember($this->cachePrefix . 'find_' . $id, $this->cacheTtl, function () use ($id) {
            return $this->model->with(['employee', 'approver'])->find($id);
        });
    }

    public function findDTO(int $id): ?EmployeeSalaryHistoryDTO
    {
        $model = $this->find($id);
        return $model ? EmployeeSalaryHistoryDTO::fromModel($model) : null;
    }

    public function findByEmployeeId(int $employeeId): Collection
    {
        return Cache::remember($this->cachePrefix . 'employee_' . $employeeId, $this->cacheTtl, function () use ($employeeId) {
            return $this->model->with(['employee', 'approver'])
                ->where('employee_id', $employeeId)
                ->orderBy('effective_date', 'desc')
                ->get();
        });
    }

    public function findByEmployeeIdDTO(int $employeeId): Collection
    {
        $records = $this->findByEmployeeId($employeeId);
        return $records->map(fn($record) => EmployeeSalaryHistoryDTO::fromModel($record));
    }

    public function findByChangeType(string $changeType): Collection
    {
        return Cache::remember($this->cachePrefix . 'type_' . $changeType, $this->cacheTtl, function () use ($changeType) {
            return $this->model->with(['employee', 'approver'])
                ->where('change_type', $changeType)
                ->orderBy('effective_date', 'desc')
                ->get();
        });
    }

    public function findByChangeTypeDTO(string $changeType): Collection
    {
        $records = $this->findByChangeType($changeType);
        return $records->map(fn($record) => EmployeeSalaryHistoryDTO::fromModel($record));
    }

    public function findByDateRange(string $startDate, string $endDate): Collection
    {
        return $this->model->with(['employee', 'approver'])
            ->whereBetween('effective_date', [$startDate, $endDate])
            ->orderBy('effective_date', 'desc')
            ->get();
    }

    public function findByDateRangeDTO(string $startDate, string $endDate): Collection
    {
        $records = $this->findByDateRange($startDate, $endDate);
        return $records->map(fn($record) => EmployeeSalaryHistoryDTO::fromModel($record));
    }

    public function findByEmployeeAndDateRange(int $employeeId, string $startDate, string $endDate): Collection
    {
        return $this->model->with(['employee', 'approver'])
            ->where('employee_id', $employeeId)
            ->whereBetween('effective_date', [$startDate, $endDate])
            ->orderBy('effective_date', 'desc')
            ->get();
    }

    public function findByEmployeeAndDateRangeDTO(int $employeeId, string $startDate, string $endDate): Collection
    {
        $records = $this->findByEmployeeAndDateRange($employeeId, $startDate, $endDate);
        return $records->map(fn($record) => EmployeeSalaryHistoryDTO::fromModel($record));
    }

    public function findByApproverId(int $approverId): Collection
    {
        return $this->model->with(['employee', 'approver'])
            ->where('approved_by', $approverId)
            ->orderBy('approved_at', 'desc')
            ->get();
    }

    public function findByApproverIdDTO(int $approverId): Collection
    {
        $records = $this->findByApproverId($approverId);
        return $records->map(fn($record) => EmployeeSalaryHistoryDTO::fromModel($record));
    }

    public function findByEffectiveDate(string $effectiveDate): Collection
    {
        return $this->model->with(['employee', 'approver'])
            ->whereDate('effective_date', $effectiveDate)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function findByEffectiveDateDTO(string $effectiveDate): Collection
    {
        $records = $this->findByEffectiveDate($effectiveDate);
        return $records->map(fn($record) => EmployeeSalaryHistoryDTO::fromModel($record));
    }

    public function findRetroactive(): Collection
    {
        return $this->model->with(['employee', 'approver'])
            ->where('is_retroactive', true)
            ->orderBy('effective_date', 'desc')
            ->get();
    }

    public function findRetroactiveDTO(): Collection
    {
        $records = $this->findRetroactive();
        return $records->map(fn($record) => EmployeeSalaryHistoryDTO::fromModel($record));
    }

    public function findByChangeAmountRange(float $minAmount, float $maxAmount): Collection
    {
        return $this->model->with(['employee', 'approver'])
            ->whereBetween('change_amount', [$minAmount, $maxAmount])
            ->orderBy('change_amount', 'desc')
            ->get();
    }

    public function findByChangeAmountRangeDTO(float $minAmount, float $maxAmount): Collection
    {
        $records = $this->findByChangeAmountRange($minAmount, $maxAmount);
        return $records->map(fn($record) => EmployeeSalaryHistoryDTO::fromModel($record));
    }

    public function findByChangePercentageRange(float $minPercentage, float $maxPercentage): Collection
    {
        return $this->model->with(['employee', 'approver'])
            ->whereBetween('change_percentage', [$minPercentage, $maxPercentage])
            ->orderBy('change_percentage', 'desc')
            ->get();
    }

    public function findByChangePercentageRangeDTO(float $minPercentage, float $maxPercentage): Collection
    {
        $records = $this->findByChangePercentageRange($minPercentage, $maxPercentage);
        return $records->map(fn($record) => EmployeeSalaryHistoryDTO::fromModel($record));
    }

    public function findLatestByEmployee(int $employeeId): ?EmployeeSalaryHistory
    {
        return Cache::remember($this->cachePrefix . 'latest_employee_' . $employeeId, $this->cacheTtl, function () use ($employeeId) {
            return $this->model->with(['employee', 'approver'])
                ->where('employee_id', $employeeId)
                ->orderBy('effective_date', 'desc')
                ->first();
        });
    }

    public function findLatestByEmployeeDTO(int $employeeId): ?EmployeeSalaryHistoryDTO
    {
        $model = $this->findLatestByEmployee($employeeId);
        return $model ? EmployeeSalaryHistoryDTO::fromModel($model) : null;
    }

    public function findByEmployeeAndType(int $employeeId, string $changeType): Collection
    {
        return $this->model->with(['employee', 'approver'])
            ->where('employee_id', $employeeId)
            ->where('change_type', $changeType)
            ->orderBy('effective_date', 'desc')
            ->get();
    }

    public function findByEmployeeAndTypeDTO(int $employeeId, string $changeType): Collection
    {
        $records = $this->findByEmployeeAndType($employeeId, $changeType);
        return $records->map(fn($record) => EmployeeSalaryHistoryDTO::fromModel($record));
    }

    public function create(array $data): EmployeeSalaryHistory
    {
        try {
            DB::beginTransaction();

            // Auto-calculate change amount if not provided
            if (!isset($data['change_amount'])) {
                $data['change_amount'] = $data['new_salary'] - $data['old_salary'];
            }

            // Auto-calculate change percentage if not provided
            if (!isset($data['change_percentage']) && $data['old_salary'] > 0) {
                $data['change_percentage'] = (($data['new_salary'] - $data['old_salary']) / $data['old_salary']) * 100;
            }

            $salaryHistory = $this->model->create($data);

            // Clear relevant caches
            $this->clearEmployeeCache($data['employee_id']);
            $this->clearTypeCache($data['change_type'] ?? '');

            DB::commit();

            Log::info('Salary history created', ['id' => $salaryHistory->id, 'employee_id' => $data['employee_id']]);

            return $salaryHistory->load(['employee', 'approver']);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to create salary history', ['error' => $e->getMessage(), 'data' => $data]);
            throw $e;
        }
    }

    public function createAndReturnDTO(array $data): EmployeeSalaryHistoryDTO
    {
        $model = $this->create($data);
        return EmployeeSalaryHistoryDTO::fromModel($model);
    }

    public function update(EmployeeSalaryHistory $salaryHistory, array $data): bool
    {
        try {
            DB::beginTransaction();

            // Auto-calculate change amount if not provided
            if (!isset($data['change_amount'])) {
                $data['change_amount'] = $data['new_salary'] - $data['old_salary'];
            }

            // Auto-calculate change percentage if not provided
            if (!isset($data['change_percentage']) && $data['old_salary'] > 0) {
                $data['change_percentage'] = (($data['new_salary'] - $data['old_salary']) / $data['old_salary']) * 100;
            }

            $updated = $salaryHistory->update($data);

            if ($updated) {
                // Clear relevant caches
                $this->clearEmployeeCache($salaryHistory->employee_id);
                $this->clearTypeCache($salaryHistory->change_type);

                Log::info('Salary history updated', ['id' => $salaryHistory->id]);
            }

            DB::commit();
            return $updated;

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to update salary history', ['error' => $e->getMessage(), 'id' => $salaryHistory->id]);
            throw $e;
        }
    }

    public function updateAndReturnDTO(EmployeeSalaryHistory $salaryHistory, array $data): ?EmployeeSalaryHistoryDTO
    {
        $updated = $this->update($salaryHistory, $data);
        return $updated ? EmployeeSalaryHistoryDTO::fromModel($salaryHistory->fresh()) : null;
    }

    public function delete(EmployeeSalaryHistory $salaryHistory): bool
    {
        try {
            DB::beginTransaction();

            $deleted = $salaryHistory->delete();

            if ($deleted) {
                // Clear relevant caches
                $this->clearEmployeeCache($salaryHistory->employee_id);
                $this->clearTypeCache($salaryHistory->change_type);

                Log::info('Salary history deleted', ['id' => $salaryHistory->id]);
            }

            DB::commit();
            return $deleted;

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to delete salary history', ['error' => $e->getMessage(), 'id' => $salaryHistory->id]);
            throw $e;
        }
    }

    public function approve(EmployeeSalaryHistory $salaryHistory, int $approvedBy): bool
    {
        try {
            DB::beginTransaction();

            $updated = $salaryHistory->update([
                'approved_by' => $approvedBy,
                'approved_at' => now(),
            ]);

            if ($updated) {
                // Clear relevant caches
                $this->clearEmployeeCache($salaryHistory->employee_id);
                $this->clearApproverCache($approvedBy);

                Log::info('Salary history approved', ['id' => $salaryHistory->id, 'approved_by' => $approvedBy]);
            }

            DB::commit();
            return $updated;

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to approve salary history', ['error' => $e->getMessage(), 'id' => $salaryHistory->id]);
            throw $e;
        }
    }

    public function reject(EmployeeSalaryHistory $salaryHistory, int $rejectedBy, string $reason = null): bool
    {
        try {
            DB::beginTransaction();

            $updated = $salaryHistory->update([
                'approved_by' => null,
                'approved_at' => null,
                'notes' => $reason ? ($salaryHistory->notes . "\nRejected by: " . $reason) : $salaryHistory->notes,
            ]);

            if ($updated) {
                // Clear relevant caches
                $this->clearEmployeeCache($salaryHistory->employee_id);

                Log::info('Salary history rejected', ['id' => $salaryHistory->id, 'rejected_by' => $rejectedBy, 'reason' => $reason]);
            }

            DB::commit();
            return $updated;

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to reject salary history', ['error' => $e->getMessage(), 'id' => $salaryHistory->id]);
            throw $e;
        }
    }

    // Additional methods will be added in the next part...

    protected function clearEmployeeCache(int $employeeId): void
    {
        Cache::forget($this->cachePrefix . 'employee_' . $employeeId);
        Cache::forget($this->cachePrefix . 'latest_employee_' . $employeeId);
        Cache::forget($this->cachePrefix . 'count_employee_' . $employeeId);
    }

    protected function clearTypeCache(string $changeType): void
    {
        Cache::forget($this->cachePrefix . 'type_' . $changeType);
        Cache::forget($this->cachePrefix . 'total_count_type_' . $changeType);
    }

    protected function clearApproverCache(int $approverId): void
    {
        Cache::forget($this->cachePrefix . 'approver_' . $approverId);
    }
}
