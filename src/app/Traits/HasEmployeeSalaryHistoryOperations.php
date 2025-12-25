<?php

namespace App\Traits;

use App\DTOs\EmployeeSalaryHistoryDTO;
use App\Models\EmployeeSalaryHistory;
use App\Repositories\Interfaces\EmployeeSalaryHistoryRepositoryInterface;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Log;

trait HasEmployeeSalaryHistoryOperations
{
    /**
     * Get all salary history records
     */
    public function getAllSalaryHistory(): Collection
    {
        return app(EmployeeSalaryHistoryRepositoryInterface::class)->all();
    }

    /**
     * Get paginated salary history records
     */
    public function getPaginatedSalaryHistory(int $perPage = 15): LengthAwarePaginator
    {
        return app(EmployeeSalaryHistoryRepositoryInterface::class)->paginate($perPage);
    }

    /**
     * Find salary history by ID
     */
    public function findSalaryHistory(int $id): ?EmployeeSalaryHistory
    {
        return app(EmployeeSalaryHistoryRepositoryInterface::class)->find($id);
    }

    /**
     * Find salary history by ID and return DTO
     */
    public function findSalaryHistoryDTO(int $id): ?EmployeeSalaryHistoryDTO
    {
        return app(EmployeeSalaryHistoryRepositoryInterface::class)->findDTO($id);
    }

    /**
     * Find salary history by employee ID
     */
    public function findSalaryHistoryByEmployee(int $employeeId): Collection
    {
        return app(EmployeeSalaryHistoryRepositoryInterface::class)->findByEmployeeId($employeeId);
    }

    /**
     * Find salary history by employee ID and return DTOs
     */
    public function findSalaryHistoryByEmployeeDTO(int $employeeId): Collection
    {
        return app(EmployeeSalaryHistoryRepositoryInterface::class)->findByEmployeeIdDTO($employeeId);
    }

    /**
     * Find salary history by change type
     */
    public function findSalaryHistoryByType(string $changeType): Collection
    {
        return app(EmployeeSalaryHistoryRepositoryInterface::class)->findByChangeType($changeType);
    }

    /**
     * Find salary history by change type and return DTOs
     */
    public function findSalaryHistoryByTypeDTO(string $changeType): Collection
    {
        return app(EmployeeSalaryHistoryRepositoryInterface::class)->findByChangeTypeDTO($changeType);
    }

    /**
     * Find salary history by date range
     */
    public function findSalaryHistoryByDateRange(string $startDate, string $endDate): Collection
    {
        return app(EmployeeSalaryHistoryRepositoryInterface::class)->findByDateRange($startDate, $endDate);
    }

    /**
     * Find salary history by date range and return DTOs
     */
    public function findSalaryHistoryByDateRangeDTO(string $startDate, string $endDate): Collection
    {
        return app(EmployeeSalaryHistoryRepositoryInterface::class)->findByDateRangeDTO($startDate, $endDate);
    }

    /**
     * Find salary history by employee and date range
     */
    public function findSalaryHistoryByEmployeeAndDateRange(int $employeeId, string $startDate, string $endDate): Collection
    {
        return app(EmployeeSalaryHistoryRepositoryInterface::class)->findByEmployeeAndDateRange($employeeId, $startDate, $endDate);
    }

    /**
     * Find salary history by employee and date range and return DTOs
     */
    public function findSalaryHistoryByEmployeeAndDateRangeDTO(int $employeeId, string $startDate, string $endDate): Collection
    {
        return app(EmployeeSalaryHistoryRepositoryInterface::class)->findByEmployeeAndDateRangeDTO($employeeId, $startDate, $endDate);
    }

    /**
     * Find latest salary history for employee
     */
    public function findLatestSalaryHistoryByEmployee(int $employeeId): ?EmployeeSalaryHistory
    {
        return app(EmployeeSalaryHistoryRepositoryInterface::class)->findLatestByEmployee($employeeId);
    }

