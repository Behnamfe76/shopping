<?php

namespace App\Facades;

use App\Services\EmployeePerformanceReviewService;
use App\DTOs\EmployeePerformanceReviewDTO;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Facade;

/**
 * @method static EmployeePerformanceReviewDTO create(array $data)
 * @method static EmployeePerformanceReviewDTO update(int $id, array $data)
 * @method static bool delete(int $id)
 * @method static EmployeePerformanceReviewDTO|null find(int $id)
 * @method static Collection findByEmployee(int $employeeId)
 * @method static bool submit(int $id)
 * @method static bool approve(int $id, int $approvedBy)
 * @method static bool reject(int $id, int $rejectedBy, string $reason = null)
 * @method static float getEmployeeRating(int $employeeId)
 * @method static Collection getEmployeeHistory(int $employeeId)
 * @method static float getDepartmentRating(int $departmentId)
 * @method static float getCompanyRating()
 * @method static Collection getPendingApprovals()
 * @method static Collection getOverdueReviews()
 * @method static Collection search(string $query)
 * @method static array getStatistics(array $filters = [])
 * @method static array generateReport(int $employeeId, string $period = 'year')
 * @method static bool sendReminders()
 * @method static bool schedule(int $employeeId, string $reviewDate)
 * @method static string export(array $filters = [], string $format = 'json')
 * @method static bool import(string $data, string $format = 'json')
 *
 * @see \App\Services\EmployeePerformanceReviewService
 */
