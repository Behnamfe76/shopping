<?php

namespace Fereydooni\Shopping\app\Actions\ProviderNote;

use Fereydooni\Shopping\app\DTOs\ProviderNoteDTO;
use Fereydooni\Shopping\app\Repositories\Interfaces\ProviderNoteRepositoryInterface;
use Fereydooni\Shopping\app\Models\ProviderNote;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class UpdateProviderNoteAction
{
    public function __construct(
        private ProviderNoteRepositoryInterface $providerNoteRepository
    ) {}

    public function execute(ProviderNote $providerNote, array $data): ProviderNoteDTO
    {
        // Validate the data
        $validator = Validator::make($data, [
            'title' => 'sometimes|string|min:1|max:255',
            'content' => 'sometimes|string|min:1|max:10000',
            'note_type' => 'sometimes|string|in:general,contract,payment,quality,performance,communication,other',
            'priority' => 'sometimes|string|in:low,medium,high,urgent',
            'is_private' => 'sometimes|boolean',
            'is_archived' => 'sometimes|boolean',
            'tags' => 'sometimes|nullable|array',
            'attachments' => 'sometimes|nullable|array',
        ]);

        if ($validator->fails()) {
            throw new \InvalidArgumentException($validator->errors()->first());
        }

        try {
            // Check if note can be modified
            if (!$this->canModifyNote($providerNote)) {
                throw new \Exception('Provider note cannot be modified');
            }

            // Process tags if provided
            if (isset($data['tags'])) {
                $data['tags'] = $this->processTags($data['tags']);
            }

            // Process attachments if provided
            if (isset($data['attachments'])) {
                $data['attachments'] = $this->processAttachments($data['attachments']);
            }

            // Update the provider note
            $providerNoteDTO = $this->providerNoteRepository->updateAndReturnDTO($providerNote, $data);

            if (!$providerNoteDTO) {
                throw new \Exception('Failed to update provider note');
            }

            // Log the action
            Log::info('Provider note updated successfully', [
                'id' => $providerNoteDTO->id,
                'provider_id' => $providerNoteDTO->provider_id,
                'user_id' => $providerNoteDTO->user_id,
                'updated_fields' => array_keys($data),
            ]);

            return $providerNoteDTO;

        } catch (\Exception $e) {
            Log::error('Failed to update provider note', [
                'error' => $e->getMessage(),
                'note_id' => $providerNote->id,
                'data' => $data,
            ]);
            throw $e;
        }
    }

    private function canModifyNote(ProviderNote $providerNote): bool
    {
        // Check if note is archived
        if ($providerNote->is_archived) {
            return false;
        }

        // Add any other business rules here
        // For example, check user permissions, note age, etc.

        return true;
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

    private function processAttachments(array $attachments): array
    {
        // Validate attachment paths
        $processedAttachments = array_map(function ($attachment) {
            if (is_string($attachment) && file_exists($attachment)) {
                return $attachment;
            }
            return null;
        }, $attachments);

        // Remove invalid attachments
        $processedAttachments = array_filter($processedAttachments);

        return array_values($processedAttachments);
    }
}
