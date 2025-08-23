<?php

namespace Fereydooni\Shopping\app\DTOs;

use Spatie\LaravelData\Data;
use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Attributes\Validation\In;
use Spatie\LaravelData\Attributes\Validation\Boolean;
use Spatie\LaravelData\Attributes\Validation\Exists;
use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Attributes\Validation\Nullable;
use Spatie\LaravelData\Attributes\Validation\StringType;
use Spatie\LaravelData\Attributes\Validation\IntegerType;
use Spatie\LaravelData\Attributes\Validation\Date;
use Spatie\LaravelData\Attributes\Validation\DateFormat;

class ProviderNoteDTO extends Data
{
    public function __construct(
        public ?int $id,
        public int $provider_id,
        public int $user_id,
        public string $note,
        public string $type,
        public bool $is_public,
        public bool $is_archived,
        public ?string $created_at,
        public ?string $updated_at,
        public ?string $deleted_at,
    ) {
    }

    public static function rules(): array
    {
        return [
            'provider_id' => [Required::class, IntegerType::class, Exists::class . ':providers,id'],
            'user_id' => [Required::class, IntegerType::class, Exists::class . ':users,id'],
            'note' => [Required::class, StringType::class, Max::class . ':1000'],
            'type' => [Required::class, StringType::class, In::class . ':general,quality,financial,contract,performance,communication'],
            'is_public' => [Boolean::class],
            'is_archived' => [Boolean::class],
        ];
    }

    public static function messages(): array
    {
        return [
            'provider_id.required' => 'Provider ID is required.',
            'provider_id.exists' => 'The selected provider does not exist.',
            'user_id.required' => 'User ID is required.',
            'user_id.exists' => 'The selected user does not exist.',
            'note.required' => 'Note content is required.',
            'note.max' => 'Note content cannot exceed 1000 characters.',
            'type.in' => 'Invalid note type.',
        ];
    }

    public static function fromModel($model): static
    {
        return new static(
            id: $model->id,
            provider_id: $model->provider_id,
            user_id: $model->user_id,
            note: $model->note,
            type: $model->type,
            is_public: $model->is_public,
            is_archived: $model->is_archived,
            created_at: $model->created_at?->toISOString(),
            updated_at: $model->updated_at?->toISOString(),
            deleted_at: $model->deleted_at?->toISOString(),
        );
    }
}
