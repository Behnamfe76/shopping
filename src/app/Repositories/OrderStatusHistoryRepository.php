<?php

namespace Fereydooni\Shopping\app\Repositories;

use Fereydooni\Shopping\app\DTOs\OrderStatusHistoryDTO;
use Fereydooni\Shopping\app\Models\OrderStatusHistory;
use Fereydooni\Shopping\app\Repositories\Interfaces\OrderStatusHistoryRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\CursorPaginator;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Request;

class OrderStatusHistoryRepository implements OrderStatusHistoryRepositoryInterface
{
    public function __construct(
        protected OrderStatusHistory $model
    ) {}

    public function all(): Collection
    {
        return $this->model->with(['order', 'changedByUser'])->get();
    }

    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        return $this->model->with(['order', 'changedByUser'])
            ->orderBy('changed_at', 'desc')
            ->paginate($perPage);
    }

    public function simplePaginate(int $perPage = 15): Paginator
    {
        return $this->model->with(['order', 'changedByUser'])
            ->orderBy('changed_at', 'desc')
            ->simplePaginate($perPage);
    }

    public function cursorPaginate(int $perPage = 15, ?string $cursor = null): CursorPaginator
    {
        return $this->model->with(['order', 'changedByUser'])
            ->orderBy('changed_at', 'desc')
            ->cursorPaginate($perPage, ['*'], 'cursor', $cursor);
    }

    public function find(int $id): ?OrderStatusHistory
    {
        return $this->model->with(['order', 'changedByUser'])->find($id);
    }

    public function findDTO(int $id): ?OrderStatusHistoryDTO
    {
        $history = $this->find($id);

        return $history ? OrderStatusHistoryDTO::fromModel($history) : null;
    }

    public function findByOrderId(int $orderId): Collection
    {
        return $this->model->with(['order', 'changedByUser'])
            ->where('order_id', $orderId)
            ->orderBy('changed_at', 'desc')
            ->get();
    }

    public function findByOrderIdDTO(int $orderId): Collection
    {
        return $this->findByOrderId($orderId)->map(function ($history) {
            return OrderStatusHistoryDTO::fromModel($history);
        });
    }

    public function findByUserId(int $userId): Collection
    {
        return $this->model->with(['order', 'changedByUser'])
            ->where('changed_by', $userId)
            ->orderBy('changed_at', 'desc')
            ->get();
    }

    public function findByUserIdDTO(int $userId): Collection
    {
        return $this->findByUserId($userId)->map(function ($history) {
            return OrderStatusHistoryDTO::fromModel($history);
        });
    }

    public function findByStatus(string $status): Collection
    {
        return $this->model->with(['order', 'changedByUser'])
            ->where('new_status', $status)
            ->orderBy('changed_at', 'desc')
            ->get();
    }

    public function findByStatusDTO(string $status): Collection
    {
        return $this->findByStatus($status)->map(function ($history) {
            return OrderStatusHistoryDTO::fromModel($history);
        });
    }

    public function findByDateRange(string $startDate, string $endDate): Collection
    {
        return $this->model->with(['order', 'changedByUser'])
            ->whereBetween('changed_at', [$startDate, $endDate])
            ->orderBy('changed_at', 'desc')
            ->get();
    }

    public function findByDateRangeDTO(string $startDate, string $endDate): Collection
    {
        return $this->findByDateRange($startDate, $endDate)->map(function ($history) {
            return OrderStatusHistoryDTO::fromModel($history);
        });
    }

    public function create(array $data): OrderStatusHistory
    {
        return $this->model->create($data);
    }

    public function createAndReturnDTO(array $data): OrderStatusHistoryDTO
    {
        $history = $this->create($data);

        return OrderStatusHistoryDTO::fromModel($history->load(['order', 'changedByUser']));
    }

    public function update(OrderStatusHistory $history, array $data): bool
    {
        return $history->update($data);
    }

    public function updateAndReturnDTO(OrderStatusHistory $history, array $data): ?OrderStatusHistoryDTO
    {
        $updated = $this->update($history, $data);

        return $updated ? OrderStatusHistoryDTO::fromModel($history->fresh()->load(['order', 'changedByUser'])) : null;
    }

    public function delete(OrderStatusHistory $history): bool
    {
        return $history->delete();
    }

    public function getHistoryCount(): int
    {
        return $this->model->count();
    }

    public function getHistoryCountByOrderId(int $orderId): int
    {
        return $this->model->where('order_id', $orderId)->count();
    }

    public function getHistoryCountByUserId(int $userId): int
    {
        return $this->model->where('changed_by', $userId)->count();
    }

    public function getHistoryCountByStatus(string $status): int
    {
        return $this->model->where('new_status', $status)->count();
    }

    public function search(string $query): Collection
    {
        return $this->model->with(['order', 'changedByUser'])
            ->where(function ($q) use ($query) {
                $q->where('note', 'like', "%{$query}%")
                    ->orWhere('reason', 'like', "%{$query}%")
                    ->orWhere('old_status', 'like', "%{$query}%")
                    ->orWhere('new_status', 'like', "%{$query}%")
                    ->orWhereHas('order', function ($orderQuery) use ($query) {
                        $orderQuery->where('id', 'like', "%{$query}%");
                    });
            })
            ->orderBy('changed_at', 'desc')
            ->get();
    }

    public function searchDTO(string $query): Collection
    {
        return $this->search($query)->map(function ($history) {
            return OrderStatusHistoryDTO::fromModel($history);
        });
    }

    public function getRecentHistory(int $limit = 10): Collection
    {
        return $this->model->with(['order', 'changedByUser'])
            ->orderBy('changed_at', 'desc')
            ->limit($limit)
            ->get();
    }

    public function getRecentHistoryDTO(int $limit = 10): Collection
    {
        return $this->getRecentHistory($limit)->map(function ($history) {
            return OrderStatusHistoryDTO::fromModel($history);
        });
    }

    public function getHistoryByChangeType(string $changeType): Collection
    {
        return $this->model->with(['order', 'changedByUser'])
            ->where('change_type', $changeType)
            ->orderBy('changed_at', 'desc')
            ->get();
    }

    public function getHistoryByChangeTypeDTO(string $changeType): Collection
    {
        return $this->getHistoryByChangeType($changeType)->map(function ($history) {
            return OrderStatusHistoryDTO::fromModel($history);
        });
    }

    public function getSystemChanges(): Collection
    {
        return $this->model->with(['order', 'changedByUser'])
            ->where('is_system_change', true)
            ->orderBy('changed_at', 'desc')
            ->get();
    }

    public function getSystemChangesDTO(): Collection
    {
        return $this->getSystemChanges()->map(function ($history) {
            return OrderStatusHistoryDTO::fromModel($history);
        });
    }

    public function getUserChanges(): Collection
    {
        return $this->model->with(['order', 'changedByUser'])
            ->where('is_system_change', false)
            ->orderBy('changed_at', 'desc')
            ->get();
    }

    public function getUserChangesDTO(): Collection
    {
        return $this->getUserChanges()->map(function ($history) {
            return OrderStatusHistoryDTO::fromModel($history);
        });
    }

    public function getStatusTransitionHistory(int $orderId, string $fromStatus, string $toStatus): Collection
    {
        return $this->model->with(['order', 'changedByUser'])
            ->where('order_id', $orderId)
            ->where('old_status', $fromStatus)
            ->where('new_status', $toStatus)
            ->orderBy('changed_at', 'desc')
            ->get();
    }

    public function getStatusTransitionHistoryDTO(int $orderId, string $fromStatus, string $toStatus): Collection
    {
        return $this->getStatusTransitionHistory($orderId, $fromStatus, $toStatus)->map(function ($history) {
            return OrderStatusHistoryDTO::fromModel($history);
        });
    }

    public function getOrderTimeline(int $orderId): Collection
    {
        return $this->model->with(['order', 'changedByUser'])
            ->where('order_id', $orderId)
            ->orderBy('changed_at', 'asc')
            ->get();
    }

    public function getOrderTimelineDTO(int $orderId): Collection
    {
        return $this->getOrderTimeline($orderId)->map(function ($history) {
            return OrderStatusHistoryDTO::fromModel($history);
        });
    }

    public function validateHistoryEntry(array $data): bool
    {
        $validator = validator($data, OrderStatusHistoryDTO::rules(), OrderStatusHistoryDTO::messages());

        return ! $validator->fails();
    }

    public function logStatusChange(int $orderId, string $oldStatus, string $newStatus, int $changedBy, ?string $note = null, array $metadata = []): OrderStatusHistory
    {
        $data = [
            'order_id' => $orderId,
            'old_status' => $oldStatus,
            'new_status' => $newStatus,
            'changed_by' => $changedBy,
            'changed_at' => now(),
            'note' => $note,
            'ip_address' => Request::ip(),
            'user_agent' => Request::userAgent(),
            'metadata' => $metadata,
            'is_system_change' => false,
            'change_type' => 'manual',
            'change_category' => 'status_update',
        ];

        return $this->create($data);
    }

    public function logStatusChangeDTO(int $orderId, string $oldStatus, string $newStatus, int $changedBy, ?string $note = null, array $metadata = []): OrderStatusHistoryDTO
    {
        $history = $this->logStatusChange($orderId, $oldStatus, $newStatus, $changedBy, $note, $metadata);

        return OrderStatusHistoryDTO::fromModel($history->load(['order', 'changedByUser']));
    }

    public function getStatusChangeFrequency(string $status): int
    {
        return $this->model->where('new_status', $status)->count();
    }

    public function getMostFrequentStatusChanges(int $limit = 10): Collection
    {
        return $this->model->select('new_status', DB::raw('count(*) as frequency'))
            ->groupBy('new_status')
            ->orderBy('frequency', 'desc')
            ->limit($limit)
            ->get();
    }

    public function getStatusChangeAnalytics(string $startDate, string $endDate): array
    {
        $analytics = $this->model->select(
            'new_status',
            DB::raw('count(*) as total_changes'),
            DB::raw('count(DISTINCT order_id) as unique_orders'),
            DB::raw('count(DISTINCT changed_by) as unique_users')
        )
            ->whereBetween('changed_at', [$startDate, $endDate])
            ->groupBy('new_status')
            ->get();

        $totalChanges = $this->model->whereBetween('changed_at', [$startDate, $endDate])->count();
        $systemChanges = $this->model->whereBetween('changed_at', [$startDate, $endDate])
            ->where('is_system_change', true)
            ->count();
        $userChanges = $this->model->whereBetween('changed_at', [$startDate, $endDate])
            ->where('is_system_change', false)
            ->count();

        return [
            'period' => [
                'start_date' => $startDate,
                'end_date' => $endDate,
            ],
            'summary' => [
                'total_changes' => $totalChanges,
                'system_changes' => $systemChanges,
                'user_changes' => $userChanges,
                'system_change_percentage' => $totalChanges > 0 ? round(($systemChanges / $totalChanges) * 100, 2) : 0,
                'user_change_percentage' => $totalChanges > 0 ? round(($userChanges / $totalChanges) * 100, 2) : 0,
            ],
            'by_status' => $analytics,
        ];
    }
}
