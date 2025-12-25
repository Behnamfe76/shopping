<?php

namespace Fereydooni\Shopping\app\Actions\ProviderNote;

use Fereydooni\Shopping\app\DTOs\ProviderNoteDTO;
use Fereydooni\Shopping\app\Repositories\Interfaces\ProviderNoteRepositoryInterface;
use Illuminate\Support\Collection;

class SearchProviderNotesAction
{
    public function __construct(
        private ProviderNoteRepositoryInterface $providerNoteRepository
    ) {}

    public function execute(string $query, ?int $providerId = null, ?string $noteType = null, ?string $priority = null, ?string $sortBy = 'created_at', ?string $sortOrder = 'desc'): Collection
    {
        try {
            // Validate query
            if (empty(trim($query))) {
                throw new \InvalidArgumentException('Search query cannot be empty');
            }

            // Perform search
            if ($providerId) {
                $notes = $this->providerNoteRepository->searchNotesByProvider($providerId, $query);
            } else {
                $notes = $this->providerNoteRepository->searchNotes($query);
            }

            // Apply additional filters
            $notes = $this->applyFilters($notes, $noteType, $priority);

            // Sort results
            $notes = $this->sortResults($notes, $sortBy, $sortOrder);

            // Convert to DTOs
            return $notes->map(fn ($note) => ProviderNoteDTO::fromModel($note));

        } catch (\Exception $e) {
            // Log error and return empty collection
            \Log::error('Failed to search provider notes', [
                'error' => $e->getMessage(),
                'query' => $query,
                'provider_id' => $providerId,
            ]);

            return collect();
        }
    }

    private function applyFilters(Collection $notes, ?string $noteType, ?string $priority): Collection
    {
        if ($noteType) {
            $notes = $notes->filter(fn ($note) => $note->note_type === $noteType || $note->type === $noteType);
        }

        if ($priority) {
            $notes = $notes->filter(fn ($note) => $note->priority === $priority);
        }

        return $notes;
    }

    private function sortResults(Collection $notes, string $sortBy, string $sortOrder): Collection
    {
        $validSortFields = ['created_at', 'updated_at', 'title', 'priority', 'note_type'];
        $validSortOrders = ['asc', 'desc'];

        if (! in_array($sortBy, $validSortFields)) {
            $sortBy = 'created_at';
        }

        if (! in_array($sortOrder, $validSortOrders)) {
            $sortOrder = 'desc';
        }

        return $notes->sortBy([
            [$sortBy, $sortOrder],
        ]);
    }
}
