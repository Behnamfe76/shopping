<?php

namespace App\Traits;

use App\Models\EmployeePosition;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

trait HasEmployeePositionSalaryManagement
{
    /**
     * Update position salary range
     */
    public function updatePositionSalaryRange(EmployeePosition $position, float $minSalary, float $maxSalary): bool
    {
        try {
            if ($minSalary > $maxSalary) {
                throw new \InvalidArgumentException('Minimum salary cannot be greater than maximum salary');
            }

            $position->update([
                'salary_min' => $minSalary,
                'salary_max' => $maxSalary
            ]);

            // Clear cache
            $this->clearSalaryCache($position);

            // Log the action
            Log::info("Position {$position->title} salary range updated", [
                'position_id' => $position->id,
                'old_min' => $position->getOriginal('salary_min'),
                'old_max' => $position->getOriginal('salary_max'),
                'new_min' => $minSalary,
                'new_max' => $maxSalary,
                'user_id' => auth()->id()
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error("Failed to update salary range for position {$position->id}", [
                'error' => $e->getMessage(),
                'position_id' => $position->id,
                'min_salary' => $minSalary,
                'max_salary' => $maxSalary
            ]);
            return false;
        }
    }

    /**
     * Update position hourly rate range
     */
    public function updatePositionHourlyRateRange(EmployeePosition $position, float $minRate, float $maxRate): bool
    {
        try {
            if ($minRate > $maxRate) {
                throw new \InvalidArgumentException('Minimum hourly rate cannot be greater than maximum hourly rate');
            }

            $position->update([
                'hourly_rate_min' => $minRate,
                'hourly_rate_max' => $maxRate
            ]);

            // Clear cache
            $this->clearSalaryCache($position);

            // Log the action
            Log::info("Position {$position->title} hourly rate range updated", [
                'position_id' => $position->id,
                'old_min' => $position->getOriginal('hourly_rate_min'),
                'old_max' => $position->getOriginal('hourly_rate_max'),
                'new_min' => $minRate,
                'new_max' => $maxRate,
                'user_id' => auth()->id()
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error("Failed to update hourly rate range for position {$position->id}", [
                'error' => $e->getMessage(),
                'position_id' => $position->id,
                'min_rate' => $minRate,
                'max_rate' => $maxRate
            ]);
            return false;
        }
    }

    /**
     * Get positions by salary range
     */
    public function getPositionsBySalaryRange(float $minSalary, float $maxSalary): Collection
    {
        $cacheKey = "positions.salary.{$minSalary}.{$maxSalary}";

        return Cache::remember($cacheKey, 3600, function () use ($minSalary, $maxSalary) {
            return EmployeePosition::where('salary_min', '>=', $minSalary)
                ->where('salary_max', '<=', $maxSalary)
                ->where('is_active', true)
                ->with(['department'])
                ->get();
        });
    }

    /**
     * Get positions by hourly rate range
     */
    public function getPositionsByHourlyRateRange(float $minRate, float $maxRate): Collection
    {
        $cacheKey = "positions.hourly.{$minRate}.{$maxRate}";

        return Cache::remember($cacheKey, 3600, function () use ($minRate, $maxRate) {
            return EmployeePosition::where('hourly_rate_min', '>=', $minRate)
                ->where('hourly_rate_max', '<=', $maxRate)
                ->where('is_active', true)
                ->with(['department'])
                ->get();
        });
    }

    /**
     * Get average salary by position level
     */
    public function getAverageSalaryByLevel(string $level): float
    {
        return Cache::remember("positions.avg_salary.{$level}", 7200, function () use ($level) {
            return EmployeePosition::where('level', $level)
                ->where('is_active', true)
                ->whereNotNull('salary_min')
                ->whereNotNull('salary_max')
                ->avg(DB::raw('(salary_min + salary_max) / 2'));
        }) ?? 0.0;
    }

    /**
     * Get average salary by department
     */
    public function getAverageSalaryByDepartment(int $departmentId): float
    {
        return Cache::remember("positions.avg_salary.dept.{$departmentId}", 7200, function () use ($departmentId) {
            return EmployeePosition::where('department_id', $departmentId)
                ->where('is_active', true)
                ->whereNotNull('salary_min')
                ->whereNotNull('salary_max')
                ->avg(DB::raw('(salary_min + salary_max) / 2'));
        }) ?? 0.0;
    }

    /**
     * Get position salary range
     */
    public function getPositionSalaryRange(EmployeePosition $position): array
    {
        return [
            'min' => $position->salary_min,
            'max' => $position->salary_max,
            'mid' => ($position->salary_min + $position->salary_max) / 2,
            'range' => $position->salary_max - $position->salary_min
        ];
    }

    /**
     * Get position hourly rate range
     */
    public function getPositionHourlyRateRange(EmployeePosition $position): array
    {
        return [
            'min' => $position->hourly_rate_min,
            'max' => $position->hourly_rate_max,
            'mid' => ($position->hourly_rate_min + $position->hourly_rate_max) / 2,
            'range' => $position->hourly_rate_max - $position->hourly_rate_min
        ];
    }

    /**
     * Get salary analysis for a position
     */
    public function getPositionSalaryAnalysis(EmployeePosition $position): array
    {
        $cacheKey = "positions.salary.analysis.{$position->id}";

        return Cache::remember($cacheKey, 3600, function () use ($position) {
            $departmentAvg = $this->getAverageSalaryByDepartment($position->department_id);
            $levelAvg = $this->getAverageSalaryByLevel($position->level);
            $positionMid = ($position->salary_min + $position->salary_max) / 2;

            return [
                'position_mid' => $positionMid,
                'department_average' => $departmentAvg,
                'level_average' => $levelAvg,
                'vs_department' => $departmentAvg > 0 ? (($positionMid - $departmentAvg) / $departmentAvg) * 100 : 0,
                'vs_level' => $levelAvg > 0 ? (($positionMid - $levelAvg) / $levelAvg) * 100 : 0,
                'market_position' => $this->getMarketPosition($positionMid, $departmentAvg, $levelAvg),
                'recommendations' => $this->getSalaryRecommendations($position, $departmentAvg, $levelAvg)
            ];
        });
    }

    /**
     * Get market position analysis
     */
    protected function getMarketPosition(float $positionSalary, float $departmentAvg, float $levelAvg): string
    {
        $deptDiff = $departmentAvg > 0 ? (($positionSalary - $departmentAvg) / $departmentAvg) * 100 : 0;
        $levelDiff = $levelAvg > 0 ? (($positionSalary - $levelAvg) / $levelAvg) * 100 : 0;

        if ($deptDiff >= 20 && $levelDiff >= 20) {
            return 'Above Market';
        } elseif ($deptDiff >= 10 && $levelDiff >= 10) {
            return 'Above Average';
        } elseif ($deptDiff >= -10 && $levelDiff >= -10) {
            return 'Market Rate';
        } elseif ($deptDiff >= -20 && $levelDiff >= -20) {
            return 'Below Average';
        } else {
            return 'Below Market';
        }
    }

    /**
     * Get salary recommendations
     */
    protected function getSalaryRecommendations(EmployeePosition $position, float $departmentAvg, float $levelAvg): array
    {
        $recommendations = [];
        $positionMid = ($position->salary_min + $position->salary_max) / 2;

        if ($departmentAvg > 0 && $positionMid < $departmentAvg * 0.9) {
            $recommendations[] = 'Consider increasing salary to align with department average';
        }

        if ($levelAvg > 0 && $positionMid < $levelAvg * 0.9) {
            $recommendations[] = 'Consider increasing salary to align with level average';
        }

        if ($positionMid > $departmentAvg * 1.2) {
            $recommendations[] = 'Salary may be above department average - review market data';
        }

        if (empty($recommendations)) {
            $recommendations[] = 'Salary appears to be well-aligned with market';
        }

        return $recommendations;
    }

    /**
     * Get total salary budget by department
     */
    public function getTotalSalaryBudgetByDepartment(int $departmentId): float
    {
        return Cache::remember("positions.salary.budget.dept.{$departmentId}", 7200, function () use ($departmentId) {
            return EmployeePosition::where('department_id', $departmentId)
                ->where('is_active', true)
                ->whereNotNull('salary_min')
                ->whereNotNull('salary_max')
                ->sum(DB::raw('(salary_min + salary_max) / 2'));
        }) ?? 0.0;
    }

    /**
     * Clear salary-related cache
     */
    protected function clearSalaryCache(EmployeePosition $position): void
    {
        Cache::forget("positions.salary.analysis.{$position->id}");
        Cache::forget("positions.avg_salary.dept.{$position->department_id}");
        Cache::forget("positions.avg_salary.{$position->level}");
        Cache::forget("positions.salary.budget.dept.{$position->department_id}");

        // Clear range caches (this is a bit broad but ensures consistency)
        Cache::flush();
    }
}
