<?php

namespace App\Traits;

use App\Models\EmployeePosition;
use App\Enums\PositionStatus;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

trait HasEmployeePositionHiringManagement
{
    /**
     * Set position to hiring status
     */
    public function setPositionToHiring(EmployeePosition $position, array $hiringDetails = []): bool
    {
        try {
            $position->update([
                'status' => PositionStatus::HIRING,
                'is_active' => true
            ]);

            // Clear cache
            $this->clearHiringCache($position);

            // Log the action
            Log::info("Position {$position->title} set to hiring", [
                'position_id' => $position->id,
                'hiring_details' => $hiringDetails,
                'user_id' => auth()->id()
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error("Failed to set position {$position->id} to hiring", [
                'error' => $e->getMessage(),
                'position_id' => $position->id,
                'hiring_details' => $hiringDetails
            ]);
            return false;
        }
    }

    /**
     * Close hiring for a position
     */
    public function closePositionHiring(EmployeePosition $position): bool
    {
        try {
            $position->update([
                'status' => PositionStatus::ACTIVE,
                'is_active' => true
            ]);

            // Clear cache
            $this->clearHiringCache($position);

            // Log the action
            Log::info("Position {$position->title} hiring closed", [
                'position_id' => $position->id,
                'user_id' => auth()->id()
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error("Failed to close hiring for position {$position->id}", [
                'error' => $e->getMessage(),
                'position_id' => $position->id
            ]);
            return false;
        }
    }

    /**
     * Get all hiring positions
     */
    public function getAllHiringPositions(): Collection
    {
        return Cache::remember('positions.hiring.all', 1800, function () {
            return EmployeePosition::where('status', PositionStatus::HIRING)
                ->where('is_active', true)
                ->with(['department', 'requirements'])
                ->orderBy('created_at', 'desc')
                ->get();
        });
    }

    /**
     * Get hiring positions by department
     */
    public function getHiringPositionsByDepartment(int $departmentId): Collection
    {
        $cacheKey = "positions.hiring.dept.{$departmentId}";

        return Cache::remember($cacheKey, 1800, function () use ($departmentId) {
            return EmployeePosition::where('status', PositionStatus::HIRING)
                ->where('department_id', $departmentId)
                ->where('is_active', true)
                ->with(['requirements'])
                ->orderBy('created_at', 'desc')
                ->get();
        });
    }

    /**
     * Get hiring positions by level
     */
    public function getHiringPositionsByLevel(string $level): Collection
    {
        $cacheKey = "positions.hiring.level.{$level}";

        return Cache::remember($cacheKey, 1800, function () use ($level) {
            return EmployeePosition::where('status', PositionStatus::HIRING)
                ->where('level', $level)
                ->where('is_active', true)
                ->with(['department', 'requirements'])
                ->orderBy('created_at', 'desc')
                ->get();
        });
    }

    /**
     * Get urgent hiring positions (positions that have been hiring for more than 30 days)
     */
    public function getUrgentHiringPositions(): Collection
    {
        return Cache::remember('positions.hiring.urgent', 900, function () {
            $thirtyDaysAgo = now()->subDays(30);

            return EmployeePosition::where('status', PositionStatus::HIRING)
                ->where('is_active', true)
                ->where('updated_at', '<=', $thirtyDaysAgo)
                ->with(['department'])
                ->orderBy('updated_at', 'asc')
                ->get();
        });
    }

    /**
     * Get hiring statistics
     */
    public function getHiringStatistics(): array
    {
        return Cache::remember('positions.hiring.stats', 3600, function () {
            $totalHiring = EmployeePosition::where('status', PositionStatus::HIRING)->count();
            $urgentHiring = $this->getUrgentHiringPositions()->count();

            $hiringByDepartment = EmployeePosition::where('status', PositionStatus::HIRING)
                ->join('employee_departments', 'employee_positions.department_id', '=', 'employee_departments.id')
                ->select('employee_departments.name', DB::raw('count(*) as count'))
                ->groupBy('employee_departments.id', 'employee_departments.name')
                ->get()
                ->pluck('count', 'name')
                ->toArray();

            $hiringByLevel = EmployeePosition::where('status', PositionStatus::HIRING)
                ->select('level', DB::raw('count(*) as count'))
                ->groupBy('level')
                ->get()
                ->pluck('count', 'level')
                ->toArray();

            return [
                'total_hiring' => $totalHiring,
                'urgent_hiring' => $urgentHiring,
                'by_department' => $hiringByDepartment,
                'by_level' => $hiringByLevel,
                'average_time_open' => $this->getAverageHiringTimeOpen()
            ];
        });
    }

    /**
     * Get average time positions have been open for hiring
     */
    protected function getAverageHiringTimeOpen(): int
    {
        $hiringPositions = EmployeePosition::where('status', PositionStatus::HIRING)
            ->where('is_active', true)
            ->get();

        if ($hiringPositions->isEmpty()) {
            return 0;
        }

        $totalDays = $hiringPositions->sum(function ($position) {
            return $position->updated_at->diffInDays(now());
        });

        return (int) ($totalDays / $hiringPositions->count());
    }

    /**
     * Get positions that need immediate attention (hiring for more than 60 days)
     */
    public function getCriticalHiringPositions(): Collection
    {
        return Cache::remember('positions.hiring.critical', 900, function () {
            $sixtyDaysAgo = now()->subDays(60);

            return EmployeePosition::where('status', PositionStatus::HIRING)
                ->where('is_active', true)
                ->where('updated_at', '<=', $sixtyDaysAgo)
                ->with(['department'])
                ->orderBy('updated_at', 'asc')
                ->get();
        });
    }

    /**
     * Get hiring pipeline summary
     */
    public function getHiringPipelineSummary(): array
    {
        return Cache::remember('positions.hiring.pipeline', 3600, function () {
            $hiringPositions = $this->getAllHiringPositions();

            $pipeline = [
                'total' => $hiringPositions->count(),
                'by_priority' => [
                    'critical' => $this->getCriticalHiringPositions()->count(),
                    'urgent' => $this->getUrgentHiringPositions()->count(),
                    'normal' => 0
                ],
                'by_department' => $hiringPositions->groupBy('department.name')->map->count(),
                'by_level' => $hiringPositions->groupBy('level')->map->count(),
                'estimated_fill_time' => $this->getEstimatedFillTime()
            ];

            $pipeline['by_priority']['normal'] = $pipeline['total'] - $pipeline['by_priority']['critical'] - $pipeline['by_priority']['urgent'];

            return $pipeline;
        });
    }

    /**
     * Get estimated time to fill positions based on historical data
     */
    protected function getEstimatedFillTime(): array
    {
        // This would typically query historical data of how long positions took to fill
        // For now, returning estimated values based on level
        return [
            'entry' => 15, // days
            'junior' => 20,
            'mid' => 30,
            'senior' => 45,
            'lead' => 60,
            'manager' => 75,
            'director' => 90,
            'executive' => 120
        ];
    }

    /**
     * Check if position has been hiring too long
     */
    public function isPositionHiringTooLong(EmployeePosition $position, int $maxDays = 30): bool
    {
        return $position->status === PositionStatus::HIRING &&
               $position->updated_at->diffInDays(now()) > $maxDays;
    }

    /**
     * Get hiring recommendations
     */
    public function getHiringRecommendations(): array
    {
        $recommendations = [];

        $criticalPositions = $this->getCriticalHiringPositions();
        if ($criticalPositions->isNotEmpty()) {
            $recommendations[] = "{$criticalPositions->count()} positions have been hiring for over 60 days - immediate action required";
        }

        $urgentPositions = $this->getUrgentHiringPositions();
        if ($urgentPositions->isNotEmpty()) {
            $recommendations[] = "{$urgentPositions->count()} positions have been hiring for over 30 days - review hiring strategy";
        }

        if (empty($recommendations)) {
            $recommendations[] = "All hiring positions are within acceptable timeframes";
        }

        return $recommendations;
    }

    /**
     * Clear hiring-related cache
     */
    protected function clearHiringCache(EmployeePosition $position): void
    {
        Cache::forget('positions.hiring.all');
        Cache::forget('positions.hiring.urgent');
        Cache::forget('positions.hiring.critical');
        Cache::forget('positions.hiring.stats');
        Cache::forget('positions.hiring.pipeline');
        Cache::forget("positions.hiring.dept.{$position->department_id}");
        Cache::forget("positions.hiring.level.{$position->level}");
    }
}