    /**
     * Find latest salary history for employee and return DTO
     */
    public function findLatestSalaryHistoryByEmployeeDTO(int $employeeId): ?EmployeeSalaryHistoryDTO
    {
        return app(EmployeeSalaryHistoryRepositoryInterface::class)->findLatestByEmployeeDTO($employeeId);
    }

    /**
     * Create new salary history record
     */
    public function createSalaryHistory(array $data): EmployeeSalaryHistory
    {
        try {
            // Validate required fields
            $this->validateSalaryHistoryData($data);

            // Auto-calculate change amount if not provided
            if (! isset($data['change_amount'])) {
                $data['change_amount'] = $data['new_salary'] - $data['old_salary'];
            }

            // Auto-calculate change percentage if not provided
            if (! isset($data['change_percentage']) && $data['old_salary'] > 0) {
                $data['change_percentage'] = (($data['new_salary'] - $data['old_salary']) / $data['old_salary']) * 100;
            }

            // Set default values
            $data['effective_date'] = $data['effective_date'] ?? now()->toDateString();
            $data['is_retroactive'] = $data['is_retroactive'] ?? false;

            $salaryHistory = app(EmployeeSalaryHistoryRepositoryInterface::class)->create($data);

            Log::info('Salary history created via trait', [
                'id' => $salaryHistory->id,
                'employee_id' => $data['employee_id'],
                'change_type' => $data['change_type'],
            ]);

            return $salaryHistory;

        } catch (\Exception $e) {
            Log::error('Failed to create salary history via trait', [
                'error' => $e->getMessage(),
                'data' => $data,
            ]);
            throw $e;
        }
    }

    /**
     * Create new salary history record and return DTO
     */
    public function createSalaryHistoryDTO(array $data): EmployeeSalaryHistoryDTO
    {
        $model = $this->createSalaryHistory($data);

        return EmployeeSalaryHistoryDTO::fromModel($model);
    }

    /**
     * Update salary history record
     */
    public function updateSalaryHistory(EmployeeSalaryHistory $salaryHistory, array $data): bool
    {
        try {
            // Validate update data
            $this->validateSalaryHistoryUpdateData($data);

            // Auto-calculate change amount if not provided
            if (! isset($data['change_amount'])) {
                $data['change_amount'] = $data['new_salary'] - $data['old_salary'];
            }

            // Auto-calculate change percentage if not provided
            if (! isset($data['change_percentage']) && $data['old_salary'] > 0) {
                $data['change_percentage'] = (($data['new_salary'] - $data['old_salary']) / $data['old_salary']) * 100;
            }

            $updated = app(EmployeeSalaryHistoryRepositoryInterface::class)->update($salaryHistory, $data);

            if ($updated) {
                Log::info('Salary history updated via trait', [
                    'id' => $salaryHistory->id,
                    'employee_id' => $salaryHistory->employee_id,
                ]);
            }

            return $updated;

        } catch (\Exception $e) {
            Log::error('Failed to update salary history via trait', [
                'error' => $e->getMessage(),
                'id' => $salaryHistory->id,
            ]);
            throw $e;
        }
    }

    /**
     * Update salary history record and return DTO
     */
    public function updateSalaryHistoryDTO(EmployeeSalaryHistory $salaryHistory, array $data): ?EmployeeSalaryHistoryDTO
    {
        $updated = $this->updateSalaryHistory($salaryHistory, $data);

        return $updated ? EmployeeSalaryHistoryDTO::fromModel($salaryHistory->fresh()) : null;
    }

    /**
     * Delete salary history record
     */
    public function deleteSalaryHistory(EmployeeSalaryHistory $salaryHistory): bool
    {
        try {
            $deleted = app(EmployeeSalaryHistoryRepositoryInterface::class)->delete($salaryHistory);

            if ($deleted) {
                Log::info('Salary history deleted via trait', [
                    'id' => $salaryHistory->id,
                    'employee_id' => $salaryHistory->employee_id,
                ]);
            }

            return $deleted;

        } catch (\Exception $e) {
            Log::error('Failed to delete salary history via trait', [
                'error' => $e->getMessage(),
                'id' => $salaryHistory->id,
            ]);
            throw $e;
        }
    }

