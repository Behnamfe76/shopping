<?php

namespace Fereydooni\Shopping\app\DTOs;

use Fereydooni\Shopping\app\Models\Provider;
use Fereydooni\Shopping\app\Models\ProviderNote;
use Fereydooni\Shopping\app\Models\User;
use Spatie\LaravelData\Attributes\Validation\ArrayType;
use Spatie\LaravelData\Attributes\Validation\Date;
use Spatie\LaravelData\Attributes\Validation\Exists;
use Spatie\LaravelData\Attributes\Validation\In;
use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Attributes\Validation\Min;
use Spatie\LaravelData\Attributes\Validation\Nullable;
use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Attributes\Validation\StringType;
use Spatie\LaravelData\Data;

class ProviderNoteDTO extends Data
{
    public function __construct(
        public ?int $id,
        #[Required, Exists('providers', 'id')]
        public int $provider_id,
        #[Required, Exists('users', 'id')]
        public int $user_id,
        #[Required, StringType, Min(1), Max(255)]
        public string $title,
        #[Required, StringType, Min(1), Max(10000)]
        public string $content,
        #[Required, In(['general', 'contract', 'payment', 'quality', 'performance', 'communication', 'other'])]
        public string $note_type,
        #[Required, In(['low', 'medium', 'high', 'urgent'])]
        public string $priority,
        #[Boolean]
        public bool $is_private = false,
        #[Boolean]
        public bool $is_archived = false,
        #[Nullable, ArrayType]
        public ?array $tags = null,
        #[Nullable, ArrayType]
        public ?array $attachments = null,
        #[Nullable, Date]
        public ?string $created_at = null,
        #[Nullable, Date]
        public ?string $updated_at = null,
        // Relationships
        public ?Provider $provider = null,
        public ?User $user = null,
    ) {}

    public static function fromModel(ProviderNote $providerNote): self
    {
        return new self(
            id: $providerNote->id,
            provider_id: $providerNote->provider_id,
            user_id: $providerNote->user_id,
            title: $providerNote->title ?? $providerNote->note ?? '',
            content: $providerNote->content ?? $providerNote->note ?? '',
            note_type: $providerNote->note_type ?? $providerNote->type ?? 'general',
            priority: $providerNote->priority ?? 'medium',
            is_private: $providerNote->is_private ?? ! ($providerNote->is_public ?? true),
            is_archived: $providerNote->is_archived ?? false,
            tags: $providerNote->tags ?? null,
            attachments: $providerNote->attachments ?? null,
            created_at: $providerNote->created_at,
            updated_at: $providerNote->updated_at,
            provider: $providerNote->relationLoaded('provider') ? $providerNote->provider : null,
            user: $providerNote->relationLoaded('user') ? $providerNote->user : null,
        );
    }

    public static function rules(): array
    {
        return [
            'provider_id' => ['required', 'integer', 'exists:providers,id'],
            'user_id' => ['required', 'integer', 'exists:users,id'],
            'title' => ['required', 'string', 'min:1', 'max:255'],
            'content' => ['required', 'string', 'min:1', 'max:10000'],
            'note_type' => ['required', 'string', 'in:general,contract,payment,quality,performance,communication,other'],
            'priority' => ['required', 'string', 'in:low,medium,high,urgent'],
            'is_private' => ['boolean'],
            'is_archived' => ['boolean'],
            'tags' => ['nullable', 'array'],
            'attachments' => ['nullable', 'array'],
        ];
    }

    public static function messages(): array
    {
        return [
            'provider_id.required' => 'Provider ID is required.',
            'provider_id.exists' => 'The selected provider does not exist.',
            'user_id.required' => 'User ID is required.',
            'user_id.exists' => 'The selected user does not exist.',
            'title.required' => 'Note title is required.',
            'title.min' => 'Note title must be at least 1 character.',
            'title.max' => 'Note title cannot exceed 255 characters.',
            'content.required' => 'Note content is required.',
            'content.min' => 'Note content must be at least 1 character.',
            'content.max' => 'Note content cannot exceed 10,000 characters.',
            'note_type.required' => 'Note type is required.',
            'note_type.in' => 'Invalid note type selected.',
            'priority.required' => 'Priority is required.',
            'priority.in' => 'Invalid priority selected.',
        ];
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'provider_id' => $this->provider_id,
            'user_id' => $this->user_id,
            'title' => $this->title,
            'content' => $this->content,
            'note_type' => $this->note_type,
            'priority' => $this->priority,
            'is_private' => $this->is_private,
            'is_archived' => $this->is_archived,
            'tags' => $this->tags,
            'attachments' => $this->attachments,
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
            'provider' => $this->provider?->toArray(),
            'user' => $this->user?->toArray(),
        ];
    }

    public function isHighPriority(): bool
    {
        return in_array($this->priority, ['high', 'urgent']);
    }

    public function isUrgent(): bool
    {
        return $this->priority === 'urgent';
    }

    public function isPublic(): bool
    {
        return ! $this->is_private;
    }

    public function isActive(): bool
    {
        return ! $this->is_archived;
    }

    public function hasTags(): bool
    {
        return ! empty($this->tags);
    }

    public function hasAttachments(): bool
    {
        return ! empty($this->attachments);
    }

    public function getTagCount(): int
    {
        return is_array($this->tags) ? count($this->tags) : 0;
    }

    public function getAttachmentCount(): int
    {
        return is_array($this->attachments) ? count($this->attachments) : 0;
    }
}
