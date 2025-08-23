<?php

namespace App\Traits;

use App\Models\EmployeePosition;
use App\Models\Employee;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

trait HasEmployeePositionSkillsMatching
{
    /**
     * Add skill requirement to position
     */
    public function addSkillRequirement(EmployeePosition $position, string $skill): bool
    {
        try {
            $currentSkills = $position->skills_required ?? [];

            if (!in_array($skill, $currentSkills)) {
                $currentSkills[] = $skill;
                $position->update(['skills_required' => $currentSkills]);

                // Clear cache
                $this->clearSkillsCache($position);

                // Log the action
                Log::info("Skill requirement added to position {$position->title}", [
                    'position_id' => $position->id,
                    'skill' => $skill,
                    'user_id' => auth()->id()
                ]);
            }

            return true;
        } catch (\Exception $e) {
            Log::error("Failed to add skill requirement to position {$position->id}", [
                'error' => $e->getMessage(),
                'position_id' => $position->id,
                'skill' => $skill
            ]);
            return false;
        }
    }

    /**
     * Remove skill requirement from position
     */
    public function removeSkillRequirement(EmployeePosition $position, string $skill): bool
    {
        try {
            $currentSkills = $position->skills_required ?? [];

            if (in_array($skill, $currentSkills)) {
                $currentSkills = array_diff($currentSkills, [$skill]);
                $position->update(['skills_required' => array_values($currentSkills)]);

                // Clear cache
                $this->clearSkillsCache($position);

                // Log the action
                Log::info("Skill requirement removed from position {$position->title}", [
                    'position_id' => $position->id,
                    'skill' => $skill,
                    'user_id' => auth()->id()
                ]);
            }

            return true;
        } catch (\Exception $e) {
            Log::error("Failed to remove skill requirement from position {$position->id}", [
                'error' => $e->getMessage(),
                'position_id' => $position->id,
                'skill' => $skill
            ]);
            return false;
        }
    }

    /**
     * Get positions by required skills
     */
    public function getPositionsBySkills(array $skills): Collection
    {
        $cacheKey = 'positions.skills.' . md5(serialize($skills));

        return Cache::remember($cacheKey, 3600, function () use ($skills) {
            return EmployeePosition::where('is_active', true)
                ->whereJsonContains('skills_required', $skills)
                ->with(['department'])
                ->get();
        });
    }

    /**
     * Find employees that match position requirements
     */
    public function findMatchingEmployees(EmployeePosition $position, int $limit = 10): Collection
    {
        $cacheKey = "positions.matching.employees.{$position->id}";

        return Cache::remember($cacheKey, 1800, function () use ($position, $limit) {
            $requiredSkills = $position->skills_required ?? [];
            $requiredExperience = $position->experience_required ?? 0;
            $requiredEducation = $position->education_required ?? '';

            $query = Employee::where('is_active', true)
                ->where('position_id', '!=', $position->id); // Exclude current position holders

            // Match by skills if available
            if (!empty($requiredSkills)) {
                $query->whereJsonContains('skills', $requiredSkills);
            }

            // Match by experience level
            if ($requiredExperience > 0) {
                $query->where('years_experience', '>=', $requiredExperience);
            }

            // Match by education level
            if (!empty($requiredEducation)) {
                $query->where('education_level', '>=', $requiredEducation);
            }

            return $query->with(['currentPosition', 'department'])
                ->orderBy('years_experience', 'desc')
                ->limit($limit)
                ->get();
        });
    }

    /**
     * Calculate skills match percentage for an employee
     */
    public function calculateSkillsMatchPercentage(EmployeePosition $position, Employee $employee): float
    {
        $requiredSkills = $position->skills_required ?? [];
        $employeeSkills = $employee->skills ?? [];

        if (empty($requiredSkills)) {
            return 100.0; // No skills required means 100% match
        }

        if (empty($employeeSkills)) {
            return 0.0; // No skills means 0% match
        }

        $matchingSkills = array_intersect($requiredSkills, $employeeSkills);
        $matchPercentage = (count($matchingSkills) / count($requiredSkills)) * 100;

        return round($matchPercentage, 2);
    }

    /**
     * Get skills gap analysis for a position
     */
    public function getSkillsGapAnalysis(EmployeePosition $position): array
    {
        $cacheKey = "positions.skills.gap.{$position->id}";

        return Cache::remember($cacheKey, 3600, function () use ($position) {
            $requiredSkills = $position->skills_required ?? [];
            $currentEmployees = $position->employees ?? collect();

            if ($currentEmployees->isEmpty()) {
                return [
                    'required_skills' => $requiredSkills,
                    'available_skills' => [],
                    'missing_skills' => $requiredSkills,
                    'coverage_percentage' => 0,
                    'recommendations' => ['No employees currently in this position']
                ];
            }

            $availableSkills = [];
            foreach ($currentEmployees as $employee) {
                $employeeSkills = $employee->skills ?? [];
                $availableSkills = array_merge($availableSkills, $employeeSkills);
            }

            $availableSkills = array_unique($availableSkills);
            $missingSkills = array_diff($requiredSkills, $availableSkills);
            $coveragePercentage = count($requiredSkills) > 0 ?
                ((count($requiredSkills) - count($missingSkills)) / count($requiredSkills)) * 100 : 100;

            $recommendations = [];
            if ($coveragePercentage < 80) {
                $recommendations[] = 'Consider hiring employees with missing skills: ' . implode(', ', $missingSkills);
            }
            if ($coveragePercentage < 60) {
                $recommendations[] = 'Critical skills gap detected - immediate action required';
            }
            if ($coveragePercentage >= 80) {
                $recommendations[] = 'Skills coverage is good';
            }

            return [
                'required_skills' => $requiredSkills,
                'available_skills' => $availableSkills,
                'missing_skills' => array_values($missingSkills),
                'coverage_percentage' => round($coveragePercentage, 2),
                'recommendations' => $recommendations
            ];
        });
    }

