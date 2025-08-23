<?php

namespace Fereydooni\Shopping\Repositories\Interfaces;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Pagination\CursorPaginator;
use Fereydooni\Shopping\Models\EmployeeTraining;
use Fereydooni\Shopping\DTOs\EmployeeTrainingDTO;

interface EmployeeTrainingRepositoryInterface
{
    /**
     * Get all employee trainings.
     */
    public function all(): Collection;

    /**
     * Get paginated employee trainings.
     */
    public function paginate(int $perPage = 15): LengthAwarePaginator;

    /**
     * Get simple paginated employee trainings.
     */
    public function simplePaginate(int $perPage = 15): Paginator;

    /**
     * Get cursor paginated employee trainings.
     */
    public function cursorPaginate(int $perPage = 15, ?string $cursor = null): CursorPaginator;

    /**
     * Find employee training by ID.
     */
    public function find(int $id): ?EmployeeTraining;

    /**
     * Find employee training by ID and return DTO.
     */
    public function findDTO(int $id): ?EmployeeTrainingDTO;

    /**
     * Find trainings by employee ID.
     */
    public function findByEmployeeId(int $employeeId): Collection;

    /**
     * Find trainings by employee ID and return DTOs.
     */
    public function findByEmployeeIdDTO(int $employeeId): Collection;

    /**
     * Find trainings by training type.
     */
    public function findByTrainingType(string $trainingType): Collection;

    /**
     * Find trainings by training type and return DTOs.
     */
    public function findByTrainingTypeDTO(string $trainingType): Collection;

    /**
     * Find trainings by status.
     */
    public function findByStatus(string $status): Collection;

    /**
     * Find trainings by status and return DTOs.
     */
    public function findByStatusDTO(string $status): Collection;

    /**
     * Find trainings by provider.
     */
    public function findByProvider(string $provider): Collection;

    /**
     * Find trainings by provider and return DTOs.
     */
    public function findByProviderDTO(string $provider): Collection;

    /**
     * Find trainings by date range.
     */
    public function findByDateRange(string $startDate, string $endDate): Collection;

    /**
     * Find trainings by date range and return DTOs.
     */
    public function findByDateRangeDTO(string $startDate, string $endDate): Collection;

    /**
     * Find trainings by employee and type.
     */
    public function findByEmployeeAndType(int $employeeId, string $trainingType): Collection;

    /**
     * Find trainings by employee and type and return DTOs.
     */
    public function findByEmployeeAndTypeDTO(int $employeeId, string $trainingType): Collection;

    /**
     * Find completed trainings.
     */
    public function findCompleted(): Collection;

    /**
     * Find completed trainings and return DTOs.
     */
    public function findCompletedDTO(): Collection;

    /**
     * Find in-progress trainings.
     */
    public function findInProgress(): Collection;

    /**
     * Find in-progress trainings and return DTOs.
     */
    public function findInProgressDTO(): Collection;

    /**
     * Find not started trainings.
     */
    public function findNotStarted(): Collection;

    /**
     * Find not started trainings and return DTOs.
     */
    public function findNotStartedDTO(): Collection;

    /**
     * Find failed trainings.
     */
    public function findFailed(): Collection;

    /**
     * Find failed trainings and return DTOs.
     */
    public function findFailedDTO(): Collection;

    /**
     * Find mandatory trainings.
     */
    public function findMandatory(): Collection;

    /**
     * Find mandatory trainings and return DTOs.
     */
    public function findMandatoryDTO(): Collection;

    /**
     * Find certification trainings.
     */
    public function findCertifications(): Collection;

    /**
     * Find certification trainings and return DTOs.
     */
    public function findCertificationsDTO(): Collection;

    /**
     * Find trainings expiring soon.
     */
    public function findExpiringSoon(int $days = 30): Collection;

    /**
     * Find trainings expiring soon and return DTOs.
     */
    public function findExpiringSoonDTO(int $days = 30): Collection;

    /**
     * Find trainings by instructor.
     */
    public function findByInstructor(string $instructor): Collection;

    /**
     * Find trainings by instructor and return DTOs.
     */
    public function findByInstructorDTO(string $instructor): Collection;

    /**
     * Find trainings by training method.
     */
    public function findByTrainingMethod(string $trainingMethod): Collection;

    /**
     * Find trainings by training method and return DTOs.
     */
    public function findByTrainingMethodDTO(string $trainingMethod): Collection;

    /**
     * Find trainings by score range.
     */
    public function findByScoreRange(float $minScore, float $maxScore): Collection;

    /**
     * Find trainings by score range and return DTOs.
     */
    public function findByScoreRangeDTO(float $minScore, float $maxScore): Collection;

