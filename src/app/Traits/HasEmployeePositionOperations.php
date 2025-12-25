<?php

namespace Fereydooni\Shopping\app\Traits;

use Fereydooni\Shopping\app\DTOs\EmployeePositionDTO;
use Fereydooni\Shopping\app\Models\EmployeePosition;
use Fereydooni\Shopping\app\Repositories\Interfaces\EmployeePositionRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

trait HasEmployeePositionOperations
{
    /**
     * Get all employee positions.
     */
    public function getAllPositions(): Collection
    {
        try {
            return app(EmployeePositionRepositoryInterface::class)->all();
        } catch (\Exception $e) {
            Log::error('Failed to get all positions', ['error' => $e->getMessage()]);

            return collect();
        }
    }

    /**
     * Get all employee positions as DTOs.
     */
    public function getAllPositionsDTO(): Collection
    {
        try {
            $positions = $this->getAllPositions();

            return $positions->map(function ($position) {
                return EmployeePositionDTO::fromModel($position);
            });
        } catch (\Exception $e) {
            Log::error('Failed to get all positions as DTOs', ['error' => $e->getMessage()]);

            return collect();
        }
    }

    /**
     * Paginate employee positions.
     */
    public function paginatePositions(int $perPage = 15): LengthAwarePaginator
    {
        try {
            return app(EmployeePositionRepositoryInterface::class)->paginate($perPage);
        } catch (\Exception $e) {
            Log::error('Failed to paginate positions', ['error' => $e->getMessage(), 'perPage' => $perPage]);

            return new LengthAwarePaginator(collect(), 0, $perPage);
        }
    }

    /**
     * Find position by ID.
     */
    public function findPosition(int $id): ?EmployeePosition
    {
        try {
            return app(EmployeePositionRepositoryInterface::class)->find($id);
        } catch (\Exception $e) {
            Log::error('Failed to find position', ['error' => $e->getMessage(), 'id' => $id]);

            return null;
        }
    }

    /**
     * Find position by ID as DTO.
     */
    public function findPositionDTO(int $id): ?EmployeePositionDTO
    {
        try {
            return app(EmployeePositionRepositoryInterface::class)->findDTO($id);
        } catch (\Exception $e) {
            Log::error('Failed to find position as DTO', ['error' => $e->getMessage(), 'id' => $id]);

            return null;
        }
    }

    /**
     * Find position by title.
     */
    public function findPositionByTitle(string $title): ?EmployeePosition
    {
        try {
            return app(EmployeePositionRepositoryInterface::class)->findByTitle($title);
        } catch (\Exception $e) {
            Log::error('Failed to find position by title', ['error' => $e->getMessage(), 'title' => $title]);

            return null;
        }
    }

    /**
     * Find position by code.
     */
    public function findPositionByCode(string $code): ?EmployeePosition
    {
        try {
            return app(EmployeePositionRepositoryInterface::class)->findByCode($code);
        } catch (\Exception $e) {
            Log::error('Failed to find position by code', ['error' => $e->getMessage(), 'code' => $code]);

            return null;
        }
    }

    /**
     * Create a new position.
     */
    public function createPosition(array $data): ?EmployeePosition
    {
        try {
            // Validate required fields
            $this->validatePositionData($data);

            return app(EmployeePositionRepositoryInterface::class)->create($data);
        } catch (\Exception $e) {
            Log::error('Failed to create position', ['error' => $e->getMessage(), 'data' => $data]);

            return null;
        }
    }

    /**
     * Create a new position and return as DTO.
     */
    public function createPositionDTO(array $data): ?EmployeePositionDTO
    {
        try {
            return app(EmployeePositionRepositoryInterface::class)->createAndReturnDTO($data);
        } catch (\Exception $e) {
            Log::error('Failed to create position as DTO', ['error' => $e->getMessage(), 'data' => $data]);

            return null;
        }
    }

    /**
     * Update an existing position.
     */
    public function updatePosition(EmployeePosition $position, array $data): bool
    {
        try {
            // Validate update data
            $this->validatePositionUpdateData($data);

            return app(EmployeePositionRepositoryInterface::class)->update($position, $data);
        } catch (\Exception $e) {
            Log::error('Failed to update position', ['error' => $e->getMessage(), 'id' => $position->id, 'data' => $data]);

            return false;
        }
    }