    /**
     * Get positions by experience level
     */
    public function getPositionsByExperienceLevel(int $minExperience): Collection
    {
        $cacheKey = "positions.experience.{$minExperience}";

        return Cache::remember($cacheKey, 3600, function () use ($minExperience) {
            return EmployeePosition::where('experience_required', '>=', $minExperience)
                ->where('is_active', true)
                ->with(['department'])
                ->orderBy('experience_required', 'asc')
                ->get();
        });
    }

    /**
     * Get positions by education level
     */
    public function getPositionsByEducationLevel(string $educationLevel): Collection
    {
        $cacheKey = "positions.education.{$educationLevel}";

        return Cache::remember($cacheKey, 3600, function () use ($educationLevel) {
            return EmployeePosition::where('education_required', '>=', $educationLevel)
                ->where('is_active', true)
                ->with(['department'])
                ->orderBy('education_required', 'asc')
                ->get();
        });
    }

    /**
     * Get skills distribution across positions
     */
    public function getSkillsDistribution(): array
    {
        return Cache::remember('positions.skills.distribution', 7200, function () {
            $positions = EmployeePosition::where('is_active', true)
                ->whereNotNull('skills_required')
                ->get();

            $skillsCount = [];
            foreach ($positions as $position) {
                $skills = $position->skills_required ?? [];
                foreach ($skills as $skill) {
                    $skillsCount[$skill] = ($skillsCount[$skill] ?? 0) + 1;
                }
            }

            arsort($skillsCount);

            return [
                'total_positions' => $positions->count(),
                'unique_skills' => count($skillsCount),
                'most_required_skills' => array_slice($skillsCount, 0, 10, true),
                'skills_by_frequency' => $skillsCount
            ];
        });
    }

    /**
     * Get positions that match employee skills
     */
    public function getPositionsForEmployee(Employee $employee, int $limit = 5): Collection
    {
        $cacheKey = "positions.for.employee.{$employee->id}";

        return Cache::remember($cacheKey, 1800, function () use ($employee, $limit) {
            $employeeSkills = $employee->skills ?? [];
            $employeeExperience = $employee->years_experience ?? 0;
            $employeeEducation = $employee->education_level ?? '';

            if (empty($employeeSkills)) {
                return collect();
            }

            $query = EmployeePosition::where('is_active', true)
                ->where('status', '!=', 'archived');

            // Find positions that require at least some of the employee's skills
            $query->whereJsonContains('skills_required', $employeeSkills);

            // Filter by experience level (position should not require more than employee has + 2 years)
            $query->where('experience_required', '<=', $employeeExperience + 2);

            // Filter by education level
            if (!empty($employeeEducation)) {
                $query->where('education_required', '<=', $employeeEducation);
            }

            return $query->with(['department'])
                ->get()
                ->map(function ($position) use ($employee) {
                    $position->match_percentage = $this->calculateSkillsMatchPercentage($position, $employee);
                    return $position;
                })
                ->sortByDesc('match_percentage')
                ->take($limit);
        });
    }

    /**
     * Get skills recommendations for a position
     */
    public function getSkillsRecommendations(EmployeePosition $position): array
    {
        $recommendations = [];
        $skillsGap = $this->getSkillsGapAnalysis($position);

        if ($skillsGap['coverage_percentage'] < 80) {
            $recommendations[] = 'Add missing skills to position requirements: ' . implode(', ', $skillsGap['missing_skills']);
        }

        if (count($position->skills_required ?? []) < 3) {
            $recommendations[] = 'Consider adding more specific skills to improve candidate matching';
        }

        if (count($position->skills_required ?? []) > 10) {
            $recommendations[] = 'Too many required skills may limit candidate pool - consider prioritizing essential skills';
        }

        if (empty($recommendations)) {
            $recommendations[] = 'Skills requirements are well-balanced';
        }

        return $recommendations;
    }

    /**
     * Clear skills-related cache
     */
    protected function clearSkillsCache(EmployeePosition $position): void
    {
        Cache::forget("positions.matching.employees.{$position->id}");
        Cache::forget("positions.skills.gap.{$position->id}");
        Cache::forget('positions.skills.distribution');

        // Clear skills-based position caches
        if ($position->skills_required) {
            foreach ($position->skills_required as $skill) {
                Cache::forget("positions.skills.{$skill}");
            }
        }
    }
}
