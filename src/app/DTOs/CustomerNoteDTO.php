<?php

namespace Fereydooni\Shopping\app\DTOs;

use Spatie\LaravelData\Data;
use Spatie\LaravelData\Attributes\Validation\Email;
use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Attributes\Validation\Min;
use Spatie\LaravelData\Attributes\Validation\Nullable;
use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Attributes\Validation\StringType;
use Spatie\LaravelData\Attributes\Validation\IntegerType;
use Spatie\LaravelData\Attributes\Validation\BooleanType;
use Spatie\LaravelData\Attributes\Validation\Date;
use Spatie\LaravelData\Attributes\Validation\Numeric;
use Spatie\LaravelData\Attributes\Validation\In;
use Spatie\LaravelData\Attributes\Validation\Unique;
use Spatie\LaravelData\Attributes\Validation\ArrayType;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Fereydooni\Shopping\app\Enums\CustomerNoteType;
use Fereydooni\Shopping\app\Enums\CustomerNotePriority;
use Fereydooni\Shopping\app\Models\CustomerNote;
use Fereydooni\Shopping\app\Models\Customer;
use Fereydooni\Shopping\app\Models\User;

class CustomerNoteDTO extends Data
{
    public function __construct(
        #[Nullable]
        public ?int $id,

        #[Required, IntegerType]
        public int $customer_id,

        #[Required, IntegerType]
        public int $user_id,

        #[Required, StringType, Max(255)]
        public string $title,

        #[Required, StringType, Max(10000)]
        public string $content,

        #[Required]
        public CustomerNoteType $note_type,

        #[Required]
        public CustomerNotePriority $priority,

        #[BooleanType]
        public bool $is_private,

        #[BooleanType]
        public bool $is_pinned,

        #[Nullable, ArrayType]
        public ?array $tags,

        #[Nullable, ArrayType]
        public ?array $attachments,

        #[Nullable]
        public ?Carbon $created_at,

        #[Nullable]
        public ?Carbon $updated_at,

        // Relationships
        #[Nullable]
        public ?Customer $customer,

        #[Nullable]
        public ?User $user,
    ) {
    }

    public static function fromModel(CustomerNote $customerNote): self
    {
        return new self(
            id: $customerNote->id,
            customer_id: $customerNote->customer_id,
            user_id: $customerNote->user_id,
            title: $customerNote->title,
            content: $customerNote->content,
            note_type: $customerNote->note_type,
            priority: $customerNote->priority,
            is_private: $customerNote->is_private,
            is_pinned: $customerNote->is_pinned,
            tags: $customerNote->tags,
            attachments: $customerNote->attachments,
            created_at: $customerNote->created_at,
            updated_at: $customerNote->updated_at,
            customer: $customerNote->customer,
            user: $customerNote->user,
        );
    }

    public static function rules(): array
    {
        return [
            'customer_id' => ['required', 'integer', 'exists:customers,id'],
            'user_id' => ['required', 'integer', 'exists:users,id'],
            'title' => ['required', 'string', 'max:255'],
            'content' => ['required', 'string', 'max:10000'],
            'note_type' => ['required', 'string', 'in:' . implode(',', array_column(CustomerNoteType::cases(), 'value'))],
            'priority' => ['required', 'string', 'in:' . implode(',', array_column(CustomerNotePriority::cases(), 'value'))],
            'is_private' => ['boolean'],
            'is_pinned' => ['boolean'],
            'tags' => ['nullable', 'array'],
            'tags.*' => ['string', 'max:50'],
            'attachments' => ['nullable', 'array'],
        ];
    }

    public static function messages(): array
    {
        return [
            'customer_id.required' => 'Customer ID is required.',
            'customer_id.exists' => 'The selected customer does not exist.',
            'user_id.required' => 'User ID is required.',
            'user_id.exists' => 'The selected user does not exist.',
            'title.required' => 'Note title is required.',
            'title.max' => 'Note title cannot exceed 255 characters.',
            'content.required' => 'Note content is required.',
            'content.max' => 'Note content cannot exceed 10,000 characters.',
            'note_type.required' => 'Note type is required.',
            'note_type.in' => 'Invalid note type selected.',
            'priority.required' => 'Priority is required.',
            'priority.in' => 'Invalid priority selected.',
            'tags.array' => 'Tags must be an array.',
            'tags.*.string' => 'Each tag must be a string.',
            'tags.*.max' => 'Each tag cannot exceed 50 characters.',
            'attachments.array' => 'Attachments must be an array.',
        ];
    }

    public function isHighPriority(): bool
    {
        return in_array($this->priority, [CustomerNotePriority::HIGH, CustomerNotePriority::URGENT]);
    }

    public function isUrgent(): bool
    {
        return $this->priority === CustomerNotePriority::URGENT;
    }

    public function isPublic(): bool
    {
        return !$this->is_private;
    }

    public function hasTags(): bool
    {
        return !empty($this->tags);
    }

    public function hasAttachments(): bool
    {
        return !empty($this->attachments);
    }

    public function getTagCount(): int
    {
        return is_array($this->tags) ? count($this->tags) : 0;
    }

    public function getAttachmentCount(): int
    {
        return is_array($this->attachments) ? count($this->attachments) : 0;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'customer_id' => $this->customer_id,
            'user_id' => $this->user_id,
            'title' => $this->title,
            'content' => $this->content,
            'note_type' => $this->note_type->value,
            'priority' => $this->priority->value,
            'is_private' => $this->is_private,
            'is_pinned' => $this->is_pinned,
            'tags' => $this->tags,
            'attachments' => $this->attachments,
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
            'customer' => $this->customer?->toArray(),
            'user' => $this->user?->toArray(),
        ];
    }
}