    /**
     * Update an existing position and return as DTO.
     */
    public function updatePositionDTO(EmployeePosition $position, array $data): ?EmployeePositionDTO
    {
        try {
            return app(EmployeePositionRepositoryInterface::class)->updateAndReturnDTO($position, $data);
        } catch (\Exception $e) {
            Log::error('Failed to update position as DTO', ['error' => $e->getMessage(), 'id' => $position->id, 'data' => $data]);

            return null;
        }
    }

    /**
     * Delete a position.
     */
    public function deletePosition(EmployeePosition $position): bool
    {
        try {
            return app(EmployeePositionRepositoryInterface::class)->delete($position);
        } catch (\Exception $e) {
            Log::error('Failed to delete position', ['error' => $e->getMessage(), 'id' => $position->id]);

            return false;
        }
    }

    /**
     * Search positions by query.
     */
    public function searchPositions(string $query): Collection
    {
        try {
            return app(EmployeePositionRepositoryInterface::class)->searchPositions($query);
        } catch (\Exception $e) {
            Log::error('Failed to search positions', ['error' => $e->getMessage(), 'query' => $query]);

            return collect();
        }
    }

    /**
     * Search positions by query and return as DTOs.
     */
    public function searchPositionsDTO(string $query): Collection
    {
        try {
            return app(EmployeePositionRepositoryInterface::class)->searchPositionsDTO($query);
        } catch (\Exception $e) {
            Log::error('Failed to search positions as DTOs', ['error' => $e->getMessage(), 'query' => $query]);

            return collect();
        }
    }

    /**
     * Get positions by department.
     */
    public function getPositionsByDepartment(int $departmentId): Collection
    {
        try {
            return app(EmployeePositionRepositoryInterface::class)->findByDepartmentId($departmentId);
        } catch (\Exception $e) {
            Log::error('Failed to get positions by department', ['error' => $e->getMessage(), 'department_id' => $departmentId]);

            return collect();
        }
    }

    /**
     * Get positions by level.
     */
    public function getPositionsByLevel(string $level): Collection
    {
        try {
            return app(EmployeePositionRepositoryInterface::class)->findByLevel($level);
        } catch (\Exception $e) {
            Log::error('Failed to get positions by level', ['error' => $e->getMessage(), 'level' => $level]);

            return collect();
        }
    }

    /**
     * Get positions by status.
     */
    public function getPositionsByStatus(string $status): Collection
    {
        try {
            return app(EmployeePositionRepositoryInterface::class)->findByStatus($status);
        } catch (\Exception $e) {
            Log::error('Failed to get positions by status', ['error' => $e->getMessage(), 'status' => $status]);

            return collect();
        }
    }

    /**
     * Get active positions.
     */
    public function getActivePositions(): Collection
    {
        try {
            return app(EmployeePositionRepositoryInterface::class)->findActive();
        } catch (\Exception $e) {
            Log::error('Failed to get active positions', ['error' => $e->getMessage()]);

            return collect();
        }
    }

    /**
     * Get hiring positions.
     */
    public function getHiringPositions(): Collection
    {
        try {
            return app(EmployeePositionRepositoryInterface::class)->findHiring();
        } catch (\Exception $e) {
            Log::error('Failed to get hiring positions', ['error' => $e->getMessage()]);

            return collect();
        }
    }

    /**
     * Get remote positions.
     */
    public function getRemotePositions(): Collection
    {
        try {
            return app(EmployeePositionRepositoryInterface::class)->findRemote();
        } catch (\Exception $e) {
            Log::error('Failed to get remote positions', ['error' => $e->getMessage()]);

            return collect();
        }
    }

    /**
     * Get total position count.
     */
    public function getTotalPositionCount(): int
    {
        try {
            return app(EmployeePositionRepositoryInterface::class)->getTotalPositionCount();
        } catch (\Exception $e) {
            Log::error('Failed to get total position count', ['error' => $e->getMessage()]);

            return 0;
        }
    }