    /**
     * Search salary history
     */
    public function searchSalaryHistory(string $query): Collection
    {
        return app(EmployeeSalaryHistoryRepositoryInterface::class)->searchSalaryHistory($query);
    }

    /**
     * Search salary history and return DTOs
     */
    public function searchSalaryHistoryDTO(string $query): Collection
    {
        return app(EmployeeSalaryHistoryRepositoryInterface::class)->searchSalaryHistoryDTO($query);
    }

    /**
     * Search salary history by employee
     */
    public function searchSalaryHistoryByEmployee(int $employeeId, string $query): Collection
    {
        return app(EmployeeSalaryHistoryRepositoryInterface::class)->searchSalaryHistoryByEmployee($employeeId, $query);
    }

    /**
     * Search salary history by employee and return DTOs
     */
    public function searchSalaryHistoryByEmployeeDTO(int $employeeId, string $query): Collection
    {
        return app(EmployeeSalaryHistoryRepositoryInterface::class)->searchSalaryHistoryByEmployeeDTO($employeeId, $query);
    }

    /**
     * Get employee salary history count
     */
    public function getEmployeeSalaryHistoryCount(int $employeeId): int
    {
        return app(EmployeeSalaryHistoryRepositoryInterface::class)->getEmployeeSalaryHistoryCount($employeeId);
    }

    /**
     * Get employee salary history count by type
     */
    public function getEmployeeSalaryHistoryCountByType(int $employeeId, string $changeType): int
    {
        return app(EmployeeSalaryHistoryRepositoryInterface::class)->getEmployeeSalaryHistoryCountByType($employeeId, $changeType);
    }

    /**
     * Validate salary history data for creation
     */
    protected function validateSalaryHistoryData(array $data): void
    {
        $requiredFields = ['employee_id', 'old_salary', 'new_salary', 'change_type', 'effective_date'];

        foreach ($requiredFields as $field) {
            if (! isset($data[$field])) {
                throw new \InvalidArgumentException("Required field '{$field}' is missing");
            }
        }

        if ($data['old_salary'] < 0 || $data['new_salary'] < 0) {
            throw new \InvalidArgumentException('Salary amounts cannot be negative');
        }

        if (! in_array($data['change_type'], ['promotion', 'merit', 'cost_of_living', 'market_adjustment', 'demotion', 'other'])) {
            throw new \InvalidArgumentException('Invalid change type');
        }

        if ($data['is_retroactive'] ?? false) {
            if (! isset($data['retroactive_start_date']) || ! isset($data['retroactive_end_date'])) {
                throw new \InvalidArgumentException('Retroactive start and end dates are required when retroactive is enabled');
            }

            if (Carbon::parse($data['retroactive_start_date'])->isAfter(Carbon::parse($data['retroactive_end_date']))) {
                throw new \InvalidArgumentException('Retroactive start date must be before end date');
            }
        }
    }

    /**
     * Validate salary history data for updates
     */
    protected function validateSalaryHistoryUpdateData(array $data): void
    {
        if (isset($data['old_salary']) && $data['old_salary'] < 0) {
            throw new \InvalidArgumentException('Old salary cannot be negative');
        }

        if (isset($data['new_salary']) && $data['new_salary'] < 0) {
            throw new \InvalidArgumentException('New salary cannot be negative');
        }

        if (isset($data['change_type']) && ! in_array($data['change_type'], ['promotion', 'merit', 'cost_of_living', 'market_adjustment', 'demotion', 'other'])) {
            throw new \InvalidArgumentException('Invalid change type');
        }

        if (isset($data['is_retroactive']) && $data['is_retroactive']) {
            if (! isset($data['retroactive_start_date']) || ! isset($data['retroactive_end_date'])) {
                throw new \InvalidArgumentException('Retroactive start and end dates are required when retroactive is enabled');
            }

            if (Carbon::parse($data['retroactive_start_date'])->isAfter(Carbon::parse($data['retroactive_end_date']))) {
                throw new \InvalidArgumentException('Retroactive start date must be before end date');
            }
        }
    }
}
