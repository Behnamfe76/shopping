<?php

namespace Fereydooni\Shopping\Repositories;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Pagination\CursorPaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Fereydooni\Shopping\Repositories\Interfaces\EmployeeTrainingRepositoryInterface;
use Fereydooni\Shopping\Models\EmployeeTraining;
use Fereydooni\Shopping\DTOs\EmployeeTrainingDTO;
use Fereydooni\Shopping\Enums\TrainingStatus;
use Fereydooni\Shopping\Enums\TrainingType;
use Fereydooni\Shopping\Enums\TrainingMethod;
use Carbon\Carbon;

class EmployeeTrainingRepository implements EmployeeTrainingRepositoryInterface
{
    protected $model;
    protected $cachePrefix = 'employee_training_';
    protected $cacheTtl = 3600; // 1 hour

    public function __construct(EmployeeTraining $model)
    {
        $this->model = $model;
    }

    public function all(): Collection
    {
        return Cache::remember($this->cachePrefix . 'all', $this->cacheTtl, function () {
            return $this->model->with('employee')->get();
        });
    }

    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        return $this->model->with('employee')
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    public function simplePaginate(int $perPage = 15): Paginator
    {
        return $this->model->with('employee')
            ->orderBy('created_at', 'desc')
            ->simplePaginate($perPage);
    }

    public function cursorPaginate(int $perPage = 15, ?string $cursor = null): CursorPaginator
    {
        return $this->model->with('employee')
            ->orderBy('created_at', 'desc')
            ->cursorPaginate($perPage, ['*'], 'cursor', $cursor);
    }

    public function find(int $id): ?EmployeeTraining
    {
        return Cache::remember($this->cachePrefix . 'find_' . $id, $this->cacheTtl, function () use ($id) {
            return $this->model->with('employee')->find($id);
        });
    }

    public function findDTO(int $id): ?EmployeeTrainingDTO
    {
        $training = $this->find($id);
        return $training ? EmployeeTrainingDTO::fromModel($training) : null;
    }

    public function findByEmployeeId(int $employeeId): Collection
    {
        return Cache::remember($this->cachePrefix . 'employee_' . $employeeId, $this->cacheTtl, function () use ($employeeId) {
            return $this->model->with('employee')
                ->where('employee_id', $employeeId)
                ->orderBy('created_at', 'desc')
                ->get();
        });
    }

    public function findByEmployeeIdDTO(int $employeeId): Collection
    {
        $trainings = $this->findByEmployeeId($employeeId);
        return $trainings->map(fn($training) => EmployeeTrainingDTO::fromModel($training));
    }

    public function findByTrainingType(string $trainingType): Collection
    {
        return Cache::remember($this->cachePrefix . 'type_' . $trainingType, $this->cacheTtl, function () use ($trainingType) {
            return $this->model->with('employee')
                ->where('training_type', $trainingType)
                ->orderBy('created_at', 'desc')
                ->get();
        });
    }

    public function findByTrainingTypeDTO(string $trainingType): Collection
    {
        $trainings = $this->findByTrainingType($trainingType);
        return $trainings->map(fn($training) => EmployeeTrainingDTO::fromModel($training));
    }

    public function findByStatus(string $status): Collection
    {
        return Cache::remember($this->cachePrefix . 'status_' . $status, $this->cacheTtl, function () use ($status) {
            return $this->model->with('employee')
                ->where('status', $status)
                ->orderBy('created_at', 'desc')
                ->get();
        });
    }

    public function findByStatusDTO(string $status): Collection
    {
        $trainings = $this->findByStatus($status);
        return $trainings->map(fn($training) => EmployeeTrainingDTO::fromModel($training));
    }