    /**
     * Get position statistics.
     */
    public function getPositionStatistics(): array
    {
        try {
            return app(EmployeePositionRepositoryInterface::class)->getPositionStatistics();
        } catch (\Exception $e) {
            Log::error('Failed to get position statistics', ['error' => $e->getMessage()]);

            return [];
        }
    }

    /**
     * Validate position data for creation.
     */
    protected function validatePositionData(array $data): void
    {
        $requiredFields = ['title', 'code', 'department_id', 'level', 'is_active', 'status', 'is_remote', 'is_travel_required'];

        foreach ($requiredFields as $field) {
            if (! isset($data[$field])) {
                throw new \InvalidArgumentException("Missing required field: {$field}");
            }
        }

        // Validate salary ranges if provided
        if (isset($data['salary_min']) && isset($data['salary_max'])) {
            if ($data['salary_min'] > $data['salary_max']) {
                throw new \InvalidArgumentException('Minimum salary cannot be greater than maximum salary');
            }
        }

        // Validate hourly rate ranges if provided
        if (isset($data['hourly_rate_min']) && isset($data['hourly_rate_max'])) {
            if ($data['hourly_rate_min'] > $data['hourly_rate_max']) {
                throw new \InvalidArgumentException('Minimum hourly rate cannot be greater than maximum hourly rate');
            }
        }

        // Validate travel percentage if travel is required
        if (isset($data['is_travel_required']) && $data['is_travel_required']) {
            if (! isset($data['travel_percentage']) || $data['travel_percentage'] <= 0) {
                throw new \InvalidArgumentException('Travel percentage is required when travel is required');
            }
        }
    }

    /**
     * Validate position data for updates.
     */
    protected function validatePositionUpdateData(array $data): void
    {
        // Validate salary ranges if provided
        if (isset($data['salary_min']) && isset($data['salary_max'])) {
            if ($data['salary_min'] > $data['salary_max']) {
                throw new \InvalidArgumentException('Minimum salary cannot be greater than maximum salary');
            }
        }

        // Validate hourly rate ranges if provided
        if (isset($data['hourly_rate_min']) && isset($data['hourly_rate_max'])) {
            if ($data['hourly_rate_min'] > $data['hourly_rate_max']) {
                throw new \InvalidArgumentException('Minimum hourly rate cannot be greater than maximum hourly rate');
            }
        }

        // Validate travel percentage if travel is required
        if (isset($data['is_travel_required']) && $data['is_travel_required']) {
            if (! isset($data['travel_percentage']) || $data['travel_percentage'] <= 0) {
                throw new \InvalidArgumentException('Travel percentage is required when travel is required');
            }
        }
    }

    /**
     * Check if position exists.
     */
    public function positionExists(int $id): bool
    {
        return $this->findPosition($id) !== null;
    }

    /**
     * Check if position code exists.
     */
    public function positionCodeExists(string $code): bool
    {
        return $this->findPositionByCode($code) !== null;
    }

    /**
     * Check if position title exists.
     */
    public function positionTitleExists(string $title): bool
    {
        return $this->findPositionByTitle($title) !== null;
    }

    /**
     * Get positions count by status.
     */
    public function getPositionsCountByStatus(string $status): int
    {
        try {
            return app(EmployeePositionRepositoryInterface::class)->getTotalPositionCountByStatus($status);
        } catch (\Exception $e) {
            Log::error('Failed to get positions count by status', ['error' => $e->getMessage(), 'status' => $status]);

            return 0;
        }
    }

    /**
     * Get positions count by level.
     */
    public function getPositionsCountByLevel(string $level): int
    {
        try {
            return app(EmployeePositionRepositoryInterface::class)->getTotalPositionCountByLevel($level);
        } catch (\Exception $e) {
            Log::error('Failed to get positions count by level', ['error' => $e->getMessage(), 'level' => $level]);

            return 0;
        }
    }

    /**
     * Get positions count by department.
     */
    public function getPositionsCountByDepartment(int $departmentId): int
    {
        try {
            return app(EmployeePositionRepositoryInterface::class)->getTotalPositionCountByDepartment($departmentId);
        } catch (\Exception $e) {
            Log::error('Failed to get positions count by department', ['error' => $e->getMessage(), 'department_id' => $departmentId]);

            return 0;
        }
    }
}
