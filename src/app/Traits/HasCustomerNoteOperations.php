<?php

namespace Fereydooni\Shopping\app\Traits;

use Fereydooni\Shopping\app\DTOs\CustomerNoteDTO;
use Fereydooni\Shopping\app\Enums\CustomerNotePriority;
use Fereydooni\Shopping\app\Enums\CustomerNoteType;
use Fereydooni\Shopping\app\Models\CustomerNote;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

trait HasCustomerNoteOperations
{
    /**
     * Create a new customer note
     */
    public function createCustomerNote(array $data): CustomerNote
    {
        $this->validateCustomerNoteData($data);

        $data['user_id'] = $data['user_id'] ?? auth()->id();

        return $this->repository->create($data);
    }

    /**
     * Create a new customer note and return DTO
     */
    public function createCustomerNoteDTO(array $data): CustomerNoteDTO
    {
        $note = $this->createCustomerNote($data);

        return CustomerNoteDTO::fromModel($note->load(['customer', 'user']));
    }

    /**
     * Update customer note
     */
    public function updateCustomerNote(CustomerNote $note, array $data): bool
    {
        $this->validateCustomerNoteData($data, $note->id);

        return $this->repository->update($note, $data);
    }

    /**
     * Update customer note and return DTO
     */
    public function updateCustomerNoteDTO(CustomerNote $note, array $data): ?CustomerNoteDTO
    {
        $updated = $this->updateCustomerNote($note, $data);

        return $updated ? CustomerNoteDTO::fromModel($note->fresh()->load(['customer', 'user'])) : null;
    }

    /**
     * Delete customer note
     */
    public function deleteCustomerNote(CustomerNote $note): bool
    {
        return $this->repository->delete($note);
    }

    /**
     * Get customer notes by customer ID
     */
    public function getCustomerNotes(int $customerId): Collection
    {
        return $this->repository->findByCustomerId($customerId);
    }

    /**
     * Get customer notes DTOs by customer ID
     */
    public function getCustomerNotesDTO(int $customerId): Collection
    {
        return $this->repository->findByCustomerIdDTO($customerId);
    }

    /**
     * Get customer notes by type
     */
    public function getCustomerNotesByType(int $customerId, string $type): Collection
    {
        return $this->repository->findByCustomerAndType($customerId, $type);
    }

    /**
     * Get customer notes by priority
     */
    public function getCustomerNotesByPriority(int $customerId, string $priority): Collection
    {
        return $this->repository->findByCustomerAndPriority($customerId, $priority);
    }

    /**
     * Get pinned customer notes
     */
    public function getPinnedCustomerNotes(int $customerId): Collection
    {
        return $this->repository->getPinnedNotesByCustomer($customerId);
    }

    /**
     * Get recent customer notes
     */
    public function getRecentCustomerNotes(int $customerId, int $limit = 10): Collection
    {
        return $this->repository->getRecentNotesByCustomer($customerId, $limit);
    }

    /**
     * Search customer notes
     */
    public function searchCustomerNotes(int $customerId, string $query): Collection
    {
        return $this->repository->searchByCustomer($customerId, $query);
    }

    /**
     * Get customer note statistics
     */
    public function getCustomerNoteStats(int $customerId): array
    {
        return $this->repository->getNoteStatsByCustomer($customerId);
    }

    /**
     * Get customer note count
     */
    public function getCustomerNoteCount(int $customerId): int
    {
        return $this->repository->getNoteCountByCustomer($customerId);
    }

    /**
     * Add tag to customer note
     */
    public function addCustomerNoteTag(CustomerNote $note, string $tag): bool
    {
        return $this->repository->addTag($note, $tag);
    }

    /**
     * Remove tag from customer note
     */
    public function removeCustomerNoteTag(CustomerNote $note, string $tag): bool
    {
        return $this->repository->removeTag($note, $tag);
    }

    /**
     * Add attachment to customer note
     */
    public function addCustomerNoteAttachment(CustomerNote $note, $file): bool
    {
        return $this->repository->addAttachment($note, $file);
    }

    /**
     * Remove attachment from customer note
     */
    public function removeCustomerNoteAttachment(CustomerNote $note, int $mediaId): bool
    {
        return $this->repository->removeAttachment($note, $mediaId);
    }

    /**
     * Get customer note attachments
     */
    public function getCustomerNoteAttachments(CustomerNote $note): Collection
    {
        return $this->repository->getAttachments($note);
    }

    /**
     * Get popular tags for customer notes
     */
    public function getCustomerNotePopularTags(int $customerId): array
    {
        return $this->repository->getPopularTagsByCustomer($customerId);
    }

    /**
     * Get customer notes by date range
     */
    public function getCustomerNotesByDateRange(int $customerId, string $startDate, string $endDate): Collection
    {
        return $this->repository->getNotesByCustomerAndDateRange($customerId, $startDate, $endDate);
    }

