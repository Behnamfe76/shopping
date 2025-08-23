<?php

namespace App\Facades;

use App\Models\EmployeeSkill;
use App\DTOs\EmployeeSkillDTO;
use App\Repositories\Interfaces\EmployeeSkillRepositoryInterface;
use App\Traits\HasEmployeeSkillOperations;
use App\Traits\HasEmployeeSkillVerificationManagement;
use App\Traits\HasEmployeeSkillCertificationManagement;
use App\Traits\HasEmployeeSkillAnalytics;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Facade;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

/**
 * @method static Collection getAllEmployeeSkills()
 * @method static Collection getEmployeeSkills(int $employeeId)
 * @method static Collection getEmployeeSkillsDTO(int $employeeId)
 * @method static EmployeeSkill|null findEmployeeSkill(int $skillId)
 * @method static EmployeeSkillDTO|null findEmployeeSkillDTO(int $skillId)
 * @method static EmployeeSkill createEmployeeSkill(array $data)
 * @method static EmployeeSkillDTO createEmployeeSkillDTO(array $data)
 * @method static bool updateEmployeeSkill(EmployeeSkill $skill, array $data)
 * @method static EmployeeSkillDTO|null updateEmployeeSkillDTO(EmployeeSkill $skill, array $data)
 * @method static bool deleteEmployeeSkill(EmployeeSkill $skill)
 * @method static Collection getEmployeeSkillsByName(string $skillName)
 * @method static Collection getEmployeeSkillsByNameDTO(string $skillName)
 * @method static Collection getEmployeeSkillsByCategory(string $skillCategory)
 * @method static Collection getEmployeeSkillsByCategoryDTO(string $skillCategory)
 * @method static Collection getEmployeeSkillsByLevel(string $proficiencyLevel)
 * @method static Collection getEmployeeSkillsByLevelDTO(string $proficiencyLevel)
 * @method static Collection getEmployeeSkillsByEmployeeAndCategory(int $employeeId, string $skillCategory)
 * @method static Collection getEmployeeSkillsByEmployeeAndCategoryDTO(int $employeeId, string $skillCategory)
 * @method static bool verifyEmployeeSkill(EmployeeSkill $skill, int $verifiedBy)
 * @method static bool unverifyEmployeeSkill(EmployeeSkill $skill)
 * @method static Collection getEmployeeVerifiedSkills(int $employeeId)
 * @method static Collection getEmployeeVerifiedSkillsDTO(int $employeeId)
 * @method static Collection getEmployeeUnverifiedSkills(int $employeeId)
 * @method static Collection getEmployeeUnverifiedSkillsDTO(int $employeeId)
 * @method static array getEmployeeVerificationStats(int $employeeId)
 * @method static array getAllEmployeesVerificationStats()
 * @method static Collection getSkillsPendingVerification()
 * @method static Collection getSkillsPendingVerificationDTO()
 * @method static bool addCertificationToSkill(EmployeeSkill $skill, array $certData)
 * @method static bool updateCertificationForSkill(EmployeeSkill $skill, array $certData)
 * @method static bool removeCertificationFromSkill(EmployeeSkill $skill)
 * @method static Collection getEmployeeCertifiedSkills(int $employeeId)
 * @method static Collection getEmployeeCertifiedSkillsDTO(int $employeeId)
 * @method static Collection getSkillsWithExpiringCertifications(int $days = 30)
 * @method static Collection getSkillsWithExpiringCertificationsDTO(int $days = 30)
 * @method static array getEmployeeCertificationStats(int $employeeId)
 * @method static array getAllEmployeesCertificationStats()
 * @method static array getEmployeeSkillsAnalytics(int $employeeId)
 * @method static array getAllEmployeesSkillsAnalytics()
 * @method static array getEmployeeSkillGaps(int $employeeId)
 * @method static array getCompanySkillGaps()
 * @method static array getSkillsTrends(string $startDate = null, string $endDate = null)
 * @method static array getTopSkills(int $limit = 10)
 */
class EmployeeSkill extends Facade
{
    use HasEmployeeSkillOperations,
        HasEmployeeSkillVerificationManagement,
        HasEmployeeSkillCertificationManagement,
        HasEmployeeSkillAnalytics;

