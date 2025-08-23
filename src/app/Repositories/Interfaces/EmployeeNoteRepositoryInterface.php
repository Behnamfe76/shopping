<?php

namespace Fereydooni\Shopping\app\Repositories\Interfaces;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Pagination\CursorPaginator;
use Fereydooni\Shopping\app\Models\EmployeeNote;
use Fereydooni\Shopping\app\DTOs\EmployeeNoteDTO;

interface EmployeeNoteRepositoryInterface
{
    // Basic CRUD operations
    public function all(): Collection;
    public function paginate(int $perPage = 15): LengthAwarePaginator;
    public function simplePaginate(int $perPage = 15): Paginator;
    public function cursorPaginate(int $perPage = 15, string $cursor = null): CursorPaginator;
    public function find(int $id): ?EmployeeNote;
    public function findDTO(int $id): ?EmployeeNoteDTO;
    public function create(array $data): EmployeeNote;
    public function createAndReturnDTO(array $data): EmployeeNoteDTO;
    public function update(EmployeeNote $employeeNote, array $data): bool;
    public function updateAndReturnDTO(EmployeeNote $employeeNote, array $data): ?EmployeeNoteDTO;
    public function delete(EmployeeNote $employeeNote): bool;

    // Find by specific criteria
    public function findByEmployeeId(int $employeeId): Collection;
    public function findByEmployeeIdDTO(int $employeeId): Collection;
    public function findByUserId(int $userId): Collection;
    public function findByUserIdDTO(int $userId): Collection;
    public function findByNoteType(string $noteType): Collection;
    public function findByNoteTypeDTO(string $noteType): Collection;
    public function findByPriority(string $priority): Collection;
    public function findByPriorityDTO(string $priority): Collection;

    // Status-based queries
    public function findPrivate(): Collection;
    public function findPrivateDTO(): Collection;
    public function findPublic(): Collection;
    public function findPublicDTO(): Collection;
    public function findArchived(): Collection;
    public function findArchivedDTO(): Collection;
    public function findActive(): Collection;
    public function findActiveDTO(): Collection;

    // Advanced queries
    public function findByTags(array $tags): Collection;
    public function findByTagsDTO(array $tags): Collection;
    public function findByDateRange(string $startDate, string $endDate): Collection;
    public function findByDateRangeDTO(string $startDate, string $endDate): Collection;
    public function findByEmployeeAndType(int $employeeId, string $noteType): Collection;
    public function findByEmployeeAndTypeDTO(int $employeeId, string $noteType): Collection;
    public function findByEmployeeAndPriority(int $employeeId, string $priority): Collection;
    public function findByEmployeeAndPriorityDTO(int $employeeId, string $priority): Collection;

    // State management
    public function archive(EmployeeNote $employeeNote): bool;
    public function unarchive(EmployeeNote $employeeNote): bool;
    public function makePrivate(EmployeeNote $employeeNote): bool;
    public function makePublic(EmployeeNote $employeeNote): bool;

    // Tag management
    public function addTags(EmployeeNote $employeeNote, array $tags): bool;
    public function removeTags(EmployeeNote $employeeNote, array $tags): bool;
    public function clearTags(EmployeeNote $employeeNote): bool;

    // Attachment management
    public function addAttachment(EmployeeNote $employeeNote, string $attachmentPath): bool;
    public function removeAttachment(EmployeeNote $employeeNote, string $attachmentPath): bool;

    // Counting and statistics
    public function getEmployeeNoteCount(int $employeeId): int;
    public function getEmployeeNoteCountByType(int $employeeId, string $noteType): int;
    public function getEmployeeNoteCountByPriority(int $employeeId, string $priority): int;
    public function getPrivateNoteCount(int $employeeId): int;
    public function getPublicNoteCount(int $employeeId): int;
    public function getArchivedNoteCount(int $employeeId): int;
    public function getActiveNoteCount(int $employeeId): int;
    public function getTotalNoteCount(): int;
    public function getTotalNoteCountByType(string $noteType): int;
    public function getTotalNoteCountByPriority(string $priority): int;

    // Recent notes
    public function getRecentNotes(int $limit = 10): Collection;
    public function getRecentNotesDTO(int $limit = 10): Collection;
    public function getRecentNotesByEmployee(int $employeeId, int $limit = 10): Collection;
    public function getRecentNotesByEmployeeDTO(int $employeeId, int $limit = 10): Collection;

    // Search functionality
    public function searchNotes(string $query): Collection;
    public function searchNotesDTO(string $query): Collection;
    public function searchNotesByEmployee(int $employeeId, string $query): Collection;
    public function searchNotesByEmployeeDTO(int $employeeId, string $query): Collection;
}