    /**
     * Validate customer note data
     */
    protected function validateCustomerNoteData(array $data, ?int $noteId = null): void
    {
        $rules = CustomerNoteDTO::rules();

        // Add unique validation for title if updating
        if ($noteId) {
            $rules['title'] = array_merge($rules['title'], ['unique:customer_notes,title,'.$noteId]);
        } else {
            $rules['title'] = array_merge($rules['title'], ['unique:customer_notes,title']);
        }

        $validator = Validator::make($data, $rules, CustomerNoteDTO::messages());

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }
    }

    /**
     * Check if user can access customer note
     */
    public function canAccessCustomerNote(CustomerNote $note, int $userId): bool
    {
        return $note->canBeViewedBy($userId);
    }

    /**
     * Check if user can edit customer note
     */
    public function canEditCustomerNote(CustomerNote $note, int $userId): bool
    {
        return $note->canBeEditedBy($userId);
    }

    /**
     * Check if user can delete customer note
     */
    public function canDeleteCustomerNote(CustomerNote $note, int $userId): bool
    {
        return $note->canBeDeletedBy($userId);
    }

    /**
     * Get customer note types
     */
    public function getCustomerNoteTypes(): array
    {
        return collect(CustomerNoteType::cases())->mapWithKeys(function ($type) {
            return [$type->value => $type->label()];
        })->toArray();
    }

    /**
     * Get customer note priorities
     */
    public function getCustomerNotePriorities(): array
    {
        return collect(CustomerNotePriority::cases())->mapWithKeys(function ($priority) {
            return [$priority->value => $priority->label()];
        })->toArray();
    }

    /**
     * Export customer notes
     */
    public function exportCustomerNotes(int $customerId, string $format = 'json'): string
    {
        $notes = $this->getCustomerNotesDTO($customerId);

        return match ($format) {
            'json' => $notes->toJson(),
            'csv' => $this->convertNotesToCsv($notes),
            default => $notes->toJson(),
        };
    }

    /**
     * Convert notes to CSV format
     */
    protected function convertNotesToCsv(Collection $notes): string
    {
        $headers = ['ID', 'Title', 'Content', 'Type', 'Priority', 'Private', 'Pinned', 'Tags', 'Created At'];
        $csv = implode(',', $headers)."\n";

        foreach ($notes as $note) {
            $row = [
                $note->id,
                $note->title,
                $note->content,
                $note->note_type->value,
                $note->priority->value,
                $note->is_private ? 'Yes' : 'No',
                $note->is_pinned ? 'Yes' : 'No',
                is_array($note->tags) ? implode(';', $note->tags) : '',
                $note->created_at?->format('Y-m-d H:i:s'),
            ];
            $csv .= implode(',', array_map('addslashes', $row))."\n";
        }

        return $csv;
    }

    /**
     * Import customer notes
     */
    public function importCustomerNotes(int $customerId, array $notesData): array
    {
        $results = ['success' => 0, 'failed' => 0, 'errors' => []];

        foreach ($notesData as $index => $noteData) {
            try {
                $noteData['customer_id'] = $customerId;
                $noteData['user_id'] = auth()->id();

                $this->createCustomerNote($noteData);
                $results['success']++;
            } catch (\Exception $e) {
                $results['failed']++;
                $results['errors'][] = 'Row '.($index + 1).': '.$e->getMessage();
            }
        }

        return $results;
    }

    /**
     * Get customer note templates
     */
    public function getCustomerNoteTemplates(): array
    {
        return [
            'support' => [
                'title' => 'Support Request',
                'content' => 'Customer contacted regarding: [Issue Description]',
                'type' => CustomerNoteType::SUPPORT,
                'priority' => CustomerNotePriority::MEDIUM,
            ],
            'complaint' => [
                'title' => 'Customer Complaint',
                'content' => 'Complaint details: [Complaint Description]',
                'type' => CustomerNoteType::COMPLAINT,
                'priority' => CustomerNotePriority::HIGH,
            ],
            'follow_up' => [
                'title' => 'Follow Up Required',
                'content' => 'Follow up needed for: [Reason]',
                'type' => CustomerNoteType::FOLLOW_UP,
                'priority' => CustomerNotePriority::MEDIUM,
            ],
            'feedback' => [
                'title' => 'Customer Feedback',
                'content' => 'Feedback received: [Feedback Details]',
                'type' => CustomerNoteType::FEEDBACK,
                'priority' => CustomerNotePriority::LOW,
            ],
        ];
    }

    /**
     * Create customer note from template
     */
    public function createCustomerNoteFromTemplate(int $customerId, string $templateKey, array $customData = []): CustomerNote
    {
        $templates = $this->getCustomerNoteTemplates();

        if (! isset($templates[$templateKey])) {
            throw new \InvalidArgumentException("Template '{$templateKey}' not found.");
        }

        $template = $templates[$templateKey];
        $data = array_merge($template, $customData, [
            'customer_id' => $customerId,
            'user_id' => auth()->id(),
        ]);

        return $this->createCustomerNote($data);
    }
}
