<?php

namespace App\Traits;

use App\Models\EmployeeSkill;
use App\Repositories\Interfaces\EmployeeSkillRepositoryInterface;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

trait HasEmployeeSkillAnalytics
{
    protected EmployeeSkillRepositoryInterface $employeeSkillRepository;

    /**
     * Get comprehensive skills analytics for an employee
     */
    public function getEmployeeSkillsAnalytics(int $employeeId): array
    {
        return Cache::remember("employee_skills_analytics_{$employeeId}", 3600, function () use ($employeeId) {
            $totalSkills = $this->employeeSkillRepository->getEmployeeSkillCount($employeeId);
            $verifiedSkills = $this->employeeSkillRepository->findVerified()->where('employee_id', $employeeId)->count();
            $certifiedSkills = $this->employeeSkillRepository->findCertified()->where('employee_id', $employeeId)->count();
            $primarySkills = $this->employeeSkillRepository->findPrimary()->where('employee_id', $employeeId)->count();
            $requiredSkills = $this->employeeSkillRepository->findRequired()->where('employee_id', $employeeId)->count();

            $totalExperience = $this->employeeSkillRepository->getEmployeeTotalExperience($employeeId);
            $avgProficiency = $this->employeeSkillRepository->getEmployeeAverageProficiency($employeeId);

            $categoryDistribution = $this->getEmployeeSkillsByCategoryDistribution($employeeId);
            $proficiencyDistribution = $this->getEmployeeSkillsByProficiencyDistribution($employeeId);

            return [
                'employee_id' => $employeeId,
                'total_skills' => $totalSkills,
                'verified_skills' => $verifiedSkills,
                'certified_skills' => $certifiedSkills,
                'primary_skills' => $primarySkills,
                'required_skills' => $requiredSkills,
                'total_experience' => $totalExperience,
                'average_proficiency' => $avgProficiency,
                'verification_rate' => $totalSkills > 0 ? round(($verifiedSkills / $totalSkills) * 100, 2) : 0,
                'certification_rate' => $totalSkills > 0 ? round(($certifiedSkills / $totalSkills) * 100, 2) : 0,
                'category_distribution' => $categoryDistribution,
                'proficiency_distribution' => $proficiencyDistribution,
                'skill_growth_trend' => $this->getEmployeeSkillGrowthTrend($employeeId),
                'proficiency_improvement' => $this->getEmployeeProficiencyImprovement($employeeId),
                'skill_gaps' => $this->getEmployeeSkillGaps($employeeId),
                'recommendations' => $this->getEmployeeSkillRecommendations($employeeId),
            ];
        });
    }

    /**
     * Get skills analytics for all employees
     */
    public function getAllEmployeesSkillsAnalytics(): array
    {
        return Cache::remember('all_employees_skills_analytics', 3600, function () {
            $totalSkills = $this->employeeSkillRepository->getTotalSkillCount();
            $verifiedSkills = $this->employeeSkillRepository->getTotalVerifiedSkillsCount();
            $certifiedSkills = $this->employeeSkillRepository->getTotalCertifiedSkillsCount();

            $categoryStats = $this->getAllSkillsByCategoryDistribution();
            $proficiencyStats = $this->getAllSkillsByProficiencyDistribution();
            $experienceStats = $this->getAllSkillsExperienceDistribution();

            return [
                'total_skills' => $totalSkills,
                'verified_skills' => $verifiedSkills,
                'certified_skills' => $certifiedSkills,
                'verification_rate' => $totalSkills > 0 ? round(($verifiedSkills / $totalSkills) * 100, 2) : 0,
                'certification_rate' => $totalSkills > 0 ? round(($certifiedSkills / $totalSkills) * 100, 2) : 0,
                'category_distribution' => $categoryStats,
                'proficiency_distribution' => $proficiencyStats,
                'experience_distribution' => $experienceStats,
                'top_skills' => $this->getTopSkills(),
                'skill_trends' => $this->getSkillsTrends(),
                'company_skill_gaps' => $this->getCompanySkillGaps(),
            ];
        });
    }

