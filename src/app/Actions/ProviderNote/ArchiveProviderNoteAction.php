<?php

namespace Fereydooni\Shopping\app\Actions\ProviderNote;

use Fereydooni\Shopping\app\DTOs\ProviderNoteDTO;
use Fereydooni\Shopping\app\Repositories\Interfaces\ProviderNoteRepositoryInterface;
use Fereydooni\Shopping\app\Models\ProviderNote;
use Illuminate\Support\Facades\Log;

class ArchiveProviderNoteAction
{
    public function __construct(
        private ProviderNoteRepositoryInterface $providerNoteRepository
    ) {}

    public function execute(ProviderNote $providerNote): ProviderNoteDTO
    {
        try {
            // Check if note can be archived
            if (!$this->canArchiveNote($providerNote)) {
                throw new \Exception('Provider note cannot be archived');
            }

            // Archive the note
            $archived = $this->providerNoteRepository->archive($providerNote);

            if (!$archived) {
                throw new \Exception('Failed to archive provider note');
            }

            // Get the updated DTO
            $providerNoteDTO = $this->providerNoteRepository->findDTO($providerNote->id);

            if (!$providerNoteDTO) {
                throw new \Exception('Failed to retrieve archived provider note');
            }

            // Log the action
            Log::info('Provider note archived successfully', [
                'id' => $providerNoteDTO->id,
                'provider_id' => $providerNoteDTO->provider_id,
                'user_id' => $providerNoteDTO->user_id,
            ]);

            return $providerNoteDTO;

        } catch (\Exception $e) {
            Log::error('Failed to archive provider note', [
                'error' => $e->getMessage(),
                'note_id' => $providerNote->id,
            ]);
            throw $e;
        }
    }

    private function canArchiveNote(ProviderNote $providerNote): bool
    {
        // Check if note is already archived
        if ($providerNote->is_archived) {
            return false;
        }

        // Add any other business rules here
        // For example, check user permissions, note age, etc.

        return true;
    }
}
