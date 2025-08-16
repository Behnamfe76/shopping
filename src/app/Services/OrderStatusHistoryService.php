<?php

namespace Fereydooni\Shopping\app\Services;

use Fereydooni\Shopping\app\Repositories\Interfaces\OrderStatusHistoryRepositoryInterface;
use Fereydooni\Shopping\app\Models\OrderStatusHistory;
use Fereydooni\Shopping\app\DTOs\OrderStatusHistoryDTO;
use Fereydooni\Shopping\app\Traits\HasCrudOperations;
use Fereydooni\Shopping\app\Traits\HasAuditTrail;
use Fereydooni\Shopping\app\Traits\HasSearchOperations;
use Fereydooni\Shopping\app\Traits\HasTimelineOperations;
use Fereydooni\Shopping\app\Traits\HasAnalyticsOperations;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\CursorPaginator;
use Illuminate\Pagination\Paginator;

class OrderStatusHistoryService
{
    use HasCrudOperations;
    use HasAuditTrail;
    use HasSearchOperations;
    use HasTimelineOperations;
    use HasAnalyticsOperations;

    public function __construct(
        protected OrderStatusHistoryRepositoryInterface $repository
    ) {
    }

    /**
     * Get all status history records
     */
    public function all(): Collection
    {
        return $this->repository->all();
    }

