<?php

namespace Fereydooni\Shopping\app\Repositories;

use Fereydooni\Shopping\app\DTOs\ProviderNoteDTO;
use Fereydooni\Shopping\app\Models\ProviderNote;
use Fereydooni\Shopping\app\Repositories\Interfaces\ProviderNoteRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\CursorPaginator;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProviderNoteRepository implements ProviderNoteRepositoryInterface
{
    public function all(): Collection
    {
        return Cache::remember('provider_notes_all', 3600, function () {
            return ProviderNote::with(['provider', 'user'])->get();
        });
    }

    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        $cacheKey = "provider_notes_paginate_{$perPage}_".request()->get('page', 1);

        return Cache::remember($cacheKey, 1800, function () use ($perPage) {
            return ProviderNote::with(['provider', 'user'])
                ->orderBy('created_at', 'desc')
                ->paginate($perPage);
        });
    }

    public function simplePaginate(int $perPage = 15): Paginator
    {
        return ProviderNote::with(['provider', 'user'])
            ->orderBy('created_at', 'desc')
            ->simplePaginate($perPage);
    }

    public function cursorPaginate(int $perPage = 15, ?string $cursor = null): CursorPaginator
    {
        return ProviderNote::with(['provider', 'user'])
            ->orderBy('created_at', 'desc')
            ->cursorPaginate($perPage, ['*'], 'cursor', $cursor);
    }

    public function find(int $id): ?ProviderNote
    {
        return Cache::remember("provider_note_{$id}", 3600, function () use ($id) {
            return ProviderNote::with(['provider', 'user'])->find($id);
        });
    }

    public function findDTO(int $id): ?ProviderNoteDTO
    {
        $providerNote = $this->find($id);

        return $providerNote ? ProviderNoteDTO::fromModel($providerNote) : null;
    }

    public function findByProviderId(int $providerId): Collection
    {
        $cacheKey = "provider_notes_provider_{$providerId}";

        return Cache::remember($cacheKey, 1800, function () use ($providerId) {
            return ProviderNote::with(['provider', 'user'])
                ->where('provider_id', $providerId)
                ->orderBy('created_at', 'desc')
                ->get();
        });
    }

    public function findByProviderIdDTO(int $providerId): Collection
    {
        $notes = $this->findByProviderId($providerId);

        return $notes->map(fn ($note) => ProviderNoteDTO::fromModel($note));
    }

    public function findByUserId(int $userId): Collection
    {
        $cacheKey = "provider_notes_user_{$userId}";

        return Cache::remember($cacheKey, 1800, function () use ($userId) {
            return ProviderNote::with(['provider', 'user'])
                ->where('user_id', $userId)
                ->orderBy('created_at', 'desc')
                ->get();
        });
    }

    public function findByUserIdDTO(int $userId): Collection
    {
        $notes = $this->findByUserId($userId);

        return $notes->map(fn ($note) => ProviderNoteDTO::fromModel($note));
    }

    public function findByNoteType(string $noteType): Collection
    {
        $cacheKey = "provider_notes_type_{$noteType}";

        return Cache::remember($cacheKey, 1800, function () use ($noteType) {
            return ProviderNote::with(['provider', 'user'])
                ->where('note_type', $noteType)
                ->orderBy('created_at', 'desc')
                ->get();
        });
    }

    public function findByNoteTypeDTO(string $noteType): Collection
    {
        $notes = $this->findByNoteType($noteType);

        return $notes->map(fn ($note) => ProviderNoteDTO::fromModel($note));
    }

    public function findByPriority(string $priority): Collection
    {
        $cacheKey = "provider_notes_priority_{$priority}";

        return Cache::remember($cacheKey, 1800, function () use ($priority) {
            return ProviderNote::with(['provider', 'user'])
                ->where('priority', $priority)
                ->orderBy('created_at', 'desc')
                ->get();
        });
    }

    public function findByPriorityDTO(string $priority): Collection
    {
        $notes = $this->findByPriority($priority);

        return $notes->map(fn ($note) => ProviderNoteDTO::fromModel($note));
    }

    public function findPrivate(): Collection
    {
        return Cache::remember('provider_notes_private', 1800, function () {
            return ProviderNote::with(['provider', 'user'])
                ->where('is_private', true)
                ->orderBy('created_at', 'desc')
                ->get();
        });
    }

    public function findPrivateDTO(): Collection
    {
        $notes = $this->findPrivate();

        return $notes->map(fn ($note) => ProviderNoteDTO::fromModel($note));
    }

    public function findPublic(): Collection
    {
        return Cache::remember('provider_notes_public', 1800, function () {
            return ProviderNote::with(['provider', 'user'])
                ->where('is_private', false)
                ->orderBy('created_at', 'desc')
                ->get();
        });
    }

    public function findPublicDTO(): Collection
    {
        $notes = $this->findPublic();

        return $notes->map(fn ($note) => ProviderNoteDTO::fromModel($note));
    }

    public function findArchived(): Collection
    {
        return Cache::remember('provider_notes_archived', 1800, function () {
            return ProviderNote::with(['provider', 'user'])
                ->where('is_archived', true)
                ->orderBy('created_at', 'desc')
                ->get();
        });
    }

    public function findArchivedDTO(): Collection
    {
        $notes = $this->findArchived();

        return $notes->map(fn ($note) => ProviderNoteDTO::fromModel($note));
    }

    public function findActive(): Collection
    {
        return Cache::remember('provider_notes_active', 1800, function () {
            return ProviderNote::with(['provider', 'user'])
                ->where('is_archived', false)
                ->orderBy('created_at', 'desc')
                ->get();
        });
    }

    public function findActiveDTO(): Collection
    {
        $notes = $this->findActive();

        return $notes->map(fn ($note) => ProviderNoteDTO::fromModel($note));
    }

    public function findByTags(array $tags): Collection
    {
        $cacheKey = 'provider_notes_tags_'.md5(serialize($tags));

        return Cache::remember($cacheKey, 1800, function () use ($tags) {
            return ProviderNote::with(['provider', 'user'])
                ->whereJsonContains('tags', $tags)
                ->orderBy('created_at', 'desc')
                ->get();
        });
    }

    public function findByTagsDTO(array $tags): Collection
    {
        $notes = $this->findByTags($tags);

        return $notes->map(fn ($note) => ProviderNoteDTO::fromModel($note));
    }

    public function findByDateRange(string $startDate, string $endDate): Collection
    {
        $cacheKey = "provider_notes_date_{$startDate}_{$endDate}";

        return Cache::remember($cacheKey, 1800, function () use ($startDate, $endDate) {
            return ProviderNote::with(['provider', 'user'])
                ->whereBetween('created_at', [$startDate, $endDate])
                ->orderBy('created_at', 'desc')
                ->get();
        });
    }

    public function findByDateRangeDTO(string $startDate, string $endDate): Collection
    {
        $notes = $this->findByDateRange($startDate, $endDate);

        return $notes->map(fn ($note) => ProviderNoteDTO::fromModel($note));
    }

    public function findByProviderAndType(int $providerId, string $noteType): Collection
    {
        $cacheKey = "provider_notes_provider_{$providerId}_type_{$noteType}";

        return Cache::remember($cacheKey, 1800, function () use ($providerId, $noteType) {
            return ProviderNote::with(['provider', 'user'])
                ->where('provider_id', $providerId)
                ->where('note_type', $noteType)
                ->orderBy('created_at', 'desc')
                ->get();
        });
    }

    public function findByProviderAndTypeDTO(int $providerId, string $noteType): Collection
    {
        $notes = $this->findByProviderAndType($providerId, $noteType);

        return $notes->map(fn ($note) => ProviderNoteDTO::fromModel($note));
    }

    public function findByProviderAndPriority(int $providerId, string $priority): Collection
    {
        $cacheKey = "provider_notes_provider_{$providerId}_priority_{$priority}";

        return Cache::remember($cacheKey, 1800, function () use ($providerId, $priority) {
            return ProviderNote::with(['provider', 'user'])
                ->where('provider_id', $providerId)
                ->where('priority', $priority)
                ->orderBy('created_at', 'desc')
                ->get();
        });
    }

    public function findByProviderAndPriorityDTO(int $providerId, string $priority): Collection
    {
        $notes = $this->findByProviderAndPriority($providerId, $priority);

        return $notes->map(fn ($note) => ProviderNoteDTO::fromModel($note));
    }

    public function create(array $data): ProviderNote
    {
        try {
            DB::beginTransaction();

            $providerNote = ProviderNote::create($data);

            // Clear relevant caches
            $this->clearProviderNoteCaches($providerNote->provider_id);

            DB::commit();

            Log::info('Provider note created', ['id' => $providerNote->id, 'provider_id' => $providerNote->provider_id]);

            return $providerNote->load(['provider', 'user']);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to create provider note', ['error' => $e->getMessage(), 'data' => $data]);
            throw $e;
        }
    }

    public function createAndReturnDTO(array $data): ProviderNoteDTO
    {
        $providerNote = $this->create($data);

        return ProviderNoteDTO::fromModel($providerNote);
    }

    public function update(ProviderNote $providerNote, array $data): bool
    {
        try {
            DB::beginTransaction();

            $updated = $providerNote->update($data);

            if ($updated) {
                // Clear relevant caches
                $this->clearProviderNoteCaches($providerNote->provider_id);

                Log::info('Provider note updated', ['id' => $providerNote->id, 'provider_id' => $providerNote->provider_id]);
            }

            DB::commit();

            return $updated;

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to update provider note', ['error' => $e->getMessage(), 'id' => $providerNote->id]);
            throw $e;
        }
    }

    public function updateAndReturnDTO(ProviderNote $providerNote, array $data): ?ProviderNoteDTO
    {
        $updated = $this->update($providerNote, $data);

        return $updated ? ProviderNoteDTO::fromModel($providerNote->fresh()) : null;
    }

    public function delete(ProviderNote $providerNote): bool
    {
        try {
            DB::beginTransaction();

            $deleted = $providerNote->delete();

            if ($deleted) {
                // Clear relevant caches
                $this->clearProviderNoteCaches($providerNote->provider_id);

                Log::info('Provider note deleted', ['id' => $providerNote->id, 'provider_id' => $providerNote->provider_id]);
            }

            DB::commit();

            return $deleted;

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to delete provider note', ['error' => $e->getMessage(), 'id' => $providerNote->id]);
            throw $e;
        }
    }

    public function archive(ProviderNote $providerNote): bool
    {
        return $this->update($providerNote, ['is_archived' => true]);
    }

    public function unarchive(ProviderNote $providerNote): bool
    {
        return $this->update($providerNote, ['is_archived' => false]);
    }

    public function makePrivate(ProviderNote $providerNote): bool
    {
        return $this->update($providerNote, ['is_private' => true]);
    }

    public function makePublic(ProviderNote $providerNote): bool
    {
        return $this->update($providerNote, ['is_private' => false]);
    }

    public function addTags(ProviderNote $providerNote, array $tags): bool
    {
        try {
            $currentTags = $providerNote->tags ?? [];
            $newTags = array_unique(array_merge($currentTags, $tags));

            return $this->update($providerNote, ['tags' => $newTags]);

        } catch (\Exception $e) {
            Log::error('Failed to add tags to provider note', ['error' => $e->getMessage(), 'id' => $providerNote->id]);

            return false;
        }
    }

    public function removeTags(ProviderNote $providerNote, array $tags): bool
    {
        try {
            $currentTags = $providerNote->tags ?? [];
            $newTags = array_diff($currentTags, $tags);

            return $this->update($providerNote, ['tags' => array_values($newTags)]);

        } catch (\Exception $e) {
            Log::error('Failed to remove tags from provider note', ['error' => $e->getMessage(), 'id' => $providerNote->id]);

            return false;
        }
    }

    public function clearTags(ProviderNote $providerNote): bool
    {
        return $this->update($providerNote, ['tags' => null]);
    }

    public function addAttachment(ProviderNote $providerNote, string $attachmentPath): bool
    {
        try {
            $currentAttachments = $providerNote->attachments ?? [];
            $currentAttachments[] = $attachmentPath;

            return $this->update($providerNote, ['attachments' => $currentAttachments]);

        } catch (\Exception $e) {
            Log::error('Failed to add attachment to provider note', ['error' => $e->getMessage(), 'id' => $providerNote->id]);

            return false;
        }
    }

    public function removeAttachment(ProviderNote $providerNote, string $attachmentPath): bool
    {
        try {
            $currentAttachments = $providerNote->attachments ?? [];
            $newAttachments = array_filter($currentAttachments, fn ($path) => $path !== $attachmentPath);

            return $this->update($providerNote, ['attachments' => array_values($newAttachments)]);

        } catch (\Exception $e) {
            Log::error('Failed to remove attachment from provider note', ['error' => $e->getMessage(), 'id' => $providerNote->id]);

            return false;
        }
    }

    public function getProviderNoteCount(int $providerId): int
    {
        $cacheKey = "provider_notes_count_provider_{$providerId}";

        return Cache::remember($cacheKey, 3600, function () use ($providerId) {
            return ProviderNote::where('provider_id', $providerId)->count();
        });
    }

    public function getProviderNoteCountByType(int $providerId, string $noteType): int
    {
        $cacheKey = "provider_notes_count_provider_{$providerId}_type_{$noteType}";

        return Cache::remember($cacheKey, 3600, function () use ($providerId, $noteType) {
            return ProviderNote::where('provider_id', $providerId)
                ->where('note_type', $noteType)
                ->count();
        });
    }

    public function getProviderNoteCountByPriority(int $providerId, string $priority): int
    {
        $cacheKey = "provider_notes_count_provider_{$providerId}_priority_{$priority}";

        return Cache::remember($cacheKey, 3600, function () use ($providerId, $priority) {
            return ProviderNote::where('provider_id', $providerId)
                ->where('priority', $priority)
                ->count();
        });
    }

    public function getPrivateNoteCount(int $providerId): int
    {
        $cacheKey = "provider_notes_count_provider_{$providerId}_private";

        return Cache::remember($cacheKey, 3600, function () use ($providerId) {
            return ProviderNote::where('provider_id', $providerId)
                ->where('is_private', true)
                ->count();
        });
    }

    public function getPublicNoteCount(int $providerId): int
    {
        $cacheKey = "provider_notes_count_provider_{$providerId}_public";

        return Cache::remember($cacheKey, 3600, function () use ($providerId) {
            return ProviderNote::where('provider_id', $providerId)
                ->where('is_private', false)
                ->count();
        });
    }

    public function getArchivedNoteCount(int $providerId): int
    {
        $cacheKey = "provider_notes_count_provider_{$providerId}_archived";

        return Cache::remember($cacheKey, 3600, function () use ($providerId) {
            return ProviderNote::where('provider_id', $providerId)
                ->where('is_archived', true)
                ->count();
        });
    }

    public function getActiveNoteCount(int $providerId): int
    {
        $cacheKey = "provider_notes_count_provider_{$providerId}_active";

        return Cache::remember($cacheKey, 3600, function () use ($providerId) {
            return ProviderNote::where('provider_id', $providerId)
                ->where('is_archived', false)
                ->count();
        });
    }

    public function getTotalNoteCount(): int
    {
        return Cache::remember('provider_notes_total_count', 3600, function () {
            return ProviderNote::count();
        });
    }

    public function getTotalNoteCountByType(string $noteType): int
    {
        $cacheKey = "provider_notes_total_count_type_{$noteType}";

        return Cache::remember($cacheKey, 3600, function () use ($noteType) {
            return ProviderNote::where('note_type', $noteType)->count();
        });
    }

    public function getTotalNoteCountByPriority(string $priority): int
    {
        $cacheKey = "provider_notes_total_count_priority_{$priority}";

        return Cache::remember($cacheKey, 3600, function () use ($priority) {
            return ProviderNote::where('priority', $priority)->count();
        });
    }

    public function getRecentNotes(int $limit = 10): Collection
    {
        $cacheKey = "provider_notes_recent_{$limit}";

        return Cache::remember($cacheKey, 900, function () use ($limit) {
            return ProviderNote::with(['provider', 'user'])
                ->orderBy('created_at', 'desc')
                ->limit($limit)
                ->get();
        });
    }

    public function getRecentNotesDTO(int $limit = 10): Collection
    {
        $notes = $this->getRecentNotes($limit);

        return $notes->map(fn ($note) => ProviderNoteDTO::fromModel($note));
    }

    public function getRecentNotesByProvider(int $providerId, int $limit = 10): Collection
    {
        $cacheKey = "provider_notes_recent_provider_{$providerId}_{$limit}";

        return Cache::remember($cacheKey, 900, function () use ($providerId, $limit) {
            return ProviderNote::with(['provider', 'user'])
                ->where('provider_id', $providerId)
                ->orderBy('created_at', 'desc')
                ->limit($limit)
                ->get();
        });
    }

    public function getRecentNotesByProviderDTO(int $providerId, int $limit = 10): Collection
    {
        $notes = $this->getRecentNotesByProvider($providerId, $limit);

        return $notes->map(fn ($note) => ProviderNoteDTO::fromModel($note));
    }

    public function searchNotes(string $query): Collection
    {
        $cacheKey = 'provider_notes_search_'.md5($query);

        return Cache::remember($cacheKey, 1800, function () use ($query) {
            return ProviderNote::with(['provider', 'user'])
                ->where(function ($q) use ($query) {
                    $q->where('title', 'like', "%{$query}%")
                        ->orWhere('content', 'like', "%{$query}%")
                        ->orWhereJsonContains('tags', [$query]);
                })
                ->orderBy('created_at', 'desc')
                ->get();
        });
    }

    public function searchNotesDTO(string $query): Collection
    {
        $notes = $this->searchNotes($query);

        return $notes->map(fn ($note) => ProviderNoteDTO::fromModel($note));
    }

    public function searchNotesByProvider(int $providerId, string $query): Collection
    {
        $cacheKey = "provider_notes_search_provider_{$providerId}_".md5($query);

        return Cache::remember($cacheKey, 1800, function () use ($providerId, $query) {
            return ProviderNote::with(['provider', 'user'])
                ->where('provider_id', $providerId)
                ->where(function ($q) use ($query) {
                    $q->where('title', 'like', "%{$query}%")
                        ->orWhere('content', 'like', "%{$query}%")
                        ->orWhereJsonContains('tags', [$query]);
                })
                ->orderBy('created_at', 'desc')
                ->get();
        });
    }

    public function searchNotesByProviderDTO(int $providerId, string $query): Collection
    {
        $notes = $this->searchNotesByProvider($providerId, $query);

        return $notes->map(fn ($note) => ProviderNoteDTO::fromModel($note));
    }

    /**
     * Clear all provider note related caches
     */
    private function clearProviderNoteCaches(int $providerId): void
    {
        $cacheKeys = [
            'provider_notes_all',
            'provider_notes_private',
            'provider_notes_public',
            'provider_notes_archived',
            'provider_notes_active',
            "provider_notes_provider_{$providerId}",
            "provider_notes_count_provider_{$providerId}",
            "provider_notes_count_provider_{$providerId}_private",
            "provider_notes_count_provider_{$providerId}_public",
            "provider_notes_count_provider_{$providerId}_archived",
            "provider_notes_count_provider_{$providerId}_active",
            'provider_notes_total_count',
        ];

        foreach ($cacheKeys as $key) {
            Cache::forget($key);
        }

        // Clear pagination caches
        for ($page = 1; $page <= 10; $page++) {
            Cache::forget("provider_notes_paginate_15_{$page}");
        }
    }
}
