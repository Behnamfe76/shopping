<?php

namespace Fereydooni\Shopping\app\Facades;

use Illuminate\Support\Facades\Facade;
use Fereydooni\Shopping\app\Repositories\Interfaces\ProviderNoteRepositoryInterface;
use Fereydooni\Shopping\app\Actions\ProviderNote\CreateProviderNoteAction;
use Fereydooni\Shopping\app\Actions\ProviderNote\UpdateProviderNoteAction;
use Fereydooni\Shopping\app\Actions\ProviderNote\ArchiveProviderNoteAction;
use Fereydooni\Shopping\app\Actions\ProviderNote\AddProviderNoteTagsAction;
use Fereydooni\Shopping\app\Actions\ProviderNote\SearchProviderNotesAction;
use Fereydooni\Shopping\app\DTOs\ProviderNoteDTO;
use Fereydooni\Shopping\app\Models\ProviderNote;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

/**
 * @method static Collection all()
 * @method static LengthAwarePaginator paginate(int $perPage = 15)
 * @method static ProviderNote find(int $id)
 * @method static ProviderNoteDTO findDTO(int $id)
 * @method static Collection findByProviderId(int $providerId)
 * @method static Collection findByProviderIdDTO(int $providerId)
 * @method static Collection findByUserId(int $userId)
 * @method static Collection findByUserIdDTO(int $userId)
 * @method static Collection findByNoteType(string $noteType)
 * @method static Collection findByNoteTypeDTO(string $noteType)
 * @method static Collection findByPriority(string $priority)
 * @method static Collection findByPriorityDTO(string $priority)
 * @method static Collection findPrivate()
 * @method static Collection findPrivateDTO()
 * @method static Collection findPublic()
 * @method static Collection findPublicDTO()
 * @method static Collection findArchived()
 * @method static Collection findArchivedDTO()
 * @method static Collection findActive()
 * @method static Collection findActiveDTO()
 * @method static Collection findByTags(array $tags)
 * @method static Collection findByTagsDTO(array $tags)
 * @method static Collection findByDateRange(string $startDate, string $endDate)
 * @method static Collection findByDateRangeDTO(string $startDate, string $endDate)
 * @method static Collection findByProviderAndType(int $providerId, string $noteType)
 * @method static Collection findByProviderAndTypeDTO(int $providerId, string $noteType)
 * @method static Collection findByProviderAndPriority(int $providerId, string $priority)
 * @method static Collection findByProviderAndPriorityDTO(int $providerId, string $priority)
 * @method static ProviderNote create(array $data)
 * @method static ProviderNoteDTO createAndReturnDTO(array $data)
 * @method static bool update(ProviderNote $providerNote, array $data)
 * @method static ProviderNoteDTO updateAndReturnDTO(ProviderNote $providerNote, array $data)
 * @method static bool delete(ProviderNote $providerNote)
 * @method static bool archive(ProviderNote $providerNote)
 * @method static bool unarchive(ProviderNote $providerNote)
 * @method static bool makePrivate(ProviderNote $providerNote)
 * @method static bool makePublic(ProviderNote $providerNote)
 * @method static bool addTags(ProviderNote $providerNote, array $tags)
 * @method static bool removeTags(ProviderNote $providerNote, array $tags)
 * @method static bool clearTags(ProviderNote $providerNote)
 * @method static bool addAttachment(ProviderNote $providerNote, string $attachmentPath)
 * @method static bool removeAttachment(ProviderNote $providerNote, string $attachmentPath)
 * @method static int getProviderNoteCount(int $providerId)
 * @method static int getProviderNoteCountByType(int $providerId, string $noteType)
 * @method static int getProviderNoteCountByPriority(int $providerId, string $priority)
 * @method static int getPrivateNoteCount(int $providerId)
 * @method static int getPublicNoteCount(int $providerId)
 * @method static int getArchivedNoteCount(int $providerId)
 * @method static int getActiveNoteCount(int $providerId)
 * @method static int getTotalNoteCount()
 * @method static int getTotalNoteCountByType(string $noteType)
 * @method static int getTotalNoteCountByPriority(string $priority)
 * @method static Collection getRecentNotes(int $limit = 10)
 * @method static Collection getRecentNotesDTO(int $limit = 10)
 * @method static Collection getRecentNotesByProvider(int $providerId, int $limit = 10)
 * @method static Collection getRecentNotesByProviderDTO(int $providerId, int $limit = 10)
 * @method static Collection searchNotes(string $query)
 * @method static Collection searchNotesDTO(string $query)
 * @method static Collection searchNotesByProvider(int $providerId, string $query)
 * @method static Collection searchNotesByProviderDTO(int $providerId, string $query)
 */
