<?php

namespace App\Repositories\Interfaces;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Pagination\CursorPaginator;
use App\Models\EmployeePerformanceReview;
use App\DTOs\EmployeePerformanceReviewDTO;

interface EmployeePerformanceReviewRepositoryInterface
{
    // Basic CRUD operations
    public function all(): Collection;
    public function paginate(int $perPage = 15): LengthAwarePaginator;
    public function simplePaginate(int $perPage = 15): Paginator;
    public function cursorPaginate(int $perPage = 15, string $cursor = null): CursorPaginator;
    public function find(int $id): ?EmployeePerformanceReview;
    public function findDTO(int $id): ?EmployeePerformanceReviewDTO;
    public function create(array $data): EmployeePerformanceReview;
    public function createAndReturnDTO(array $data): EmployeePerformanceReviewDTO;
    public function update(EmployeePerformanceReview $review, array $data): bool;
    public function updateAndReturnDTO(EmployeePerformanceReview $review, array $data): ?EmployeePerformanceReviewDTO;
    public function delete(EmployeePerformanceReview $review): bool;

    // Find by specific criteria
    public function findByEmployeeId(int $employeeId): Collection;
    public function findByEmployeeIdDTO(int $employeeId): Collection;
    public function findByReviewerId(int $reviewerId): Collection;
    public function findByReviewerIdDTO(int $reviewerId): Collection;
    public function findByStatus(string $status): Collection;
    public function findByStatusDTO(string $status): Collection;
    public function findByRatingRange(float $minRating, float $maxRating): Collection;
    public function findByRatingRangeDTO(float $minRating, float $maxRating): Collection;
    public function findByDateRange(string $startDate, string $endDate): Collection;
    public function findByDateRangeDTO(string $startDate, string $endDate): Collection;
    public function findByReviewPeriod(string $startDate, string $endDate): Collection;
    public function findByReviewPeriodDTO(string $startDate, string $endDate): Collection;

    // Status-based queries
    public function findPendingApproval(): Collection;
    public function findPendingApprovalDTO(): Collection;
    public function findApproved(): Collection;
    public function findApprovedDTO(): Collection;
    public function findRejected(): Collection;
    public function findRejectedDTO(): Collection;
    public function findOverdue(): Collection;
    public function findOverdueDTO(): Collection;

    // Specialized queries
    public function findByEmployeeAndPeriod(int $employeeId, string $startDate, string $endDate): ?EmployeePerformanceReview;
    public function findByEmployeeAndPeriodDTO(int $employeeId, string $startDate, string $endDate): ?EmployeePerformanceReviewDTO;
    public function findLatestByEmployee(int $employeeId): ?EmployeePerformanceReview;
    public function findLatestByEmployeeDTO(int $employeeId): ?EmployeePerformanceReviewDTO;
    public function findUpcomingReviews(string $date = null): Collection;
    public function findUpcomingReviewsDTO(string $date = null): Collection;

    // Workflow operations
    public function approve(EmployeePerformanceReview $review, int $approvedBy): bool;
    public function reject(EmployeePerformanceReview $review, int $rejectedBy, string $reason = null): bool;
    public function submitForApproval(EmployeePerformanceReview $review): bool;

    // Rating and statistics
    public function getEmployeeAverageRating(int $employeeId): float;
    public function getEmployeeRatingHistory(int $employeeId): Collection;
    public function getEmployeeRatingHistoryDTO(int $employeeId): Collection;
    public function getDepartmentAverageRating(int $departmentId): float;
    public function getCompanyAverageRating(): float;

    // Statistics and analytics
    public function getReviewStatistics(): array;
    public function getEmployeeReviewStatistics(int $employeeId): array;
    public function getDepartmentReviewStatistics(int $departmentId): array;
    public function getOverdueReviewsCount(): int;
    public function getPendingApprovalCount(): int;
    public function getCompletedReviewsCount(string $startDate = null, string $endDate = null): int;
    public function getReviewCountByStatus(string $status): int;
    public function getReviewCountByRating(float $rating): int;

    // Search functionality
    public function searchReviews(string $query): Collection;
    public function searchReviewsDTO(string $query): Collection;
    public function searchReviewsByEmployee(int $employeeId, string $query): Collection;
    public function searchReviewsByEmployeeDTO(int $employeeId, string $query): Collection;

    // Import/Export
    public function exportReviews(array $filters = []): string;
    public function importReviews(string $data): bool;
}
