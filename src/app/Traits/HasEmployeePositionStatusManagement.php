<?php

namespace App\Traits;

use App\Models\EmployeePosition;
use App\Enums\PositionStatus;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

trait HasEmployeePositionStatusManagement
{
    /**
     * Activate a position
     */
    public function activatePosition(EmployeePosition $position): bool
    {
        try {
            $position->update(['status' => PositionStatus::ACTIVE]);

            // Clear cache
            $this->clearPositionCache($position);

            // Log the action
            Log::info("Position {$position->title} activated", [
                'position_id' => $position->id,
                'user_id' => auth()->id()
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error("Failed to activate position {$position->id}", [
                'error' => $e->getMessage(),
                'position_id' => $position->id
            ]);
            return false;
        }
    }

    /**
     * Deactivate a position
     */
    public function deactivatePosition(EmployeePosition $position): bool
    {
        try {
            $position->update(['status' => PositionStatus::INACTIVE]);

            // Clear cache
            $this->clearPositionCache($position);

            // Log the action
            Log::info("Position {$position->title} deactivated", [
                'position_id' => $position->id,
                'user_id' => auth()->id()
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error("Failed to deactivate position {$position->id}", [
                'error' => $e->getMessage(),
                'position_id' => $position->id
            ]);
            return false;
        }
    }

    /**
     * Archive a position
     */
    public function archivePosition(EmployeePosition $position): bool
    {
        try {
            $position->update(['status' => PositionStatus::ARCHIVED]);

            // Clear cache
            $this->clearPositionCache($position);

            // Log the action
            Log::info("Position {$position->title} archived", [
                'position_id' => $position->id,
                'user_id' => auth()->id()
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error("Failed to archive position {$position->id}", [
                'error' => $e->getMessage(),
                'position_id' => $position->id
            ]);
            return false;
        }
    }

    /**
     * Set position to hiring status
     */
    public function setPositionHiring(EmployeePosition $position): bool
    {
        try {
            $position->update(['status' => PositionStatus::HIRING]);

            // Clear cache
            $this->clearPositionCache($position);

            // Log the action
            Log::info("Position {$position->title} set to hiring", [
                'position_id' => $position->id,
                'user_id' => auth()->id()
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error("Failed to set position {$position->id} to hiring", [
                'error' => $e->getMessage(),
                'position_id' => $position->id
            ]);
            return false;
        }
    }

    /**
     * Set position to frozen status
     */
    public function setPositionFrozen(EmployeePosition $position): bool
    {
        try {
            $position->update(['status' => PositionStatus::FROZEN]);

            // Clear cache
            $this->clearPositionCache($position);

            // Log the action
            Log::info("Position {$position->title} set to frozen", [
                'position_id' => $position->id,
                'user_id' => auth()->id()
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error("Failed to set position {$position->id} to frozen", [
                'error' => $e->getMessage(),
                'position_id' => $position->id
            ]);
            return false;
        }
    }

    /**
     * Get all active positions
     */
    public function getActivePositions(): Collection
    {
        return Cache::remember('positions.active', 3600, function () {
            return EmployeePosition::where('status', PositionStatus::ACTIVE)
                ->where('is_active', true)
                ->with(['department', 'employees'])
                ->get();
        });
    }

    /**
     * Get all inactive positions
     */
    public function getInactivePositions(): Collection
    {
        return Cache::remember('positions.inactive', 3600, function () {
            return EmployeePosition::where('status', PositionStatus::INACTIVE)
                ->with(['department', 'employees'])
                ->get();
        });
    }

    /**
     * Get all hiring positions
     */
    public function getHiringPositions(): Collection
    {
        return Cache::remember('positions.hiring', 1800, function () {
            return EmployeePosition::where('status', PositionStatus::HIRING)
                ->where('is_active', true)
                ->with(['department'])
                ->get();
        });
    }

    /**
     * Get all archived positions
     */
    public function getArchivedPositions(): Collection
    {
        return Cache::remember('positions.archived', 7200, function () {
            return EmployeePosition::where('status', PositionStatus::ARCHIVED)
                ->with(['department'])
                ->get();
        });
    }

    /**
     * Get all frozen positions
     */
    public function getFrozenPositions(): Collection
    {
        return Cache::remember('positions.frozen', 3600, function () {
            return EmployeePosition::where('status', PositionStatus::FROZEN)
                ->with(['department'])
                ->get();
        });
    }

    /**
     * Check if position is active
     */
    public function isPositionActive(EmployeePosition $position): bool
    {
        return $position->status === PositionStatus::ACTIVE && $position->is_active;
    }

    /**
     * Check if position is hiring
     */
    public function isPositionHiring(EmployeePosition $position): bool
    {
        return $position->status === PositionStatus::HIRING;
    }

    /**
     * Check if position is frozen
     */
    public function isPositionFrozen(EmployeePosition $position): bool
    {
        return $position->status === PositionStatus::FROZEN;
    }

    /**
     * Check if position is archived
     */
    public function isPositionArchived(EmployeePosition $position): bool
    {
        return $position->status === PositionStatus::ARCHIVED;
    }

    /**
     * Get position status count
     */
    public function getPositionStatusCount(): array
    {
        return Cache::remember('positions.status.count', 3600, function () {
            return [
                'active' => EmployeePosition::where('status', PositionStatus::ACTIVE)->count(),
                'inactive' => EmployeePosition::where('status', PositionStatus::INACTIVE)->count(),
                'hiring' => EmployeePosition::where('status', PositionStatus::HIRING)->count(),
                'frozen' => EmployeePosition::where('status', PositionStatus::FROZEN)->count(),
                'archived' => EmployeePosition::where('status', PositionStatus::ARCHIVED)->count(),
            ];
        });
    }

    /**
     * Clear position cache
     */
    protected function clearPositionCache(EmployeePosition $position): void
    {
        Cache::forget('positions.active');
        Cache::forget('positions.inactive');
        Cache::forget('positions.hiring');
        Cache::forget('positions.archived');
        Cache::forget('positions.frozen');
        Cache::forget('positions.status.count');
    }
}