class ProviderNote extends Facade
{
    protected static function getFacadeAccessor()
    {
        return ProviderNoteRepositoryInterface::class;
    }

    /**
     * Create a new provider note using the action class
     */
    public static function createNote(array $data): ProviderNoteDTO
    {
        $action = app(CreateProviderNoteAction::class);
        return $action->execute($data);
    }

    /**
     * Update a provider note using the action class
     */
    public static function updateNote(ProviderNote $providerNote, array $data): ProviderNoteDTO
    {
        $action = app(UpdateProviderNoteAction::class);
        return $action->execute($providerNote, $data);
    }

    /**
     * Archive a provider note using the action class
     */
    public static function archiveNote(ProviderNote $providerNote): ProviderNoteDTO
    {
        $action = app(ArchiveProviderNoteAction::class);
        return $action->execute($providerNote);
    }

    /**
     * Add tags to a provider note using the action class
     */
    public static function addTagsToNote(ProviderNote $providerNote, array $tags): ProviderNoteDTO
    {
        $action = app(AddProviderNoteTagsAction::class);
        return $action->execute($providerNote, $tags);
    }

    /**
     * Search provider notes using the action class
     */
    public static function searchNotes(string $query, ?int $providerId = null, ?string $noteType = null, ?string $priority = null, ?string $sortBy = 'created_at', ?string $sortOrder = 'desc'): Collection
    {
        $action = app(SearchProviderNotesAction::class);
        return $action->execute($query, $providerId, $noteType, $priority, $sortBy, $sortOrder);
    }

    /**
     * Get provider note statistics
     */
    public static function getStatistics(int $providerId): array
    {
        $repository = app(ProviderNoteRepositoryInterface::class);

        return [
            'total' => $repository->getProviderNoteCount($providerId),
            'by_type' => [
                'general' => $repository->getProviderNoteCountByType($providerId, 'general'),
                'contract' => $repository->getProviderNoteCountByType($providerId, 'contract'),
                'payment' => $repository->getProviderNoteCountByType($providerId, 'payment'),
                'quality' => $repository->getProviderNoteCountByType($providerId, 'quality'),
                'performance' => $repository->getProviderNoteCountByType($providerId, 'performance'),
                'communication' => $repository->getProviderNoteCountByType($providerId, 'communication'),
                'other' => $repository->getProviderNoteCountByType($providerId, 'other'),
            ],
            'by_priority' => [
                'low' => $repository->getProviderNoteCountByPriority($providerId, 'low'),
                'medium' => $repository->getProviderNoteCountByPriority($providerId, 'medium'),
                'high' => $repository->getProviderNoteCountByPriority($providerId, 'high'),
                'urgent' => $repository->getProviderNoteCountByPriority($providerId, 'urgent'),
            ],
            'private' => $repository->getPrivateNoteCount($providerId),
            'public' => $repository->getPublicNoteCount($providerId),
            'archived' => $repository->getArchivedNoteCount($providerId),
            'active' => $repository->getActiveNoteCount($providerId),
        ];
    }

    /**
     * Get global provider note statistics
     */
    public static function getGlobalStatistics(): array
    {
        $repository = app(ProviderNoteRepositoryInterface::class);

        return [
            'total' => $repository->getTotalNoteCount(),
            'by_type' => [
                'general' => $repository->getTotalNoteCountByType('general'),
                'contract' => $repository->getTotalNoteCountByType('contract'),
                'payment' => $repository->getTotalNoteCountByType('payment'),
                'quality' => $repository->getTotalNoteCountByType('quality'),
                'performance' => $repository->getTotalNoteCountByType('performance'),
                'communication' => $repository->getTotalNoteCountByType('communication'),
                'other' => $repository->getTotalNoteCountByType('other'),
            ],
            'by_priority' => [
                'low' => $repository->getTotalNoteCountByPriority('low'),
                'medium' => $repository->getTotalNoteCountByPriority('medium'),
                'high' => $repository->getTotalNoteCountByPriority('high'),
                'urgent' => $repository->getTotalNoteCountByPriority('urgent'),
            ],
        ];
    }
}