    /**
     * Get paginated status history records
     */
    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        return $this->repository->paginate($perPage);
    }

    /**
     * Get simple paginated status history records
     */
    public function simplePaginate(int $perPage = 15): Paginator
    {
        return $this->repository->simplePaginate($perPage);
    }

    /**
     * Get cursor paginated status history records
     */
    public function cursorPaginate(int $perPage = 15, string $cursor = null): CursorPaginator
    {
        return $this->repository->cursorPaginate($perPage, $cursor);
    }

    /**
     * Find status history by ID
     */
    public function find(int $id): ?OrderStatusHistory
    {
        return $this->repository->find($id);
    }

    /**
     * Find status history by ID and return DTO
     */
    public function findDTO(int $id): ?OrderStatusHistoryDTO
    {
        return $this->repository->findDTO($id);
    }

    /**
     * Find status history by order ID
     */
    public function findByOrderId(int $orderId): Collection
    {
        return $this->repository->findByOrderId($orderId);
    }

    /**
     * Find status history by order ID and return DTOs
     */
    public function findByOrderIdDTO(int $orderId): Collection
    {
        return $this->repository->findByOrderIdDTO($orderId);
    }

    /**
     * Find status history by user ID
     */
    public function findByUserId(int $userId): Collection
    {
        return $this->repository->findByUserId($userId);
    }

    /**
     * Find status history by user ID and return DTOs
     */
    public function findByUserIdDTO(int $userId): Collection
    {
        return $this->repository->findByUserIdDTO($userId);
    }

    /**
     * Find status history by status
     */
    public function findByStatus(string $status): Collection
    {
        return $this->repository->findByStatus($status);
    }

    /**
     * Find status history by status and return DTOs
     */
    public function findByStatusDTO(string $status): Collection
    {
        return $this->repository->findByStatusDTO($status);
    }

    /**
     * Find status history by date range
     */
    public function findByDateRange(string $startDate, string $endDate): Collection
    {
        return $this->repository->findByDateRange($startDate, $endDate);
    }

    /**
     * Find status history by date range and return DTOs
     */
    public function findByDateRangeDTO(string $startDate, string $endDate): Collection
    {
        return $this->repository->findByDateRangeDTO($startDate, $endDate);
    }

    /**
     * Create a new status history record
     */
    public function create(array $data): OrderStatusHistory
    {
        return $this->repository->create($data);
    }

    /**
     * Create a new status history record and return DTO
     */
    public function createDTO(array $data): OrderStatusHistoryDTO
    {
        return $this->repository->createAndReturnDTO($data);
    }

    /**
     * Update status history record
     */
    public function update(OrderStatusHistory $history, array $data): bool
    {
        return $this->repository->update($history, $data);
    }

    /**
     * Update status history record and return DTO
     */
    public function updateDTO(OrderStatusHistory $history, array $data): ?OrderStatusHistoryDTO
    {
        return $this->repository->updateAndReturnDTO($history, $data);
    }

    /**
     * Delete status history record
     */
    public function delete(OrderStatusHistory $history): bool
    {
        return $this->repository->delete($history);
    }

    /**
     * Get total history count
     */
    public function getHistoryCount(): int
    {
        return $this->repository->getHistoryCount();
    }

    /**
     * Get history count by order ID
     */
    public function getHistoryCountByOrderId(int $orderId): int
    {
        return $this->repository->getHistoryCountByOrderId($orderId);
    }

    /**
     * Get history count by user ID
     */
    public function getHistoryCountByUserId(int $userId): int
    {
        return $this->repository->getHistoryCountByUserId($userId);
    }

    /**
     * Get history count by status
     */
    public function getHistoryCountByStatus(string $status): int
    {
        return $this->repository->getHistoryCountByStatus($status);
    }

    /**
     * Search status history records
     */
    public function search(string $query): Collection
    {
        return $this->repository->search($query);
    }

    /**
     * Search status history records and return DTOs
     */
    public function searchDTO(string $query): Collection
    {
        return $this->repository->searchDTO($query);
    }

    /**
     * Get recent history records
     */
    public function getRecentHistory(int $limit = 10): Collection
    {
        return $this->repository->getRecentHistory($limit);
    }

    /**
     * Get recent history records and return DTOs
     */
    public function getRecentHistoryDTO(int $limit = 10): Collection
    {
        return $this->repository->getRecentHistoryDTO($limit);
    }

    /**
     * Get history by change type
     */
    public function getHistoryByChangeType(string $changeType): Collection
    {
        return $this->repository->getHistoryByChangeType($changeType);
    }

    /**
     * Get history by change type and return DTOs
     */
    public function getHistoryByChangeTypeDTO(string $changeType): Collection
    {
        return $this->repository->getHistoryByChangeTypeDTO($changeType);
    }

    /**
     * Get system changes
     */
    public function getSystemChanges(): Collection
    {
        return $this->repository->getSystemChanges();
    }

    /**
     * Get system changes and return DTOs
     */
    public function getSystemChangesDTO(): Collection
    {
        return $this->repository->getSystemChangesDTO();
    }

    /**
     * Get user changes
     */
    public function getUserChanges(): Collection
    {
        return $this->repository->getUserChanges();
    }

    /**
     * Get user changes and return DTOs
     */
    public function getUserChangesDTO(): Collection
    {
        return $this->repository->getUserChangesDTO();
    }

    /**
     * Get status transition history
     */
    public function getStatusTransitionHistory(int $orderId, string $fromStatus, string $toStatus): Collection
    {
        return $this->repository->getStatusTransitionHistory($orderId, $fromStatus, $toStatus);
    }

    /**
     * Get status transition history and return DTOs
     */
    public function getStatusTransitionHistoryDTO(int $orderId, string $fromStatus, string $toStatus): Collection
    {
        return $this->repository->getStatusTransitionHistoryDTO($orderId, $fromStatus, $toStatus);
    }

    /**
     * Get order timeline
     */
    public function getOrderTimeline(int $orderId): Collection
    {
        return $this->repository->getOrderTimeline($orderId);
    }

    /**
     * Get order timeline and return DTOs
     */
    public function getOrderTimelineDTO(int $orderId): Collection
    {
        return $this->repository->getOrderTimelineDTO($orderId);
    }

    /**
     * Validate history entry
     */
    public function validateHistoryEntry(array $data): bool
    {
        return $this->repository->validateHistoryEntry($data);
    }

    /**
     * Log status change
     */
    public function logStatusChange(int $orderId, string $oldStatus, string $newStatus, int $changedBy, string $note = null, array $metadata = []): OrderStatusHistory
    {
        return $this->repository->logStatusChange($orderId, $oldStatus, $newStatus, $changedBy, $note, $metadata);
    }

    /**
     * Log status change and return DTO
     */
    public function logStatusChangeDTO(int $orderId, string $oldStatus, string $newStatus, int $changedBy, string $note = null, array $metadata = []): OrderStatusHistoryDTO
    {
        return $this->repository->logStatusChangeDTO($orderId, $oldStatus, $newStatus, $changedBy, $note, $metadata);
    }

    /**
     * Get status change frequency
     */
    public function getStatusChangeFrequency(string $status): int
    {
        return $this->repository->getStatusChangeFrequency($status);
    }

    /**
     * Get most frequent status changes
     */
    public function getMostFrequentStatusChanges(int $limit = 10): Collection
    {
        return $this->repository->getMostFrequentStatusChanges($limit);
    }

    /**
     * Get status change analytics
     */
    public function getStatusChangeAnalytics(string $startDate, string $endDate): array
    {
        return $this->repository->getStatusChangeAnalytics($startDate, $endDate);
    }
}
