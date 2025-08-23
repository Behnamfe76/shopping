<?php

namespace Fereydooni\Shopping\app\Services;

use Fereydooni\Shopping\app\Repositories\Interfaces\EmployeeNoteRepositoryInterface;
use Fereydooni\Shopping\app\DTOs\EmployeeNoteDTO;
use Fereydooni\Shopping\app\Models\EmployeeNote;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Pagination\LengthAwarePaginator;

class EmployeeNoteService
{
    public function __construct(
        protected EmployeeNoteRepositoryInterface $employeeNoteRepository
    ) {}

    public function getAllNotes(int $perPage = 15): LengthAwarePaginator
    {
        return $this->employeeNoteRepository->paginate($perPage);
    }

    public function findNote(int $id): ?EmployeeNoteDTO
    {
        return $this->employeeNoteRepository->findDTO($id);
    }

    public function createNote(array $data): EmployeeNoteDTO
    {
        return $this->employeeNoteRepository->createAndReturnDTO($data);
    }

    public function updateNote(int $id, array $data): ?EmployeeNoteDTO
    {
        $note = $this->employeeNoteRepository->find($id);
        
        if (!$note) {
            return null;
        }
        
        return $this->employeeNoteRepository->updateAndReturnDTO($note, $data);
    }

    public function deleteNote(int $id): bool
    {
        $note = $this->employeeNoteRepository->find($id);
        
        if (!$note) {
            return false;
        }
        
        return $this->employeeNoteRepository->delete($note);
    }

    public function getEmployeeNotes(int $employeeId, array $filters = []): EloquentCollection
    {
        return $this->employeeNoteRepository->findByEmployeeIdDTO($employeeId);
    }

    public function archiveNote(int $id): bool
    {
        $note = $this->employeeNoteRepository->find($id);
        
        if (!$note) {
            return false;
        }
        
        return $this->employeeNoteRepository->archive($note);
    }

    public function unarchiveNote(int $id): bool
    {
        $note = $this->employeeNoteRepository->find($id);
        
        if (!$note) {
            return false;
        }
        
        return $this->employeeNoteRepository->unarchive($note);
    }

    public function searchNotes(string $query): Collection
    {
        return $this->employeeNoteRepository->searchNotesDTO($query);
    }

    public function searchEmployeeNotes(int $employeeId, string $query): Collection
    {
        return $this->employeeNoteRepository->searchNotesByEmployeeDTO($employeeId, $query);
    }

    public function getNoteStatistics(?int $employeeId = null): array
    {
        if ($employeeId) {
            return [
                'total' => $this->employeeNoteRepository->getEmployeeNoteCount($employeeId),
                'active' => $this->employeeNoteRepository->getActiveNoteCount($employeeId),
                'archived' => $this->employeeNoteRepository->getArchivedNoteCount($employeeId),
                'private' => $this->employeeNoteRepository->getPrivateNoteCount($employeeId),
                'public' => $this->employeeNoteRepository->getPublicNoteCount($employeeId),
            ];
        }
        
        return [
            'total' => $this->employeeNoteRepository->getTotalNoteCount(),
            'by_type' => [
                'performance' => $this->employeeNoteRepository->getTotalNoteCountByType('performance'),
                'general' => $this->employeeNoteRepository->getTotalNoteCountByType('general'),
                'warning' => $this->employeeNoteRepository->getTotalNoteCountByType('warning'),
                'praise' => $this->employeeNoteRepository->getTotalNoteCountByType('praise'),
                'incident' => $this->employeeNoteRepository->getTotalNoteCountByType('incident'),
                'training' => $this->employeeNoteRepository->getTotalNoteCountByType('training'),
                'goal' => $this->employeeNoteRepository->getTotalNoteCountByType('goal'),
                'feedback' => $this->employeeNoteRepository->getTotalNoteCountByType('feedback'),
                'other' => $this->employeeNoteRepository->getTotalNoteCountByType('other'),
            ],
            'by_priority' => [
                'low' => $this->employeeNoteRepository->getTotalNoteCountByPriority('low'),
                'medium' => $this->employeeNoteRepository->getTotalNoteCountByPriority('medium'),
                'high' => $this->employeeNoteRepository->getTotalNoteCountByPriority('high'),
                'urgent' => $this->employeeNoteRepository->getTotalNoteCountByPriority('urgent'),
            ],
        ];
    }

    public function getRecentNotes(int $employeeId, int $limit = 10): Collection
    {
        return $this->employeeNoteRepository->getRecentNotesByEmployeeDTO($employeeId, $limit);
    }
}
