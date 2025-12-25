<?php

namespace Fereydooni\Shopping\app\Repositories;

use Fereydooni\Shopping\app\DTOs\EmployeeTimeOffDTO;
use Fereydooni\Shopping\app\Enums\TimeOffStatus;
use Fereydooni\Shopping\app\Enums\TimeOffType;
use Fereydooni\Shopping\app\Models\EmployeeTimeOff;
use Fereydooni\Shopping\app\Repositories\Interfaces\EmployeeTimeOffRepositoryInterface;
use Illuminate\Pagination\CursorPaginator;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class EmployeeTimeOffRepository implements EmployeeTimeOffRepositoryInterface
{
    protected $model;

    protected $cachePrefix = 'employee_time_off';

    protected $cacheTtl = 3600; // 1 hour

    public function __construct(EmployeeTimeOff $model)
    {
        $this->model = $model;
    }

    // Basic CRUD operations
    public function all(): Collection
    {
        return Cache::remember("{$this->cachePrefix}:all", $this->cacheTtl, function () {
            return $this->model->with(['employee', 'user', 'approver', 'rejector'])->get();
        });
    }

    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        $cacheKey = "{$this->cachePrefix}:paginate:{$perPage}:".request()->get('page', 1);

        return Cache::remember($cacheKey, $this->cacheTtl, function () use ($perPage) {
            return $this->model->with(['employee', 'user', 'approver', 'rejector'])
                ->orderBy('created_at', 'desc')
                ->paginate($perPage);
        });
    }

    public function simplePaginate(int $perPage = 15): Paginator
    {
        return $this->model->with(['employee', 'user', 'approver', 'rejector'])
            ->orderBy('created_at', 'desc')
            ->simplePaginate($perPage);
    }

    public function cursorPaginate(int $perPage = 15, ?string $cursor = null): CursorPaginator
    {
        return $this->model->with(['employee', 'user', 'approver', 'rejector'])
            ->orderBy('created_at', 'desc')
            ->cursorPaginate($perPage, ['*'], 'cursor', $cursor);
    }

    // Find operations
    public function find(int $id): ?EmployeeTimeOff
    {
        $cacheKey = "{$this->cachePrefix}:find:{$id}";

        return Cache::remember($cacheKey, $this->cacheTtl, function () use ($id) {
            return $this->model->with(['employee', 'user', 'approver', 'rejector'])->find($id);
        });
    }

    public function findDTO(int $id): ?EmployeeTimeOffDTO
    {
        $timeOff = $this->find($id);

        return $timeOff ? EmployeeTimeOffDTO::fromModel($timeOff) : null;
    }

    public function findByEmployeeId(int $employeeId): Collection
    {
        $cacheKey = "{$this->cachePrefix}:employee:{$employeeId}";

        return Cache::remember($cacheKey, $this->cacheTtl, function () use ($employeeId) {
            return $this->model->byEmployee($employeeId)
                ->with(['employee', 'user', 'approver', 'rejector'])
                ->orderBy('start_date', 'desc')
                ->get();
        });
    }

    public function findByEmployeeIdDTO(int $employeeId): Collection
    {
        return $this->findByEmployeeId($employeeId)->map(function ($timeOff) {
            return EmployeeTimeOffDTO::fromModel($timeOff);
        });
    }

    public function findByUserId(int $userId): Collection
    {
        $cacheKey = "{$this->cachePrefix}:user:{$userId}";

        return Cache::remember($cacheKey, $this->cacheTtl, function () use ($userId) {
            return $this->model->byUser($userId)
                ->with(['employee', 'user', 'approver', 'rejector'])
                ->orderBy('start_date', 'desc')
                ->get();
        });
    }

    public function findByUserIdDTO(int $userId): Collection
    {
        return $this->findByUserId($userId)->map(function ($timeOff) {
            return EmployeeTimeOffDTO::fromModel($timeOff);
        });
    }

    public function findByStatus(string $status): Collection
    {
        $cacheKey = "{$this->cachePrefix}:status:{$status}";

        return Cache::remember($cacheKey, $this->cacheTtl, function () use ($status) {
            return $this->model->byStatus($status)
                ->with(['employee', 'user', 'approver', 'rejector'])
                ->orderBy('created_at', 'desc')
                ->get();
        });
    }

    public function findByStatusDTO(string $status): Collection
    {
        return $this->findByStatus($status)->map(function ($timeOff) {
            return EmployeeTimeOffDTO::fromModel($timeOff);
        });
    }

    public function findByTimeOffType(string $timeOffType): Collection
    {
        $cacheKey = "{$this->cachePrefix}:type:{$timeOffType}";

        return Cache::remember($cacheKey, $this->cacheTtl, function () use ($timeOffType) {
            return $this->model->byTimeOffType($timeOffType)
                ->with(['employee', 'user', 'approver', 'rejector'])
                ->orderBy('start_date', 'desc')
                ->get();
        });
    }

    public function findByTimeOffTypeDTO(string $timeOffType): Collection
    {
        return $this->findByTimeOffType($timeOffType)->map(function ($timeOff) {
            return EmployeeTimeOffDTO::fromModel($timeOff);
        });
    }

    // Date range operations
    public function findByDateRange(string $startDate, string $endDate): Collection
    {
        $cacheKey = "{$this->cachePrefix}:date_range:{$startDate}:{$endDate}";

        return Cache::remember($cacheKey, $this->cacheTtl, function () use ($startDate, $endDate) {
            return $this->model->byDateRange($startDate, $endDate)
                ->with(['employee', 'user', 'approver', 'rejector'])
                ->orderBy('start_date', 'asc')
                ->get();
        });
    }

    public function findByDateRangeDTO(string $startDate, string $endDate): Collection
    {
        return $this->findByDateRange($startDate, $endDate)->map(function ($timeOff) {
            return EmployeeTimeOffDTO::fromModel($timeOff);
        });
    }

    public function findByEmployeeAndDateRange(int $employeeId, string $startDate, string $endDate): Collection
    {
        $cacheKey = "{$this->cachePrefix}:employee_date_range:{$employeeId}:{$startDate}:{$endDate}";

        return Cache::remember($cacheKey, $this->cacheTtl, function () use ($employeeId, $startDate, $endDate) {
            return $this->model->byEmployee($employeeId)
                ->byDateRange($startDate, $endDate)
                ->with(['employee', 'user', 'approver', 'rejector'])
                ->orderBy('start_date', 'asc')
                ->get();
        });
    }

    public function findByEmployeeAndDateRangeDTO(int $employeeId, string $startDate, string $endDate): Collection
    {
        return $this->findByEmployeeAndDateRange($employeeId, $startDate, $endDate)->map(function ($timeOff) {
            return EmployeeTimeOffDTO::fromModel($timeOff);
        });
    }

    // Status-based operations
    public function findPending(): Collection
    {
        return $this->findByStatus(TimeOffStatus::PENDING->value);
    }

    public function findPendingDTO(): Collection
    {
        return $this->findByStatusDTO(TimeOffStatus::PENDING->value);
    }

    public function findApproved(): Collection
    {
        return $this->findByStatus(TimeOffStatus::APPROVED->value);
    }

    public function findApprovedDTO(): Collection
    {
        return $this->findByStatusDTO(TimeOffStatus::APPROVED->value);
    }

    public function findRejected(): Collection
    {
        return $this->findByStatus(TimeOffStatus::REJECTED->value);
    }

    public function findRejectedDTO(): Collection
    {
        return $this->findByStatusDTO(TimeOffStatus::REJECTED->value);
    }

    public function findCancelled(): Collection
    {
        return $this->findByStatus(TimeOffStatus::CANCELLED->value);
    }

    public function findCancelledDTO(): Collection
    {
        return $this->findByStatusDTO(TimeOffStatus::CANCELLED->value);
    }

    public function findUrgent(): Collection
    {
        $cacheKey = "{$this->cachePrefix}:urgent";

        return Cache::remember($cacheKey, $this->cacheTtl, function () {
            return $this->model->urgent()
                ->with(['employee', 'user', 'approver', 'rejector'])
                ->orderBy('created_at', 'desc')
                ->get();
        });
    }

    public function findUrgentDTO(): Collection
    {
        return $this->findUrgent()->map(function ($timeOff) {
            return EmployeeTimeOffDTO::fromModel($timeOff);
        });
    }

    // Overlap detection
    public function findOverlapping(int $employeeId, string $startDate, string $endDate): Collection
    {
        $cacheKey = "{$this->cachePrefix}:overlapping:{$employeeId}:{$startDate}:{$endDate}";

        return Cache::remember($cacheKey, $this->cacheTtl, function () use ($employeeId, $startDate, $endDate) {
            return $this->model->overlapping($employeeId, $startDate, $endDate)
                ->with(['employee', 'user', 'approver', 'rejector'])
                ->get();
        });
    }

    public function findOverlappingDTO(int $employeeId, string $startDate, string $endDate): Collection
    {
        return $this->findOverlapping($employeeId, $startDate, $endDate)->map(function ($timeOff) {
            return EmployeeTimeOffDTO::fromModel($timeOff);
        });
    }

    // Approver operations
    public function findByApproverId(int $approverId): Collection
    {
        $cacheKey = "{$this->cachePrefix}:approver:{$approverId}";

        return Cache::remember($cacheKey, $this->cacheTtl, function () use ($approverId) {
            return $this->model->byApprover($approverId)
                ->with(['employee', 'user', 'approver', 'rejector'])
                ->orderBy('created_at', 'desc')
                ->get();
        });
    }

    public function findByApproverIdDTO(int $approverId): Collection
    {
        return $this->findByApproverId($approverId)->map(function ($timeOff) {
            return EmployeeTimeOffDTO::fromModel($timeOff);
        });
    }

    // Time-based operations
    public function findUpcoming(int $employeeId, ?string $date = null): Collection
    {
        $date = $date ?: now()->format('Y-m-d');
        $cacheKey = "{$this->cachePrefix}:upcoming:{$employeeId}:{$date}";

        return Cache::remember($cacheKey, $this->cacheTtl, function () use ($employeeId, $date) {
            return $this->model->byEmployee($employeeId)
                ->upcoming($date)
                ->with(['employee', 'user', 'approver', 'rejector'])
                ->orderBy('start_date', 'asc')
                ->get();
        });
    }

    public function findUpcomingDTO(int $employeeId, ?string $date = null): Collection
    {
        return $this->findUpcoming($employeeId, $date)->map(function ($timeOff) {
            return EmployeeTimeOffDTO::fromModel($timeOff);
        });
    }

    public function findPast(int $employeeId, ?string $date = null): Collection
    {
        $date = $date ?: now()->format('Y-m-d');
        $cacheKey = "{$this->cachePrefix}:past:{$employeeId}:{$date}";

        return Cache::remember($cacheKey, $this->cacheTtl, function () use ($employeeId, $date) {
            return $this->model->byEmployee($employeeId)
                ->past($date)
                ->with(['employee', 'user', 'approver', 'rejector'])
                ->orderBy('start_date', 'desc')
                ->get();
        });
    }

    public function findPastDTO(int $employeeId, ?string $date = null): Collection
    {
        return $this->findPast($employeeId, $date)->map(function ($timeOff) {
            return EmployeeTimeOffDTO::fromModel($timeOff);
        });
    }

    // Create and update operations
    public function create(array $data): EmployeeTimeOff
    {
        try {
            DB::beginTransaction();

            $timeOff = $this->model->create($data);

            // Clear relevant cache
            $this->clearCache();

            DB::commit();

            Log::info('Employee time-off request created', ['id' => $timeOff->id, 'employee_id' => $timeOff->employee_id]);

            return $timeOff->load(['employee', 'user', 'approver', 'rejector']);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to create employee time-off request', ['error' => $e->getMessage(), 'data' => $data]);
            throw $e;
        }
    }

    public function createAndReturnDTO(array $data): EmployeeTimeOffDTO
    {
        $timeOff = $this->create($data);

        return EmployeeTimeOffDTO::fromModel($timeOff);
    }

    public function update(EmployeeTimeOff $timeOff, array $data): bool
    {
        try {
            DB::beginTransaction();

            $updated = $timeOff->update($data);

            if ($updated) {
                // Clear relevant cache
                $this->clearCache();

                Log::info('Employee time-off request updated', ['id' => $timeOff->id, 'employee_id' => $timeOff->employee_id]);
            }

            DB::commit();

            return $updated;

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to update employee time-off request', ['error' => $e->getMessage(), 'id' => $timeOff->id]);
            throw $e;
        }
    }

    public function updateAndReturnDTO(EmployeeTimeOff $timeOff, array $data): ?EmployeeTimeOffDTO
    {
        $updated = $this->update($timeOff, $data);

        return $updated ? EmployeeTimeOffDTO::fromModel($timeOff->fresh()) : null;
    }

    public function delete(EmployeeTimeOff $timeOff): bool
    {
        try {
            DB::beginTransaction();

            $deleted = $timeOff->delete();

            if ($deleted) {
                // Clear relevant cache
                $this->clearCache();

                Log::info('Employee time-off request deleted', ['id' => $timeOff->id, 'employee_id' => $timeOff->employee_id]);
            }

            DB::commit();

            return $deleted;

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to delete employee time-off request', ['error' => $e->getMessage(), 'id' => $timeOff->id]);
            throw $e;
        }
    }

    // Workflow operations
    public function approve(EmployeeTimeOff $timeOff, int $approvedBy): bool
    {
        try {
            DB::beginTransaction();

            $approved = $timeOff->approve($approvedBy);

            if ($approved) {
                // Clear relevant cache
                $this->clearCache();

                Log::info('Employee time-off request approved', ['id' => $timeOff->id, 'approved_by' => $approvedBy]);
            }

            DB::commit();

            return $approved;

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to approve employee time-off request', ['error' => $e->getMessage(), 'id' => $timeOff->id]);
            throw $e;
        }
    }

    public function reject(EmployeeTimeOff $timeOff, int $rejectedBy, ?string $reason = null): bool
    {
        try {
            DB::beginTransaction();

            $rejected = $timeOff->reject($rejectedBy, $reason);

            if ($rejected) {
                // Clear relevant cache
                $this->clearCache();

                Log::info('Employee time-off request rejected', ['id' => $timeOff->id, 'rejected_by' => $rejectedBy, 'reason' => $reason]);
            }

            DB::commit();

            return $rejected;

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to reject employee time-off request', ['error' => $e->getMessage(), 'id' => $timeOff->id]);
            throw $e;
        }
    }

    public function cancel(EmployeeTimeOff $timeOff, ?string $reason = null): bool
    {
        try {
            DB::beginTransaction();

            $cancelled = $timeOff->cancel($reason);

            if ($cancelled) {
                // Clear relevant cache
                $this->clearCache();

                Log::info('Employee time-off request cancelled', ['id' => $timeOff->id, 'reason' => $reason]);
            }

            DB::commit();

            return $cancelled;

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to cancel employee time-off request', ['error' => $e->getMessage(), 'id' => $timeOff->id]);
            throw $e;
        }
    }

    public function markAsUrgent(EmployeeTimeOff $timeOff): bool
    {
        try {
            $marked = $timeOff->markAsUrgent();

            if ($marked) {
                $this->clearCache();
                Log::info('Employee time-off request marked as urgent', ['id' => $timeOff->id]);
            }

            return $marked;

        } catch (\Exception $e) {
            Log::error('Failed to mark employee time-off request as urgent', ['error' => $e->getMessage(), 'id' => $timeOff->id]);
            throw $e;
        }
    }

    public function removeUrgentFlag(EmployeeTimeOff $timeOff): bool
    {
        try {
            $removed = $timeOff->removeUrgentFlag();

            if ($removed) {
                $this->clearCache();
                Log::info('Employee time-off request urgent flag removed', ['id' => $timeOff->id]);
            }

            return $removed;

        } catch (\Exception $e) {
            Log::error('Failed to remove urgent flag from employee time-off request', ['error' => $e->getMessage(), 'id' => $timeOff->id]);
            throw $e;
        }
    }

    // Count operations
    public function getEmployeeTimeOffCount(int $employeeId): int
    {
        $cacheKey = "{$this->cachePrefix}:count:employee:{$employeeId}";

        return Cache::remember($cacheKey, $this->cacheTtl, function () use ($employeeId) {
            return $this->model->byEmployee($employeeId)->count();
        });
    }

    public function getEmployeeTimeOffCountByType(int $employeeId, string $timeOffType): int
    {
        $cacheKey = "{$this->cachePrefix}:count:employee_type:{$employeeId}:{$timeOffType}";

        return Cache::remember($cacheKey, $this->cacheTtl, function () use ($employeeId, $timeOffType) {
            return $this->model->byEmployee($employeeId)->byTimeOffType($timeOffType)->count();
        });
    }

    public function getEmployeeTimeOffCountByStatus(int $employeeId, string $status): int
    {
        $cacheKey = "{$this->cachePrefix}:count:employee_status:{$employeeId}:{$status}";

        return Cache::remember($cacheKey, $this->cacheTtl, function () use ($employeeId, $status) {
            return $this->model->byEmployee($employeeId)->byStatus($status)->count();
        });
    }

    public function getEmployeeTotalDaysUsed(int $employeeId, ?string $year = null): float
    {
        $year = $year ?: now()->year;
        $cacheKey = "{$this->cachePrefix}:days_used:{$employeeId}:{$year}";

        return Cache::remember($cacheKey, $this->cacheTtl, function () use ($employeeId, $year) {
            return $this->model->byEmployee($employeeId)
                ->byStatus(TimeOffStatus::APPROVED->value)
                ->whereYear('start_date', $year)
                ->sum('total_days');
        });
    }

    public function getEmployeeTotalHoursUsed(int $employeeId, ?string $year = null): float
    {
        $year = $year ?: now()->year;
        $cacheKey = "{$this->cachePrefix}:hours_used:{$employeeId}:{$year}";

        return Cache::remember($cacheKey, $this->cacheTtl, function () use ($employeeId, $year) {
            return $this->model->byEmployee($employeeId)
                ->byStatus(TimeOffStatus::APPROVED->value)
                ->whereYear('start_date', $year)
                ->sum('total_hours');
        });
    }

    public function getEmployeeRemainingDays(int $employeeId, string $timeOffType, ?string $year = null): float
    {
        // This would typically integrate with employee benefits/entitlements
        // For now, returning a placeholder calculation
        $year = $year ?: now()->year;
        $cacheKey = "{$this->cachePrefix}:remaining_days:{$employeeId}:{$timeOffType}:{$year}";

        return Cache::remember($cacheKey, $this->cacheTtl, function () use ($employeeId, $timeOffType, $year) {
            // Placeholder: assume 20 days per year for vacation, 10 for sick, etc.
            $entitlement = match ($timeOffType) {
                TimeOffType::VACATION->value => 20,
                TimeOffType::SICK->value => 10,
                TimeOffType::PERSONAL->value => 5,
                default => 0,
            };

            $used = $this->getEmployeeTotalDaysUsed($employeeId, $year);

            return max(0, $entitlement - $used);
        });
    }

    public function getEmployeeRemainingHours(int $employeeId, string $timeOffType, ?string $year = null): float
    {
        $remainingDays = $this->getEmployeeRemainingDays($employeeId, $timeOffType, $year);

        return $remainingDays * 8; // Assuming 8-hour workday
    }

    public function getTotalTimeOffCount(): int
    {
        $cacheKey = "{$this->cachePrefix}:count:total";

        return Cache::remember($cacheKey, $this->cacheTtl, function () {
            return $this->model->count();
        });
    }

    public function getTotalTimeOffCountByType(string $timeOffType): int
    {
        $cacheKey = "{$this->cachePrefix}:count:type:{$timeOffType}";

        return Cache::remember($cacheKey, $this->cacheTtl, function () use ($timeOffType) {
            return $this->model->byTimeOffType($timeOffType)->count();
        });
    }

    public function getTotalTimeOffCountByStatus(string $status): int
    {
        $cacheKey = "{$this->cachePrefix}:count:status:{$status}";

        return Cache::remember($cacheKey, $this->cacheTtl, function () use ($status) {
            return $this->model->byStatus($status)->count();
        });
    }

    public function getPendingApprovalCount(): int
    {
        return $this->getTotalTimeOffCountByStatus(TimeOffStatus::PENDING->value);
    }

    public function getUrgentRequestCount(): int
    {
        $cacheKey = "{$this->cachePrefix}:count:urgent";

        return Cache::remember($cacheKey, $this->cacheTtl, function () {
            return $this->model->urgent()->count();
        });
    }

    public function getOverlappingRequestCount(int $employeeId, string $startDate, string $endDate): int
    {
        return $this->findOverlapping($employeeId, $startDate, $endDate)->count();
    }

    // Search operations
    public function searchTimeOff(string $query): Collection
    {
        $cacheKey = "{$this->cachePrefix}:search:".md5($query);

        return Cache::remember($cacheKey, $this->cacheTtl, function () use ($query) {
            return $this->model->where(function ($q) use ($query) {
                $q->where('reason', 'like', "%{$query}%")
                    ->orWhere('description', 'like', "%{$query}%")
                    ->orWhereHas('employee', function ($empQ) use ($query) {
                        $empQ->where('first_name', 'like', "%{$query}%")
                            ->orWhere('last_name', 'like', "%{$query}%");
                    });
            })->with(['employee', 'user', 'approver', 'rejector'])
                ->orderBy('created_at', 'desc')
                ->get();
        });
    }

    public function searchTimeOffDTO(string $query): Collection
    {
        return $this->searchTimeOff($query)->map(function ($timeOff) {
            return EmployeeTimeOffDTO::fromModel($timeOff);
        });
    }

    public function searchTimeOffByEmployee(int $employeeId, string $query): Collection
    {
        $cacheKey = "{$this->cachePrefix}:search_employee:{$employeeId}:".md5($query);

        return Cache::remember($cacheKey, $this->cacheTtl, function () use ($employeeId, $query) {
            return $this->model->byEmployee($employeeId)
                ->where(function ($q) use ($query) {
                    $q->where('reason', 'like', "%{$query}%")
                        ->orWhere('description', 'like', "%{$query}%");
                })
                ->with(['employee', 'user', 'approver', 'rejector'])
                ->orderBy('created_at', 'desc')
                ->get();
        });
    }

    public function searchTimeOffByEmployeeDTO(int $employeeId, string $query): Collection
    {
        return $this->searchTimeOffByEmployee($employeeId, $query)->map(function ($timeOff) {
            return EmployeeTimeOffDTO::fromModel($timeOff);
        });
    }

    // Import/Export operations
    public function exportTimeOffData(array $filters = []): string
    {
        try {
            $query = $this->model->with(['employee', 'user', 'approver', 'rejector']);

            // Apply filters
            if (isset($filters['employee_id'])) {
                $query->byEmployee($filters['employee_id']);
            }

            if (isset($filters['status'])) {
                $query->byStatus($filters['status']);
            }

            if (isset($filters['time_off_type'])) {
                $query->byTimeOffType($filters['time_off_type']);
            }

            if (isset($filters['start_date']) && isset($filters['end_date'])) {
                $query->byDateRange($filters['start_date'], $filters['end_date']);
            }

            $data = $query->get();

            // Convert to CSV format
            $csv = "ID,Employee ID,User ID,Time Off Type,Start Date,End Date,Reason,Status,Approved By,Approved At,Rejected By,Rejected At,Rejection Reason,Is Half Day,Is Urgent,Created At\n";

            foreach ($data as $row) {
                $csv .= implode(',', [
                    $row->id,
                    $row->employee_id,
                    $row->user_id,
                    $row->time_off_type->value,
                    $row->start_date->format('Y-m-d'),
                    $row->end_date->format('Y-m-d'),
                    '"'.str_replace('"', '""', $row->reason).'"',
                    $row->status->value,
                    $row->approved_by,
                    $row->approved_at?->format('Y-m-d H:i:s'),
                    $row->rejected_by,
                    $row->rejected_at?->format('Y-m-d H:i:s'),
                    '"'.str_replace('"', '""', $row->rejection_reason ?? '').'"',
                    $row->is_half_day ? 'Yes' : 'No',
                    $row->is_urgent ? 'Yes' : 'No',
                    $row->created_at->format('Y-m-d H:i:s'),
                ])."\n";
            }

            return $csv;

        } catch (\Exception $e) {
            Log::error('Failed to export time-off data', ['error' => $e->getMessage()]);
            throw $e;
        }
    }

    public function importTimeOffData(string $data): bool
    {
        try {
            DB::beginTransaction();

            $lines = explode("\n", trim($data));
            $headers = str_getcsv(array_shift($lines));

            $imported = 0;
            $errors = [];

            foreach ($lines as $lineNumber => $line) {
                if (empty(trim($line))) {
                    continue;
                }

                $row = array_combine($headers, str_getcsv($line));

                try {
                    $this->create([
                        'employee_id' => (int) $row['Employee ID'],
                        'user_id' => (int) $row['User ID'],
                        'time_off_type' => $row['Time Off Type'],
                        'start_date' => $row['Start Date'],
                        'end_date' => $row['End Date'],
                        'reason' => $row['Reason'],
                        'status' => $row['Status'],
                        'approved_by' => $row['Approved By'] ? (int) $row['Approved By'] : null,
                        'approved_at' => $row['Approved At'] ?: null,
                        'rejected_by' => $row['Rejected By'] ? (int) $row['Rejected By'] : null,
                        'rejected_at' => $row['Rejected At'] ?: null,
                        'rejection_reason' => $row['Rejection Reason'] ?: null,
                        'is_half_day' => $row['Is Half Day'] === 'Yes',
                        'is_urgent' => $row['Is Urgent'] === 'Yes',
                    ]);

                    $imported++;

                } catch (\Exception $e) {
                    $errors[] = 'Line '.($lineNumber + 2).': '.$e->getMessage();
                }
            }

            if (! empty($errors)) {
                Log::warning('Time-off data import completed with errors', ['errors' => $errors, 'imported' => $imported]);
            }

            DB::commit();

            Log::info('Time-off data import completed', ['imported' => $imported, 'errors' => count($errors)]);

            return true;

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to import time-off data', ['error' => $e->getMessage()]);
            throw $e;
        }
    }

    // Statistics operations
    public function getTimeOffStatistics(?int $employeeId = null): array
    {
        $cacheKey = "{$this->cachePrefix}:stats".($employeeId ? ":employee:{$employeeId}" : '');

        return Cache::remember($cacheKey, $this->cacheTtl, function () use ($employeeId) {
            $query = $this->model;

            if ($employeeId) {
                $query->byEmployee($employeeId);
            }

            $total = $query->count();
            $pending = $query->clone()->pending()->count();
            $approved = $query->clone()->approved()->count();
            $rejected = $query->clone()->rejected()->count();
            $cancelled = $query->clone()->cancelled()->count();
            $urgent = $query->clone()->urgent()->count();

            $byType = [];
            foreach (TimeOffType::cases() as $type) {
                $byType[$type->value] = $query->clone()->byTimeOffType($type->value)->count();
            }

            return [
                'total' => $total,
                'pending' => $pending,
                'approved' => $approved,
                'rejected' => $rejected,
                'cancelled' => $cancelled,
                'urgent' => $urgent,
                'by_type' => $byType,
                'approval_rate' => $total > 0 ? round(($approved / $total) * 100, 2) : 0,
            ];
        });
    }

    public function getDepartmentTimeOffStatistics(int $departmentId): array
    {
        $cacheKey = "{$this->cachePrefix}:stats:department:{$departmentId}";

        return Cache::remember($cacheKey, $this->cacheTtl, function () {
            // This would need to join with employees table to filter by department
            // For now, returning basic stats
            return [
                'total_requests' => 0,
                'pending_requests' => 0,
                'approved_requests' => 0,
                'rejected_requests' => 0,
                'average_approval_time' => 0,
            ];
        });
    }

    public function getCompanyTimeOffStatistics(): array
    {
        $cacheKey = "{$this->cachePrefix}:stats:company";

        return Cache::remember($cacheKey, $this->cacheTtl, function () {
            $total = $this->model->count();
            $pending = $this->model->pending()->count();
            $approved = $this->model->approved()->count();
            $rejected = $this->model->rejected()->count();
            $cancelled = $this->model->cancelled()->count();
            $urgent = $this->model->urgent()->count();

            $byType = [];
            foreach (TimeOffType::cases() as $type) {
                $byType[$type->value] = $this->model->byTimeOffType($type->value)->count();
            }

            $byStatus = [];
            foreach (TimeOffStatus::cases() as $status) {
                $byStatus[$status->value] = $this->model->byStatus($status->value)->count();
            }

            return [
                'total_requests' => $total,
                'pending_requests' => $pending,
                'approved_requests' => $approved,
                'rejected_requests' => $rejected,
                'cancelled_requests' => $cancelled,
                'urgent_requests' => $urgent,
                'by_type' => $byType,
                'by_status' => $byStatus,
                'approval_rate' => $total > 0 ? round(($approved / $total) * 100, 2) : 0,
                'rejection_rate' => $total > 0 ? round(($rejected / $total) * 100, 2) : 0,
                'average_approval_time' => $this->calculateAverageApprovalTime(),
            ];
        });
    }

    // Helper methods
    protected function clearCache(): void
    {
        Cache::flush();
    }

    protected function calculateAverageApprovalTime(): float
    {
        $approvedRequests = $this->model->approved()
            ->whereNotNull('approved_at')
            ->whereNotNull('created_at')
            ->get();

        if ($approvedRequests->isEmpty()) {
            return 0;
        }

        $totalHours = $approvedRequests->sum(function ($request) {
            return $request->created_at->diffInHours($request->approved_at);
        });

        return round($totalHours / $approvedRequests->count(), 2);
    }
}