    public function findByProvider(string $provider): Collection
    {
        return $this->model->with('employee')
            ->where('provider', 'like', "%{$provider}%")
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function findByProviderDTO(string $provider): Collection
    {
        $trainings = $this->findByProvider($provider);
        return $trainings->map(fn($training) => EmployeeTrainingDTO::fromModel($training));
    }

    public function findByDateRange(string $startDate, string $endDate): Collection
    {
        return $this->model->with('employee')
            ->whereBetween('start_date', [$startDate, $endDate])
            ->orWhereBetween('end_date', [$startDate, $endDate])
            ->orderBy('start_date', 'asc')
            ->get();
    }

    public function findByDateRangeDTO(string $startDate, string $endDate): Collection
    {
        $trainings = $this->findByDateRange($startDate, $endDate);
        return $trainings->map(fn($training) => EmployeeTrainingDTO::fromModel($training));
    }

    public function findByEmployeeAndType(int $employeeId, string $trainingType): Collection
    {
        return $this->model->with('employee')
            ->where('employee_id', $employeeId)
            ->where('training_type', $trainingType)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function findByEmployeeAndTypeDTO(int $employeeId, string $trainingType): Collection
    {
        $trainings = $this->findByEmployeeAndType($employeeId, $trainingType);
        return $trainings->map(fn($training) => EmployeeTrainingDTO::fromModel($training));
    }

    public function findCompleted(): Collection
    {
        return Cache::remember($this->cachePrefix . 'completed', $this->cacheTtl, function () {
            return $this->model->with('employee')->completed()->get();
        });
    }

    public function findCompletedDTO(): Collection
    {
        $trainings = $this->findCompleted();
        return $trainings->map(fn($training) => EmployeeTrainingDTO::fromModel($training));
    }

    public function findInProgress(): Collection
    {
        return Cache::remember($this->cachePrefix . 'in_progress', $this->cacheTtl, function () {
            return $this->model->with('employee')
                ->where('status', TrainingStatus::IN_PROGRESS)
                ->get();
        });
    }

    public function findInProgressDTO(): Collection
    {
        $trainings = $this->findInProgress();
        return $trainings->map(fn($training) => EmployeeTrainingDTO::fromModel($training));
    }

    public function findNotStarted(): Collection
    {
        return Cache::remember($this->cachePrefix . 'not_started', $this->cacheTtl, function () {
            return $this->model->with('employee')
                ->where('status', TrainingStatus::NOT_STARTED)
                ->get();
        });
    }

    public function findNotStartedDTO(): Collection
    {
        $trainings = $this->findNotStarted();
        return $trainings->map(fn($training) => EmployeeTrainingDTO::fromModel($training));
    }

    public function findFailed(): Collection
    {
        return Cache::remember($this->cachePrefix . 'failed', $this->cacheTtl, function () {
            return $this->model->with('employee')
                ->where('status', TrainingStatus::FAILED)
                ->get();
        });
    }

    public function findFailedDTO(): Collection
    {
        $trainings = $this->findFailed();
        return $trainings->map(fn($training) => EmployeeTrainingDTO::fromModel($training));
    }

    public function findMandatory(): Collection
    {
        return Cache::remember($this->cachePrefix . 'mandatory', $this->cacheTtl, function () {
            return $this->model->with('employee')->mandatory()->get();
        });
    }

    public function findMandatoryDTO(): Collection
    {
        $trainings = $this->findMandatory();
        return $trainings->map(fn($training) => EmployeeTrainingDTO::fromModel($training));
    }

    public function findCertifications(): Collection
    {
        return Cache::remember($this->cachePrefix . 'certifications', $this->cacheTtl, function () {
            return $this->model->with('employee')->certifications()->get();
        });
    }

    public function findCertificationsDTO(): Collection
    {
        $trainings = $this->findCertifications();
        return $trainings->map(fn($training) => EmployeeTrainingDTO::fromModel($training));
    }

    public function findExpiringSoon(int $days = 30): Collection
    {
        return Cache::remember($this->cachePrefix . 'expiring_' . $days, $this->cacheTtl, function () use ($days) {
            return $this->model->with('employee')->expiringSoon($days)->get();
        });
    }

    public function findExpiringSoonDTO(int $days = 30): Collection
    {
        $trainings = $this->findExpiringSoon($days);
        return $trainings->map(fn($training) => EmployeeTrainingDTO::fromModel($training));
    }

    public function findByInstructor(string $instructor): Collection
    {
        return $this->model->with('employee')
            ->where('instructor', 'like', "%{$instructor}%")
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function findByInstructorDTO(string $instructor): Collection
    {
        $trainings = $this->findByInstructor($instructor);
        return $trainings->map(fn($training) => EmployeeTrainingDTO::fromModel($training));
    }

    public function findByTrainingMethod(string $trainingMethod): Collection
    {
        return $this->model->with('employee')
            ->where('training_method', $trainingMethod)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function findByTrainingMethodDTO(string $trainingMethod): Collection
    {
        $trainings = $this->findByTrainingMethod($trainingMethod);
        return $trainings->map(fn($training) => EmployeeTrainingDTO::fromModel($training));
    }

    public function findByScoreRange(float $minScore, float $maxScore): Collection
    {
        return $this->model->with('employee')
            ->whereBetween('score', [$minScore, $maxScore])
            ->orderBy('score', 'desc')
            ->get();
    }

    public function findByScoreRangeDTO(float $minScore, float $maxScore): Collection
    {
        $trainings = $this->findByScoreRange($minScore, $maxScore);
        return $trainings->map(fn($training) => EmployeeTrainingDTO::fromModel($training));
    }

    public function create(array $data): EmployeeTraining
    {
        try {
            DB::beginTransaction();
            
            $training = $this->model->create($data);
            
            $this->clearCache();
            
            DB::commit();
            
            Log::info('Employee training created', ['id' => $training->id, 'employee_id' => $training->employee_id]);
            
            return $training;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to create employee training', ['error' => $e->getMessage(), 'data' => $data]);
            throw $e;
        }
    }

    public function createAndReturnDTO(array $data): EmployeeTrainingDTO
    {
        $training = $this->create($data);
        return EmployeeTrainingDTO::fromModel($training);
    }

    public function update(EmployeeTraining $training, array $data): bool
    {
        try {
            DB::beginTransaction();
            
            $updated = $training->update($data);
            
            if ($updated) {
                $this->clearCache();
            }
            
            DB::commit();
            
            Log::info('Employee training updated', ['id' => $training->id, 'employee_id' => $training->employee_id]);
            
            return $updated;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to update employee training', ['error' => $e->getMessage(), 'id' => $training->id]);
            throw $e;
        }
    }

    public function updateAndReturnDTO(EmployeeTraining $training, array $data): ?EmployeeTrainingDTO
    {
        $updated = $this->update($training, $data);
        return $updated ? EmployeeTrainingDTO::fromModel($training->fresh()) : null;
    }

    public function delete(EmployeeTraining $training): bool
    {
        try {
            DB::beginTransaction();
            
            $deleted = $training->delete();
            
            if ($deleted) {
                $this->clearCache();
            }
            
            DB::commit();
            
            Log::info('Employee training deleted', ['id' => $training->id, 'employee_id' => $training->employee_id]);
            
            return $deleted;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to delete employee training', ['error' => $e->getMessage(), 'id' => $training->id]);
            throw $e;
        }
    }

    public function start(EmployeeTraining $training): bool
    {
        return $this->update($training, [
            'status' => TrainingStatus::IN_PROGRESS,
            'start_date' => now(),
        ]);
    }

    public function complete(EmployeeTraining $training, ?float $score = null, ?string $grade = null): bool
    {
        $data = [
            'status' => TrainingStatus::COMPLETED,
            'completion_date' => now(),
        ];

        if ($score !== null) {
            $data['score'] = $score;
        }

        if ($grade !== null) {
            $data['grade'] = $grade;
        }

        return $this->update($training, $data);
    }

    public function fail(EmployeeTraining $training, ?string $reason = null): bool
    {
        $data = [
            'status' => TrainingStatus::FAILED,
        ];

        if ($reason !== null) {
            $data['failure_reason'] = $reason;
        }

        return $this->update($training, $data);
    }

    public function cancel(EmployeeTraining $training, ?string $reason = null): bool
    {
        $data = [
            'status' => TrainingStatus::CANCELLED,
        ];

        if ($reason !== null) {
            $data['cancellation_reason'] = $reason;
        }

        return $this->update($training, $data);
    }

    public function renew(EmployeeTraining $training, ?string $renewalDate = null): bool
    {
        if (!$training->canBeRenewed()) {
            return false;
        }

        $renewalDate = $renewalDate ?: now()->addYear();

        return $this->update($training, [
            'renewal_date' => $renewalDate,
            'expiry_date' => $renewalDate->addYear(),
        ]);
    }

    public function updateProgress(EmployeeTraining $training, float $hoursCompleted): bool
    {
        return $this->update($training, [
            'hours_completed' => $hoursCompleted,
        ]);
    }

    public function getEmployeeTrainingCount(int $employeeId): int
    {
        return Cache::remember($this->cachePrefix . 'count_employee_' . $employeeId, $this->cacheTtl, function () use ($employeeId) {
            return $this->model->where('employee_id', $employeeId)->count();
        });
    }

    public function getEmployeeTrainingCountByType(int $employeeId, string $trainingType): int
    {
        return Cache::remember($this->cachePrefix . 'count_employee_type_' . $employeeId . '_' . $trainingType, $this->cacheTtl, function () use ($employeeId, $trainingType) {
            return $this->model->where('employee_id', $employeeId)
                ->where('training_type', $trainingType)
                ->count();
        });
    }

    public function getEmployeeTrainingCountByStatus(int $employeeId, string $status): int
    {
        return Cache::remember($this->cachePrefix . 'count_employee_status_' . $employeeId . '_' . $status, $this->cacheTtl, function () use ($employeeId, $status) {
            return $this->model->where('employee_id', $employeeId)
                ->where('status', $status)
                ->count();
        });
    }

    public function getEmployeeTotalHours(int $employeeId): float
    {
        return Cache::remember($this->cachePrefix . 'hours_employee_' . $employeeId, $this->cacheTtl, function () use ($employeeId) {
            return $this->model->where('employee_id', $employeeId)
                ->where('status', TrainingStatus::COMPLETED)
                ->sum('total_hours');
        });
    }

    public function getEmployeeTotalCost(int $employeeId): float
    {
        return Cache::remember($this->cachePrefix . 'cost_employee_' . $employeeId, $this->cacheTtl, function () use ($employeeId) {
            return $this->model->where('employee_id', $employeeId)->sum('cost');
        });
    }

    public function getEmployeeAverageScore(int $employeeId): float
    {
        return Cache::remember($this->cachePrefix . 'score_employee_' . $employeeId, $this->cacheTtl, function () use ($employeeId) {
            return $this->model->where('employee_id', $employeeId)
                ->where('status', TrainingStatus::COMPLETED)
                ->whereNotNull('score')
                ->avg('score') ?: 0;
        });
    }

    public function getEmployeeCertifications(int $employeeId): Collection
    {
        return Cache::remember($this->cachePrefix . 'certifications_employee_' . $employeeId, $this->cacheTtl, function () use ($employeeId) {
            return $this->model->with('employee')
                ->where('employee_id', $employeeId)
                ->where('is_certification', true)
                ->where('status', TrainingStatus::COMPLETED)
                ->get();
        });
    }

    public function getEmployeeCertificationsDTO(int $employeeId): Collection
    {
        $trainings = $this->getEmployeeCertifications($employeeId);
        return $trainings->map(fn($training) => EmployeeTrainingDTO::fromModel($training));
    }

    public function getEmployeeMandatoryTrainings(int $employeeId): Collection
    {
        return Cache::remember($this->cachePrefix . 'mandatory_employee_' . $employeeId, $this->cacheTtl, function () use ($employeeId) {
            return $this->model->with('employee')
                ->where('employee_id', $employeeId)
                ->where('is_mandatory', true)
                ->get();
        });
    }

    public function getEmployeeMandatoryTrainingsDTO(int $employeeId): Collection
    {
        $trainings = $this->getEmployeeMandatoryTrainings($employeeId);
        return $trainings->map(fn($training) => EmployeeTrainingDTO::fromModel($training));
    }

    public function getTotalTrainingCount(): int
    {
        return Cache::remember($this->cachePrefix . 'total_count', $this->cacheTtl, function () {
            return $this->model->count();
        });
    }

    public function getTotalTrainingCountByType(string $trainingType): int
    {
        return Cache::remember($this->cachePrefix . 'total_count_type_' . $trainingType, $this->cacheTtl, function () use ($trainingType) {
            return $this->model->where('training_type', $trainingType)->count();
        });
    }

    public function getTotalTrainingCountByStatus(string $status): int
    {
        return Cache::remember($this->cachePrefix . 'total_count_status_' . $status, $this->cacheTtl, function () use ($status) {
            return $this->model->where('status', $status)->count();
        });
    }

    public function getTotalHours(): float
    {
        return Cache::remember($this->cachePrefix . 'total_hours', $this->cacheTtl, function () {
            return $this->model->where('status', TrainingStatus::COMPLETED)->sum('total_hours');
        });
    }

    public function getTotalCost(): float
    {
        return Cache::remember($this->cachePrefix . 'total_cost', $this->cacheTtl, function () {
            return $this->model->sum('cost');
        });
    }

    public function getAverageScore(): float
    {
        return Cache::remember($this->cachePrefix . 'average_score', $this->cacheTtl, function () {
            return $this->model->where('status', TrainingStatus::COMPLETED)
                ->whereNotNull('score')
                ->avg('score') ?: 0;
        });
    }

    public function getCompletedTrainingsCount(): int
    {
        return $this->getTotalTrainingCountByStatus(TrainingStatus::COMPLETED->value);
    }

    public function getInProgressTrainingsCount(): int
    {
        return $this->getTotalTrainingCountByStatus(TrainingStatus::IN_PROGRESS->value);
    }

    public function getFailedTrainingsCount(): int
    {
        return $this->getTotalTrainingCountByStatus(TrainingStatus::FAILED->value);
    }

    public function getExpiringCertificationsCount(int $days = 30): int
    {
        return Cache::remember($this->cachePrefix . 'expiring_certifications_' . $days, $this->cacheTtl, function () use ($days) {
            return $this->model->where('is_certification', true)
                ->where('status', TrainingStatus::COMPLETED)
                ->where('expiry_date', '<=', now()->addDays($days))
                ->where('expiry_date', '>', now())
                ->count();
        });
    }

    public function searchTrainings(string $query): Collection
    {
        return $this->model->with('employee')
            ->where(function ($q) use ($query) {
                $q->where('training_name', 'like', "%{$query}%")
                  ->orWhere('provider', 'like', "%{$query}%")
                  ->orWhere('description', 'like', "%{$query}%")
                  ->orWhere('instructor', 'like', "%{$query}%");
            })
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function searchTrainingsDTO(string $query): Collection
    {
        $trainings = $this->searchTrainings($query);
        return $trainings->map(fn($training) => EmployeeTrainingDTO::fromModel($training));
    }

    public function searchTrainingsByEmployee(int $employeeId, string $query): Collection
    {
        return $this->model->with('employee')
            ->where('employee_id', $employeeId)
            ->where(function ($q) use ($query) {
                $q->where('training_name', 'like', "%{$query}%")
                  ->orWhere('provider', 'like', "%{$query}%")
                  ->orWhere('description', 'like', "%{$query}%");
            })
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function searchTrainingsByEmployeeDTO(int $employeeId, string $query): Collection
    {
        $trainings = $this->searchTrainingsByEmployee($employeeId, $query);
        return $trainings->map(fn($training) => EmployeeTrainingDTO::fromModel($training));
    }

    public function exportTrainingData(array $filters = []): string
    {
        $query = $this->model->with('employee');

        if (isset($filters['employee_id'])) {
            $query->where('employee_id', $filters['employee_id']);
        }

        if (isset($filters['training_type'])) {
            $query->where('training_type', $filters['training_type']);
        }

        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (isset($filters['start_date'])) {
            $query->where('start_date', '>=', $filters['start_date']);
        }

        if (isset($filters['end_date'])) {
            $query->where('end_date', '<=', $filters['end_date']);
        }

        $trainings = $query->get();

        // Convert to CSV format
        $csv = "ID,Employee ID,Employee Name,Training Type,Training Name,Provider,Status,Start Date,End Date,Completion Date,Score,Grade,Hours Completed,Total Hours,Cost\n";

        foreach ($trainings as $training) {
            $csv .= sprintf(
                "%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s\n",
                $training->id,
                $training->employee_id,
                $training->employee ? $training->employee->first_name . ' ' . $training->employee->last_name : 'N/A',
                $training->training_type->value,
                $training->training_name,
                $training->provider,
                $training->status->value,
                $training->start_date?->toDateString() ?: 'N/A',
                $training->end_date?->toDateString() ?: 'N/A',
                $training->completion_date?->toDateString() ?: 'N/A',
                $training->score ?: 'N/A',
                $training->grade ?: 'N/A',
                $training->hours_completed ?: 'N/A',
                $training->total_hours ?: 'N/A',
                $training->cost ?: 'N/A'
            );
        }

        return $csv;
    }

    public function importTrainingData(string $data): bool
    {
        try {
            DB::beginTransaction();

            $lines = explode("\n", trim($data));
            $headers = str_getcsv(array_shift($lines));

            foreach ($lines as $line) {
                if (empty(trim($line))) continue;

                $row = array_combine($headers, str_getcsv($line));
                
                if (isset($row['Employee ID']) && isset($row['Training Name'])) {
                    $this->model->create([
                        'employee_id' => $row['Employee ID'],
                        'training_name' => $row['Training Name'],
                        'provider' => $row['Provider'] ?? 'Unknown',
                        'training_type' => $row['Training Type'] ?? TrainingType::OTHER->value,
                        'status' => $row['Status'] ?? TrainingStatus::NOT_STARTED->value,
                        'training_method' => $row['Training Method'] ?? TrainingMethod::ONLINE->value,
                        'is_mandatory' => false,
                        'is_certification' => false,
                        'is_renewable' => false,
                    ]);
                }
            }

            $this->clearCache();
            
            DB::commit();
            
            Log::info('Training data imported successfully');
            
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to import training data', ['error' => $e->getMessage()]);
            throw $e;
        }
    }

    public function getTrainingStatistics(?int $employeeId = null): array
    {
        $cacheKey = $this->cachePrefix . 'stats' . ($employeeId ? '_employee_' . $employeeId : '');
        
        return Cache::remember($cacheKey, $this->cacheTtl, function () use ($employeeId) {
            $query = $this->model;
            
            if ($employeeId) {
                $query->where('employee_id', $employeeId);
            }

            return [
                'total_trainings' => $query->count(),
                'completed_trainings' => $query->where('status', TrainingStatus::COMPLETED)->count(),
                'in_progress_trainings' => $query->where('status', TrainingStatus::IN_PROGRESS)->count(),
                'not_started_trainings' => $query->where('status', TrainingStatus::NOT_STARTED)->count(),
                'failed_trainings' => $query->where('status', TrainingStatus::FAILED)->count(),
                'total_hours' => $query->where('status', TrainingStatus::COMPLETED)->sum('total_hours'),
                'total_cost' => $query->sum('cost'),
                'average_score' => $query->where('status', TrainingStatus::COMPLETED)->whereNotNull('score')->avg('score') ?: 0,
                'mandatory_trainings' => $query->where('is_mandatory', true)->count(),
                'certifications' => $query->where('is_certification', true)->count(),
            ];
        });
    }

    public function getDepartmentTrainingStatistics(int $departmentId): array
    {
        return Cache::remember($this->cachePrefix . 'stats_department_' . $departmentId, $this->cacheTtl, function () use ($departmentId) {
            $employeeIds = DB::table('employees')->where('department_id', $departmentId)->pluck('id');
            
            return $this->getTrainingStatistics($employeeIds->toArray());
        });
    }

    public function getCompanyTrainingStatistics(): array
    {
        return $this->getTrainingStatistics();
    }

    public function getTrainingEffectiveness(?int $employeeId = null): array
    {
        $cacheKey = $this->cachePrefix . 'effectiveness' . ($employeeId ? '_employee_' . $employeeId : '');
        
        return Cache::remember($cacheKey, $this->cacheTtl, function () use ($employeeId) {
            $query = $this->model;
            
            if ($employeeId) {
                $query->where('employee_id', $employeeId);
            }

            $completedTrainings = $query->where('status', TrainingStatus::COMPLETED)->get();
            
            $effectiveness = [
                'completion_rate' => 0,
                'average_score' => 0,
                'pass_rate' => 0,
                'certification_rate' => 0,
            ];

            if ($completedTrainings->count() > 0) {
                $effectiveness['completion_rate'] = ($completedTrainings->count() / $query->count()) * 100;
                $effectiveness['average_score'] = $completedTrainings->whereNotNull('score')->avg('score') ?: 0;
                $effectiveness['pass_rate'] = ($completedTrainings->where('score', '>=', 70)->count() / $completedTrainings->count()) * 100;
                $effectiveness['certification_rate'] = ($completedTrainings->where('is_certification', true)->count() / $completedTrainings->count()) * 100;
            }

            return $effectiveness;
        });
    }

    public function getTrainingTrends(?string $startDate = null, ?string $endDate = null): array
    {
        $cacheKey = $this->cachePrefix . 'trends_' . ($startDate ?: 'all') . '_' . ($endDate ?: 'all');
        
        return Cache::remember($cacheKey, $this->cacheTtl, function () use ($startDate, $endDate) {
            $query = $this->model;
            
            if ($startDate) {
                $query->where('created_at', '>=', $startDate);
            }
            
            if ($endDate) {
                $query->where('created_at', '<=', $endDate);
            }

            $trainings = $query->selectRaw('DATE(created_at) as date, COUNT(*) as count')
                ->groupBy('date')
                ->orderBy('date')
                ->get();

            $trends = [];
            foreach ($trainings as $training) {
                $trends[$training->date] = $training->count;
            }

            return $trends;
        });
    }

    /**
     * Clear all cached data.
     */
    protected function clearCache(): void
    {
        Cache::flush();
    }
}
