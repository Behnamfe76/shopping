<?php

namespace Fereydooni\Shopping\app\Repositories\Interfaces;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Pagination\CursorPaginator;
use Fereydooni\Shopping\app\Models\CustomerNote;
use Fereydooni\Shopping\app\DTOs\CustomerNoteDTO;

interface CustomerNoteRepositoryInterface
{
    // Basic CRUD operations
    public function all(): Collection;
    public function paginate(int $perPage = 15): LengthAwarePaginator;
    public function simplePaginate(int $perPage = 15): Paginator;
    public function cursorPaginate(int $perPage = 15, string $cursor = null): CursorPaginator;
    public function find(int $id): ?CustomerNote;
    public function findDTO(int $id): ?CustomerNoteDTO;
    public function create(array $data): CustomerNote;
    public function createAndReturnDTO(array $data): CustomerNoteDTO;
    public function update(CustomerNote $note, array $data): bool;
    public function updateAndReturnDTO(CustomerNote $note, array $data): ?CustomerNoteDTO;
    public function delete(CustomerNote $note): bool;

    // Customer-specific queries
    public function findByCustomerId(int $customerId): Collection;
    public function findByCustomerIdDTO(int $customerId): Collection;
    public function getNoteCountByCustomer(int $customerId): int;
    public function getRecentNotesByCustomer(int $customerId, int $limit = 10): Collection;
    public function getRecentNotesByCustomerDTO(int $customerId, int $limit = 10): Collection;
    public function getPinnedNotesByCustomer(int $customerId): Collection;
    public function getPinnedNotesByCustomerDTO(int $customerId): Collection;
    public function getNotesByCustomerAndDateRange(int $customerId, string $startDate, string $endDate): Collection;
    public function getNotesByCustomerAndDateRangeDTO(int $customerId, string $startDate, string $endDate): Collection;

    // User-specific queries
    public function findByUserId(int $userId): Collection;
    public function findByUserIdDTO(int $userId): Collection;

    // Type and priority queries
    public function findByType(string $type): Collection;
    public function findByTypeDTO(string $type): Collection;
    public function findByPriority(string $priority): Collection;
    public function findByPriorityDTO(string $priority): Collection;
    public function findByCustomerAndType(int $customerId, string $type): Collection;
    public function findByCustomerAndTypeDTO(int $customerId, string $type): Collection;
    public function findByCustomerAndPriority(int $customerId, string $priority): Collection;
    public function findByCustomerAndPriorityDTO(int $customerId, string $priority): Collection;
    public function getNoteCountByType(string $type): int;
    public function getNoteCountByPriority(string $priority): int;

    // Privacy and pinning queries
    public function findPublic(): Collection;
    public function findPublicDTO(): Collection;
    public function findPrivate(): Collection;
    public function findPrivateDTO(): Collection;
    public function findPinned(): Collection;
    public function findPinnedDTO(): Collection;
    public function getPinnedNoteCount(): int;
    public function getPrivateNoteCount(): int;

    // Date range queries
    public function findByDateRange(string $startDate, string $endDate): Collection;
    public function findByDateRangeDTO(string $startDate, string $endDate): Collection;

    // Tag queries
    public function findByTag(string $tag): Collection;
    public function findByTagDTO(string $tag): Collection;
    public function getPopularTags(): array;
    public function getPopularTagsByCustomer(int $customerId): array;

    // Status management
    public function pin(CustomerNote $note): bool;
    public function unpin(CustomerNote $note): bool;
    public function makePrivate(CustomerNote $note): bool;
    public function makePublic(CustomerNote $note): bool;
    public function addTag(CustomerNote $note, string $tag): bool;
    public function removeTag(CustomerNote $note, string $tag): bool;

    // Attachment management
    public function addAttachment(CustomerNote $note, $file): bool;
    public function removeAttachment(CustomerNote $note, int $mediaId): bool;
    public function getAttachments(CustomerNote $note): Collection;

    // Search functionality
    public function search(string $query): Collection;
    public function searchDTO(string $query): Collection;
    public function searchByCustomer(int $customerId, string $query): Collection;
    public function searchByCustomerDTO(int $customerId, string $query): Collection;

    // Recent notes
    public function getRecentNotes(int $limit = 10): Collection;
    public function getRecentNotesDTO(int $limit = 10): Collection;

    // Statistics and analytics
    public function getNoteCount(): int;
    public function getNoteStats(): array;
    public function getNoteStatsByCustomer(int $customerId): array;
    public function getNoteStatsByType(): array;
    public function getNoteStatsByPriority(): array;
    public function getNoteStatsByDateRange(string $startDate, string $endDate): array;

    // Validation
    public function validateNote(array $data): bool;
}