    protected static function getFacadeAccessor()
    {
        return 'employee-skill';
    }

    /**
     * Get the repository instance
     */
    protected static function getRepository(): EmployeeSkillRepositoryInterface
    {
        return app(EmployeeSkillRepositoryInterface::class);
    }

    /**
     * Set the repository instance
     */
    public static function setRepository(EmployeeSkillRepositoryInterface $repository): void
    {
        static::$employeeSkillRepository = $repository;
    }

    /**
     * Initialize the facade with repository
     */
    public static function init(): void
    {
        $facade = new static();
        $facade->employeeSkillRepository = static::getRepository();
        
        // Store the instance for static access
        static::$resolvedInstance['employee-skill'] = $facade;
    }

    /**
     * Get employee skills with advanced filtering
     */
    public static function getEmployeeSkillsAdvanced(int $employeeId, array $filters = []): Collection
    {
        try {
            $query = static::getRepository()->findByEmployeeId($employeeId);
            
            // Apply filters
            if (isset($filters['category'])) {
                $query = $query->where('skill_category', $filters['category']);
            }
            
            if (isset($filters['level'])) {
                $query = $query->where('proficiency_level', $filters['level']);
            }
            
            if (isset($filters['verified'])) {
                $query = $query->where('is_verified', $filters['verified']);
            }
            
            if (isset($filters['certified'])) {
                if ($filters['certified']) {
                    $query = $query->whereNotNull('certification_name');
                } else {
                    $query = $query->whereNull('certification_name');
                }
            }
            
            if (isset($filters['active'])) {
                $query = $query->where('is_active', $filters['active']);
            }
            
            return $query;
        } catch (\Exception $e) {
            Log::error('Failed to get employee skills with advanced filtering', [
                'employee_id' => $employeeId,
                'filters' => $filters,
                'error' => $e->getMessage()
            ]);
            return collect();
        }
    }

    /**
     * Search skills across all employees
     */
    public static function searchAllSkills(string $query, array $filters = []): Collection
    {
        try {
            $results = static::getRepository()->searchSkills($query);
            
            // Apply additional filters
            if (isset($filters['category'])) {
                $results = $results->where('skill_category', $filters['category']);
            }
            
            if (isset($filters['level'])) {
                $results = $results->where('proficiency_level', $filters['level']);
            }
            
            if (isset($filters['verified'])) {
                $results = $results->where('is_verified', $filters['verified']);
            }
            
            return $results;
        } catch (\Exception $e) {
            Log::error('Failed to search all skills', [
                'query' => $query,
                'filters' => $filters,
                'error' => $e->getMessage()
            ]);
            return collect();
        }
    }

    /**
     * Get skills statistics with caching
     */
    public static function getSkillsStatisticsCached(int $employeeId = null, int $ttl = 3600): array
    {
        $cacheKey = $employeeId ? "skills_stats_employee_{$employeeId}" : 'skills_stats_all';
        
        return Cache::remember($cacheKey, $ttl, function () use ($employeeId) {
            if ($employeeId) {
                return static::getRepository()->getSkillsStatistics($employeeId);
            }
            return static::getRepository()->getSkillsStatistics();
        });
    }

    /**
     * Export skills data with filters
     */
    public static function exportSkillsDataFiltered(array $filters = []): string
    {
        try {
            return static::getRepository()->exportSkillsData($filters);
        } catch (\Exception $e) {
            Log::error('Failed to export skills data', [
                'filters' => $filters,
                'error' => $e->getMessage()
            ]);
            return '';
        }
    }

    /**
     * Import skills data with validation
     */
    public static function importSkillsDataValidated(string $data, array $options = []): array
    {
        try {
            $result = static::getRepository()->importSkillsData($data);
            
            return [
                'success' => $result,
                'message' => $result ? 'Skills imported successfully' : 'Failed to import skills',
                'timestamp' => now()->toISOString(),
            ];
        } catch (\Exception $e) {
            Log::error('Failed to import skills data', [
                'options' => $options,
                'error' => $e->getMessage()
            ]);
            
            return [
                'success' => false,
                'message' => 'Error importing skills: ' . $e->getMessage(),
                'timestamp' => now()->toISOString(),
            ];
        }
    }

