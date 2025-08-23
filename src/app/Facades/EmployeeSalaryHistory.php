<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;
use App\Models\EmployeeSalaryHistory;
use App\Repositories\EmployeeSalaryHistoryRepository;
use App\DTOs\EmployeeSalaryHistoryDTO;
use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Pagination\CursorPaginator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Notification;
use Exception;

class EmployeeSalaryHistoryFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return EmployeeSalaryHistoryRepository::class;
    }

    // Static methods for easy access
    public static function all(): Collection
    {
        try {
            return static::getFacadeRoot()->all();
        } catch (Exception $e) {
            Log::error('Error fetching all salary history records', ['error' => $e->getMessage()]);
            return collect();
        }
    }

    public static function paginate(int $perPage = 15): LengthAwarePaginator
    {
        try {
            return static::getFacadeRoot()->paginate($perPage);
        } catch (Exception $e) {
            Log::error('Error paginating salary history records', ['error' => $e->getMessage()]);
            return new LengthAwarePaginator([], 0, $perPage);
        }
    }

    public static function find(int $id): ?EmployeeSalaryHistory
    {
        try {
            return static::getFacadeRoot()->find($id);
        } catch (Exception $e) {
            Log::error('Error finding salary history record', ['id' => $id, 'error' => $e->getMessage()]);
            return null;
        }
    }

    public static function findByEmployee(int $employeeId): Collection
    {
        try {
            return static::getFacadeRoot()->findByEmployeeId($employeeId);
        } catch (Exception $e) {
            Log::error('Error finding salary history by employee', ['employee_id' => $employeeId, 'error' => $e->getMessage()]);
            return collect();
        }
    }

    public static function create(array $data): ?EmployeeSalaryHistory
    {
        try {
            $record = static::getFacadeRoot()->create($data);

            // Fire event
            Event::dispatch('employee.salary-history.created', $record);

            // Clear cache
            Cache::tags(['salary-history', 'employee-' . $data['employee_id']])->flush();

            return $record;
        } catch (Exception $e) {
            Log::error('Error creating salary history record', ['data' => $data, 'error' => $e->getMessage()]);
            return null;
        }
    }

    public static function update(int $id, array $data): bool
    {
        try {
            $record = static::find($id);
            if (!$record) {
                return false;
            }

            $result = static::getFacadeRoot()->update($record, $data);

            if ($result) {
                // Fire event
                Event::dispatch('employee.salary-history.updated', $record);

                // Clear cache
                Cache::tags(['salary-history', 'employee-' . $record->employee_id])->flush();
            }

            return $result;
        } catch (Exception $e) {
            Log::error('Error updating salary history record', ['id' => $id, 'data' => $data, 'error' => $e->getMessage()]);
            return false;
        }
    }

    public static function delete(int $id): bool
    {
        try {
            $record = static::find($id);
            if (!$record) {
                return false;
            }

            $result = static::getFacadeRoot()->delete($record);

            if ($result) {
                // Fire event
                Event::dispatch('employee.salary-history.deleted', $record);

                // Clear cache
                Cache::tags(['salary-history', 'employee-' . $record->employee_id])->flush();
            }

            return $result;
        } catch (Exception $e) {
            Log::error('Error deleting salary history record', ['id' => $id, 'error' => $e->getMessage()]);
            return false;
        }
    }

    public static function approve(int $id, int $approvedBy): bool
    {
        try {
            $record = static::find($id);
            if (!$record) {
                return false;
            }

            $result = static::getFacadeRoot()->approve($record, $approvedBy);

            if ($result) {
                // Fire event
                Event::dispatch('employee.salary-history.approved', $record);

                // Clear cache
                Cache::tags(['salary-history', 'employee-' . $record->employee_id])->flush();
            }

            return $result;
        } catch (Exception $e) {
            Log::error('Error approving salary history record', ['id' => $id, 'approved_by' => $approvedBy, 'error' => $e->getMessage()]);
            return false;
        }
    }

    public static function reject(int $id, int $rejectedBy, string $reason = null): bool
    {
        try {
            $record = static::find($id);
            if (!$record) {
                return false;
            }

            $result = static::getFacadeRoot()->reject($record, $rejectedBy, $reason);

            if ($result) {
                // Fire event
                Event::dispatch('employee.salary-history.rejected', $record);

                // Clear cache
                Cache::tags(['salary-history', 'employee-' . $record->employee_id])->flush();
            }

            return $result;
        } catch (Exception $e) {
            Log::error('Error rejecting salary history record', ['id' => $id, 'rejected_by' => $rejectedBy, 'error' => $e->getMessage()]);
            return false;
        }
    }

    public static function getStatistics(string $startDate = null, string $endDate = null): array
    {
        try {
            return static::getFacadeRoot()->getSalaryChangeStatistics($startDate, $endDate);
        } catch (Exception $e) {
            Log::error('Error getting salary statistics', ['start_date' => $startDate, 'end_date' => $endDate, 'error' => $e->getMessage()]);
            return [];
        }
    }

    public static function getEmployeeGrowth(int $employeeId): float
    {
        try {
            return static::getFacadeRoot()->getEmployeeSalaryGrowth($employeeId);
        } catch (Exception $e) {
            Log::error('Error getting employee salary growth', ['employee_id' => $employeeId, 'error' => $e->getMessage()]);
            return 0.0;
        }
    }

    public static function search(string $query): Collection
    {
        try {
            return static::getFacadeRoot()->searchSalaryHistory($query);
        } catch (Exception $e) {
            Log::error('Error searching salary history', ['query' => $query, 'error' => $e->getMessage()]);
            return collect();
        }
    }

    // Method chaining support
    public static function forEmployee(int $employeeId): self
    {
        return new class($employeeId) {
            private $employeeId;
            private $repository;

            public function __construct($employeeId)
            {
                $this->employeeId = $employeeId;
                $this->repository = app(EmployeeSalaryHistoryRepository::class);
            }

            public function getHistory(): Collection
            {
                return $this->repository->findByEmployeeId($this->employeeId);
            }

            public function getLatest(): ?EmployeeSalaryHistory
            {
                return $this->repository->findLatestByEmployee($this->employeeId);
            }

            public function getGrowth(): float
            {
                return $this->repository->getEmployeeSalaryGrowth($this->employeeId);
            }

            public function getCurrentSalary(): float
            {
                return $this->repository->getEmployeeCurrentSalary($this->employeeId);
            }

            public function getStartingSalary(): float
            {
                return $this->repository->getEmployeeStartingSalary($this->employeeId);
            }

            public function getTotalIncrease(string $startDate = null, string $endDate = null): float
            {
                return $this->repository->getEmployeeTotalSalaryIncrease($this->employeeId, $startDate, $endDate);
            }

            public function getTotalDecrease(string $startDate = null, string $endDate = null): float
            {
                return $this->repository->getEmployeeTotalSalaryDecrease($this->employeeId, $startDate, $endDate);
            }

            public function getAverageChange(): float
            {
                return $this->repository->getEmployeeAverageSalaryChange($this->employeeId);
            }
        };
    }

    public static function byType(string $changeType): self
    {
        return new class($changeType) {
            private $changeType;
            private $repository;

            public function __construct($changeType)
            {
                $this->changeType = $changeType;
                $this->repository = app(EmployeeSalaryHistoryRepository::class);
            }

            public function getAll(): Collection
            {
                return $this->repository->findByChangeType($this->changeType);
            }

            public function getCount(): int
            {
                return $this->repository->getTotalSalaryHistoryCountByType($this->changeType);
            }

            public function getAverageChange(): float
            {
                return $this->repository->getAverageSalaryChangeByType($this->changeType);
            }
        };
    }

    public static function byDateRange(string $startDate, string $endDate): self
    {
        return new class($startDate, $endDate) {
            private $startDate;
            private $endDate;
            private $repository;

            public function __construct($startDate, $endDate)
            {
                $this->startDate = $startDate;
                $this->endDate = $endDate;
                $this->repository = app(EmployeeSalaryHistoryRepository::class);
            }

            public function getAll(): Collection
            {
                return $this->repository->findByDateRange($this->startDate, $this->endDate);
            }

            public function getStatistics(): array
            {
                return $this->repository->getSalaryChangeStatistics($this->startDate, $this->endDate);
            }

            public function getTotalIncrease(): float
            {
                return $this->repository->getTotalSalaryIncrease($this->startDate, $this->endDate);
            }

            public function getTotalDecrease(): float
            {
                return $this->repository->getTotalSalaryDecrease($this->startDate, $this->endDate);
            }

            public function getTrends(): array
            {
                return $this->repository->getSalaryTrends($this->startDate, $this->endDate);
            }
        };
    }
}
