<?php

namespace Fereydooni\Shopping\app\Repositories;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Pagination\CursorPaginator;
use Fereydooni\Shopping\app\Repositories\Interfaces\EmployeeNoteRepositoryInterface;
use Fereydooni\Shopping\app\Models\EmployeeNote;
use Fereydooni\Shopping\app\DTOs\EmployeeNoteDTO;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class EmployeeNoteRepository implements EmployeeNoteRepositoryInterface
{
    public function __construct(
        protected EmployeeNote $model
    ) {}

    public function all(): Collection
    {
        return $this->model->with(['employee', 'user'])->get();
    }

    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        return $this->model->with(['employee', 'user'])
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    public function simplePaginate(int $perPage = 15): Paginator
    {
        return $this->model->with(['employee', 'user'])
            ->orderBy('created_at', 'desc')
            ->simplePaginate($perPage);
    }

    public function cursorPaginate(int $perPage = 15, string $cursor = null): CursorPaginator
    {
        return $this->model->with(['employee', 'user'])
            ->orderBy('created_at', 'desc')
            ->cursorPaginate($perPage, ['*'], 'cursor', $cursor);
    }

    public function find(int $id): ?EmployeeNote
    {
        return $this->model->with(['employee', 'user'])->find($id);
    }

    public function findDTO(int $id): ?EmployeeNoteDTO
    {
        $note = $this->find($id);
        return $note ? EmployeeNoteDTO::fromModel($note) : null;
    }

    public function create(array $data): EmployeeNote
    {
        try {
            DB::beginTransaction();
            
            $note = $this->model->create($data);
            
            DB::commit();
            return $note->load(['employee', 'user']);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to create employee note: ' . $e->getMessage());
            throw $e;
        }
    }

    public function createAndReturnDTO(array $data): EmployeeNoteDTO
    {
        $note = $this->create($data);
        return EmployeeNoteDTO::fromModel($note);
    }

    public function update(EmployeeNote $employeeNote, array $data): bool
    {
        try {
            DB::beginTransaction();
            
            $result = $employeeNote->update($data);
            
            DB::commit();
            return $result;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to update employee note: ' . $e->getMessage());
            throw $e;
        }
    }

    public function updateAndReturnDTO(EmployeeNote $employeeNote, array $data): ?EmployeeNoteDTO
    {
        $updated = $this->update($employeeNote, $data);
        return $updated ? EmployeeNoteDTO::fromModel($employeeNote->fresh()) : null;
    }

    public function delete(EmployeeNote $employeeNote): bool
    {
        try {
            DB::beginTransaction();
            
            $result = $employeeNote->delete();
            
            DB::commit();
            return $result;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to delete employee note: ' . $e->getMessage());
            throw $e;
        }
    }

    public function findByEmployeeId(int $employeeId): Collection
    {
        return $this->model->byEmployee($employeeId)
            ->with(['employee', 'user'])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function findByEmployeeIdDTO(int $employeeId): Collection
    {
        return $this->findByEmployeeId($employeeId)
            ->map(fn($note) => EmployeeNoteDTO::fromModel($note));
    }

    public function findByUserId(int $userId): Collection
    {
        return $this->model->byUser($userId)
            ->with(['employee', 'user'])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function findByUserIdDTO(int $userId): Collection
    {
        return $this->findByUserId($userId)
            ->map(fn($note) => EmployeeNoteDTO::fromModel($note));
    }

    public function findByNoteType(string $noteType): Collection
    {
        return $this->model->byType($noteType)
            ->with(['employee', 'user'])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function findByNoteTypeDTO(string $noteType): Collection
    {
        return $this->findByNoteType($noteType)
            ->map(fn($note) => EmployeeNoteDTO::fromModel($note));
    }

    public function findByPriority(string $priority): Collection
    {
        return $this->model->byPriority($priority)
            ->with(['employee', 'user'])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function findByPriorityDTO(string $priority): Collection
    {
        return $this->findByPriority($priority)
            ->map(fn($note) => EmployeeNoteDTO::fromModel($note));
    }

    public function findPrivate(): Collection
    {
        return $this->model->private()
            ->with(['employee', 'user'])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function findPrivateDTO(): Collection
    {
        return $this->findPrivate()
            ->map(fn($note) => EmployeeNoteDTO::fromModel($note));
    }

    public function findPublic(): Collection
    {
        return $this->model->public()
            ->with(['employee', 'user'])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function findPublicDTO(): Collection
    {
        return $this->findPublic()
            ->map(fn($note) => EmployeeNoteDTO::fromModel($note));
    }

    public function findArchived(): Collection
    {
        return $this->model->archived()
            ->with(['employee', 'user'])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function findArchivedDTO(): Collection
    {
        return $this->findArchived()
            ->map(fn($note) => EmployeeNoteDTO::fromModel($note));
    }

    public function findActive(): Collection
    {
        return $this->model->active()
            ->with(['employee', 'user'])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function findActiveDTO(): Collection
    {
        return $this->findActive()
            ->map(fn($note) => EmployeeNoteDTO::fromModel($note));
    }

    public function findByTags(array $tags): Collection
    {
        return $this->model->byTags($tags)
            ->with(['employee', 'user'])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function findByTagsDTO(array $tags): Collection
    {
        return $this->findByTags($tags)
            ->map(fn($note) => EmployeeNoteDTO::fromModel($note));
    }

    public function findByDateRange(string $startDate, string $endDate): Collection
    {
        return $this->model->byDateRange($startDate, $endDate)
            ->with(['employee', 'user'])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function findByDateRangeDTO(string $startDate, string $endDate): Collection
    {
        return $this->findByDateRange($startDate, $endDate)
            ->map(fn($note) => EmployeeNoteDTO::fromModel($note));
    }

    public function findByEmployeeAndType(int $employeeId, string $noteType): Collection
    {
        return $this->model->byEmployee($employeeId)
            ->byType($noteType)
            ->with(['employee', 'user'])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function findByEmployeeAndTypeDTO(int $employeeId, string $noteType): Collection
    {
        return $this->findByEmployeeAndType($employeeId, $noteType)
            ->map(fn($note) => EmployeeNoteDTO::fromModel($note));
    }

    public function findByEmployeeAndPriority(int $employeeId, string $priority): Collection
    {
        return $this->model->byEmployee($employeeId)
            ->byPriority($priority)
            ->with(['employee', 'user'])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function findByEmployeeAndPriorityDTO(int $employeeId, string $priority): Collection
    {
        return $this->findByEmployeeAndPriority($employeeId, $priority)
            ->map(fn($note) => EmployeeNoteDTO::fromModel($note));
    }

    public function archive(EmployeeNote $employeeNote): bool
    {
        return $this->update($employeeNote, ['is_archived' => true]);
    }

    public function unarchive(EmployeeNote $employeeNote): bool
    {
        return $this->update($employeeNote, ['is_archived' => false]);
    }

    public function makePrivate(EmployeeNote $employeeNote): bool
    {
        return $this->update($employeeNote, ['is_private' => true]);
    }

    public function makePublic(EmployeeNote $employeeNote): bool
    {
        return $this->update($employeeNote, ['is_private' => false]);
    }

    public function addTags(EmployeeNote $employeeNote, array $tags): bool
    {
        $currentTags = $employeeNote->tags ?? [];
        $newTags = array_unique(array_merge($currentTags, $tags));
        return $this->update($employeeNote, ['tags' => $newTags]);
    }

    public function removeTags(EmployeeNote $employeeNote, array $tags): bool
    {
        $currentTags = $employeeNote->tags ?? [];
        $newTags = array_diff($currentTags, $tags);
        return $this->update($employeeNote, ['tags' => array_values($newTags)]);
    }

    public function clearTags(EmployeeNote $employeeNote): bool
    {
        return $this->update($employeeNote, ['tags' => []]);
    }

    public function addAttachment(EmployeeNote $employeeNote, string $attachmentPath): bool
    {
        $currentAttachments = $employeeNote->attachments ?? [];
        $currentAttachments[] = $attachmentPath;
        return $this->update($employeeNote, ['attachments' => $currentAttachments]);
    }

    public function removeAttachment(EmployeeNote $employeeNote, string $attachmentPath): bool
    {
        $currentAttachments = $employeeNote->attachments ?? [];
        $newAttachments = array_filter($currentAttachments, fn($path) => $path !== $attachmentPath);
        return $this->update($employeeNote, ['attachments' => array_values($newAttachments)]);
    }

    public function getEmployeeNoteCount(int $employeeId): int
    {
        return $this->model->byEmployee($employeeId)->count();
    }

    public function getEmployeeNoteCountByType(int $employeeId, string $noteType): int
    {
        return $this->model->byEmployee($employeeId)->byType($noteType)->count();
    }

    public function getEmployeeNoteCountByPriority(int $employeeId, string $priority): int
    {
        return $this->model->byEmployee($employeeId)->byPriority($priority)->count();
    }

    public function getPrivateNoteCount(int $employeeId): int
    {
        return $this->model->byEmployee($employeeId)->private()->count();
    }

    public function getPublicNoteCount(int $employeeId): int
    {
        return $this->model->byEmployee($employeeId)->public()->count();
    }

    public function getArchivedNoteCount(int $employeeId): int
    {
        return $this->model->byEmployee($employeeId)->archived()->count();
    }

    public function getActiveNoteCount(int $employeeId): int
    {
        return $this->model->byEmployee($employeeId)->active()->count();
    }

    public function getTotalNoteCount(): int
    {
        return $this->model->count();
    }

    public function getTotalNoteCountByType(string $noteType): int
    {
        return $this->model->byType($noteType)->count();
    }

    public function getTotalNoteCountByPriority(string $priority): int
    {
        return $this->model->byPriority($priority)->count();
    }

    public function getRecentNotes(int $limit = 10): Collection
    {
        return $this->model->with(['employee', 'user'])
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    public function getRecentNotesDTO(int $limit = 10): Collection
    {
        return $this->getRecentNotes($limit)
            ->map(fn($note) => EmployeeNoteDTO::fromModel($note));
    }

    public function getRecentNotesByEmployee(int $employeeId, int $limit = 10): Collection
    {
        return $this->model->byEmployee($employeeId)
            ->with(['employee', 'user'])
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    public function getRecentNotesByEmployeeDTO(int $employeeId, int $limit = 10): Collection
    {
        return $this->getRecentNotesByEmployee($employeeId, $limit)
            ->map(fn($note) => EmployeeNoteDTO::fromModel($note));
    }

    public function searchNotes(string $query): Collection
    {
        return $this->model->search($query)
            ->with(['employee', 'user'])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function searchNotesDTO(string $query): Collection
    {
        return $this->searchNotes($query)
            ->map(fn($note) => EmployeeNoteDTO::fromModel($note));
    }

    public function searchNotesByEmployee(int $employeeId, string $query): Collection
    {
        return $this->model->byEmployee($employeeId)
            ->search($query)
            ->with(['employee', 'user'])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function searchNotesByEmployeeDTO(int $employeeId, string $query): Collection
    {
        return $this->searchNotesByEmployee($employeeId, $query)
            ->map(fn($note) => EmployeeNoteDTO::fromModel($note));
    }
}