    /**
     * Get skills dashboard data
     */
    public static function getSkillsDashboard(int $employeeId = null): array
    {
        try {
            $data = [];
            
            if ($employeeId) {
                $data['employee_stats'] = static::getSkillsStatisticsCached($employeeId);
                $data['employee_analytics'] = static::getEmployeeSkillsAnalytics($employeeId);
                $data['employee_gaps'] = static::getEmployeeSkillGaps($employeeId);
                $data['recent_skills'] = static::getRepository()
                    ->findByEmployeeId($employeeId)
                    ->sortByDesc('created_at')
                    ->take(5);
            } else {
                $data['company_stats'] = static::getSkillsStatisticsCached();
                $data['company_analytics'] = static::getAllEmployeesSkillsAnalytics();
                $data['company_gaps'] = static::getCompanySkillGaps();
                $data['top_skills'] = static::getTopSkills(5);
                $data['recent_skills'] = static::getRepository()
                    ->all()
                    ->sortByDesc('created_at')
                    ->take(10);
            }
            
            $data['timestamp'] = now()->toISOString();
            
            return $data;
        } catch (\Exception $e) {
            Log::error('Failed to get skills dashboard', [
                'employee_id' => $employeeId,
                'error' => $e->getMessage()
            ]);
            return ['error' => 'Failed to load dashboard data'];
        }
    }

    /**
     * Clear all skill-related caches
     */
    public static function clearAllCaches(): void
    {
        try {
            Cache::flush();
            Log::info('All skill-related caches cleared');
        } catch (\Exception $e) {
            Log::error('Failed to clear caches', [
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Get skill recommendations for an employee
     */
    public static function getSkillRecommendations(int $employeeId, int $limit = 5): array
    {
        try {
            $currentSkills = static::getRepository()->findByEmployeeId($employeeId);
            $recommendations = [];
            
            // Get complementary skills
            $complementarySkills = static::getComplementarySkills($currentSkills->pluck('skill_name')->toArray());
            if (!empty($complementarySkills)) {
                $recommendations['complementary'] = array_slice($complementarySkills, 0, $limit);
            }
            
            // Get improvement recommendations
            $improvements = static::getSkillImprovementRecommendations($currentSkills);
            if (!empty($improvements)) {
                $recommendations['improvements'] = array_slice($improvements, 0, $limit);
            }
            
            return $recommendations;
        } catch (\Exception $e) {
            Log::error('Failed to get skill recommendations', [
                'employee_id' => $employeeId,
                'error' => $e->getMessage()
            ]);
            return [];
        }
    }

    /**
     * Get complementary skills
     */
    private static function getComplementarySkills(array $currentSkills): array
    {
        $complementaryMap = [
            'PHP' => ['MySQL', 'JavaScript', 'HTML', 'CSS'],
            'JavaScript' => ['HTML', 'CSS', 'React', 'Node.js'],
            'Python' => ['Django', 'Flask', 'Pandas', 'NumPy'],
            'Java' => ['Spring', 'Hibernate', 'Maven', 'JUnit'],
        ];
        
        $recommendations = [];
        foreach ($currentSkills as $skill) {
            if (isset($complementaryMap[$skill])) {
                $recommendations = array_merge($recommendations, $complementaryMap[$skill]);
            }
        }
        
        return array_unique($recommendations);
    }

    /**
     * Get skill improvement recommendations
     */
    private static function getSkillImprovementRecommendations(Collection $skills): array
    {
        $recommendations = [];
        
        foreach ($skills as $skill) {
            if ($skill->getProficiencyNumericValue() < 4) {
                $recommendations[] = [
                    'skill_name' => $skill->skill_name,
                    'current_level' => $skill->proficiency_level->value,
                    'action' => 'Improve proficiency',
                    'priority' => $skill->is_required ? 'High' : 'Medium',
                ];
            }
        }
        
        return $recommendations;
    }
}
