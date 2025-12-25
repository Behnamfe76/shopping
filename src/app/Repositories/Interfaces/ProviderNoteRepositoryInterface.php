<?php

namespace Fereydooni\Shopping\app\Repositories\Interfaces;

use Fereydooni\Shopping\app\DTOs\ProviderNoteDTO;
use Fereydooni\Shopping\app\Models\ProviderNote;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\CursorPaginator;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;

interface ProviderNoteRepositoryInterface
{
    /**
     * Get all provider notes
     */
    public function all(): Collection;

    /**
     * Get paginated provider notes
     */
    public function paginate(int $perPage = 15): LengthAwarePaginator;

    /**
     * Get simple paginated provider notes
     */
    public function simplePaginate(int $perPage = 15): Paginator;

    /**
     * Get cursor paginated provider notes
     */
    public function cursorPaginate(int $perPage = 15, ?string $cursor = null): CursorPaginator;

    /**
     * Find provider note by ID
     */
    public function find(int $id): ?ProviderNote;

    /**
     * Find provider note by ID and return DTO
     */
    public function findDTO(int $id): ?ProviderNoteDTO;

    /**
     * Find provider notes by provider ID
     */
    public function findByProviderId(int $providerId): Collection;

    /**
     * Find provider notes by provider ID and return DTOs
     */
    public function findByProviderIdDTO(int $providerId): Collection;

    /**
     * Find provider notes by user ID
     */
    public function findByUserId(int $userId): Collection;

    /**
     * Find provider notes by user ID and return DTOs
     */
    public function findByUserIdDTO(int $userId): Collection;

    /**
     * Find provider notes by note type
     */
    public function findByNoteType(string $noteType): Collection;

    /**
     * Find provider notes by note type and return DTOs
     */
    public function findByNoteTypeDTO(string $noteType): Collection;

    /**
     * Find provider notes by priority
     */
    public function findByPriority(string $priority): Collection;

    /**
     * Find provider notes by priority and return DTOs
     */
    public function findByPriorityDTO(string $priority): Collection;

    /**
     * Find private provider notes
     */
    public function findPrivate(): Collection;

    /**
     * Find private provider notes and return DTOs
     */
    public function findPrivateDTO(): Collection;

    /**
     * Find public provider notes
     */
    public function findPublic(): Collection;

    /**
     * Find public provider notes and return DTOs
     */
    public function findPublicDTO(): Collection;

    /**
     * Find archived provider notes
     */
    public function findArchived(): Collection;

    /**
     * Find archived provider notes and return DTOs
     */
    public function findArchivedDTO(): Collection;

    /**
     * Find active provider notes
     */
    public function findActive(): Collection;

    /**
     * Find active provider notes and return DTOs
     */
    public function findActiveDTO(): Collection;

    /**
     * Find provider notes by tags
     */
    public function findByTags(array $tags): Collection;

    /**
     * Find provider notes by tags and return DTOs
     */
    public function findByTagsDTO(array $tags): Collection;

    /**
     * Find provider notes by date range
     */
    public function findByDateRange(string $startDate, string $endDate): Collection;

    /**
     * Find provider notes by date range and return DTOs
     */
    public function findByDateRangeDTO(string $startDate, string $endDate): Collection;

    /**
     * Find provider notes by provider and type
     */
    public function findByProviderAndType(int $providerId, string $noteType): Collection;

    /**
     * Find provider notes by provider and type and return DTOs
     */
    public function findByProviderAndTypeDTO(int $providerId, string $noteType): Collection;

    /**
     * Find provider notes by provider and priority
     */
    public function findByProviderAndPriority(int $providerId, string $priority): Collection;

    /**
     * Find provider notes by provider and priority and return DTOs
     */
    public function findByProviderAndPriorityDTO(int $providerId, string $priority): Collection;

    /**
     * Create new provider note
     */
    public function create(array $data): ProviderNote;

    /**
     * Create new provider note and return DTO
     */
    public function createAndReturnDTO(array $data): ProviderNoteDTO;

    /**
     * Update provider note
     */
    public function update(ProviderNote $providerNote, array $data): bool;

    /**
     * Update provider note and return DTO
     */
    public function updateAndReturnDTO(ProviderNote $providerNote, array $data): ?ProviderNoteDTO;

    /**
     * Delete provider note
     */
    public function delete(ProviderNote $providerNote): bool;

    /**
     * Archive provider note
     */
    public function archive(ProviderNote $providerNote): bool;

    /**
     * Unarchive provider note
     */
    public function unarchive(ProviderNote $providerNote): bool;

    /**
     * Make provider note private
     */
    public function makePrivate(ProviderNote $providerNote): bool;

    /**
     * Make provider note public
     */
    public function makePublic(ProviderNote $providerNote): bool;

    /**
     * Add tags to provider note
     */
    public function addTags(ProviderNote $providerNote, array $tags): bool;

    /**
     * Remove tags from provider note
     */
    public function removeTags(ProviderNote $providerNote, array $tags): bool;

    /**
     * Clear all tags from provider note
     */
    public function clearTags(ProviderNote $providerNote): bool;

    /**
     * Add attachment to provider note
     */
    public function addAttachment(ProviderNote $providerNote, string $attachmentPath): bool;

    /**
     * Remove attachment from provider note
     */
    public function removeAttachment(ProviderNote $providerNote, string $attachmentPath): bool;

    /**
     * Get provider note count
     */
    public function getProviderNoteCount(int $providerId): int;

    /**
     * Get provider note count by type
     */
    public function getProviderNoteCountByType(int $providerId, string $noteType): int;

    /**
     * Get provider note count by priority
     */
    public function getProviderNoteCountByPriority(int $providerId, string $priority): int;

    /**
     * Get private note count
     */
    public function getPrivateNoteCount(int $providerId): int;

    /**
     * Get public note count
     */
    public function getPublicNoteCount(int $providerId): int;

    /**
     * Get archived note count
     */
    public function getArchivedNoteCount(int $providerId): int;

    /**
     * Get active note count
     */
    public function getActiveNoteCount(int $providerId): int;

    /**
     * Get total note count
     */
    public function getTotalNoteCount(): int;

    /**
     * Get total note count by type
     */
    public function getTotalNoteCountByType(string $noteType): int;

    /**
     * Get total note count by priority
     */
    public function getTotalNoteCountByPriority(string $priority): int;

    /**
     * Get recent notes
     */
    public function getRecentNotes(int $limit = 10): Collection;

    /**
     * Get recent notes and return DTOs
     */
    public function getRecentNotesDTO(int $limit = 10): Collection;

    /**
     * Get recent notes by provider
     */
    public function getRecentNotesByProvider(int $providerId, int $limit = 10): Collection;

    /**
     * Get recent notes by provider and return DTOs
     */
    public function getRecentNotesByProviderDTO(int $providerId, int $limit = 10): Collection;

    /**
     * Search notes
     */
    public function searchNotes(string $query): Collection;

    /**
     * Search notes and return DTOs
     */
    public function searchNotesDTO(string $query): Collection;

    /**
     * Search notes by provider
     */
    public function searchNotesByProvider(int $providerId, string $query): Collection;

    /**
     * Search notes by provider and return DTOs
     */
    public function searchNotesByProviderDTO(int $providerId, string $query): Collection;
}
