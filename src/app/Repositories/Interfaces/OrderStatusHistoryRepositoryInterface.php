<?php

namespace Fereydooni\Shopping\app\Repositories\Interfaces;

use Fereydooni\Shopping\app\Models\OrderStatusHistory;
use Fereydooni\Shopping\app\DTOs\OrderStatusHistoryDTO;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\CursorPaginator;
use Illuminate\Pagination\Paginator;

interface OrderStatusHistoryRepositoryInterface
{
    /**
     * Get all status history records
     */
    public function all(): Collection;

    /**
     * Get paginated status history records (LengthAwarePaginator)
     */
    public function paginate(int $perPage = 15): LengthAwarePaginator;

    /**
     * Get simple paginated status history records
     */
    public function simplePaginate(int $perPage = 15): Paginator;

    /**
     * Get cursor paginated status history records
     */
    public function cursorPaginate(int $perPage = 15, string $cursor = null): CursorPaginator;

    /**
     * Find status history by ID
     */
    public function find(int $id): ?OrderStatusHistory;

    /**
     * Find status history by ID and return DTO
     */
    public function findDTO(int $id): ?OrderStatusHistoryDTO;

    /**
     * Find status history by order ID
     */
    public function findByOrderId(int $orderId): Collection;

    /**
     * Find status history by order ID and return DTOs
     */
    public function findByOrderIdDTO(int $orderId): Collection;

    /**
     * Find status history by user ID
     */
    public function findByUserId(int $userId): Collection;

    /**
     * Find status history by user ID and return DTOs
     */
    public function findByUserIdDTO(int $userId): Collection;

    /**
     * Find status history by status
     */
    public function findByStatus(string $status): Collection;

    /**
     * Find status history by status and return DTOs
     */
    public function findByStatusDTO(string $status): Collection;

    /**
     * Find status history by date range
     */
    public function findByDateRange(string $startDate, string $endDate): Collection;

    /**
     * Find status history by date range and return DTOs
     */
    public function findByDateRangeDTO(string $startDate, string $endDate): Collection;

    /**
     * Create a new status history record
     */
    public function create(array $data): OrderStatusHistory;

    /**
     * Create a new status history record and return DTO
     */
    public function createAndReturnDTO(array $data): OrderStatusHistoryDTO;

    /**
     * Update status history record
     */
    public function update(OrderStatusHistory $history, array $data): bool;

    /**
     * Update status history record and return DTO
     */
    public function updateAndReturnDTO(OrderStatusHistory $history, array $data): ?OrderStatusHistoryDTO;

    /**
     * Delete status history record
     */
    public function delete(OrderStatusHistory $history): bool;

    /**
     * Get total history count
     */
    public function getHistoryCount(): int;

    /**
     * Get history count by order ID
     */
    public function getHistoryCountByOrderId(int $orderId): int;

    /**
     * Get history count by user ID
     */
    public function getHistoryCountByUserId(int $userId): int;

    /**
     * Get history count by status
     */
    public function getHistoryCountByStatus(string $status): int;

    /**
     * Search status history records
     */
    public function search(string $query): Collection;

    /**
     * Search status history records and return DTOs
     */
    public function searchDTO(string $query): Collection;

    /**
     * Get recent history records
     */
    public function getRecentHistory(int $limit = 10): Collection;

    /**
     * Get recent history records and return DTOs
     */
    public function getRecentHistoryDTO(int $limit = 10): Collection;

    /**
     * Get history by change type
     */
    public function getHistoryByChangeType(string $changeType): Collection;

    /**
     * Get history by change type and return DTOs
     */
    public function getHistoryByChangeTypeDTO(string $changeType): Collection;

    /**
     * Get system changes
     */
    public function getSystemChanges(): Collection;

    /**
     * Get system changes and return DTOs
     */
    public function getSystemChangesDTO(): Collection;

    /**
     * Get user changes
     */
    public function getUserChanges(): Collection;

    /**
     * Get user changes and return DTOs
     */
    public function getUserChangesDTO(): Collection;

    /**
     * Get status transition history
     */
    public function getStatusTransitionHistory(int $orderId, string $fromStatus, string $toStatus): Collection;

    /**
     * Get status transition history and return DTOs
     */
    public function getStatusTransitionHistoryDTO(int $orderId, string $fromStatus, string $toStatus): Collection;

    /**
     * Get order timeline
     */
    public function getOrderTimeline(int $orderId): Collection;

    /**
     * Get order timeline and return DTOs
     */
    public function getOrderTimelineDTO(int $orderId): Collection;

    /**
     * Validate history entry
     */
    public function validateHistoryEntry(array $data): bool;

    /**
     * Log status change
     */
    public function logStatusChange(int $orderId, string $oldStatus, string $newStatus, int $changedBy, string $note = null, array $metadata = []): OrderStatusHistory;

    /**
     * Log status change and return DTO
     */
    public function logStatusChangeDTO(int $orderId, string $oldStatus, string $newStatus, int $changedBy, string $note = null, array $metadata = []): OrderStatusHistoryDTO;

    /**
     * Get status change frequency
     */
    public function getStatusChangeFrequency(string $status): int;

    /**
     * Get most frequent status changes
     */
    public function getMostFrequentStatusChanges(int $limit = 10): Collection;

    /**
     * Get status change analytics
     */
    public function getStatusChangeAnalytics(string $startDate, string $endDate): array;
}
