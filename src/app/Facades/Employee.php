<?php

namespace Fereydooni\Shopping\app\Facades;

use Fereydooni\Shopping\app\Services\EmployeeService;
use Illuminate\Support\Facades\Facade;

/**
 * @method static \Illuminate\Database\Eloquent\Collection getAllEmployees()
 * @method static \Illuminate\Pagination\LengthAwarePaginator getPaginatedEmployees(int $perPage = 15)
 * @method static \Illuminate\Pagination\Paginator getSimplePaginatedEmployees(int $perPage = 15)
 * @method static \Illuminate\Pagination\CursorPaginator getCursorPaginatedEmployees(int $perPage = 15, string $cursor = null)
 * @method static \Fereydooni\Shopping\app\Models\Employee|null getEmployee(int $id)
 * @method static \Fereydooni\Shopping\app\DTOs\EmployeeDTO|null getEmployeeDTO(int $id)
 * @method static \Fereydooni\Shopping\app\DTOs\EmployeeDTO createEmployee(array $data)
 * @method static \Fereydooni\Shopping\app\DTOs\EmployeeDTO|null updateEmployee(\Fereydooni\Shopping\app\Models\Employee $employee, array $data)
 * @method static bool deleteEmployee(\Fereydooni\Shopping\app\Models\Employee $employee)
 *
 * // Employee onboarding and offboarding
 * @method static \Fereydooni\Shopping\app\DTOs\EmployeeDTO onboardEmployee(array $data)
 * @method static bool completeOnboarding(\Fereydooni\Shopping\app\Models\Employee $employee)
 * @method static bool offboardEmployee(\Fereydooni\Shopping\app\Models\Employee $employee, string $reason = null)
 *
 * // Employee profile management
 * @method static \Fereydooni\Shopping\app\DTOs\EmployeeDTO updateEmployeeProfile(\Fereydooni\Shopping\app\Models\Employee $employee, array $data)
 * @method static \Fereydooni\Shopping\app\DTOs\EmployeeDTO updateContactInfo(\Fereydooni\Shopping\app\Models\Employee $employee, array $contactData)
 *
 * // Employee status management
 * @method static bool activateEmployee(\Fereydooni\Shopping\app\Models\Employee $employee)
 * @method static bool deactivateEmployee(\Fereydooni\Shopping\app\Models\Employee $employee)
 * @method static bool terminateEmployee(\Fereydooni\Shopping\app\Models\Employee $employee, string $reason = null)
 * @method static bool rehireEmployee(\Fereydooni\Shopping\app\Models\Employee $employee, string $hireDate = null)
 *
 * // Salary and compensation management
 * @method static bool updateSalary(\Fereydooni\Shopping\app\Models\Employee $employee, float $newSalary, string $effectiveDate = null)
 * @method static bool updatePosition(\Fereydooni\Shopping\app\Models\Employee $employee, string $newPosition, string $effectiveDate = null)
 * @method static bool updateDepartment(\Fereydooni\Shopping\app\Models\Employee $employee, string $newDepartment, string $effectiveDate = null)
 *
 * // Performance management
 * @method static bool updatePerformanceRating(\Fereydooni\Shopping\app\Models\Employee $employee, float $rating, string $reviewDate = null)
 * @method static bool schedulePerformanceReview(\Fereydooni\Shopping\app\Models\Employee $employee, string $reviewDate)
 * @method static \Illuminate\Database\Eloquent\Collection getEmployeesWithUpcomingReviews(int $daysAhead = 30)
 * @method static \Illuminate\Database\Eloquent\Collection getEmployeesNeedingReviews()
 * @method static \Illuminate\Database\Eloquent\Collection getTopPerformers(int $limit = 10)
 *
 * // Time-off management
 * @method static bool requestTimeOff(\Fereydooni\Shopping\app\Models\Employee $employee, string $type, int $days, string $startDate, string $endDate, string $reason = null)
 * @method static bool addVacationDays(\Fereydooni\Shopping\app\Models\Employee $employee, int $days)
 * @method static bool useVacationDays(\Fereydooni\Shopping\app\Models\Employee $employee, int $days)
 * @method static bool addSickDays(\Fereydooni\Shopping\app\Models\Employee $employee, int $days)
 * @method static bool useSickDays(\Fereydooni\Shopping\app\Models\Employee $employee, int $days)
 * @method static \Illuminate\Database\Eloquent\Collection getEmployeesWithLowVacationDays(int $threshold = 5)
 *
 * // Benefits administration
 * @method static array enrollInBenefits(\Fereydooni\Shopping\app\Models\Employee $employee)
 * @method static array unenrollFromBenefits(\Fereydooni\Shopping\app\Models\Employee $employee)
 * @method static bool isEmployeeEligibleForBenefits(\Fereydooni\Shopping\app\Models\Employee $employee)
 * @method static \Illuminate\Database\Eloquent\Collection getEmployeesEnrolledInBenefits()
 * @method static \Illuminate\Database\Eloquent\Collection getEmployeesNotEnrolledInBenefits()
 *
 * // Employee hierarchy management
 * @method static bool assignManager(\Fereydooni\Shopping\app\Models\Employee $employee, int $managerId)
 * @method static bool removeManager(\Fereydooni\Shopping\app\Models\Employee $employee)
 * @method static \Illuminate\Database\Eloquent\Collection getEmployeeSubordinates(int $employeeId)
 * @method static \Illuminate\Database\Eloquent\Collection getEmployeeManagers(int $employeeId)
 * @method static \Fereydooni\Shopping\app\Models\Employee|null getDirectManager(int $employeeId)
 * @method static \Illuminate\Database\Eloquent\Collection getAllManagers()
 * @method static array getOrganizationalChart(int $rootEmployeeId = null)
 *
 * // Employee training and development
 * @method static bool updateTrainingRecords(\Fereydooni\Shopping\app\Models\Employee $employee, array $trainingData)
 * @method static bool updateSkills(\Fereydooni\Shopping\app\Models\Employee $employee, array $skills)
 * @method static bool updateCertifications(\Fereydooni\Shopping\app\Models\Employee $employee, array $certifications)
 * @method static array getEmployeeSkills(int $employeeId)
 * @method static array getEmployeeCertifications(int $employeeId)
 *
 * // Employee retention strategies
 * @method static \Illuminate\Database\Eloquent\Collection identifyAtRiskEmployees()
 * @method static array generateRetentionRecommendations(\Fereydooni\Shopping\app\Models\Employee $employee)
 *
 * // Employee communication management
 * @method static bool sendEmployeeNotification(\Fereydooni\Shopping\app\Models\Employee $employee, string $type, string $message)
 * @method static array sendBulkEmployeeNotifications(\Illuminate\Database\Eloquent\Collection $employees, string $type, string $message)
 *
 * // Employee analytics and reporting
 * @method static array getEmployeeDashboardData()
 * @method static array generateEmployeeReport(string $department = null, string $period = 'current')
 * @method static array getEmployeeStats()
 * @method static array getEmployeeStatsByStatus()
 * @method static array getEmployeeStatsByDepartment()
 * @method static array getEmployeeStatsByEmploymentType()
 * @method static array getEmployeeDemographics()
 * @method static array getPerformanceDistribution()
 * @method static array getSalaryDistribution()
 * @method static array getTimeOffStats()
 * @method static array getBenefitsEnrollmentStats()
 * @method static array getHierarchyStats()
 *
 * // Employee queries
 * @method static \Fereydooni\Shopping\app\Models\Employee|null findByUserId(int $userId)
 * @method static \Fereydooni\Shopping\app\Models\Employee|null findByEmail(string $email)
 * @method static \Fereydooni\Shopping\app\Models\Employee|null findByPhone(string $phone)
 * @method static \Fereydooni\Shopping\app\Models\Employee|null findByEmployeeNumber(string $employeeNumber)
 * @method static \Illuminate\Database\Eloquent\Collection findByStatus(string $status)
 * @method static \Illuminate\Database\Eloquent\Collection findByEmploymentType(string $employmentType)
 * @method static \Illuminate\Database\Eloquent\Collection findByDepartment(string $department)
 * @method static \Illuminate\Database\Eloquent\Collection findByPosition(string $position)
 * @method static \Illuminate\Database\Eloquent\Collection findActive()
 * @method static \Illuminate\Database\Eloquent\Collection findInactive()
 * @method static \Illuminate\Database\Eloquent\Collection findTerminated()
 * @method static \Illuminate\Database\Eloquent\Collection findBySalaryRange(float $minSalary, float $maxSalary)
 * @method static \Illuminate\Database\Eloquent\Collection findByPerformanceRating(float $minRating, float $maxRating)
 * @method static \Illuminate\Database\Eloquent\Collection findByHireDateRange(string $startDate, string $endDate)
 *
 * // Employee search
 * @method static \Illuminate\Database\Eloquent\Collection search(string $query)
 * @method static \Illuminate\Database\Eloquent\Collection searchByDepartment(string $department, string $query)
 * @method static \Illuminate\Database\Eloquent\Collection searchByPosition(string $position, string $query)
 *
 * // Employee statistics
 * @method static int getEmployeeCount()
 * @method static int getActiveEmployeeCount()
 * @method static int getInactiveEmployeeCount()
 * @method static int getTerminatedEmployeeCount()
 * @method static int getEmployeeCountByStatus(string $status)
 * @method static int getEmployeeCountByDepartment(string $department)
 * @method static int getEmployeeCountByEmploymentType(string $employmentType)
 * @method static float getTotalSalary()
 * @method static float getAverageSalary()
 * @method static float getAveragePerformanceRating()
 *
 * // Employee utilities
 * @method static string generateEmployeeNumber()
 * @method static bool isEmployeeNumberUnique(string $employeeNumber)
 * @method static bool validateEmployee(array $data)
 * @method static bool addEmployeeNote(\Fereydooni\Shopping\app\Models\Employee $employee, string $note, string $type = 'general')
 * @method static \Illuminate\Database\Eloquent\Collection getEmployeeNotes(\Fereydooni\Shopping\app\Models\Employee $employee)
 */
class Employee extends Facade
{
    /**
     * Get the registered name of the component.
     */
    protected static function getFacadeAccessor(): string
    {
        return EmployeeService::class;
    }
}
