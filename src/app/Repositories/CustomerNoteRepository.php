<?php

namespace Fereydooni\Shopping\app\Repositories;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Pagination\CursorPaginator;
use Illuminate\Support\Facades\Validator;
use Fereydooni\Shopping\app\Repositories\Interfaces\CustomerNoteRepositoryInterface;
use Fereydooni\Shopping\app\Models\CustomerNote;
use Fereydooni\Shopping\app\DTOs\CustomerNoteDTO;
use Fereydooni\Shopping\app\Enums\CustomerNoteType;
use Fereydooni\Shopping\app\Enums\CustomerNotePriority;

class CustomerNoteRepository implements CustomerNoteRepositoryInterface
{
    public function __construct(
        protected CustomerNote $model
    ) {}

    // Basic CRUD operations
    public function all(): Collection
    {
        return $this->model->with(['customer', 'user'])->get();
    }

    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        return $this->model->with(['customer', 'user'])->paginate($perPage);
    }

    public function simplePaginate(int $perPage = 15): Paginator
    {
        return $this->model->with(['customer', 'user'])->simplePaginate($perPage);
    }

    public function cursorPaginate(int $perPage = 15, string $cursor = null): CursorPaginator
    {
        return $this->model->with(['customer', 'user'])->cursorPaginate($perPage, ['*'], 'cursor', $cursor);
    }

    public function find(int $id): ?CustomerNote
    {
        return $this->model->with(['customer', 'user'])->find($id);
    }

    public function findDTO(int $id): ?CustomerNoteDTO
    {
        $note = $this->find($id);
        return $note ? CustomerNoteDTO::fromModel($note) : null;
    }

    public function create(array $data): CustomerNote
    {
        return $this->model->create($data);
    }

    public function createAndReturnDTO(array $data): CustomerNoteDTO
    {
        $note = $this->create($data);
        return CustomerNoteDTO::fromModel($note->load(['customer', 'user']));
    }

    public function update(CustomerNote $note, array $data): bool
    {
        return $note->update($data);
    }

    public function updateAndReturnDTO(CustomerNote $note, array $data): ?CustomerNoteDTO
    {
        $updated = $this->update($note, $data);
        return $updated ? CustomerNoteDTO::fromModel($note->fresh()->load(['customer', 'user'])) : null;
    }

    public function delete(CustomerNote $note): bool
    {
        return $note->delete();
    }

    // Customer-specific queries
    public function findByCustomerId(int $customerId): Collection
    {
        return $this->model->byCustomer($customerId)->with(['customer', 'user'])->get();
    }

    public function findByCustomerIdDTO(int $customerId): Collection
    {
        return $this->findByCustomerId($customerId)->map(fn($note) => CustomerNoteDTO::fromModel($note));
    }

    public function getNoteCountByCustomer(int $customerId): int
    {
        return $this->model->byCustomer($customerId)->count();
    }

    public function getRecentNotesByCustomer(int $customerId, int $limit = 10): Collection
    {
        return $this->model->byCustomer($customerId)
            ->with(['customer', 'user'])
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    public function getRecentNotesByCustomerDTO(int $customerId, int $limit = 10): Collection
    {
        return $this->getRecentNotesByCustomer($customerId, $limit)
            ->map(fn($note) => CustomerNoteDTO::fromModel($note));
    }

    public function getPinnedNotesByCustomer(int $customerId): Collection
    {
        return $this->model->byCustomer($customerId)
            ->pinned()
            ->with(['customer', 'user'])
            ->get();
    }

    public function getPinnedNotesByCustomerDTO(int $customerId): Collection
    {
        return $this->getPinnedNotesByCustomer($customerId)
            ->map(fn($note) => CustomerNoteDTO::fromModel($note));
    }

    public function getNotesByCustomerAndDateRange(int $customerId, string $startDate, string $endDate): Collection
    {
        return $this->model->byCustomer($customerId)
            ->byDateRange($startDate, $endDate)
            ->with(['customer', 'user'])
            ->get();
    }

    public function getNotesByCustomerAndDateRangeDTO(int $customerId, string $startDate, string $endDate): Collection
    {
        return $this->getNotesByCustomerAndDateRange($customerId, $startDate, $endDate)
            ->map(fn($note) => CustomerNoteDTO::fromModel($note));
    }

    // User-specific queries
    public function findByUserId(int $userId): Collection
    {
        return $this->model->byUser($userId)->with(['customer', 'user'])->get();
    }

    public function findByUserIdDTO(int $userId): Collection
    {
        return $this->findByUserId($userId)->map(fn($note) => CustomerNoteDTO::fromModel($note));
    }

    // Type and priority queries
    public function findByType(string $type): Collection
    {
        $noteType = CustomerNoteType::from($type);
        return $this->model->byType($noteType)->with(['customer', 'user'])->get();
    }

    public function findByTypeDTO(string $type): Collection
    {
        return $this->findByType($type)->map(fn($note) => CustomerNoteDTO::fromModel($note));
    }

    public function findByPriority(string $priority): Collection
    {
        $notePriority = CustomerNotePriority::from($priority);
        return $this->model->byPriority($notePriority)->with(['customer', 'user'])->get();
    }

    public function findByPriorityDTO(string $priority): Collection
    {
        return $this->findByPriority($priority)->map(fn($note) => CustomerNoteDTO::fromModel($note));
    }

    public function findByCustomerAndType(int $customerId, string $type): Collection
    {
        $noteType = CustomerNoteType::from($type);
        return $this->model->byCustomer($customerId)
            ->byType($noteType)
            ->with(['customer', 'user'])
            ->get();
    }

    public function findByCustomerAndTypeDTO(int $customerId, string $type): Collection
    {
        return $this->findByCustomerAndType($customerId, $type)
            ->map(fn($note) => CustomerNoteDTO::fromModel($note));
    }

    public function findByCustomerAndPriority(int $customerId, string $priority): Collection
    {
        $notePriority = CustomerNotePriority::from($priority);
        return $this->model->byCustomer($customerId)
            ->byPriority($notePriority)
            ->with(['customer', 'user'])
            ->get();
    }

    public function findByCustomerAndPriorityDTO(int $customerId, string $priority): Collection
    {
        return $this->findByCustomerAndPriority($customerId, $priority)
            ->map(fn($note) => CustomerNoteDTO::fromModel($note));
    }

    public function getNoteCountByType(string $type): int
    {
        $noteType = CustomerNoteType::from($type);
        return $this->model->byType($noteType)->count();
    }

    public function getNoteCountByPriority(string $priority): int
    {
        $notePriority = CustomerNotePriority::from($priority);
        return $this->model->byPriority($notePriority)->count();
    }

    // Privacy and pinning queries
    public function findPublic(): Collection
    {
        return $this->model->public()->with(['customer', 'user'])->get();
    }

    public function findPublicDTO(): Collection
    {
        return $this->findPublic()->map(fn($note) => CustomerNoteDTO::fromModel($note));
    }

    public function findPrivate(): Collection
    {
        return $this->model->private()->with(['customer', 'user'])->get();
    }

    public function findPrivateDTO(): Collection
    {
        return $this->findPrivate()->map(fn($note) => CustomerNoteDTO::fromModel($note));
    }

    public function findPinned(): Collection
    {
        return $this->model->pinned()->with(['customer', 'user'])->get();
    }

    public function findPinnedDTO(): Collection
    {
        return $this->findPinned()->map(fn($note) => CustomerNoteDTO::fromModel($note));
    }

    public function getPinnedNoteCount(): int
    {
        return $this->model->pinned()->count();
    }

    public function getPrivateNoteCount(): int
    {
        return $this->model->private()->count();
    }

    // Date range queries
    public function findByDateRange(string $startDate, string $endDate): Collection
    {
        return $this->model->byDateRange($startDate, $endDate)->with(['customer', 'user'])->get();
    }

    public function findByDateRangeDTO(string $startDate, string $endDate): Collection
    {
        return $this->findByDateRange($startDate, $endDate)
            ->map(fn($note) => CustomerNoteDTO::fromModel($note));
    }

    // Tag queries
    public function findByTag(string $tag): Collection
    {
        return $this->model->byTag($tag)->with(['customer', 'user'])->get();
    }

    public function findByTagDTO(string $tag): Collection
    {
        return $this->findByTag($tag)->map(fn($note) => CustomerNoteDTO::fromModel($note));
    }

    public function getPopularTags(): array
    {
        $tags = $this->model->whereNotNull('tags')->pluck('tags')->flatten();
        return $tags->countBy()->sortDesc()->take(10)->toArray();
    }

    public function getPopularTagsByCustomer(int $customerId): array
    {
        $tags = $this->model->byCustomer($customerId)
            ->whereNotNull('tags')
            ->pluck('tags')
            ->flatten();
        return $tags->countBy()->sortDesc()->take(10)->toArray();
    }

    // Status management
    public function pin(CustomerNote $note): bool
    {
        return $note->pin();
    }

    public function unpin(CustomerNote $note): bool
    {
        return $note->unpin();
    }

    public function makePrivate(CustomerNote $note): bool
    {
        return $note->makePrivate();
    }

    public function makePublic(CustomerNote $note): bool
    {
        return $note->makePublic();
    }

    public function addTag(CustomerNote $note, string $tag): bool
    {
        return $note->addTag($tag);
    }

    public function removeTag(CustomerNote $note, string $tag): bool
    {
        return $note->removeTag($tag);
    }

    // Attachment management
    public function addAttachment(CustomerNote $note, $file): bool
    {
        try {
            $note->addMedia($file)->toMediaCollection('attachments');
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function removeAttachment(CustomerNote $note, int $mediaId): bool
    {
        try {
            $media = $note->getMedia('attachments')->find($mediaId);
            if ($media) {
                $media->delete();
                return true;
            }
            return false;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function getAttachments(CustomerNote $note): Collection
    {
        return $note->getMedia('attachments');
    }

    // Search functionality
    public function search(string $query): Collection
    {
        return $this->model->search($query)->with(['customer', 'user'])->get();
    }

    public function searchDTO(string $query): Collection
    {
        return $this->search($query)->map(fn($note) => CustomerNoteDTO::fromModel($note));
    }

    public function searchByCustomer(int $customerId, string $query): Collection
    {
        return $this->model->byCustomer($customerId)
            ->search($query)
            ->with(['customer', 'user'])
            ->get();
    }

    public function searchByCustomerDTO(int $customerId, string $query): Collection
    {
        return $this->searchByCustomer($customerId, $query)
            ->map(fn($note) => CustomerNoteDTO::fromModel($note));
    }

    // Recent notes
    public function getRecentNotes(int $limit = 10): Collection
    {
        return $this->model->with(['customer', 'user'])
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    public function getRecentNotesDTO(int $limit = 10): Collection
    {
        return $this->getRecentNotes($limit)->map(fn($note) => CustomerNoteDTO::fromModel($note));
    }

    // Statistics and analytics
    public function getNoteCount(): int
    {
        return $this->model->count();
    }

    public function getNoteStats(): array
    {
        return [
            'total' => $this->getNoteCount(),
            'by_type' => $this->getNoteStatsByType(),
            'by_priority' => $this->getNoteStatsByPriority(),
            'pinned' => $this->getPinnedNoteCount(),
            'private' => $this->getPrivateNoteCount(),
            'popular_tags' => $this->getPopularTags(),
        ];
    }

    public function getNoteStatsByCustomer(int $customerId): array
    {
        return [
            'total' => $this->getNoteCountByCustomer($customerId),
            'by_type' => collect(CustomerNoteType::cases())->mapWithKeys(function ($type) use ($customerId) {
                return [$type->value => $this->model->byCustomer($customerId)->byType($type)->count()];
            })->toArray(),
            'by_priority' => collect(CustomerNotePriority::cases())->mapWithKeys(function ($priority) use ($customerId) {
                return [$priority->value => $this->model->byCustomer($customerId)->byPriority($priority)->count()];
            })->toArray(),
            'pinned' => $this->model->byCustomer($customerId)->pinned()->count(),
            'private' => $this->model->byCustomer($customerId)->private()->count(),
            'popular_tags' => $this->getPopularTagsByCustomer($customerId),
        ];
    }

    public function getNoteStatsByType(): array
    {
        return collect(CustomerNoteType::cases())->mapWithKeys(function ($type) {
            return [$type->value => $this->getNoteCountByType($type->value)];
        })->toArray();
    }

    public function getNoteStatsByPriority(): array
    {
        return collect(CustomerNotePriority::cases())->mapWithKeys(function ($priority) {
            return [$priority->value => $this->getNoteCountByPriority($priority->value)];
        })->toArray();
    }

    public function getNoteStatsByDateRange(string $startDate, string $endDate): array
    {
        $notes = $this->findByDateRange($startDate, $endDate);
        
        return [
            'total' => $notes->count(),
            'by_type' => $notes->groupBy('note_type')->map->count()->toArray(),
            'by_priority' => $notes->groupBy('priority')->map->count()->toArray(),
            'pinned' => $notes->where('is_pinned', true)->count(),
            'private' => $notes->where('is_private', true)->count(),
        ];
    }

    // Validation
    public function validateNote(array $data): bool
    {
        $rules = CustomerNoteDTO::rules();
        $validator = Validator::make($data, $rules);
        return !$validator->fails();
    }
}
