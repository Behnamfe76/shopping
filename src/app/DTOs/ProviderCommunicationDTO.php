<?php

namespace App\DTOs;

use Spatie\LaravelData\Data;
use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Attributes\Validation\StringType;
use Spatie\LaravelData\Attributes\Validation\IntegerType;
use Spatie\LaravelData\Attributes\Validation\FloatType;
use Spatie\LaravelData\Attributes\Validation\BooleanType;
use Spatie\LaravelData\Attributes\Validation\Email;
use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Attributes\Validation\Min;
use Spatie\LaravelData\Attributes\Validation\In;
use Spatie\LaravelData\Attributes\Validation\Nullable;
use Spatie\LaravelData\Attributes\Validation\Date;
use Spatie\LaravelData\Attributes\Validation\ArrayType;
use Spatie\LaravelData\Attributes\Validation\Json;
use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Attributes\WithTransformer;
use Spatie\LaravelData\Transformers\DateTimeTransformer;
use App\Models\ProviderCommunication;
use App\Enums\CommunicationType;
use App\Enums\Direction;
use App\Enums\Status;
use App\Enums\Priority;
use Carbon\Carbon;

class ProviderCommunicationDTO extends Data
{
    public function __construct(
        #[Nullable]
        public ?int $id = null,

        #[Required, IntegerType]
        public int $provider_id,

        #[Required, IntegerType]
        public int $user_id,

        #[Required, StringType, In(CommunicationType::class)]
        public string $communication_type,

        #[Required, StringType, Max(255)]
        public string $subject,

        #[Required, StringType, Max(10000)]
        public string $message,

        #[Required, StringType, In(Direction::class)]
        public string $direction,

        #[Required, StringType, In(Status::class)]
        public string $status,

        #[Nullable, Date]
        public ?string $sent_at = null,

        #[Nullable, Date]
        public ?string $read_at = null,

        #[Nullable, Date]
        public ?string $replied_at = null,

        #[Required, StringType, In(Priority::class)]
        public string $priority = 'normal',

        #[BooleanType]
        public bool $is_urgent = false,

        #[BooleanType]
        public bool $is_archived = false,

        #[Nullable, ArrayType, Json]
        public ?array $attachments = null,

        #[Nullable, ArrayType, Json]
        public ?array $tags = null,

        #[Nullable, StringType]
        public ?string $thread_id = null,

        #[Nullable, IntegerType]
        public ?int $parent_id = null,

        #[Nullable, IntegerType, Min(0)]
        public ?int $response_time = null,

        #[Nullable, FloatType, Min(0), Max(5)]
        public ?float $satisfaction_rating = null,

        #[Nullable, StringType, Max(1000)]
        public ?string $notes = null,

        #[Nullable, Date]
        public ?string $created_at = null,

        #[Nullable, Date]
        public ?string $updated_at = null,
    ) {}

    public static function fromModel(ProviderCommunication $model): self
    {
        return new self(
            id: $model->id,
            provider_id: $model->provider_id,
            user_id: $model->user_id,
            communication_type: $model->communication_type,
            subject: $model->subject,
            message: $model->message,
            direction: $model->direction,
            status: $model->status,
            sent_at: $model->sent_at?->toISOString(),
            read_at: $model->read_at?->toISOString(),
            replied_at: $model->replied_at?->toISOString(),
            priority: $model->priority,
            is_urgent: $model->is_urgent,
            is_archived: $model->is_archived,
            attachments: $model->attachments,
            tags: $model->tags,
            thread_id: $model->thread_id,
            parent_id: $model->parent_id,
            response_time: $model->response_time,
            satisfaction_rating: $model->satisfaction_rating,
            notes: $model->notes,
            created_at: $model->created_at?->toISOString(),
            updated_at: $model->updated_at?->toISOString(),
        );
    }

    public static function rules(): array
    {
        return [
            'provider_id' => ['required', 'integer', 'exists:providers,id'],
            'user_id' => ['required', 'integer', 'exists:users,id'],
            'communication_type' => ['required', 'string', 'in:' . implode(',', CommunicationType::values())],
            'subject' => ['required', 'string', 'max:255'],
            'message' => ['required', 'string', 'max:10000'],
            'direction' => ['required', 'string', 'in:' . implode(',', Direction::values())],
            'status' => ['required', 'string', 'in:' . implode(',', Status::values())],
            'sent_at' => ['nullable', 'date'],
            'read_at' => ['nullable', 'date'],
            'replied_at' => ['nullable', 'date'],
            'priority' => ['required', 'string', 'in:' . implode(',', Priority::values())],
            'is_urgent' => ['boolean'],
            'is_archived' => ['boolean'],
            'attachments' => ['nullable', 'array'],
            'tags' => ['nullable', 'array'],
            'thread_id' => ['nullable', 'string', 'max:255'],
            'parent_id' => ['nullable', 'integer', 'exists:provider_communications,id'],
            'response_time' => ['nullable', 'integer', 'min:0'],
            'satisfaction_rating' => ['nullable', 'numeric', 'min:0', 'max:5'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ];
    }

    public static function messages(): array
    {
        return [
            'provider_id.required' => 'Provider ID is required.',
            'provider_id.exists' => 'The selected provider does not exist.',
            'user_id.required' => 'User ID is required.',
            'user_id.exists' => 'The selected user does not exist.',
            'communication_type.required' => 'Communication type is required.',
            'communication_type.in' => 'Invalid communication type.',
            'subject.required' => 'Subject is required.',
            'subject.max' => 'Subject cannot exceed 255 characters.',
            'message.required' => 'Message is required.',
            'message.max' => 'Message cannot exceed 10000 characters.',
            'direction.required' => 'Direction is required.',
            'direction.in' => 'Invalid direction.',
            'status.required' => 'Status is required.',
            'status.in' => 'Invalid status.',
            'priority.required' => 'Priority is required.',
            'priority.in' => 'Invalid priority.',
            'response_time.min' => 'Response time cannot be negative.',
            'satisfaction_rating.min' => 'Satisfaction rating cannot be less than 0.',
            'satisfaction_rating.max' => 'Satisfaction rating cannot exceed 5.',
            'notes.max' => 'Notes cannot exceed 1000 characters.',
        ];
    }
}
