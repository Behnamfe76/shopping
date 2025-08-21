<?php

namespace Fereydooni\Shopping\app\Services;

use Illuminate\Support\Collection;
use Fereydooni\Shopping\app\Repositories\Interfaces\CustomerNoteRepositoryInterface;
use Fereydooni\Shopping\app\Models\CustomerNote;
use Fereydooni\Shopping\app\DTOs\CustomerNoteDTO;
use Fereydooni\Shopping\app\Traits\HasCrudOperations;
use Fereydooni\Shopping\app\Traits\HasSearchOperations;
use Fereydooni\Shopping\app\Traits\HasCustomerNoteOperations;
use Fereydooni\Shopping\app\Traits\HasCustomerNoteStatusManagement;
use Fereydooni\Shopping\app\Traits\HasNotesManagement;

class CustomerNoteService
{
    use HasCrudOperations,
        HasSearchOperations,
        HasCustomerNoteOperations,
        HasCustomerNoteStatusManagement,
        HasNotesManagement;

    public function __construct(
        protected CustomerNoteRepositoryInterface $repository
    ) {}

    /**
     * Get all customer notes with pagination
     */
    public function getAllNotes(int $perPage = 15): Collection
    {
        return $this->repository->all();
    }

    /**
     * Get customer notes with pagination
     */
    public function getNotesPaginated(int $perPage = 15): Collection
    {
        return $this->repository->paginate($perPage);
    }

    /**
     * Get customer note by ID
     */
    public function getNote(int $id): ?CustomerNote
    {
        return $this->repository->find($id);
    }

    /**
     * Get customer note DTO by ID
     */
    public function getNoteDTO(int $id): ?CustomerNoteDTO
    {
        return $this->repository->findDTO($id);
    }

    /**
     * Create a new customer note
     */
    public function createNote(array $data): CustomerNote
    {
        return $this->createCustomerNote($data);
    }

    /**
     * Create a new customer note and return DTO
     */
    public function createNoteDTO(array $data): CustomerNoteDTO
    {
        return $this->createCustomerNoteDTO($data);
    }

    /**
     * Update customer note
     */
    public function updateNote(CustomerNote $note, array $data): bool
    {
        return $this->updateCustomerNote($note, $data);
    }

    /**
     * Update customer note and return DTO
     */
    public function updateNoteDTO(CustomerNote $note, array $data): ?CustomerNoteDTO
    {
        return $this->updateCustomerNoteDTO($note, $data);
    }

    /**
     * Delete customer note
     */
    public function deleteNote(CustomerNote $note): bool
    {
        return $this->deleteCustomerNote($note);
    }

    /**
     * Search customer notes
     */
    public function searchNotes(string $query): Collection
    {
        return $this->repository->search($query);
    }

    /**
     * Search customer notes and return DTOs
     */
    public function searchNotesDTO(string $query): Collection
    {
        return $this->repository->searchDTO($query);
    }

    /**
     * Get recent notes
     */
    public function getRecentNotes(int $limit = 10): Collection
    {
        return $this->repository->getRecentNotes($limit);
    }

    /**
     * Get recent notes DTOs
     */
    public function getRecentNotesDTO(int $limit = 10): Collection
    {
        return $this->repository->getRecentNotesDTO($limit);
    }

    /**
     * Get note statistics
     */
    public function getNoteStats(): array
    {
        return $this->repository->getNoteStats();
    }

    /**
     * Get note statistics by customer
     */
    public function getNoteStatsByCustomer(int $customerId): array
    {
        return $this->repository->getNoteStatsByCustomer($customerId);
    }

    /**
     * Get note statistics by type
     */
    public function getNoteStatsByType(): array
    {
        return $this->repository->getNoteStatsByType();
    }

    /**
     * Get note statistics by priority
     */
    public function getNoteStatsByPriority(): array
    {
        return $this->repository->getNoteStatsByPriority();
    }

    /**
     * Get note statistics by date range
     */
    public function getNoteStatsByDateRange(string $startDate, string $endDate): array
    {
        return $this->repository->getNoteStatsByDateRange($startDate, $endDate);
    }