    /**
     * Get skills analytics for a department
     */
    public function getDepartmentSkillsAnalytics(int $departmentId): array
    {
        return Cache::remember("department_skills_analytics_{$departmentId}", 3600, function () use ($departmentId) {
            // This would need to be implemented based on your department structure
            // For now, returning basic structure
            return [
                'department_id' => $departmentId,
                'total_employees' => 0,
                'total_skills' => 0,
                'average_skills_per_employee' => 0,
                'verification_rate' => 0,
                'certification_rate' => 0,
                'category_distribution' => [],
                'proficiency_distribution' => [],
                'skill_gaps' => [],
            ];
        });
    }

    /**
     * Get skill gaps analysis for an employee
     */
    public function getEmployeeSkillGaps(int $employeeId): array
    {
        return Cache::remember("employee_skill_gaps_{$employeeId}", 3600, function () use ($employeeId) {
            return $this->employeeSkillRepository->getSkillsGapAnalysis($employeeId);
        });
    }

    /**
     * Get skill gaps analysis for the company
     */
    public function getCompanySkillGaps(): array
    {
        return Cache::remember('company_skill_gaps', 3600, function () {
            return $this->employeeSkillRepository->getSkillsGapAnalysis();
        });
    }

    /**
     * Get skills trends over time
     */
    public function getSkillsTrends(?string $startDate = null, ?string $endDate = null): array
    {
        $cacheKey = 'skills_trends_'.($startDate ?? 'all').'_'.($endDate ?? 'all');

        return Cache::remember($cacheKey, 1800, function () use ($startDate, $endDate) {
            return $this->employeeSkillRepository->getSkillsTrends($startDate, $endDate);
        });
    }

    /**
     * Get top skills across the company
     */
    public function getTopSkills(int $limit = 10): array
    {
        return Cache::remember("top_skills_{$limit}", 3600, function () use ($limit) {
            $skills = $this->employeeSkillRepository->all();

            $skillCounts = [];
            foreach ($skills as $skill) {
                $skillName = $skill->skill_name;
                if (! isset($skillCounts[$skillName])) {
                    $skillCounts[$skillName] = [
                        'name' => $skillName,
                        'count' => 0,
                        'categories' => [],
                        'avg_proficiency' => 0,
                        'total_experience' => 0,
                    ];
                }

                $skillCounts[$skillName]['count']++;
                $skillCounts[$skillName]['categories'][] = $skill->skill_category->value;
                $skillCounts[$skillName]['avg_proficiency'] += $skill->getProficiencyNumericValue();
                $skillCounts[$skillName]['total_experience'] += $skill->years_experience;
            }

            // Calculate averages
            foreach ($skillCounts as &$skill) {
                $skill['avg_proficiency'] = round($skill['avg_proficiency'] / $skill['count'], 2);
                $skill['categories'] = array_unique($skill['categories']);
            }

            // Sort by count and limit
            uasort($skillCounts, function ($a, $b) {
                return $b['count'] - $a['count'];
            });

            return array_slice($skillCounts, 0, $limit);
        });
    }

    /**
     * Get skills by category distribution for an employee
     */
    private function getEmployeeSkillsByCategoryDistribution(int $employeeId): array
    {
        $skills = $this->employeeSkillRepository->findByEmployeeId($employeeId);

        $distribution = [];
        foreach ($skills as $skill) {
            $category = $skill->skill_category->value;
            if (! isset($distribution[$category])) {
                $distribution[$category] = 0;
            }
            $distribution[$category]++;
        }

        return $distribution;
    }

    /**
     * Get skills by proficiency distribution for an employee
     */
    private function getEmployeeSkillsByProficiencyDistribution(int $employeeId): array
    {
        $skills = $this->employeeSkillRepository->findByEmployeeId($employeeId);

        $distribution = [];
        foreach ($skills as $skill) {
            $level = $skill->proficiency_level->value;
            if (! isset($distribution[$level])) {
                $distribution[$level] = 0;
            }
            $distribution[$level]++;
        }

        return $distribution;
    }

