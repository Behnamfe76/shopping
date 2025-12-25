<?php

namespace Fereydooni\Shopping\app\Actions\ProviderNote;

use Fereydooni\Shopping\app\DTOs\ProviderNoteDTO;
use Fereydooni\Shopping\app\Notifications\ProviderNote\ProviderNoteCreated;
use Fereydooni\Shopping\app\Repositories\Interfaces\ProviderNoteRepositoryInterface;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Validator;

class CreateProviderNoteAction
{
    public function __construct(
        private ProviderNoteRepositoryInterface $providerNoteRepository
    ) {}

    public function execute(array $data): ProviderNoteDTO
    {
        // Validate the data
        $validator = Validator::make($data, [
            'provider_id' => 'required|integer|exists:providers,id',
            'user_id' => 'required|integer|exists:users,id',
            'title' => 'required|string|min:1|max:255',
            'content' => 'required|string|min:1|max:10000',
            'note_type' => 'required|string|in:general,contract,payment,quality,performance,communication,other',
            'priority' => 'required|string|in:low,medium,high,urgent',
            'is_private' => 'boolean',
            'is_archived' => 'boolean',
            'tags' => 'nullable|array',
            'attachments' => 'nullable|array',
        ]);

        if ($validator->fails()) {
            throw new \InvalidArgumentException($validator->errors()->first());
        }

        try {
            // Set default values
            $data['is_private'] = $data['is_private'] ?? false;
            $data['is_archived'] = $data['is_archived'] ?? false;
            $data['tags'] = $data['tags'] ?? [];
            $data['attachments'] = $data['attachments'] ?? [];

            // Process tags if provided
            if (! empty($data['tags'])) {
                $data['tags'] = $this->processTags($data['tags']);
            }

            // Process attachments if provided
            if (! empty($data['attachments'])) {
                $data['attachments'] = $this->processAttachments($data['attachments']);
            }

            // Create the provider note
            $providerNoteDTO = $this->providerNoteRepository->createAndReturnDTO($data);

            // Send notifications
            $this->sendNotifications($providerNoteDTO);

            // Log the action
            Log::info('Provider note created successfully', [
                'id' => $providerNoteDTO->id,
                'provider_id' => $providerNoteDTO->provider_id,
                'user_id' => $providerNoteDTO->user_id,
                'title' => $providerNoteDTO->title,
                'note_type' => $providerNoteDTO->note_type,
                'priority' => $providerNoteDTO->priority,
            ]);

            return $providerNoteDTO;

        } catch (\Exception $e) {
            Log::error('Failed to create provider note', [
                'error' => $e->getMessage(),
                'data' => $data,
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

    private function sendNotifications(ProviderNoteDTO $providerNoteDTO): void
    {
        try {
            // Send notification to relevant stakeholders
            // This could include the provider, team members, etc.
            // For now, we'll just log that notifications would be sent

            Log::info('Provider note notifications would be sent', [
                'note_id' => $providerNoteDTO->id,
                'provider_id' => $providerNoteDTO->provider_id,
            ]);

            // TODO: Implement actual notification sending
            // Notification::send($recipients, new ProviderNoteCreated($providerNoteDTO));

        } catch (\Exception $e) {
            Log::warning('Failed to send provider note notifications', [
                'error' => $e->getMessage(),
                'note_id' => $providerNoteDTO->id,
            ]);
        }
    }
}