class EmployeePerformanceReview extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return EmployeePerformanceReviewService::class;
    }

    /**
     * Create a new performance review
     */
    public static function createReview(array $data): EmployeePerformanceReviewDTO
    {
        return static::getFacadeRoot()->createReview($data);
    }

    /**
     * Update an existing performance review
     */
    public static function updateReview(int $id, array $data): EmployeePerformanceReviewDTO
    {
        return static::getFacadeRoot()->updateReview($id, $data);
    }

    /**
     * Delete a performance review
     */
    public static function deleteReview(int $id): bool
    {
        return static::getFacadeRoot()->deleteReview($id);
    }

    /**
     * Find a performance review by ID
     */
    public static function findReview(int $id): ?EmployeePerformanceReviewDTO
    {
        return static::getFacadeRoot()->find($id);
    }

    /**
     * Get employee reviews
     */
    public static function getEmployeeReviews(int $employeeId, array $filters = []): Collection
    {
        return static::getFacadeRoot()->getEmployeeReviews($employeeId, $filters);
    }

    /**
     * Get employee's latest review
     */
    public static function getEmployeeLatestReview(int $employeeId): ?EmployeePerformanceReviewDTO
    {
        return static::getFacadeRoot()->getEmployeeLatestReview($employeeId);
    }

    /**
     * Get employee rating history
     */
    public static function getEmployeeRatingHistory(int $employeeId): Collection
    {
        return static::getFacadeRoot()->getEmployeeRatingHistory($employeeId);
    }

    /**
     * Get employee average rating
     */
    public static function getEmployeeAverageRating(int $employeeId): float
    {
        return static::getFacadeRoot()->getEmployeeAverageRating($employeeId);
    }

    /**
     * Get department reviews
     */
    public static function getDepartmentReviews(int $departmentId, array $filters = []): Collection
    {
        return static::getFacadeRoot()->getDepartmentReviews($departmentId, $filters);
    }

    /**
     * Get department average rating
     */
    public static function getDepartmentAverageRating(int $departmentId): float
    {
        return static::getFacadeRoot()->getDepartmentAverageRating($departmentId);
    }

    /**
     * Get company reviews
     */
    public static function getCompanyReviews(array $filters = []): Collection
    {
        return static::getFacadeRoot()->getCompanyReviews($filters);
    }

    /**
     * Get company average rating
     */
    public static function getCompanyAverageRating(): float
    {
        return static::getFacadeRoot()->getCompanyAverageRating();
    }

    /**
     * Get pending approvals
     */
    public static function getPendingApprovals(): Collection
    {
        return static::getFacadeRoot()->getPendingApprovals();
    }

    /**
     * Get overdue reviews
     */
    public static function getOverdueReviews(): Collection
    {
        return static::getFacadeRoot()->getOverdueReviews();
    }

    /**
     * Get upcoming reviews
     */
    public static function getUpcomingReviews(string $date = null): Collection
    {
        return static::getFacadeRoot()->getUpcomingReviews($date);
    }

    /**
     * Search reviews
     */
    public static function searchReviews(string $query, array $filters = []): Collection
    {
        return static::getFacadeRoot()->searchReviews($query, $filters);
    }

    /**
     * Get review statistics
     */
    public static function getReviewStatistics(array $filters = []): array
    {
        return static::getFacadeRoot()->getReviewStatistics($filters);
    }

    /**
     * Get employee review statistics
     */
    public static function getEmployeeReviewStatistics(int $employeeId): array
    {
        return static::getFacadeRoot()->getEmployeeReviewStatistics($employeeId);
    }

    /**
     * Get department review statistics
     */
    public static function getDepartmentReviewStatistics(int $departmentId): array
    {
        return static::getFacadeRoot()->getDepartmentReviewStatistics($departmentId);
    }

    /**
     * Generate performance report
     */
    public static function generatePerformanceReport(int $employeeId, string $period = 'year'): array
    {
        return static::getFacadeRoot()->generatePerformanceReport($employeeId, $period);
    }

    /**
     * Generate department report
     */
    public static function generateDepartmentReport(int $departmentId, string $period = 'year'): array
    {
        return static::getFacadeRoot()->generateDepartmentReport($departmentId, $period);
    }

    /**
     * Generate company report
     */
    public static function generateCompanyReport(string $period = 'year'): array
    {
        return static::getFacadeRoot()->generateCompanyReport($period);
    }

    /**
     * Send review reminders
     */
    public static function sendReviewReminders(): bool
    {
        return static::getFacadeRoot()->sendReviewReminders();
    }

    /**
     * Schedule a review
     */
    public static function scheduleReview(int $employeeId, string $reviewDate): bool
    {
        return static::getFacadeRoot()->scheduleReview($employeeId, $reviewDate);
    }

    /**
     * Assign reviewer to a review
     */
    public static function assignReviewer(int $reviewId, int $reviewerId): bool
    {
        return static::getFacadeRoot()->assignReviewer($reviewId, $reviewerId);
    }

    /**
     * Export reviews
     */
    public static function exportReviews(array $filters = [], string $format = 'json'): string
    {
        return static::getFacadeRoot()->exportReviews($filters, $format);
    }

    /**
     * Import reviews
     */
    public static function importReviews(string $data, string $format = 'json'): bool
    {
        return static::getFacadeRoot()->importReviews($data, $format);
    }

    /**
     * Bulk approve reviews
     */
    public static function bulkApproveReviews(array $reviewIds): bool
    {
        return static::getFacadeRoot()->bulkApproveReviews($reviewIds);
    }

    /**
     * Bulk reject reviews
     */
    public static function bulkRejectReviews(array $reviewIds, string $reason = null): bool
    {
        return static::getFacadeRoot()->bulkRejectReviews($reviewIds, $reason);
    }

    /**
     * Sync review data
     */
    public static function syncReviewData(): bool
    {
        return static::getFacadeRoot()->syncReviewData();
    }

    /**
     * Submit a review for approval
     */
    public static function submitReview(int $id): bool
    {
        return static::getFacadeRoot()->submitReview($id);
    }

    /**
     * Approve a review
     */
    public static function approveReview(int $id, int $approvedBy): bool
    {
        return static::getFacadeRoot()->approveReview($id, $approvedBy);
    }

    /**
     * Reject a review
     */
    public static function rejectReview(int $id, int $rejectedBy, string $reason = null): bool
    {
        return static::getFacadeRoot()->rejectReview($id, $rejectedBy, $reason);
    }

    /**
     * Get all reviews with pagination
     */
    public static function getAllReviews(int $perPage = 15)
    {
        return static::getFacadeRoot()->repository->paginate($perPage);
    }

    /**
     * Get reviews by status
     */
    public static function getReviewsByStatus(string $status): Collection
    {
        return static::getFacadeRoot()->repository->findByStatusDTO($status);
    }

    /**
     * Get reviews by rating range
     */
    public static function getReviewsByRatingRange(float $minRating, float $maxRating): Collection
    {
        return static::getFacadeRoot()->repository->findByRatingRangeDTO($minRating, $maxRating);
    }

    /**
     * Get reviews by date range
     */
    public static function getReviewsByDateRange(string $startDate, string $endDate): Collection
    {
        return static::getFacadeRoot()->repository->findByDateRangeDTO($startDate, $endDate);
    }

    /**
     * Get reviews by review period
     */
    public static function getReviewsByReviewPeriod(string $startDate, string $endDate): Collection
    {
        return static::getFacadeRoot()->repository->findByReviewPeriodDTO($startDate, $endDate);
    }

    /**
     * Get reviews by employee and period
     */
    public static function getReviewsByEmployeeAndPeriod(int $employeeId, string $startDate, string $endDate): ?EmployeePerformanceReviewDTO
    {
        return static::getFacadeRoot()->repository->findByEmployeeAndPeriodDTO($employeeId, $startDate, $endDate);
    }

    /**
     * Get latest review by employee
     */
    public static function getLatestReviewByEmployee(int $employeeId): ?EmployeePerformanceReviewDTO
    {
        return static::getFacadeRoot()->repository->findLatestByEmployeeDTO($employeeId);
    }

    /**
     * Get overdue reviews count
     */
    public static function getOverdueReviewsCount(): int
    {
        return static::getFacadeRoot()->repository->getOverdueReviewsCount();
    }

    /**
     * Get pending approval count
     */
    public static function getPendingApprovalCount(): int
    {
        return static::getFacadeRoot()->repository->getPendingApprovalCount();
    }

    /**
     * Get completed reviews count
     */
    public static function getCompletedReviewsCount(string $startDate = null, string $endDate = null): int
    {
        return static::getFacadeRoot()->repository->getCompletedReviewsCount($startDate, $endDate);
    }

    /**
     * Get review count by status
     */
    public static function getReviewCountByStatus(string $status): int
    {
        return static::getFacadeRoot()->repository->getReviewCountByStatus($status);
    }

    /**
     * Get review count by rating
     */
    public static function getReviewCountByRating(float $rating): int
    {
        return static::getFacadeRoot()->repository->getReviewCountByRating($rating);
    }

    /**
     * Search reviews by employee
     */
    public static function searchReviewsByEmployee(int $employeeId, string $query): Collection
    {
        return static::getFacadeRoot()->repository->searchReviewsByEmployeeDTO($employeeId, $query);
    }

    /**
     * Get performance analytics
     */
    public static function getPerformanceAnalytics(array $filters = []): array
    {
        return static::getFacadeRoot()->actions->generatePerformanceAnalytics($filters);
    }

    /**
     * Create review template
     */
    public static function createReviewTemplate(array $data): array
    {
        return static::getFacadeRoot()->actions->createReviewTemplate($data);
    }

    /**
     * Calculate employee rating
     */
    public static function calculateEmployeeRating(int $employeeId): float
    {
        return static::getFacadeRoot()->actions->calculateEmployeeRating($employeeId);
    }

    /**
     * Check if review can be submitted
     */
    public static function canSubmitReview(int $reviewId): bool
    {
        $review = static::getFacadeRoot()->repository->find($reviewId);
        return $review ? $review->canBeSubmitted() : false;
    }

    /**
     * Check if review can be approved
     */
    public static function canApproveReview(int $reviewId): bool
    {
        $review = static::getFacadeRoot()->repository->find($reviewId);
        return $review ? $review->canBeApproved() : false;
    }

    /**
     * Check if review can be rejected
     */
    public static function canRejectReview(int $reviewId): bool
    {
        $review = static::getFacadeRoot()->repository->find($reviewId);
        return $review ? $review->canBeRejected() : false;
    }

    /**
     * Check if review is editable
     */
    public static function isReviewEditable(int $reviewId): bool
    {
        $review = static::getFacadeRoot()->repository->find($reviewId);
        return $review ? $review->isEditable() : false;
    }

    /**
     * Get review status options
     */
    public static function getStatusOptions(): array
    {
        return \App\Models\EmployeePerformanceReview::getStatusOptions();
    }

    /**
     * Get rating options
     */
    public static function getRatingOptions(): array
    {
        return \App\Models\EmployeePerformanceReview::getRatingOptions();
    }

    /**
     * Get review status colors
     */
    public static function getStatusColors(): array
    {
        $statuses = \App\Enums\EmployeePerformanceReviewStatus::cases();
        $colors = [];

        foreach ($statuses as $status) {
            $colors[$status->value] = $status->color();
        }

        return $colors;
    }

    /**
     * Get rating colors
     */
    public static function getRatingColors(): array
    {
        $ratings = \App\Enums\EmployeePerformanceReviewRating::cases();
        $colors = [];

        foreach ($ratings as $rating) {
            $colors[$rating->value] = $rating->color();
        }

        return $colors;
    }

    /**
     * Get review status icons
     */
    public static function getStatusIcons(): array
    {
        $statuses = \App\Enums\EmployeePerformanceReviewStatus::cases();
        $icons = [];

        foreach ($statuses as $status) {
            $icons[$status->value] = $status->icon();
        }

        return $icons;
    }

    /**
     * Get rating icons
     */
    public static function getRatingIcons(): array
    {
        $ratings = \App\Enums\EmployeePerformanceReviewRating::cases();
        $icons = [];

        foreach ($ratings as $rating) {
            $icons[$rating->value] = $rating->icon();
        }

        return $icons;
    }
}
