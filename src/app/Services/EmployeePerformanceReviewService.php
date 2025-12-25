<?php

namespace App\Services;

use App\Actions\EmployeePerformanceReviewActions;
use App\DTOs\EmployeePerformanceReviewDTO;
use App\Repositories\Interfaces\EmployeePerformanceReviewRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class EmployeePerformanceReviewService
{
    protected $actions;

    protected $repository;

    protected $cachePrefix = 'employee_performance_review_service_';

    protected $cacheTtl = 1800; // 30 minutes

    public function __construct(
        EmployeePerformanceReviewActions $actions,
        EmployeePerformanceReviewRepositoryInterface $repository
    ) {
        $this->actions = $actions;
        $this->repository = $repository;
    }

    /**
     * Create a new performance review
     */
    public function createReview(array $data): EmployeePerformanceReviewDTO
    {
        try {
            $review = $this->actions->createReview($data);

            $this->clearCache();

            Log::info('Performance review created via service', [
                'review_id' => $review->id,
                'employee_id' => $review->employee_id,
            ]);

            return $review;
        } catch (\Exception $e) {
            Log::error('Service failed to create performance review', [
                'error' => $e->getMessage(),
                'data' => $data,
            ]);
            throw $e;
        }
    }

    /**
     * Update an existing performance review
     */
    public function updateReview(int $id, array $data): EmployeePerformanceReviewDTO
    {
        try {
            $review = $this->repository->find($id);

            if (! $review) {
                throw new \Exception('Performance review not found');
            }

            $updatedReview = $this->actions->updateReview($review, $data);

            $this->clearCache();

            Log::info('Performance review updated via service', [
                'review_id' => $id,
                'employee_id' => $updatedReview->employee_id,
            ]);

            return $updatedReview;
        } catch (\Exception $e) {
            Log::error('Service failed to update performance review', [
                'error' => $e->getMessage(),
                'review_id' => $id,
                'data' => $data,
            ]);
            throw $e;
        }
    }

    /**
     * Delete a performance review
     */
    public function deleteReview(int $id): bool
    {
        try {
            $review = $this->repository->find($id);

            if (! $review) {
                throw new \Exception('Performance review not found');
            }

            $result = $this->actions->deleteReview($review);

            if ($result) {
                $this->clearCache();

                Log::info('Performance review deleted via service', [
                    'review_id' => $id,
                    'employee_id' => $review->employee_id,
                ]);
            }

            return $result;
        } catch (\Exception $e) {
            Log::error('Service failed to delete performance review', [
                'error' => $e->getMessage(),
                'review_id' => $id,
            ]);
            throw $e;
        }
    }

    /**
     * Submit a review for approval
     */
    public function submitReview(int $id): bool
    {
        try {
            $review = $this->repository->find($id);

            if (! $review) {
                throw new \Exception('Performance review not found');
            }

            $result = $this->actions->submitReview($review);

            if ($result) {
                $this->clearCache();

                Log::info('Performance review submitted via service', [
                    'review_id' => $id,
                    'employee_id' => $review->employee_id,
                ]);
            }

            return $result;
        } catch (\Exception $e) {
            Log::error('Service failed to submit performance review', [
                'error' => $e->getMessage(),
                'review_id' => $id,
            ]);
            throw $e;
        }
    }

    /**
     * Approve a performance review
     */
    public function approveReview(int $id, int $approvedBy): bool
    {
        try {
            $review = $this->repository->find($id);

            if (! $review) {
                throw new \Exception('Performance review not found');
            }

            $result = $this->actions->approveReview($review, $approvedBy);

            if ($result) {
                $this->clearCache();

                Log::info('Performance review approved via service', [
                    'review_id' => $id,
                    'employee_id' => $review->employee_id,
                    'approved_by' => $approvedBy,
                ]);
            }

            return $result;
        } catch (\Exception $e) {
            Log::error('Service failed to approve performance review', [
                'error' => $e->getMessage(),
                'review_id' => $id,
                'approved_by' => $approvedBy,
            ]);
            throw $e;
        }
    }

    /**
     * Reject a performance review
     */
    public function rejectReview(int $id, int $rejectedBy, ?string $reason = null): bool
    {
        try {
            $review = $this->repository->find($id);

            if (! $review) {
                throw new \Exception('Performance review not found');
            }

            $result = $this->actions->rejectReview($review, $rejectedBy, $reason);

            if ($result) {
                $this->clearCache();

                Log::info('Performance review rejected via service', [
                    'review_id' => $id,
                    'employee_id' => $review->employee_id,
                    'rejected_by' => $rejectedBy,
                    'reason' => $reason,
                ]);
            }

            return $result;
        } catch (\Exception $e) {
            Log::error('Service failed to reject performance review', [
                'error' => $e->getMessage(),
                'review_id' => $id,
                'rejected_by' => $rejectedBy,
            ]);
            throw $e;
        }
    }

    /**
     * Get employee reviews with filters
     */
    public function getEmployeeReviews(int $employeeId, array $filters = []): Collection
    {
        $cacheKey = $this->cachePrefix.'employee_reviews_'.$employeeId.'_'.md5(serialize($filters));

        return Cache::remember($cacheKey, $this->cacheTtl, function () use ($employeeId, $filters) {
            $query = $this->repository->findByEmployeeId($employeeId);

            // Apply filters
            if (isset($filters['status'])) {
                $query = $query->filter(function ($review) use ($filters) {
                    return $review->status === $filters['status'];
                });
            }

            if (isset($filters['date_range'])) {
                $query = $query->filter(function ($review) use ($filters) {
                    return $review->review_date >= $filters['date_range'][0]
                           && $review->review_date <= $filters['date_range'][1];
                });
            }

            return $query;
        });
    }

    /**
     * Get employee's latest review
     */
    public function getEmployeeLatestReview(int $employeeId): ?EmployeePerformanceReviewDTO
    {
        return Cache::remember(
            $this->cachePrefix.'latest_review_'.$employeeId,
            $this->cacheTtl,
            function () use ($employeeId) {
                return $this->repository->findLatestByEmployeeDTO($employeeId);
            }
        );
    }

    /**
     * Get employee rating history
     */
    public function getEmployeeRatingHistory(int $employeeId): Collection
    {
        return Cache::remember(
            $this->cachePrefix.'rating_history_'.$employeeId,
            $this->cacheTtl,
            function () use ($employeeId) {
                return $this->repository->getEmployeeRatingHistoryDTO($employeeId);
            }
        );
    }

    /**
     * Get employee average rating
     */
    public function getEmployeeAverageRating(int $employeeId): float
    {
        return Cache::remember(
            $this->cachePrefix.'avg_rating_'.$employeeId,
            $this->cacheTtl,
            function () use ($employeeId) {
                return $this->actions->calculateEmployeeRating($employeeId);
            }
        );
    }

    /**
     * Get department reviews with filters
     */
    public function getDepartmentReviews(int $departmentId, array $filters = []): Collection
    {
        $cacheKey = $this->cachePrefix.'dept_reviews_'.$departmentId.'_'.md5(serialize($filters));

        return Cache::remember($cacheKey, $this->cacheTtl, function () {
            // This would need to be implemented in the repository
            // For now, return empty collection
            return collect();
        });
    }

    /**
     * Get department average rating
     */
    public function getDepartmentAverageRating(int $departmentId): float
    {
        return Cache::remember(
            $this->cachePrefix.'dept_avg_rating_'.$departmentId,
            $this->cacheTtl,
            function () use ($departmentId) {
                return $this->repository->getDepartmentAverageRating($departmentId);
            }
        );
    }

    /**
     * Get company reviews with filters
     */
    public function getCompanyReviews(array $filters = []): Collection
    {
        $cacheKey = $this->cachePrefix.'company_reviews_'.md5(serialize($filters));

        return Cache::remember($cacheKey, $this->cacheTtl, function () use ($filters) {
            $query = $this->repository->all();

            // Apply filters
            if (isset($filters['status'])) {
                $query = $query->filter(function ($review) use ($filters) {
                    return $review->status === $filters['status'];
                });
            }

            if (isset($filters['date_range'])) {
                $query = $query->filter(function ($review) use ($filters) {
                    return $review->review_date >= $filters['date_range'][0]
                           && $review->review_date <= $filters['date_range'][1];
                });
            }

            return $query;
        });
    }

    /**
     * Get company average rating
     */
    public function getCompanyAverageRating(): float
    {
        return Cache::remember(
            $this->cachePrefix.'company_avg_rating',
            $this->cacheTtl,
            function () {
                return $this->repository->getCompanyAverageRating();
            }
        );
    }

    /**
     * Get pending approvals
     */
    public function getPendingApprovals(): Collection
    {
        return Cache::remember(
            $this->cachePrefix.'pending_approvals',
            $this->cacheTtl,
            function () {
                return $this->repository->findPendingApprovalDTO();
            }
        );
    }

    /**
     * Get overdue reviews
     */
    public function getOverdueReviews(): Collection
    {
        return Cache::remember(
            $this->cachePrefix.'overdue_reviews',
            $this->cacheTtl,
            function () {
                return $this->repository->findOverdueDTO();
            }
        );
    }

    /**
     * Get upcoming reviews
     */
    public function getUpcomingReviews(?string $date = null): Collection
    {
        $cacheKey = $this->cachePrefix.'upcoming_reviews_'.($date ?? 'now');

        return Cache::remember($cacheKey, $this->cacheTtl, function () use ($date) {
            return $this->repository->findUpcomingReviewsDTO($date);
        });
    }

    /**
     * Search reviews
     */
    public function searchReviews(string $query, array $filters = []): Collection
    {
        $cacheKey = $this->cachePrefix.'search_'.md5($query.serialize($filters));

        return Cache::remember($cacheKey, $this->cacheTtl, function () use ($query, $filters) {
            $results = $this->repository->searchReviewsDTO($query);

            // Apply additional filters
            if (isset($filters['status'])) {
                $results = $results->filter(function ($review) use ($filters) {
                    return $review->status === $filters['status'];
                });
            }

            return $results;
        });
    }

    /**
     * Get review statistics
     */
    public function getReviewStatistics(array $filters = []): array
    {
        $cacheKey = $this->cachePrefix.'statistics_'.md5(serialize($filters));

        return Cache::remember($cacheKey, $this->cacheTtl, function () {
            return $this->repository->getReviewStatistics();
        });
    }

    /**
     * Get employee review statistics
     */
    public function getEmployeeReviewStatistics(int $employeeId): array
    {
        return Cache::remember(
            $this->cachePrefix.'employee_stats_'.$employeeId,
            $this->cacheTtl,
            function () use ($employeeId) {
                return $this->repository->getEmployeeReviewStatistics($employeeId);
            }
        );
    }

    /**
     * Get department review statistics
     */
    public function getDepartmentReviewStatistics(int $departmentId): array
    {
        return Cache::remember(
            $this->cachePrefix.'dept_stats_'.$departmentId,
            $this->cacheTtl,
            function () use ($departmentId) {
                return $this->repository->getDepartmentReviewStatistics($departmentId);
            }
        );
    }

    /**
     * Generate performance report
     */
    public function generatePerformanceReport(int $employeeId, string $period = 'year'): array
    {
        $cacheKey = $this->cachePrefix.'report_'.$employeeId.'_'.$period;

        return Cache::remember($cacheKey, $this->cacheTtl, function () use ($employeeId, $period) {
            return $this->actions->generatePerformanceReport($employeeId, $period);
        });
    }

    /**
     * Generate department report
     */
    public function generateDepartmentReport(int $departmentId, string $period = 'year'): array
    {
        $cacheKey = $this->cachePrefix.'dept_report_'.$departmentId.'_'.$period;

        return Cache::remember($cacheKey, $this->cacheTtl, function () use ($departmentId, $period) {
            // This would need to be implemented
            return [
                'department_id' => $departmentId,
                'period' => $period,
                'total_employees' => 0,
                'average_rating' => 0.0,
                'completion_rate' => 0.0,
            ];
        });
    }

    /**
     * Generate company report
     */
    public function generateCompanyReport(string $period = 'year'): array
    {
        $cacheKey = $this->cachePrefix.'company_report_'.$period;

        return Cache::remember($cacheKey, $this->cacheTtl, function () use ($period) {
            $statistics = $this->repository->getReviewStatistics();

            return [
                'period' => $period,
                'total_reviews' => $statistics['total'],
                'completion_rate' => $statistics['completion_rate'],
                'approval_rate' => $statistics['approval_rate'],
                'average_rating' => $this->getCompanyAverageRating(),
                'overdue_count' => $this->repository->getOverdueReviewsCount(),
                'pending_approval_count' => $this->repository->getPendingApprovalCount(),
            ];
        });
    }

    /**
     * Send review reminders
     */
    public function sendReviewReminders(): bool
    {
        try {
            $overdueReviews = $this->repository->findOverdue();
            $upcomingReviews = $this->repository->findUpcomingReviews();

            $remindersSent = 0;

            // Send reminders for overdue reviews
            foreach ($overdueReviews as $review) {
                if ($this->actions->sendReviewReminder($review)) {
                    $remindersSent++;
                }
            }

            // Send reminders for upcoming reviews
            foreach ($upcomingReviews as $review) {
                if ($this->actions->sendReviewReminder($review)) {
                    $remindersSent++;
                }
            }

            Log::info('Review reminders sent', ['count' => $remindersSent]);

            return $remindersSent > 0;
        } catch (\Exception $e) {
            Log::error('Failed to send review reminders', ['error' => $e->getMessage()]);
            throw $e;
        }
    }

    /**
     * Schedule a review
     */
    public function scheduleReview(int $employeeId, string $reviewDate): bool
    {
        try {
            $result = $this->actions->scheduleReview($employeeId, $reviewDate);

            if ($result) {
                $this->clearCache();

                Log::info('Review scheduled via service', [
                    'employee_id' => $employeeId,
                    'review_date' => $reviewDate,
                ]);
            }

            return $result;
        } catch (\Exception $e) {
            Log::error('Service failed to schedule review', [
                'error' => $e->getMessage(),
                'employee_id' => $employeeId,
                'review_date' => $reviewDate,
            ]);
            throw $e;
        }
    }

    /**
     * Assign reviewer to a review
     */
    public function assignReviewer(int $reviewId, int $reviewerId): bool
    {
        try {
            $review = $this->repository->find($reviewId);

            if (! $review) {
                throw new \Exception('Performance review not found');
            }

            $result = $this->actions->assignReviewer($review, $reviewerId);

            if ($result) {
                $this->clearCache();

                Log::info('Reviewer assigned via service', [
                    'review_id' => $reviewId,
                    'reviewer_id' => $reviewerId,
                ]);
            }

            return $result;
        } catch (\Exception $e) {
            Log::error('Service failed to assign reviewer', [
                'error' => $e->getMessage(),
                'review_id' => $reviewId,
                'reviewer_id' => $reviewerId,
            ]);
            throw $e;
        }
    }

    /**
     * Export reviews
     */
    public function exportReviews(array $filters = [], string $format = 'json'): string
    {
        try {
            $data = $this->actions->exportPerformanceData($filters);

            Log::info('Reviews exported via service', [
                'format' => $format,
                'filters' => $filters,
            ]);

            return $data;
        } catch (\Exception $e) {
            Log::error('Service failed to export reviews', [
                'error' => $e->getMessage(),
                'filters' => $filters,
                'format' => $format,
            ]);
            throw $e;
        }
    }

    /**
     * Import reviews
     */
    public function importReviews(string $data, string $format = 'json'): bool
    {
        try {
            $result = $this->actions->importPerformanceData($data);

            if ($result) {
                $this->clearCache();

                Log::info('Reviews imported via service', [
                    'format' => $format,
                ]);
            }

            return $result;
        } catch (\Exception $e) {
            Log::error('Service failed to import reviews', [
                'error' => $e->getMessage(),
                'format' => $format,
            ]);
            throw $e;
        }
    }

    /**
     * Bulk approve reviews
     */
    public function bulkApproveReviews(array $reviewIds): bool
    {
        try {
            $approvedCount = 0;
            $errors = [];

            foreach ($reviewIds as $reviewId) {
                try {
                    if ($this->approveReview($reviewId, auth()->id() ?? 1)) {
                        $approvedCount++;
                    }
                } catch (\Exception $e) {
                    $errors[] = "Review {$reviewId}: ".$e->getMessage();
                }
            }

            Log::info('Bulk approve completed', [
                'total_requested' => count($reviewIds),
                'approved_count' => $approvedCount,
                'errors' => $errors,
            ]);

            return $approvedCount > 0;
        } catch (\Exception $e) {
            Log::error('Service failed to bulk approve reviews', [
                'error' => $e->getMessage(),
                'review_ids' => $reviewIds,
            ]);
            throw $e;
        }
    }

    /**
     * Bulk reject reviews
     */
    public function bulkRejectReviews(array $reviewIds, ?string $reason = null): bool
    {
        try {
            $rejectedCount = 0;
            $errors = [];

            foreach ($reviewIds as $reviewId) {
                try {
                    if ($this->rejectReview($reviewId, auth()->id() ?? 1, $reason)) {
                        $rejectedCount++;
                    }
                } catch (\Exception $e) {
                    $errors[] = "Review {$reviewId}: ".$e->getMessage();
                }
            }

            Log::info('Bulk reject completed', [
                'total_requested' => count($reviewIds),
                'rejected_count' => $rejectedCount,
                'errors' => $errors,
            ]);

            return $rejectedCount > 0;
        } catch (\Exception $e) {
            Log::error('Service failed to bulk reject reviews', [
                'error' => $e->getMessage(),
                'review_ids' => $reviewIds,
                'reason' => $reason,
            ]);
            throw $e;
        }
    }

    /**
     * Sync review data
     */
    public function syncReviewData(): bool
    {
        try {
            // Mark overdue reviews
            $overdueReviews = $this->repository->findOverdue();
            foreach ($overdueReviews as $review) {
                $review->markAsOverdue();
            }

            // Send reminders
            $this->sendReviewReminders();

            Log::info('Review data sync completed');

            return true;
        } catch (\Exception $e) {
            Log::error('Service failed to sync review data', [
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * Clear cache
     */
    protected function clearCache(): void
    {
        $keys = [
            'employee_reviews_*',
            'latest_review_*',
            'rating_history_*',
            'avg_rating_*',
            'dept_reviews_*',
            'dept_avg_rating_*',
            'company_reviews_*',
            'company_avg_rating',
            'pending_approvals',
            'overdue_reviews',
            'upcoming_reviews_*',
            'search_*',
            'statistics_*',
            'employee_stats_*',
            'dept_stats_*',
            'report_*',
            'dept_report_*',
            'company_report_*',
        ];

        foreach ($keys as $key) {
            Cache::forget($this->cachePrefix.$key);
        }
    }
}