    /**
     * Get popular tags
     */
    public function getPopularTags(): array
    {
        return $this->repository->getPopularTags();
    }

    /**
     * Get popular tags by customer
     */
    public function getPopularTagsByCustomer(int $customerId): array
    {
        return $this->repository->getPopularTagsByCustomer($customerId);
    }

    /**
     * Get notes by type
     */
    public function getNotesByType(string $type): Collection
    {
        return $this->repository->findByType($type);
    }

    /**
     * Get notes by priority
     */
    public function getNotesByPriority(string $priority): Collection
    {
        return $this->repository->findByPriority($priority);
    }

    /**
     * Get public notes
     */
    public function getPublicNotes(): Collection
    {
        return $this->repository->findPublic();
    }

    /**
     * Get private notes
     */
    public function getPrivateNotes(): Collection
    {
        return $this->repository->findPrivate();
    }

    /**
     * Get pinned notes
     */
    public function getPinnedNotes(): Collection
    {
        return $this->repository->findPinned();
    }

    /**
     * Get notes by date range
     */
    public function getNotesByDateRange(string $startDate, string $endDate): Collection
    {
        return $this->repository->findByDateRange($startDate, $endDate);
    }

    /**
     * Get notes by tag
     */
    public function getNotesByTag(string $tag): Collection
    {
        return $this->repository->findByTag($tag);
    }

    /**
     * Get notes by user
     */
    public function getNotesByUser(int $userId): Collection
    {
        return $this->repository->findByUserId($userId);
    }

    /**
     * Get note count
     */
    public function getNoteCount(): int
    {
        return $this->repository->getNoteCount();
    }

    /**
     * Get note count by type
     */
    public function getNoteCountByType(string $type): int
    {
        return $this->repository->getNoteCountByType($type);
    }

    /**
     * Get note count by priority
     */
    public function getNoteCountByPriority(string $priority): int
    {
        return $this->repository->getNoteCountByPriority($priority);
    }

    /**
     * Get pinned note count
     */
    public function getPinnedNoteCount(): int
    {
        return $this->repository->getPinnedNoteCount();
    }

    /**
     * Get private note count
     */
    public function getPrivateNoteCount(): int
    {
        return $this->repository->getPrivateNoteCount();
    }

    /**
     * Validate note data
     */
    public function validateNote(array $data): bool
    {
        return $this->repository->validateNote($data);
    }

    /**
     * Export customer notes
     */
    public function exportCustomerNotes(int $customerId, string $format = 'json'): string
    {
        return $this->exportCustomerNotes($customerId, $format);
    }

    /**
     * Import customer notes
     */
    public function importCustomerNotes(int $customerId, array $notesData): array
    {
        return $this->importCustomerNotes($customerId, $notesData);
    }

    /**
     * Create note from template
     */
    public function createNoteFromTemplate(int $customerId, string $templateKey, array $customData = []): CustomerNote
    {
        return $this->createCustomerNoteFromTemplate($customerId, $templateKey, $customData);
    }

    /**
     * Get note templates
     */
    public function getNoteTemplates(): array
    {
        return $this->getCustomerNoteTemplates();
    }

    /**
     * Get note types
     */
    public function getNoteTypes(): array
    {
        return $this->getCustomerNoteTypes();
    }

    /**
     * Get note priorities
     */
    public function getNotePriorities(): array
    {
        return $this->getCustomerNotePriorities();
    }

    /**
     * Check if user can access note
     */
    public function canAccessNote(CustomerNote $note, int $userId): bool
    {
        return $this->canAccessCustomerNote($note, $userId);
    }

    /**
     * Check if user can edit note
     */
    public function canEditNote(CustomerNote $note, int $userId): bool
    {
        return $this->canEditCustomerNote($note, $userId);
    }

    /**
     * Check if user can delete note
     */
    public function canDeleteNote(CustomerNote $note, int $userId): bool
    {
        return $this->canDeleteCustomerNote($note, $userId);
    }
}
