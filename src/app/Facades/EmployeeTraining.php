<?php

namespace Fereydooni\Shopping\Facades;

use Fereydooni\Shopping\Actions\EmployeeTraining\CalculateTrainingProgressAction;
use Fereydooni\Shopping\Actions\EmployeeTraining\CompleteEmployeeTrainingAction;
use Fereydooni\Shopping\Actions\EmployeeTraining\CreateEmployeeTrainingAction;
use Fereydooni\Shopping\Actions\EmployeeTraining\FailEmployeeTrainingAction;
use Fereydooni\Shopping\Actions\EmployeeTraining\RenewEmployeeTrainingAction;
use Fereydooni\Shopping\Actions\EmployeeTraining\StartEmployeeTrainingAction;
use Fereydooni\Shopping\Actions\EmployeeTraining\UpdateEmployeeTrainingAction;
use Fereydooni\Shopping\DTOs\EmployeeTrainingDTO;
use Fereydooni\Shopping\Models\EmployeeTraining;
use Fereydooni\Shopping\Repositories\Interfaces\EmployeeTrainingRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Facade;

/**
 * @method static Collection all()
 * @method static LengthAwarePaginator paginate(int $perPage = 15)
 * @method static Collection findByEmployeeId(int $employeeId)
 * @method static Collection findByTrainingType(string $trainingType)
 * @method static Collection findByStatus(string $status)
 * @method static Collection findByProvider(string $provider)
 * @method static Collection findByDateRange(string $startDate, string $endDate)
 * @method static Collection findCompleted()
 * @method static Collection findInProgress()
 * @method static Collection findNotStarted()
 * @method static Collection findFailed()
 * @method static Collection findMandatory()
 * @method static Collection findCertifications()
 * @method static Collection findExpiringSoon(string $days = 30)
 * @method static Collection searchTrainings(string $query)
 * @method static array getTrainingStatistics(int $employeeId = null)
 * @method static array getTrainingEffectiveness(int $employeeId = null)
 * @method static array getTrainingTrends(string $startDate = null, string $endDate = null)
 * @method static EmployeeTrainingDTO create(array $data)
 * @method static EmployeeTrainingDTO update(EmployeeTraining $training, array $data)
 * @method static EmployeeTrainingDTO start(EmployeeTraining $training)
 * @method static EmployeeTrainingDTO complete(EmployeeTraining $training, ?float $score = null, ?string $grade = null)
 * @method static EmployeeTrainingDTO fail(EmployeeTraining $training, ?string $reason = null)
 * @method static EmployeeTrainingDTO renew(EmployeeTraining $training, ?string $renewalDate = null)
 * @method static array calculateProgress(EmployeeTraining $training)
 * @method static bool delete(EmployeeTraining $training)
 * @method static EmployeeTraining find(int $id)
 * @method static EmployeeTrainingDTO findDTO(int $id)
 * @method static int getEmployeeTrainingCount(int $employeeId)
 * @method static float getEmployeeTotalHours(int $employeeId)
 * @method static float getEmployeeTotalCost(int $employeeId)
 * @method static float getEmployeeAverageScore(int $employeeId)
 * @method static Collection getEmployeeCertifications(int $employeeId)
 * @method static Collection getEmployeeMandatoryTrainings(int $employeeId)
 * @method static int getTotalTrainingCount()
 * @method static float getTotalHours()
 * @method static float getTotalCost()
 * @method static float getAverageScore()
 * @method static int getCompletedTrainingsCount()
 * @method static int getInProgressTrainingsCount()
 * @method static int getFailedTrainingsCount()
 * @method static int getExpiringCertificationsCount(string $days = 30)
 * @method static string exportTrainingData(array $filters = [])
 * @method static bool importTrainingData(string $data)
 * @method static array getDepartmentTrainingStatistics(int $departmentId)
 * @method static array getCompanyTrainingStatistics()
 */