    /**
     * Create new employee training.
     */
    public function create(array $data): EmployeeTraining;

    /**
     * Create new employee training and return DTO.
     */
    public function createAndReturnDTO(array $data): EmployeeTrainingDTO;

    /**
     * Update employee training.
     */
    public function update(EmployeeTraining $training, array $data): bool;

    /**
     * Update employee training and return DTO.
     */
    public function updateAndReturnDTO(EmployeeTraining $training, array $data): ?EmployeeTrainingDTO;

    /**
     * Delete employee training.
     */
    public function delete(EmployeeTraining $training): bool;

    /**
     * Start employee training.
     */
    public function start(EmployeeTraining $training): bool;

    /**
     * Complete employee training.
     */
    public function complete(EmployeeTraining $training, ?float $score = null, ?string $grade = null): bool;

    /**
     * Fail employee training.
     */
    public function fail(EmployeeTraining $training, ?string $reason = null): bool;

    /**
     * Cancel employee training.
     */
    public function cancel(EmployeeTraining $training, ?string $reason = null): bool;

    /**
     * Renew employee training.
     */
    public function renew(EmployeeTraining $training, ?string $renewalDate = null): bool;

    /**
     * Update training progress.
     */
    public function updateProgress(EmployeeTraining $training, float $hoursCompleted): bool;

    /**
     * Get employee training count.
     */
    public function getEmployeeTrainingCount(int $employeeId): int;

    /**
     * Get employee training count by type.
     */
    public function getEmployeeTrainingCountByType(int $employeeId, string $trainingType): int;

    /**
     * Get employee training count by status.
     */
    public function getEmployeeTrainingCountByStatus(int $employeeId, string $status): int;

    /**
     * Get employee total training hours.
     */
    public function getEmployeeTotalHours(int $employeeId): float;

    /**
     * Get employee total training cost.
     */
    public function getEmployeeTotalCost(int $employeeId): float;

    /**
     * Get employee average training score.
     */
    public function getEmployeeAverageScore(int $employeeId): float;

    /**
     * Get employee certifications.
     */
    public function getEmployeeCertifications(int $employeeId): Collection;

    /**
     * Get employee certifications and return DTOs.
     */
    public function getEmployeeCertificationsDTO(int $employeeId): Collection;

    /**
     * Get employee mandatory trainings.
     */
    public function getEmployeeMandatoryTrainings(int $employeeId): Collection;

    /**
     * Get employee mandatory trainings and return DTOs.
     */
    public function getEmployeeMandatoryTrainingsDTO(int $employeeId): Collection;

    /**
     * Get total training count.
     */
    public function getTotalTrainingCount(): int;

    /**
     * Get total training count by type.
     */
    public function getTotalTrainingCountByType(string $trainingType): int;

    /**
     * Get total training count by status.
     */
    public function getTotalTrainingCountByStatus(string $status): int;

    /**
     * Get total training hours.
     */
    public function getTotalHours(): float;

    /**
     * Get total training cost.
     */
    public function getTotalCost(): float;

    /**
     * Get average training score.
     */
    public function getAverageScore(): float;

    /**
     * Get completed trainings count.
     */
    public function getCompletedTrainingsCount(): int;

    /**
     * Get in-progress trainings count.
     */
    public function getInProgressTrainingsCount(): int;

    /**
     * Get failed trainings count.
     */
    public function getFailedTrainingsCount(): int;

    /**
     * Get expiring certifications count.
     */
    public function getExpiringCertificationsCount(int $days = 30): int;

    /**
     * Search trainings.
     */
    public function searchTrainings(string $query): Collection;

    /**
     * Search trainings and return DTOs.
     */
    public function searchTrainingsDTO(string $query): Collection;

    /**
     * Search trainings by employee.
     */
    public function searchTrainingsByEmployee(int $employeeId, string $query): Collection;

    /**
     * Search trainings by employee and return DTOs.
     */
    public function searchTrainingsByEmployeeDTO(int $employeeId, string $query): Collection;

    /**
     * Export training data.
     */
    public function exportTrainingData(array $filters = []): string;

    /**
     * Import training data.
     */
    public function importTrainingData(string $data): bool;

    /**
     * Get training statistics.
     */
    public function getTrainingStatistics(?int $employeeId = null): array;

    /**
     * Get department training statistics.
     */
    public function getDepartmentTrainingStatistics(int $departmentId): array;

    /**
     * Get company training statistics.
     */
    public function getCompanyTrainingStatistics(): array;

    /**
     * Get training effectiveness.
     */
    public function getTrainingEffectiveness(?int $employeeId = null): array;

    /**
     * Get training trends.
     */
    public function getTrainingTrends(?string $startDate = null, ?string $endDate = null): array;
}
