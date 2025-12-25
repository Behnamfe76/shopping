<?php

namespace App\Listeners\EmployeePosition;

use App\Events\EmployeePosition\EmployeePositionArchived;
use App\Events\EmployeePosition\EmployeePositionCreated;
use App\Events\EmployeePosition\EmployeePositionSalaryUpdated;
use App\Events\EmployeePosition\EmployeePositionSetHiring;
use App\Events\EmployeePosition\EmployeePositionUpdated;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class UpdatePositionMetrics implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Handle the event.
     */
    public function handle($event): void
    {
        try {
            if ($event instanceof EmployeePositionCreated) {
                $this->handlePositionCreated($event);
            } elseif ($event instanceof EmployeePositionUpdated) {
                $this->handlePositionUpdated($event);
            } elseif ($event instanceof EmployeePositionSalaryUpdated) {
                $this->handleSalaryUpdated($event);
            } elseif ($event instanceof EmployeePositionSetHiring) {
                $this->handlePositionSetHiring($event);
            } elseif ($event instanceof EmployeePositionArchived) {
                $this->handlePositionArchived($event);
            }
        } catch (\Exception $e) {
            Log::error('Failed to update position metrics', [
                'event' => get_class($event),
                'position_id' => $event->position->id ?? null,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Handle position created event
     */
    protected function handlePositionCreated(EmployeePositionCreated $event): void
    {
        $position = $event->position;

        // Update department metrics
        $this->updateDepartmentMetrics($position->department_id);

        // Update level metrics
        $this->updateLevelMetrics($position->level->value);

        // Update overall position metrics
        $this->updateOverallMetrics();

        // Clear relevant caches
        $this->clearMetricsCache($position);

        Log::info('Position metrics updated for new position', [
            'position_id' => $position->id,
            'title' => $position->title,
        ]);
    }

    /**
     * Handle position updated event
     */
    protected function handlePositionUpdated(EmployeePositionUpdated $event): void
    {
        $position = $event->position;
        $changes = $event->changes;

        // Check if department changed
        if (isset($changes['department_id'])) {
            $this->updateDepartmentMetrics($changes['department_id']); // Old department
            $this->updateDepartmentMetrics($position->department_id); // New department
        }

        // Check if level changed
        if (isset($changes['level'])) {
            $this->updateLevelMetrics($changes['level']); // Old level
            $this->updateLevelMetrics($position->level->value); // New level
        }

        // Update overall metrics
        $this->updateOverallMetrics();

        // Clear relevant caches
        $this->clearMetricsCache($position);

        Log::info('Position metrics updated for position update', [
            'position_id' => $position->id,
            'title' => $position->title,
            'changes' => $changes,
        ]);
    }

    /**
     * Handle salary updated event
     */
    protected function handleSalaryUpdated(EmployeePositionSalaryUpdated $event): void
    {
        $position = $event->position;

        // Update salary-related metrics
        $this->updateSalaryMetrics($position);

        // Update department salary metrics
        $this->updateDepartmentSalaryMetrics($position->department_id);

        // Update level salary metrics
        $this->updateLevelSalaryMetrics($position->level->value);

        // Clear relevant caches
        $this->clearMetricsCache($position);

        Log::info('Salary metrics updated for position', [
            'position_id' => $position->id,
            'title' => $position->title,
        ]);
    }

    /**
     * Handle position set to hiring event
     */
    protected function handlePositionSetHiring(EmployeePositionSetHiring $event): void
    {
        $position = $event->position;

        // Update hiring metrics
        $this->updateHiringMetrics($position);

        // Update department hiring metrics
        $this->updateDepartmentHiringMetrics($position->department_id);

        // Clear relevant caches
        $this->clearMetricsCache($position);

        Log::info('Hiring metrics updated for position', [
            'position_id' => $position->id,
            'title' => $position->title,
        ]);
    }

    /**
     * Handle position archived event
     */
    protected function handlePositionArchived(EmployeePositionArchived $event): void
    {
        $position = $event->position;

        // Update archive metrics
        $this->updateArchiveMetrics($position);

        // Update department metrics
        $this->updateDepartmentMetrics($position->department_id);

        // Update level metrics
        $this->updateLevelMetrics($position->level->value);

        // Update overall metrics
        $this->updateOverallMetrics();

        // Clear relevant caches
        $this->clearMetricsCache($position);

        Log::info('Archive metrics updated for position', [
            'position_id' => $position->id,
            'title' => $position->title,
        ]);
    }

    /**
     * Update department metrics
     */
    protected function updateDepartmentMetrics(int $departmentId): void
    {
        $cacheKey = "metrics.department.{$departmentId}";

        $metrics = Cache::remember($cacheKey, 3600, function () use ($departmentId) {
            return [
                'total_positions' => DB::table('employee_positions')
                    ->where('department_id', $departmentId)
                    ->count(),
                'active_positions' => DB::table('employee_positions')
                    ->where('department_id', $departmentId)
                    ->where('is_active', true)
                    ->count(),
                'hiring_positions' => DB::table('employee_positions')
                    ->where('department_id', $departmentId)
                    ->where('status', 'hiring')
                    ->count(),
                'archived_positions' => DB::table('employee_positions')
                    ->where('department_id', $departmentId)
                    ->where('status', 'archived')
                    ->count(),
                'total_salary_budget' => DB::table('employee_positions')
                    ->where('department_id', $departmentId)
                    ->where('is_active', true)
                    ->whereNotNull('salary_min')
                    ->whereNotNull('salary_max')
                    ->sum(DB::raw('(salary_min + salary_max) / 2')),
            ];
        });

        Cache::put($cacheKey, $metrics, 3600);
    }

    /**
     * Update level metrics
     */
    protected function updateLevelMetrics(string $level): void
    {
        $cacheKey = "metrics.level.{$level}";

        $metrics = Cache::remember($cacheKey, 3600, function () use ($level) {
            return [
                'total_positions' => DB::table('employee_positions')
                    ->where('level', $level)
                    ->count(),
                'active_positions' => DB::table('employee_positions')
                    ->where('level', $level)
                    ->where('is_active', true)
                    ->count(),
                'hiring_positions' => DB::table('employee_positions')
                    ->where('level', $level)
                    ->where('status', 'hiring')
                    ->count(),
                'average_salary' => DB::table('employee_positions')
                    ->where('level', $level)
                    ->where('is_active', true)
                    ->whereNotNull('salary_min')
                    ->whereNotNull('salary_max')
                    ->avg(DB::raw('(salary_min + salary_max) / 2')),
            ];
        });

        Cache::put($cacheKey, $metrics, 3600);
    }

    /**
     * Update overall metrics
     */
    protected function updateOverallMetrics(): void
    {
        $cacheKey = 'metrics.overall';

        $metrics = Cache::remember($cacheKey, 3600, function () {
            return [
                'total_positions' => DB::table('employee_positions')->count(),
                'active_positions' => DB::table('employee_positions')
                    ->where('is_active', true)
                    ->count(),
                'hiring_positions' => DB::table('employee_positions')
                    ->where('status', 'hiring')
                    ->count(),
                'archived_positions' => DB::table('employee_positions')
                    ->where('status', 'archived')
                    ->count(),
                'total_salary_budget' => DB::table('employee_positions')
                    ->where('is_active', true)
                    ->whereNotNull('salary_min')
                    ->whereNotNull('salary_max')
                    ->sum(DB::raw('(salary_min + salary_max) / 2')),
                'average_salary' => DB::table('employee_positions')
                    ->where('is_active', true)
                    ->whereNotNull('salary_min')
                    ->whereNotNull('salary_max')
                    ->avg(DB::raw('(salary_min + salary_max) / 2')),
            ];
        });

        Cache::put($cacheKey, $metrics, 3600);
    }

    /**
     * Update salary metrics
     */
    protected function updateSalaryMetrics($position): void
    {
        $cacheKey = 'metrics.salary';

        $metrics = Cache::remember($cacheKey, 7200, function () {
            return [
                'total_salary_budget' => DB::table('employee_positions')
                    ->where('is_active', true)
                    ->whereNotNull('salary_min')
                    ->whereNotNull('salary_max')
                    ->sum(DB::raw('(salary_min + salary_max) / 2')),
                'average_salary' => DB::table('employee_positions')
                    ->where('is_active', true)
                    ->whereNotNull('salary_min')
                    ->whereNotNull('salary_max')
                    ->avg(DB::raw('(salary_min + salary_max) / 2')),
                'salary_by_level' => DB::table('employee_positions')
                    ->select('level', DB::raw('AVG((salary_min + salary_max) / 2) as avg_salary'))
                    ->where('is_active', true)
                    ->whereNotNull('salary_min')
                    ->whereNotNull('salary_max')
                    ->groupBy('level')
                    ->pluck('avg_salary', 'level')
                    ->toArray(),
            ];
        });

        Cache::put($cacheKey, $metrics, 7200);
    }

    /**
     * Update hiring metrics
     */
    protected function updateHiringMetrics($position): void
    {
        $cacheKey = 'metrics.hiring';

        $metrics = Cache::remember($cacheKey, 1800, function () {
            return [
                'total_hiring' => DB::table('employee_positions')
                    ->where('status', 'hiring')
                    ->count(),
                'hiring_by_department' => DB::table('employee_positions')
                    ->join('employee_departments', 'employee_positions.department_id', '=', 'employee_departments.id')
                    ->where('employee_positions.status', 'hiring')
                    ->select('employee_departments.name', DB::raw('count(*) as count'))
                    ->groupBy('employee_departments.id', 'employee_departments.name')
                    ->pluck('count', 'name')
                    ->toArray(),
                'hiring_by_level' => DB::table('employee_positions')
                    ->where('status', 'hiring')
                    ->select('level', DB::raw('count(*) as count'))
                    ->groupBy('level')
                    ->pluck('count', 'level')
                    ->toArray(),
            ];
        });

        Cache::put($cacheKey, $metrics, 1800);
    }

    /**
     * Update archive metrics
     */
    protected function updateArchiveMetrics($position): void
    {
        $cacheKey = 'metrics.archive';

        $metrics = Cache::remember($cacheKey, 3600, function () {
            return [
                'total_archived' => DB::table('employee_positions')
                    ->where('status', 'archived')
                    ->count(),
                'archived_by_department' => DB::table('employee_positions')
                    ->join('employee_departments', 'employee_positions.department_id', '=', 'employee_departments.id')
                    ->where('employee_positions.status', 'archived')
                    ->select('employee_departments.name', DB::raw('count(*) as count'))
                    ->groupBy('employee_departments.id', 'employee_departments.name')
                    ->pluck('count', 'name')
                    ->toArray(),
                'archived_by_level' => DB::table('employee_positions')
                    ->where('status', 'archived')
                    ->select('level', DB::raw('count(*) as count'))
                    ->groupBy('level')
                    ->pluck('count', 'level')
                    ->toArray(),
            ];
        });

        Cache::put($cacheKey, $metrics, 3600);
    }

    /**
     * Update department salary metrics
     */
    protected function updateDepartmentSalaryMetrics(int $departmentId): void
    {
        $cacheKey = "metrics.department.salary.{$departmentId}";

        $metrics = Cache::remember($cacheKey, 7200, function () use ($departmentId) {
            return [
                'total_salary_budget' => DB::table('employee_positions')
                    ->where('department_id', $departmentId)
                    ->where('is_active', true)
                    ->whereNotNull('salary_min')
                    ->whereNotNull('salary_max')
                    ->sum(DB::raw('(salary_min + salary_max) / 2')),
                'average_salary' => DB::table('employee_positions')
                    ->where('department_id', $departmentId)
                    ->where('is_active', true)
                    ->whereNotNull('salary_min')
                    ->whereNotNull('salary_max')
                    ->avg(DB::raw('(salary_min + salary_max) / 2')),
            ];
        });

        Cache::put($cacheKey, $metrics, 7200);
    }

    /**
     * Update level salary metrics
     */
    protected function updateLevelSalaryMetrics(string $level): void
    {
        $cacheKey = "metrics.level.salary.{$level}";

        $metrics = Cache::remember($cacheKey, 7200, function () use ($level) {
            return [
                'total_salary_budget' => DB::table('employee_positions')
                    ->where('level', $level)
                    ->where('is_active', true)
                    ->whereNotNull('salary_min')
                    ->whereNotNull('salary_max')
                    ->sum(DB::raw('(salary_min + salary_max) / 2')),
                'average_salary' => DB::table('employee_positions')
                    ->where('level', $level)
                    ->where('is_active', true)
                    ->whereNotNull('salary_min')
                    ->whereNotNull('salary_max')
                    ->avg(DB::raw('(salary_min + salary_max) / 2')),
            ];
        });

        Cache::put($cacheKey, $metrics, 7200);
    }

    /**
     * Update department hiring metrics
     */
    protected function updateDepartmentHiringMetrics(int $departmentId): void
    {
        $cacheKey = "metrics.department.hiring.{$departmentId}";

        $metrics = Cache::remember($cacheKey, 1800, function () use ($departmentId) {
            return [
                'total_hiring' => DB::table('employee_positions')
                    ->where('department_id', $departmentId)
                    ->where('status', 'hiring')
                    ->count(),
                'hiring_by_level' => DB::table('employee_positions')
                    ->where('department_id', $departmentId)
                    ->where('status', 'hiring')
                    ->select('level', DB::raw('count(*) as count'))
                    ->groupBy('level')
                    ->pluck('count', 'level')
                    ->toArray(),
            ];
        });

        Cache::put($cacheKey, $metrics, 1800);
    }

    /**
     * Clear metrics cache
     */
    protected function clearMetricsCache($position): void
    {
        // Clear department metrics
        Cache::forget("metrics.department.{$position->department_id}");
        Cache::forget("metrics.department.salary.{$position->department_id}");
        Cache::forget("metrics.department.hiring.{$position->department_id}");

        // Clear level metrics
        Cache::forget("metrics.level.{$position->level->value}");
        Cache::forget("metrics.level.salary.{$position->level->value}");

        // Clear overall metrics
        Cache::forget('metrics.overall');
        Cache::forget('metrics.salary');
        Cache::forget('metrics.hiring');
        Cache::forget('metrics.archive');
    }
}
