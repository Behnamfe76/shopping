<?php

namespace Fereydooni\Shopping\app\Facades;

use Fereydooni\Shopping\app\Services\CustomerNoteService;
use Illuminate\Support\Facades\Facade;

/**
 * @method static \Illuminate\Support\Collection getAllNotes(int $perPage = 15)
 * @method static \Illuminate\Support\Collection getNotesPaginated(int $perPage = 15)
 * @method static \Fereydooni\Shopping\app\Models\CustomerNote|null getNote(int $id)
 * @method static \Fereydooni\Shopping\app\DTOs\CustomerNoteDTO|null getNoteDTO(int $id)
 * @method static \Fereydooni\Shopping\app\Models\CustomerNote createNote(array $data)
 * @method static \Fereydooni\Shopping\app\DTOs\CustomerNoteDTO createNoteDTO(array $data)
 * @method static bool updateNote(\Fereydooni\Shopping\app\Models\CustomerNote $note, array $data)
 * @method static \Fereydooni\Shopping\app\DTOs\CustomerNoteDTO|null updateNoteDTO(\Fereydooni\Shopping\app\Models\CustomerNote $note, array $data)
 * @method static bool deleteNote(\Fereydooni\Shopping\app\Models\CustomerNote $note)
 * @method static \Illuminate\Support\Collection searchNotes(string $query)
 * @method static \Illuminate\Support\Collection searchNotesDTO(string $query)
 * @method static \Illuminate\Support\Collection getRecentNotes(int $limit = 10)
 * @method static \Illuminate\Support\Collection getRecentNotesDTO(int $limit = 10)
 * @method static array getNoteStats()
 * @method static array getNoteStatsByCustomer(int $customerId)
 * @method static array getNoteStatsByType()
 * @method static array getNoteStatsByPriority()
 * @method static array getNoteStatsByDateRange(string $startDate, string $endDate)
 * @method static array getPopularTags()
 * @method static array getPopularTagsByCustomer(int $customerId)
 * @method static \Illuminate\Support\Collection getNotesByType(string $type)
 * @method static \Illuminate\Support\Collection getNotesByPriority(string $priority)
 * @method static \Illuminate\Support\Collection getPublicNotes()
 * @method static \Illuminate\Support\Collection getPrivateNotes()
 * @method static \Illuminate\Support\Collection getPinnedNotes()
 * @method static \Illuminate\Support\Collection getNotesByDateRange(string $startDate, string $endDate)
 * @method static \Illuminate\Support\Collection getNotesByTag(string $tag)
 * @method static \Illuminate\Support\Collection getNotesByUser(int $userId)
 * @method static int getNoteCount()
 * @method static int getNoteCountByType(string $type)
 * @method static int getNoteCountByPriority(string $priority)
 * @method static int getPinnedNoteCount()
 * @method static int getPrivateNoteCount()
 * @method static bool validateNote(array $data)
 * @method static string exportCustomerNotes(int $customerId, string $format = 'json')
 * @method static array importCustomerNotes(int $customerId, array $notesData)
 * @method static \Fereydooni\Shopping\app\Models\CustomerNote createNoteFromTemplate(int $customerId, string $templateKey, array $customData = [])
 * @method static array getNoteTemplates()
 * @method static array getNoteTypes()
 * @method static array getNotePriorities()
 * @method static bool canAccessNote(\Fereydooni\Shopping\app\Models\CustomerNote $note, int $userId)
 * @method static bool canEditNote(\Fereydooni\Shopping\app\Models\CustomerNote $note, int $userId)
 * @method static bool canDeleteNote(\Fereydooni\Shopping\app\Models\CustomerNote $note, int $userId)
 * @method static \Illuminate\Support\Collection getCustomerNotes(int $customerId)
 * @method static \Illuminate\Support\Collection getCustomerNotesDTO(int $customerId)
 * @method static \Illuminate\Support\Collection getCustomerNotesByType(int $customerId, string $type)
 * @method static \Illuminate\Support\Collection getCustomerNotesByPriority(int $customerId, string $priority)
 * @method static \Illuminate\Support\Collection getPinnedCustomerNotes(int $customerId)
 * @method static \Illuminate\Support\Collection getRecentCustomerNotes(int $customerId, int $limit = 10)
 * @method static \Illuminate\Support\Collection searchCustomerNotes(int $customerId, string $query)
 * @method static array getCustomerNoteStats(int $customerId)
 * @method static int getCustomerNoteCount(int $customerId)
 * @method static bool addCustomerNoteTag(\Fereydooni\Shopping\app\Models\CustomerNote $note, string $tag)
 * @method static bool removeCustomerNoteTag(\Fereydooni\Shopping\app\Models\CustomerNote $note, string $tag)
 * @method static bool addCustomerNoteAttachment(\Fereydooni\Shopping\app\Models\CustomerNote $note, $file)
 * @method static bool removeCustomerNoteAttachment(\Fereydooni\Shopping\app\Models\CustomerNote $note, int $mediaId)
 * @method static \Illuminate\Support\Collection getCustomerNoteAttachments(\Fereydooni\Shopping\app\Models\CustomerNote $note)
 * @method static array getCustomerNotePopularTags(int $customerId)
 * @method static \Illuminate\Support\Collection getCustomerNotesByDateRange(int $customerId, string $startDate, string $endDate)
 * @method static bool pinCustomerNote(\Fereydooni\Shopping\app\Models\CustomerNote $note)
 * @method static bool unpinCustomerNote(\Fereydooni\Shopping\app\Models\CustomerNote $note)
 * @method static bool makeCustomerNotePrivate(\Fereydooni\Shopping\app\Models\CustomerNote $note)
 * @method static bool makeCustomerNotePublic(\Fereydooni\Shopping\app\Models\CustomerNote $note)
 * @method static \Fereydooni\Shopping\app\Models\CustomerNote createCustomerNote(array $data)
 * @method static \Fereydooni\Shopping\app\DTOs\CustomerNoteDTO createCustomerNoteDTO(array $data)
 * @method static bool updateCustomerNote(\Fereydooni\Shopping\app\Models\CustomerNote $note, array $data)
 * @method static \Fereydooni\Shopping\app\DTOs\CustomerNoteDTO|null updateCustomerNoteDTO(\Fereydooni\Shopping\app\Models\CustomerNote $note, array $data)
 * @method static bool deleteCustomerNote(\Fereydooni\Shopping\app\Models\CustomerNote $note)
 * @method static bool canAccessCustomerNote(\Fereydooni\Shopping\app\Models\CustomerNote $note, int $userId)
 * @method static bool canEditCustomerNote(\Fereydooni\Shopping\app\Models\CustomerNote $note, int $userId)
 * @method static bool canDeleteCustomerNote(\Fereydooni\Shopping\app\Models\CustomerNote $note, int $userId)
 * @method static array getCustomerNoteTypes()
 * @method static array getCustomerNotePriorities()
 * @method static string exportCustomerNotes(int $customerId, string $format = 'json')
 * @method static array importCustomerNotes(int $customerId, array $notesData)
 * @method static array getCustomerNoteTemplates()
 * @method static \Fereydooni\Shopping\app\Models\CustomerNote createCustomerNoteFromTemplate(int $customerId, string $templateKey, array $customData = [])
 */
class CustomerNote extends Facade
{
    /**
     * Get the registered name of the component.
     */
    protected static function getFacadeAccessor(): string
    {
        return CustomerNoteService::class;
    }
}