    /**
     * Get all skills by category distribution
     */
    private function getAllSkillsByCategoryDistribution(): array
    {
        $skills = $this->employeeSkillRepository->all();

        $distribution = [];
        foreach ($skills as $skill) {
            $category = $skill->skill_category->value;
            if (! isset($distribution[$category])) {
                $distribution[$category] = 0;
            }
            $distribution[$category]++;
        }

        return $distribution;
    }

    /**
     * Get all skills by proficiency distribution
     */
    private function getAllSkillsByProficiencyDistribution(): array
    {
        $skills = $this->employeeSkillRepository->all();

        $distribution = [];
        foreach ($skills as $skill) {
            $level = $skill->proficiency_level->value;
            if (! isset($distribution[$level])) {
                $distribution[$level] = 0;
            }
            $distribution[$level]++;
        }

        return $distribution;
    }

    /**
     * Get all skills experience distribution
     */
    private function getAllSkillsExperienceDistribution(): array
    {
        $skills = $this->employeeSkillRepository->all();

        $distribution = [
            '0-1 years' => 0,
            '1-3 years' => 0,
            '3-5 years' => 0,
            '5-10 years' => 0,
            '10+ years' => 0,
        ];

        foreach ($skills as $skill) {
            $experience = $skill->years_experience;

            if ($experience <= 1) {
                $distribution['0-1 years']++;
            } elseif ($experience <= 3) {
                $distribution['1-3 years']++;
            } elseif ($experience <= 5) {
                $distribution['3-5 years']++;
            } elseif ($experience <= 10) {
                $distribution['5-10 years']++;
            } else {
                $distribution['10+ years']++;
            }
        }

        return $distribution;
    }

    /**
     * Get employee skill growth trend
     */
    private function getEmployeeSkillGrowthTrend(int $employeeId): array
    {
        $skills = $this->employeeSkillRepository->findByEmployeeId($employeeId);

        $monthlyGrowth = [];
        foreach ($skills as $skill) {
            $month = $skill->created_at->format('Y-m');
            if (! isset($monthlyGrowth[$month])) {
                $monthlyGrowth[$month] = 0;
            }
            $monthlyGrowth[$month]++;
        }

        ksort($monthlyGrowth);

        return [
            'monthly_growth' => $monthlyGrowth,
            'total_growth' => array_sum($monthlyGrowth),
            'growth_rate' => count($monthlyGrowth) > 1 ?
                round((end($monthlyGrowth) - reset($monthlyGrowth)) / reset($monthlyGrowth) * 100, 2) : 0,
        ];
    }

    /**
     * Get employee proficiency improvement
     */
    private function getEmployeeProficiencyImprovement(int $employeeId): array
    {
        $skills = $this->employeeSkillRepository->findByEmployeeId($employeeId);

        $improvements = [];
        foreach ($skills as $skill) {
            $improvements[] = [
                'skill_name' => $skill->skill_name,
                'current_level' => $skill->proficiency_level->value,
                'current_numeric' => $skill->getProficiencyNumericValue(),
                'experience' => $skill->years_experience,
                'improvement_potential' => $this->calculateImprovementPotential($skill),
            ];
        }

        return [
            'skills' => $improvements,
            'average_improvement_potential' => count($improvements) > 0 ?
                round(array_sum(array_column($improvements, 'improvement_potential')) / count($improvements), 2) : 0,
        ];
    }

    /**
     * Calculate improvement potential for a skill
     */
    private function calculateImprovementPotential(EmployeeSkill $skill): float
    {
        $maxLevel = 5; // Assuming 5 is the highest proficiency level
        $currentLevel = $skill->getProficiencyNumericValue();
        $experience = $skill->years_experience;

        // Base improvement potential
        $potential = $maxLevel - $currentLevel;

        // Adjust based on experience
        if ($experience < 2) {
            $potential *= 1.5; // High potential for new skills
        } elseif ($experience < 5) {
            $potential *= 1.2; // Medium potential
        } else {
            $potential *= 0.8; // Lower potential for experienced skills
        }

        return round($potential, 2);
    }

