<?php

namespace App\Repositories;

use App\DTOs\ProviderCommunicationDTO;
use App\Enums\Status;
use App\Models\ProviderCommunication;
use App\Repositories\Interfaces\ProviderCommunicationRepositoryInterface;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\CursorPaginator;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProviderCommunicationRepository implements ProviderCommunicationRepositoryInterface
{
    protected $model;

    protected $cachePrefix = 'provider_communication_';

    protected $cacheTtl = 3600; // 1 hour

    public function __construct(ProviderCommunication $model)
    {
        $this->model = $model;
    }

    // Basic CRUD operations
    public function all(): Collection
    {
        return Cache::remember($this->cachePrefix.'all', $this->cacheTtl, function () {
            return $this->model->with(['provider', 'user'])->get();
        });
    }

    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        return $this->model->with(['provider', 'user'])
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    public function simplePaginate(int $perPage = 15): Paginator
    {
        return $this->model->with(['provider', 'user'])
            ->orderBy('created_at', 'desc')
            ->simplePaginate($perPage);
    }

    public function cursorPaginate(int $perPage = 15, ?string $cursor = null): CursorPaginator
    {
        return $this->model->with(['provider', 'user'])
            ->orderBy('created_at', 'desc')
            ->cursorPaginate($perPage, ['*'], 'cursor', $cursor);
    }

    public function find(int $id): ?ProviderCommunication
    {
        return Cache::remember($this->cachePrefix.'find_'.$id, $this->cacheTtl, function () use ($id) {
            return $this->model->with(['provider', 'user', 'replies', 'thread'])->find($id);
        });
    }

    public function findDTO(int $id): ?ProviderCommunicationDTO
    {
        $model = $this->find($id);

        return $model ? ProviderCommunicationDTO::fromModel($model) : null;
    }

    public function findByProviderId(int $providerId): Collection
    {
        return Cache::remember($this->cachePrefix.'provider_'.$providerId, $this->cacheTtl, function () use ($providerId) {
            return $this->model->byProvider($providerId)
                ->with(['user', 'replies'])
                ->orderBy('created_at', 'desc')
                ->get();
        });
    }

    public function findByProviderIdDTO(int $providerId): Collection
    {
        return $this->findByProviderId($providerId)->map(function ($item) {
            return ProviderCommunicationDTO::fromModel($item);
        });
    }

    public function findByUserId(int $userId): Collection
    {
        return Cache::remember($this->cachePrefix.'user_'.$userId, $this->cacheTtl, function () use ($userId) {
            return $this->model->byUser($userId)
                ->with(['provider', 'replies'])
                ->orderBy('created_at', 'desc')
                ->get();
        });
    }

    public function findByUserIdDTO(int $userId): Collection
    {
        return $this->findByUserId($userId)->map(function ($item) {
            return ProviderCommunicationDTO::fromModel($item);
        });
    }

    public function findByCommunicationType(string $communicationType): Collection
    {
        return $this->model->byType($communicationType)
            ->with(['provider', 'user'])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function findByCommunicationTypeDTO(string $communicationType): Collection
    {
        return $this->findByCommunicationType($communicationType)->map(function ($item) {
            return ProviderCommunicationDTO::fromModel($item);
        });
    }

    public function findByDirection(string $direction): Collection
    {
        return $this->model->byDirection($direction)
            ->with(['provider', 'user'])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function findByDirectionDTO(string $direction): Collection
    {
        return $this->findByDirection($direction)->map(function ($item) {
            return ProviderCommunicationDTO::fromModel($item);
        });
    }

    public function findByStatus(string $status): Collection
    {
        return $this->model->byStatus($status)
            ->with(['provider', 'user'])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function findByStatusDTO(string $status): Collection
    {
        return $this->findByStatus($status)->map(function ($item) {
            return ProviderCommunicationDTO::fromModel($item);
        });
    }

    public function findByPriority(string $priority): Collection
    {
        return $this->model->byPriority($priority)
            ->with(['provider', 'user'])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function findByPriorityDTO(string $priority): Collection
    {
        return $this->findByPriority($priority)->map(function ($item) {
            return ProviderCommunicationDTO::fromModel($item);
        });
    }

    public function findByThreadId(string $threadId): Collection
    {
        return $this->model->byThread($threadId)
            ->with(['provider', 'user'])
            ->orderBy('created_at', 'asc')
            ->get();
    }

    public function findByThreadIdDTO(string $threadId): Collection
    {
        return $this->findByThreadId($threadId)->map(function ($item) {
            return ProviderCommunicationDTO::fromModel($item);
        });
    }

    public function findByParentId(int $parentId): Collection
    {
        return $this->model->where('parent_id', $parentId)
            ->with(['provider', 'user'])
            ->orderBy('created_at', 'asc')
            ->get();
    }

    public function findByParentIdDTO(int $parentId): Collection
    {
        return $this->findByParentId($parentId)->map(function ($item) {
            return ProviderCommunicationDTO::fromModel($item);
        });
    }

    // Special queries
    public function findUrgent(): Collection
    {
        return $this->model->urgent()
            ->with(['provider', 'user'])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function findUrgentDTO(): Collection
    {
        return $this->findUrgent()->map(function ($item) {
            return ProviderCommunicationDTO::fromModel($item);
        });
    }

    public function findArchived(): Collection
    {
        return $this->model->archived()
            ->with(['provider', 'user'])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function findArchivedDTO(): Collection
    {
        return $this->findArchived()->map(function ($item) {
            return ProviderCommunicationDTO::fromModel($item);
        });
    }

    public function findUnread(): Collection
    {
        return $this->model->unread()
            ->with(['provider', 'user'])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function findUnreadDTO(): Collection
    {
        return $this->findUnread()->map(function ($item) {
            return ProviderCommunicationDTO::fromModel($item);
        });
    }

    public function findUnreplied(): Collection
    {
        return $this->model->unreplied()
            ->with(['provider', 'user'])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function findUnrepliedDTO(): Collection
    {
        return $this->findUnreplied()->map(function ($item) {
            return ProviderCommunicationDTO::fromModel($item);
        });
    }

    // Create and update operations
    public function create(array $data): ProviderCommunication
    {
        try {
            DB::beginTransaction();

            $communication = $this->model->create($data);

            // Generate thread_id if not provided
            if (empty($data['thread_id'])) {
                $communication->update(['thread_id' => uniqid('thread_')]);
            }

            DB::commit();
            $this->clearCache();

            return $communication->load(['provider', 'user']);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Failed to create provider communication: '.$e->getMessage());
            throw $e;
        }
    }

    public function createAndReturnDTO(array $data): ProviderCommunicationDTO
    {
        $communication = $this->create($data);

        return ProviderCommunicationDTO::fromModel($communication);
    }

    public function update(ProviderCommunication $providerCommunication, array $data): bool
    {
        try {
            DB::beginTransaction();

            $result = $providerCommunication->update($data);

            DB::commit();
            $this->clearCache();

            return $result;
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Failed to update provider communication: '.$e->getMessage());
            throw $e;
        }
    }

    public function updateAndReturnDTO(ProviderCommunication $providerCommunication, array $data): ?ProviderCommunicationDTO
    {
        $result = $this->update($providerCommunication, $data);

        return $result ? ProviderCommunicationDTO::fromModel($providerCommunication->fresh()) : null;
    }

    public function delete(ProviderCommunication $providerCommunication): bool
    {
        try {
            DB::beginTransaction();

            $result = $providerCommunication->delete();

            DB::commit();
            $this->clearCache();

            return $result;
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Failed to delete provider communication: '.$e->getMessage());
            throw $e;
        }
    }

    // Status management
    public function markAsRead(ProviderCommunication $providerCommunication): bool
    {
        return $this->update($providerCommunication, ['read_at' => now()]);
    }

    public function markAsReplied(ProviderCommunication $providerCommunication): bool
    {
        return $this->update($providerCommunication, ['replied_at' => now()]);
    }

    public function markAsClosed(ProviderCommunication $providerCommunication): bool
    {
        return $this->update($providerCommunication, ['status' => Status::CLOSED]);
    }

    public function archive(ProviderCommunication $providerCommunication): bool
    {
        return $this->update($providerCommunication, ['is_archived' => true]);
    }

    public function unarchive(ProviderCommunication $providerCommunication): bool
    {
        return $this->update($providerCommunication, ['is_archived' => false]);
    }

    public function setUrgent(ProviderCommunication $providerCommunication): bool
    {
        return $this->update($providerCommunication, ['is_urgent' => true]);
    }

    public function unsetUrgent(ProviderCommunication $providerCommunication): bool
    {
        return $this->update($providerCommunication, ['is_urgent' => false]);
    }

    // Count operations
    public function getCommunicationCount(int $providerId): int
    {
        return Cache::remember($this->cachePrefix.'count_provider_'.$providerId, $this->cacheTtl, function () use ($providerId) {
            return $this->model->byProvider($providerId)->count();
        });
    }

    public function getUnreadCount(int $providerId): int
    {
        return Cache::remember($this->cachePrefix.'unread_count_provider_'.$providerId, $this->cacheTtl, function () use ($providerId) {
            return $this->model->byProvider($providerId)->unread()->count();
        });
    }

    public function getUrgentCount(int $providerId): int
    {
        return Cache::remember($this->cachePrefix.'urgent_count_provider_'.$providerId, $this->cacheTtl, function () use ($providerId) {
            return $this->model->byProvider($providerId)->urgent()->count();
        });
    }

    // Search operations
    public function searchCommunications(string $query): Collection
    {
        return $this->model->where(function ($q) use ($query) {
            $q->where('subject', 'like', "%{$query}%")
                ->orWhere('message', 'like', "%{$query}%")
                ->orWhere('notes', 'like', "%{$query}%");
        })
            ->with(['provider', 'user'])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function searchCommunicationsDTO(string $query): Collection
    {
        return $this->searchCommunications($query)->map(function ($item) {
            return ProviderCommunicationDTO::fromModel($item);
        });
    }

    // Helper methods
    protected function clearCache(): void
    {
        Cache::flush();
    }

    // Additional methods will be implemented as needed...
    // This is a comprehensive base implementation
}
