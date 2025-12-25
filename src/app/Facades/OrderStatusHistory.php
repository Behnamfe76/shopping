<?php

namespace Fereydooni\Shopping\app\Facades;

use Fereydooni\Shopping\app\DTOs\OrderStatusHistoryDTO;
use Fereydooni\Shopping\app\Models\OrderStatusHistory;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\CursorPaginator;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Facade;

/**
 * @method static Collection all()
 * @method static LengthAwarePaginator paginate(int $perPage = 15)
 * @method static Paginator simplePaginate(int $perPage = 15)
 * @method static CursorPaginator cursorPaginate(int $perPage = 15, string $cursor = null)
 * @method static OrderStatusHistory|null find(int $id)
 * @method static OrderStatusHistoryDTO|null findDTO(int $id)
 * @method static Collection findByOrderId(int $orderId)
 * @method static Collection findByOrderIdDTO(int $orderId)
 * @method static Collection findByUserId(int $userId)
 * @method static Collection findByUserIdDTO(int $userId)
 * @method static Collection findByStatus(string $status)
 * @method static Collection findByStatusDTO(string $status)
 * @method static Collection findByDateRange(string $startDate, string $endDate)
 * @method static Collection findByDateRangeDTO(string $startDate, string $endDate)
 * @method static OrderStatusHistory create(array $data)
 * @method static OrderStatusHistoryDTO createDTO(array $data)
 * @method static bool update(OrderStatusHistory $history, array $data)
 * @method static OrderStatusHistoryDTO|null updateDTO(OrderStatusHistory $history, array $data)
 * @method static bool delete(OrderStatusHistory $history)
 * @method static int getHistoryCount()
 * @method static int getHistoryCountByOrderId(int $orderId)
 * @method static int getHistoryCountByUserId(int $userId)
 * @method static int getHistoryCountByStatus(string $status)
 * @method static Collection search(string $query)
 * @method static Collection searchDTO(string $query)
 * @method static Collection getRecentHistory(int $limit = 10)
 * @method static Collection getRecentHistoryDTO(int $limit = 10)
 * @method static Collection getHistoryByChangeType(string $changeType)
 * @method static Collection getHistoryByChangeTypeDTO(string $changeType)
 * @method static Collection getSystemChanges()
 * @method static Collection getSystemChangesDTO()
 * @method static Collection getUserChanges()
 * @method static Collection getUserChangesDTO()
 * @method static Collection getStatusTransitionHistory(int $orderId, string $fromStatus, string $toStatus)
 * @method static Collection getStatusTransitionHistoryDTO(int $orderId, string $fromStatus, string $toStatus)
 * @method static Collection getOrderTimeline(int $orderId)
 * @method static Collection getOrderTimelineDTO(int $orderId)
 * @method static bool validateHistoryEntry(array $data)
 * @method static OrderStatusHistory logStatusChange(int $orderId, string $oldStatus, string $newStatus, int $changedBy, string $note = null, array $metadata = [])
 * @method static OrderStatusHistoryDTO logStatusChangeDTO(int $orderId, string $oldStatus, string $newStatus, int $changedBy, string $note = null, array $metadata = [])
 * @method static int getStatusChangeFrequency(string $status)
 * @method static Collection getMostFrequentStatusChanges(int $limit = 10)
 * @method static array getStatusChangeAnalytics(string $startDate, string $endDate)
 *
 * @see \Fereydooni\Shopping\app\Services\OrderStatusHistoryService
 */
class OrderStatusHistory extends Facade
{
    /**
     * Get the registered name of the component.
     */
    protected static function getFacadeAccessor(): string
    {
        return 'shopping.order-status-history';
    }
}