    /**
     * Get employee skill recommendations
     */
    private function getEmployeeSkillRecommendations(int $employeeId): array
    {
        $currentSkills = $this->employeeSkillRepository->findByEmployeeId($employeeId);
        $skillNames = $currentSkills->pluck('skill_name')->toArray();

        $recommendations = [];

        // Recommend complementary skills
        $complementarySkills = $this->getComplementarySkills($skillNames);
        if (! empty($complementarySkills)) {
            $recommendations['complementary_skills'] = $complementarySkills;
        }

        // Recommend skill improvements
        $improvementSkills = $this->getSkillImprovementRecommendations($currentSkills);
        if (! empty($improvementSkills)) {
            $recommendations['improvement_skills'] = $improvementSkills;
        }

        // Recommend new skill categories
        $newCategories = $this->getNewCategoryRecommendations($currentSkills);
        if (! empty($newCategories)) {
            $recommendations['new_categories'] = $newCategories;
        }

        return $recommendations;
    }

    /**
     * Get complementary skills based on current skills
     */
    private function getComplementarySkills(array $currentSkillNames): array
    {
        // This is a simplified implementation
        // In a real system, you might use ML or predefined skill relationships
        $complementaryMap = [
            'PHP' => ['MySQL', 'JavaScript', 'HTML', 'CSS'],
            'JavaScript' => ['HTML', 'CSS', 'React', 'Node.js'],
            'Python' => ['Django', 'Flask', 'Pandas', 'NumPy'],
            'Java' => ['Spring', 'Hibernate', 'Maven', 'JUnit'],
        ];

        $recommendations = [];
        foreach ($currentSkillNames as $skill) {
            if (isset($complementaryMap[$skill])) {
                $recommendations = array_merge($recommendations, $complementaryMap[$skill]);
            }
        }

        return array_unique($recommendations);
    }

    /**
     * Get skill improvement recommendations
     */
    private function getSkillImprovementRecommendations(Collection $currentSkills): array
    {
        $recommendations = [];

        foreach ($currentSkills as $skill) {
            if ($skill->getProficiencyNumericValue() < 4) { // Assuming 4+ is advanced
                $recommendations[] = [
                    'skill_name' => $skill->skill_name,
                    'current_level' => $skill->proficiency_level->value,
                    'recommended_action' => 'Improve proficiency',
                    'priority' => $skill->is_required ? 'High' : 'Medium',
                ];
            }
        }

        return $recommendations;
    }

    /**
     * Get new category recommendations
     */
    private function getNewCategoryRecommendations(Collection $currentSkills): array
    {
        $currentCategories = $currentSkills->pluck('skill_category')->unique()->pluck('value')->toArray();
        $allCategories = ['technical', 'soft_skills', 'languages', 'tools', 'methodologies', 'certifications', 'other'];

        $missingCategories = array_diff($allCategories, $currentCategories);

        $recommendations = [];
        foreach ($missingCategories as $category) {
            $recommendations[] = [
                'category' => $category,
                'reason' => 'Diversify skill portfolio',
                'priority' => 'Medium',
            ];
        }

        return $recommendations;
    }

    /**
     * Clear analytics-related caches
     */
    private function clearAnalyticsCaches(?int $employeeId = null): void
    {
        if ($employeeId) {
            Cache::forget("employee_skills_analytics_{$employeeId}");
            Cache::forget("employee_skill_gaps_{$employeeId}");
        }

        Cache::forget('all_employees_skills_analytics');
        Cache::forget('company_skill_gaps');
        Cache::forget('top_skills_10');
        Cache::forget('skills_trends_all_all');
    }
}