class EmployeeTraining extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'employee-training';
    }

    /**
     * Get all employee trainings
     */
    public static function getAll(): Collection
    {
        return app(EmployeeTrainingRepositoryInterface::class)->all();
    }

    /**
     * Get paginated employee trainings
     */
    public static function getPaginated(int $perPage = 15): LengthAwarePaginator
    {
        return app(EmployeeTrainingRepositoryInterface::class)->paginate($perPage);
    }

    /**
     * Find training by ID
     */
    public static function findById(int $id): ?EmployeeTraining
    {
        return app(EmployeeTrainingRepositoryInterface::class)->find($id);
    }

    /**
     * Find training by ID and return DTO
     */
    public static function findByIdDTO(int $id): ?EmployeeTrainingDTO
    {
        return app(EmployeeTrainingRepositoryInterface::class)->findDTO($id);
    }

    /**
     * Find trainings by employee ID
     */
    public static function findByEmployee(int $employeeId): Collection
    {
        return app(EmployeeTrainingRepositoryInterface::class)->findByEmployeeId($employeeId);
    }

    /**
     * Find trainings by employee ID and return DTOs
     */
    public static function findByEmployeeDTO(int $employeeId): Collection
    {
        return app(EmployeeTrainingRepositoryInterface::class)->findByEmployeeIdDTO($employeeId);
    }

    /**
     * Find trainings by type
     */
    public static function findByType(string $trainingType): Collection
    {
        return app(EmployeeTrainingRepositoryInterface::class)->findByTrainingType($trainingType);
    }

    /**
     * Find trainings by status
     */
    public static function findByStatus(string $status): Collection
    {
        return app(EmployeeTrainingRepositoryInterface::class)->findByStatus($status);
    }

    /**
     * Find completed trainings
     */
    public static function getCompleted(): Collection
    {
        return app(EmployeeTrainingRepositoryInterface::class)->findCompleted();
    }

    /**
     * Find in-progress trainings
     */
    public static function getInProgress(): Collection
    {
        return app(EmployeeTrainingRepositoryInterface::class)->findInProgress();
    }

    /**
     * Find not started trainings
     */
    public static function getNotStarted(): Collection
    {
        return app(EmployeeTrainingRepositoryInterface::class)->findNotStarted();
    }

    /**
     * Find failed trainings
     */
    public static function getFailed(): Collection
    {
        return app(EmployeeTrainingRepositoryInterface::class)->findFailed();
    }

    /**
     * Find mandatory trainings
     */
    public static function getMandatory(): Collection
    {
        return app(EmployeeTrainingRepositoryInterface::class)->findMandatory();
    }

    /**
     * Find certification trainings
     */
    public static function getCertifications(): Collection
    {
        return app(EmployeeTrainingRepositoryInterface::class)->findCertifications();
    }

    /**
     * Find trainings expiring soon
     */
    public static function getExpiringSoon(string $days = 30): Collection
    {
        return app(EmployeeTrainingRepositoryInterface::class)->findExpiringSoon($days);
    }

    /**
     * Search trainings
     */
    public static function search(string $query): Collection
    {
        return app(EmployeeTrainingRepositoryInterface::class)->searchTrainings($query);
    }

    /**
     * Create new training
     */
    public static function create(array $data): EmployeeTrainingDTO
    {
        return app(CreateEmployeeTrainingAction::class)->execute($data);
    }

    /**
     * Update training
     */
    public static function update(EmployeeTraining $training, array $data): EmployeeTrainingDTO
    {
        return app(UpdateEmployeeTrainingAction::class)->execute($training, $data);
    }

    /**
     * Start training
     */
    public static function start(EmployeeTraining $training): EmployeeTrainingDTO
    {
        return app(StartEmployeeTrainingAction::class)->execute($training);
    }

    /**
     * Complete training
     */
    public static function complete(EmployeeTraining $training, ?float $score = null, ?string $grade = null): EmployeeTrainingDTO
    {
        return app(CompleteEmployeeTrainingAction::class)->execute($training, $score, $grade);
    }

    /**
     * Fail training
     */
    public static function fail(EmployeeTraining $training, ?string $reason = null): EmployeeTrainingDTO
    {
        return app(FailEmployeeTrainingAction::class)->execute($training, $reason);
    }

    /**
     * Renew training
     */
    public static function renew(EmployeeTraining $training, ?string $renewalDate = null): EmployeeTrainingDTO
    {
        return app(RenewEmployeeTrainingAction::class)->execute($training, $renewalDate);
    }

    /**
     * Calculate training progress
     */
    public static function calculateProgress(EmployeeTraining $training): array
    {
        return app(CalculateTrainingProgressAction::class)->execute($training);
    }

    /**
     * Delete training
     */
    public static function delete(EmployeeTraining $training): bool
    {
        return app(EmployeeTrainingRepositoryInterface::class)->delete($training);
    }

    /**
     * Get employee training count
     */
    public static function getEmployeeCount(int $employeeId): int
    {
        return app(EmployeeTrainingRepositoryInterface::class)->getEmployeeTrainingCount($employeeId);
    }

    /**
     * Get employee total training hours
     */
    public static function getEmployeeHours(int $employeeId): float
    {
        return app(EmployeeTrainingRepositoryInterface::class)->getEmployeeTotalHours($employeeId);
    }

    /**
     * Get employee total training cost
     */
    public static function getEmployeeCost(int $employeeId): float
    {
        return app(EmployeeTrainingRepositoryInterface::class)->getEmployeeTotalCost($employeeId);
    }

    /**
     * Get employee average score
     */
    public static function getEmployeeScore(int $employeeId): float
    {
        return app(EmployeeTrainingRepositoryInterface::class)->getEmployeeAverageScore($employeeId);
    }

    /**
     * Get employee certifications
     */
    public static function getEmployeeCertifications(int $employeeId): Collection
    {
        return app(EmployeeTrainingRepositoryInterface::class)->getEmployeeCertifications($employeeId);
    }

    /**
     * Get employee mandatory trainings
     */
    public static function getEmployeeMandatory(int $employeeId): Collection
    {
        return app(EmployeeTrainingRepositoryInterface::class)->getEmployeeMandatoryTrainings($employeeId);
    }

    /**
     * Get total training count
     */
    public static function getTotalCount(): int
    {
        return app(EmployeeTrainingRepositoryInterface::class)->getTotalTrainingCount();
    }

    /**
     * Get total training hours
     */
    public static function getTotalHours(): float
    {
        return app(EmployeeTrainingRepositoryInterface::class)->getTotalHours();
    }

    /**
     * Get total training cost
     */
    public static function getTotalCost(): float
    {
        return app(EmployeeTrainingRepositoryInterface::class)->getTotalCost();
    }

    /**
     * Get average score
     */
    public static function getAverageScore(): float
    {
        return app(EmployeeTrainingRepositoryInterface::class)->getAverageScore();
    }

    /**
     * Get completed trainings count
     */
    public static function getCompletedCount(): int
    {
        return app(EmployeeTrainingRepositoryInterface::class)->getCompletedTrainingsCount();
    }

    /**
     * Get in-progress trainings count
     */
    public static function getInProgressCount(): int
    {
        return app(EmployeeTrainingRepositoryInterface::class)->getInProgressTrainingsCount();
    }

    /**
     * Get failed trainings count
     */
    public static function getFailedCount(): int
    {
        return app(EmployeeTrainingRepositoryInterface::class)->getFailedTrainingsCount();
    }

    /**
     * Get expiring certifications count
     */
    public static function getExpiringCount(string $days = 30): int
    {
        return app(EmployeeTrainingRepositoryInterface::class)->getExpiringCertificationsCount($days);
    }

    /**
     * Get training statistics
     */
    public static function getStatistics(?int $employeeId = null): array
    {
        return app(EmployeeTrainingRepositoryInterface::class)->getTrainingStatistics($employeeId);
    }

    /**
     * Get training effectiveness
     */
    public static function getEffectiveness(?int $employeeId = null): array
    {
        return app(EmployeeTrainingRepositoryInterface::class)->getTrainingEffectiveness($employeeId);
    }

    /**
     * Get training trends
     */
    public static function getTrends(?string $startDate = null, ?string $endDate = null): array
    {
        return app(EmployeeTrainingRepositoryInterface::class)->getTrainingTrends($startDate, $endDate);
    }

    /**
     * Get department training statistics
     */
    public static function getDepartmentStatistics(int $departmentId): array
    {
        return app(EmployeeTrainingRepositoryInterface::class)->getDepartmentTrainingStatistics($departmentId);
    }

    /**
     * Get company training statistics
     */
    public static function getCompanyStatistics(): array
    {
        return app(EmployeeTrainingRepositoryInterface::class)->getCompanyTrainingStatistics();
    }

    /**
     * Export training data
     */
    public static function export(array $filters = []): string
    {
        return app(EmployeeTrainingRepositoryInterface::class)->exportTrainingData($filters);
    }

    /**
     * Import training data
     */
    public static function import(string $data): bool
    {
        return app(EmployeeTrainingRepositoryInterface::class)->importTrainingData($data);
    }
}
