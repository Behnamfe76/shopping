<?php

namespace Fereydooni\Shopping\app\DTOs;

use Spatie\LaravelData\Attributes\Validation\Email;
use Spatie\LaravelData\Attributes\Validation\Nullable;
use Spatie\LaravelData\Attributes\Validation\StringType;
use Spatie\LaravelData\Attributes\WithTransformer;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Transformers\DateTimeTransformer;

class UserDTO extends Data
{
    public function __construct(
        #[Nullable]
        public ?int $id = null,

        #[Nullable, StringType]
        public ?string $name = null,

        #[Nullable, Email]
        public ?string $email = null,

        #[Nullable]
        #[WithTransformer(DateTimeTransformer::class)]
        public ?string $created_at = null,

        #[Nullable]
        #[WithTransformer(DateTimeTransformer::class)]
        public ?string $updated_at = null,
    ) {}

    public static function fromModel($user): self
    {
        if (! $user) {
            return new self;
        }

        return new self(
            id: $user->id ?? null,
            name: $user->name ?? null,
            email: $user->email ?? null,
            created_at: $user->created_at?->format('Y-m-d H:i:s'),
            updated_at: $user->updated_at?->format('Y-m-d H:i:s'),
        );
    }
}
