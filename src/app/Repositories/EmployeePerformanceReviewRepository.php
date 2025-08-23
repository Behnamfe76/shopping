<?php

namespace App\Repositories;

use App\Repositories\Interfaces\EmployeePerformanceReviewRepositoryInterface;
use App\Models\EmployeePerformanceReview;
use App\DTOs\EmployeePerformanceReviewDTO;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Pagination\CursorPaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class EmployeePerformanceReviewRepository implements EmployeePerformanceReviewRepositoryInterface
{
    protected $model;
    protected $cachePrefix = 'employee_performance_review_';
    protected $cacheTtl = 3600; // 1 hour

    public function __construct(EmployeePerformanceReview $model)
    {
        $this->model = $model;
    }

    // Basic CRUD operations
    public function all(): Collection
    {
        return Cache::remember($this->cachePrefix . 'all', $this->cacheTtl, function () {
            return $this->model->with(['employee', 'reviewer', 'approver'])->get();
        });
    }

    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        return $this->model->with(['employee', 'reviewer', 'approver'])
                          ->orderBy('created_at', 'desc')
                          ->paginate($perPage);
    }

    public function simplePaginate(int $perPage = 15): Paginator
    {
        return $this->model->with(['employee', 'reviewer', 'approver'])
                          ->orderBy('created_at', 'desc')
                          ->simplePaginate($perPage);
    }

    public function cursorPaginate(int $perPage = 15, string $cursor = null): CursorPaginator
    {
        return $this->model->with(['employee', 'reviewer', 'approver'])
                          ->orderBy('created_at', 'desc')
                          ->cursorPaginate($perPage, ['*'], 'cursor', $cursor);
    }

    public function find(int $id): ?EmployeePerformanceReview
    {
        return Cache::remember($this->cachePrefix . 'find_' . $id, $this->cacheTtl, function () use ($id) {
            return $this->model->with(['employee', 'reviewer', 'approver'])->find($id);
        });
    }

    public function findDTO(int $id): ?EmployeePerformanceReviewDTO
    {
        $model = $this->find($id);
        return $model ? EmployeePerformanceReviewDTO::fromModel($model) : null;
    }

    public function create(array $data): EmployeePerformanceReview
    {
        try {
            DB::beginTransaction();

            $review = $this->model->create($data);

            $this->clearCache();

            DB::commit();

            Log::info('Employee performance review created', ['id' => $review->id, 'employee_id' => $review->employee_id]);

            return $review->load(['employee', 'reviewer']);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to create employee performance review', ['error' => $e->getMessage(), 'data' => $data]);
            throw $e;
        }
    }

    public function createAndReturnDTO(array $data): EmployeePerformanceReviewDTO
    {
        $review = $this->create($data);
        return EmployeePerformanceReviewDTO::fromModel($review);
    }

    public function update(EmployeePerformanceReview $review, array $data): bool
    {
        try {
            DB::beginTransaction();

            $result = $review->update($data);

            $this->clearCache();

            DB::commit();

            if ($result) {
                Log::info('Employee performance review updated', ['id' => $review->id]);
            }

            return $result;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to update employee performance review', ['error' => $e->getMessage(), 'id' => $review->id]);
            throw $e;
        }
    }

    public function updateAndReturnDTO(EmployeePerformanceReview $review, array $data): ?EmployeePerformanceReviewDTO
    {
        $result = $this->update($review, $data);
        return $result ? EmployeePerformanceReviewDTO::fromModel($review->fresh()) : null;
    }

    public function delete(EmployeePerformanceReview $review): bool
    {
        try {
            DB::beginTransaction();

            $result = $review->delete();

            $this->clearCache();

            DB::commit();

            if ($result) {
                Log::info('Employee performance review deleted', ['id' => $review->id]);
            }

            return $result;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to delete employee performance review', ['error' => $e->getMessage(), 'id' => $review->id]);
            throw $e;
        }
    }

    // Find by specific criteria
    public function findByEmployeeId(int $employeeId): Collection
    {
        return Cache::remember($this->cachePrefix . 'employee_' . $employeeId, $this->cacheTtl, function () use ($employeeId) {
            return $this->model->byEmployee($employeeId)
                              ->with(['employee', 'reviewer', 'approver'])
                              ->orderBy('review_date', 'desc')
                              ->get();
        });
    }

    public function findByEmployeeIdDTO(int $employeeId): Collection
    {
        return $this->findByEmployeeId($employeeId)->map(function ($review) {
            return EmployeePerformanceReviewDTO::fromModel($review);
        });
    }

    public function findByReviewerId(int $reviewerId): Collection
    {
        return Cache::remember($this->cachePrefix . 'reviewer_' . $reviewerId, $this->cacheTtl, function () use ($reviewerId) {
            return $this->model->byReviewer($reviewerId)
                              ->with(['employee', 'reviewer', 'approver'])
                              ->orderBy('review_date', 'desc')
                              ->get();
        });
    }

    public function findByReviewerIdDTO(int $reviewerId): Collection
    {
        return $this->findByReviewerId($reviewerId)->map(function ($review) {
            return EmployeePerformanceReviewDTO::fromModel($review);
        });
    }

    public function findByStatus(string $status): Collection
    {
        return Cache::remember($this->cachePrefix . 'status_' . $status, $this->cacheTtl, function () use ($status) {
            return $this->model->byStatus($status)
                              ->with(['employee', 'reviewer', 'approver'])
                              ->orderBy('created_at', 'desc')
                              ->get();
        });
    }

    public function findByStatusDTO(string $status): Collection
    {
        return $this->findByStatus($status)->map(function ($review) {
            return EmployeePerformanceReviewDTO::fromModel($review);
        });
    }

    public function findByRatingRange(float $minRating, float $maxRating): Collection
    {
        return $this->model->byRatingRange($minRating, $maxRating)
                          ->with(['employee', 'reviewer', 'approver'])
                          ->orderBy('overall_rating', 'desc')
                          ->get();
    }

    public function findByRatingRangeDTO(float $minRating, float $maxRating): Collection
    {
        return $this->findByRatingRange($minRating, $maxRating)->map(function ($review) {
            return EmployeePerformanceReviewDTO::fromModel($review);
        });
    }

    public function findByDateRange(string $startDate, string $endDate): Collection
    {
        return $this->model->byDateRange($startDate, $endDate)
                          ->with(['employee', 'reviewer', 'approver'])
                          ->orderBy('review_date', 'desc')
                          ->get();
    }

    public function findByDateRangeDTO(string $startDate, string $endDate): Collection
    {
        return $this->findByDateRange($startDate, $endDate)->map(function ($review) {
            return EmployeePerformanceReviewDTO::fromModel($review);
        });
    }

    public function findByReviewPeriod(string $startDate, string $endDate): Collection
    {
        return $this->model->byReviewPeriod($startDate, $endDate)
                          ->with(['employee', 'reviewer', 'approver'])
                          ->orderBy('review_period_start', 'desc')
                          ->get();
    }

    public function findByReviewPeriodDTO(string $startDate, string $endDate): Collection
    {
        return $this->findByReviewPeriod($startDate, $endDate)->map(function ($review) {
            return EmployeePerformanceReviewDTO::fromModel($review);
        });
    }

    // Status-based queries
    public function findPendingApproval(): Collection
    {
        return Cache::remember($this->cachePrefix . 'pending_approval', $this->cacheTtl, function () {
            return $this->model->pendingApproval()
                              ->with(['employee', 'reviewer'])
                              ->orderBy('created_at', 'asc')
                              ->get();
        });
    }

    public function findPendingApprovalDTO(): Collection
    {
        return $this->findPendingApproval()->map(function ($review) {
            return EmployeePerformanceReviewDTO::fromModel($review);
        });
    }

    public function findApproved(): Collection
    {
        return Cache::remember($this->cachePrefix . 'approved', $this->cacheTtl, function () {
            return $this->model->approved()
                              ->with(['employee', 'reviewer', 'approver'])
                              ->orderBy('approved_at', 'desc')
                              ->get();
        });
    }

    public function findApprovedDTO(): Collection
    {
        return $this->findApproved()->map(function ($review) {
            return EmployeePerformanceReviewDTO::fromModel($review);
        });
    }

    public function findRejected(): Collection
    {
        return Cache::remember($this->cachePrefix . 'rejected', $this->cacheTtl, function () {
            return $this->model->rejected()
                              ->with(['employee', 'reviewer'])
                              ->orderBy('updated_at', 'desc')
                              ->get();
        });
    }

    public function findRejectedDTO(): Collection
    {
        return $this->findRejected()->map(function ($review) {
            return EmployeePerformanceReviewDTO::fromModel($review);
        });
    }

    public function findOverdue(): Collection
    {
        return Cache::remember($this->cachePrefix . 'overdue', $this->cacheTtl, function () {
            return $this->model->overdue()
                              ->with(['employee', 'reviewer'])
                              ->orderBy('next_review_date', 'asc')
                              ->get();
        });
    }

    public function findOverdueDTO(): Collection
    {
        return $this->findOverdue()->map(function ($review) {
            return EmployeePerformanceReviewDTO::fromModel($review);
        });
    }

    // Specialized queries
    public function findByEmployeeAndPeriod(int $employeeId, string $startDate, string $endDate): ?EmployeePerformanceReview
    {
        return $this->model->byEmployee($employeeId)
                          ->byReviewPeriod($startDate, $endDate)
                          ->with(['employee', 'reviewer', 'approver'])
                          ->first();
    }

    public function findByEmployeeAndPeriodDTO(int $employeeId, string $startDate, string $endDate): ?EmployeePerformanceReviewDTO
    {
        $review = $this->findByEmployeeAndPeriod($employeeId, $startDate, $endDate);
        return $review ? EmployeePerformanceReviewDTO::fromModel($review) : null;
    }

    public function findLatestByEmployee(int $employeeId): ?EmployeePerformanceReview
    {
        return $this->model->byEmployee($employeeId)
                          ->with(['employee', 'reviewer', 'approver'])
                          ->orderBy('review_date', 'desc')
                          ->first();
    }

    public function findLatestByEmployeeDTO(int $employeeId): ?EmployeePerformanceReviewDTO
    {
        $review = $this->findLatestByEmployee($employeeId);
        return $review ? EmployeePerformanceReviewDTO::fromModel($review) : null;
    }

    public function findUpcomingReviews(string $date = null): Collection
    {
        return $this->model->upcoming($date)
                          ->with(['employee', 'reviewer'])
                          ->orderBy('next_review_date', 'asc')
                          ->get();
    }

    public function findUpcomingReviewsDTO(string $date = null): Collection
    {
        return $this->findUpcomingReviews($date)->map(function ($review) {
            return EmployeePerformanceReviewDTO::fromModel($review);
        });
    }

    // Workflow operations
    public function approve(EmployeePerformanceReview $review, int $approvedBy): bool
    {
        return $review->approve($approvedBy);
    }

    public function reject(EmployeePerformanceReview $review, int $rejectedBy, string $reason = null): bool
    {
        return $review->reject($rejectedBy, $reason);
    }

    public function submitForApproval(EmployeePerformanceReview $review): bool
    {
        return $review->submitForApproval();
    }

    // Rating and statistics
    public function getEmployeeAverageRating(int $employeeId): float
    {
        return Cache::remember($this->cachePrefix . 'avg_rating_' . $employeeId, $this->cacheTtl, function () use ($employeeId) {
            return $this->model->byEmployee($employeeId)
                              ->where('status', 'approved')
                              ->avg('overall_rating') ?? 0.0;
        });
    }

    public function getEmployeeRatingHistory(int $employeeId): Collection
    {
        return $this->model->byEmployee($employeeId)
                          ->where('status', 'approved')
                          ->orderBy('review_date', 'desc')
                          ->get(['overall_rating', 'performance_score', 'review_date']);
    }

    public function getEmployeeRatingHistoryDTO(int $employeeId): Collection
    {
        return $this->getEmployeeRatingHistory($employeeId)->map(function ($review) {
            return EmployeePerformanceReviewDTO::fromModel($review);
        });
    }

    public function getDepartmentAverageRating(int $departmentId): float
    {
        return Cache::remember($this->cachePrefix . 'dept_avg_rating_' . $departmentId, $this->cacheTtl, function () use ($departmentId) {
            return $this->model->whereHas('employee', function ($query) use ($departmentId) {
                $query->where('department_id', $departmentId);
            })->where('status', 'approved')->avg('overall_rating') ?? 0.0;
        });
    }

    public function getCompanyAverageRating(): float
    {
        return Cache::remember($this->cachePrefix . 'company_avg_rating', $this->cacheTtl, function () {
            return $this->model->where('status', 'approved')->avg('overall_rating') ?? 0.0;
        });
    }

    // Statistics and analytics
    public function getReviewStatistics(): array
    {
        return Cache::remember($this->cachePrefix . 'statistics', $this->cacheTtl, function () {
            $total = $this->model->count();
            $draft = $this->model->where('status', 'draft')->count();
            $submitted = $this->model->where('status', 'submitted')->count();
            $pendingApproval = $this->model->where('status', 'pending_approval')->count();
            $approved = $this->model->where('status', 'approved')->count();
            $rejected = $this->model->where('status', 'rejected')->count();
            $overdue = $this->model->where('status', 'overdue')->count();

            return [
                'total' => $total,
                'draft' => $draft,
                'submitted' => $submitted,
                'pending_approval' => $pendingApproval,
                'approved' => $approved,
                'rejected' => $rejected,
                'overdue' => $overdue,
                'completion_rate' => $total > 0 ? (($approved + $rejected) / $total) * 100 : 0,
                'approval_rate' => ($approved + $rejected) > 0 ? ($approved / ($approved + $rejected)) * 100 : 0,
            ];
        });
    }

    public function getEmployeeReviewStatistics(int $employeeId): array
    {
        return Cache::remember($this->cachePrefix . 'employee_stats_' . $employeeId, $this->cacheTtl, function () use ($employeeId) {
            $reviews = $this->model->byEmployee($employeeId);
            $total = $reviews->count();
            $approved = $reviews->where('status', 'approved')->count();
            $rejected = $reviews->where('status', 'rejected')->count();
            $avgRating = $reviews->where('status', 'approved')->avg('overall_rating') ?? 0.0;
            $avgScore = $reviews->where('status', 'approved')->avg('performance_score') ?? 0.0;

            return [
                'total_reviews' => $total,
                'approved_reviews' => $approved,
                'rejected_reviews' => $rejected,
                'average_rating' => $avgRating,
                'average_score' => $avgScore,
                'approval_rate' => $total > 0 ? (($approved + $rejected) > 0 ? ($approved / ($approved + $rejected)) * 100 : 0) : 0,
            ];
        });
    }

    public function getDepartmentReviewStatistics(int $departmentId): array
    {
        return Cache::remember($this->cachePrefix . 'dept_stats_' . $departmentId, $this->cacheTtl, function () use ($departmentId) {
            $reviews = $this->model->whereHas('employee', function ($query) use ($departmentId) {
                $query->where('department_id', $departmentId);
            });

            $total = $reviews->count();
            $approved = $reviews->where('status', 'approved')->count();
            $rejected = $reviews->where('status', 'rejected')->count();
            $avgRating = $reviews->where('status', 'approved')->avg('overall_rating') ?? 0.0;
            $avgScore = $reviews->where('status', 'approved')->avg('performance_score') ?? 0.0;

            return [
                'total_reviews' => $total,
                'approved_reviews' => $approved,
                'rejected_reviews' => $rejected,
                'average_rating' => $avgRating,
                'average_score' => $avgScore,
                'approval_rate' => $total > 0 ? (($approved + $rejected) > 0 ? ($approved / ($approved + $rejected)) * 100 : 0) : 0,
            ];
        });
    }

    public function getOverdueReviewsCount(): int
    {
        return Cache::remember($this->cachePrefix . 'overdue_count', $this->cacheTtl, function () {
            return $this->model->overdue()->count();
        });
    }

    public function getPendingApprovalCount(): int
    {
        return Cache::remember($this->cachePrefix . 'pending_approval_count', $this->cacheTtl, function () {
            return $this->model->pendingApproval()->count();
        });
    }

    public function getCompletedReviewsCount(string $startDate = null, string $endDate = null): int
    {
        $query = $this->model->whereIn('status', ['approved', 'rejected']);

        if ($startDate && $endDate) {
            $query->whereBetween('review_date', [$startDate, $endDate]);
        }

        return $query->count();
    }

    public function getReviewCountByStatus(string $status): int
    {
        return $this->model->where('status', $status)->count();
    }

    public function getReviewCountByRating(float $rating): int
    {
        return $this->model->where('overall_rating', $rating)->count();
    }

    // Search functionality
    public function searchReviews(string $query): Collection
    {
        return $this->model->where(function ($q) use ($query) {
            $q->whereHas('employee', function ($subQ) use ($query) {
                $subQ->where('first_name', 'like', "%{$query}%")
                      ->orWhere('last_name', 'like', "%{$query}%")
                      ->orWhere('email', 'like', "%{$query}%");
            })
            ->orWhereHas('reviewer', function ($subQ) use ($query) {
                $subQ->where('name', 'like', "%{$query}%")
                      ->orWhere('email', 'like', "%{$query}%");
            })
            ->orWhere('strengths', 'like', "%{$query}%")
            ->orWhere('areas_for_improvement', 'like', "%{$query}%")
            ->orWhere('recommendations', 'like', "%{$query}%");
        })
        ->with(['employee', 'reviewer', 'approver'])
        ->orderBy('created_at', 'desc')
        ->get();
    }

    public function searchReviewsDTO(string $query): Collection
    {
        return $this->searchReviews($query)->map(function ($review) {
            return EmployeePerformanceReviewDTO::fromModel($review);
        });
    }

    public function searchReviewsByEmployee(int $employeeId, string $query): Collection
    {
        return $this->model->byEmployee($employeeId)
                          ->where(function ($q) use ($query) {
                              $q->where('strengths', 'like', "%{$query}%")
                                ->orWhere('areas_for_improvement', 'like', "%{$query}%")
                                ->orWhere('recommendations', 'like', "%{$query}%")
                                ->orWhere('employee_comments', 'like', "%{$query}%")
                                ->orWhere('reviewer_comments', 'like', "%{$query}%");
                          })
                          ->with(['employee', 'reviewer', 'approver'])
                          ->orderBy('created_at', 'desc')
                          ->get();
    }

    public function searchReviewsByEmployeeDTO(int $employeeId, string $query): Collection
    {
        return $this->searchReviewsByEmployee($employeeId, $query)->map(function ($review) {
            return EmployeePerformanceReviewDTO::fromModel($review);
        });
    }

    // Import/Export
    public function exportReviews(array $filters = []): string
    {
        $query = $this->model->with(['employee', 'reviewer', 'approver']);

        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (isset($filters['employee_id'])) {
            $query->where('employee_id', $filters['employee_id']);
        }

        if (isset($filters['date_range'])) {
            $query->whereBetween('review_date', $filters['date_range']);
        }

        $reviews = $query->get();

        // Convert to JSON for export
        return $reviews->toJson();
    }

    public function importReviews(string $data): bool
    {
        try {
            DB::beginTransaction();

            $reviews = json_decode($data, true);

            foreach ($reviews as $reviewData) {
                // Remove id and timestamps for import
                unset($reviewData['id'], $reviewData['created_at'], $reviewData['updated_at']);

                $this->model->create($reviewData);
            }

            $this->clearCache();

            DB::commit();

            Log::info('Employee performance reviews imported successfully', ['count' => count($reviews)]);

            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to import employee performance reviews', ['error' => $e->getMessage()]);
            throw $e;
        }
    }

    // Cache management
    protected function clearCache(): void
    {
        Cache::forget($this->cachePrefix . 'all');
        Cache::forget($this->cachePrefix . 'pending_approval');
        Cache::forget($this->cachePrefix . 'approved');
        Cache::forget($this->cachePrefix . 'rejected');
        Cache::forget($this->cachePrefix . 'overdue');
        Cache::forget($this->cachePrefix . 'statistics');
        Cache::forget($this->cachePrefix . 'pending_approval_count');
        Cache::forget($this->cachePrefix . 'overdue_count');
        Cache::forget($this->cachePrefix . 'company_avg_rating');

        // Clear employee-specific caches
        $employeeIds = $this->model->distinct('employee_id')->pluck('employee_id');
        foreach ($employeeIds as $employeeId) {
            Cache::forget($this->cachePrefix . 'employee_' . $employeeId);
            Cache::forget($this->cachePrefix . 'avg_rating_' . $employeeId);
            Cache::forget($this->cachePrefix . 'employee_stats_' . $employeeId);
        }

        // Clear reviewer-specific caches
        $reviewerIds = $this->model->distinct('reviewer_id')->pluck('reviewer_id');
        foreach ($reviewerIds as $reviewerId) {
            Cache::forget($this->cachePrefix . 'reviewer_' . $reviewerId);
        }

        // Clear status-specific caches
        $statuses = ['draft', 'submitted', 'pending_approval', 'approved', 'rejected', 'overdue'];
        foreach ($statuses as $status) {
            Cache::forget($this->cachePrefix . 'status_' . $status);
        }
    }
}
