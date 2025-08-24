<?php

namespace Fereydooni\Shopping\app\Actions\ProviderNote;

use Fereydooni\Shopping\app\DTOs\ProviderNoteDTO;
use Fereydooni\Shopping\app\Repositories\Interfaces\ProviderNoteRepositoryInterface;
use Fereydooni\Shopping\app\Models\ProviderNote;
use Illuminate\Support\Facades\Log;

class AddProviderNoteTagsAction
{
    public function __construct(
        private ProviderNoteRepositoryInterface $providerNoteRepository
    ) {}

    public function execute(ProviderNote $providerNote, array $tags): ProviderNoteDTO
    {
        try {
            // Validate tags
            if (empty($tags)) {
                throw new \InvalidArgumentException('Tags cannot be empty');
            }

            // Process tags
            $processedTags = $this->processTags($tags);

            if (empty($processedTags)) {
                throw new \InvalidArgumentException('No valid tags provided');
            }

            // Add tags to the note
            $added = $this->providerNoteRepository->addTags($providerNote, $processedTags);

            if (!$added) {
                throw new \Exception('Failed to add tags to provider note');
            }

            // Get the updated DTO
            $providerNoteDTO = $this->providerNoteRepository->findDTO($providerNote->id);

            if (!$providerNoteDTO) {
                throw new \Exception('Failed to retrieve updated provider note');
            }

            // Log the action
            Log::info('Tags added to provider note successfully', [
                'note_id' => $providerNoteDTO->id,
                'provider_id' => $providerNoteDTO->provider_id,
                'added_tags' => $processedTags,
            ]);

            return $providerNoteDTO;

        } catch (\Exception $e) {
            Log::error('Failed to add tags to provider note', [
                'error' => $e->getMessage(),
                'note_id' => $providerNote->id,
                'tags' => $tags,
            ]);
            throw $e;
        }
    }

    private function processTags(array $tags): array
    {
        // Clean and validate tags
        $processedTags = array_map(function ($tag) {
            $tag = trim($tag);
            return strlen($tag) > 0 ? $tag : null;
        }, $tags);

        // Remove empty tags and duplicates
        $processedTags = array_filter($processedTags);
        $processedTags = array_unique($processedTags);

        return array_values($processedTags);
    }
}
