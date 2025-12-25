<?php

namespace Fereydooni\Shopping\app\DTOs;

use Fereydooni\Shopping\app\Enums\EmployeeNotePriority;
use Fereydooni\Shopping\app\Enums\EmployeeNoteType;
use Fereydooni\Shopping\app\Models\EmployeeNote;
use Illuminate\Support\Carbon;
use Spatie\LaravelData\Attributes\Validation\ArrayType;
use Spatie\LaravelData\Attributes\Validation\BooleanType;
use Spatie\LaravelData\Attributes\Validation\In;
use Spatie\LaravelData\Attributes\Validation\IntegerType;
use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Attributes\Validation\Nullable;
use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Attributes\Validation\StringType;
use Spatie\LaravelData\Data;

class EmployeeNoteDTO extends Data
{
    public function __construct(
        #[Nullable]
        public ?int $id,

        #[Required, IntegerType]
        public int $employee_id,

        #[Required, IntegerType]
        public int $user_id,

        #[Required, StringType, Max(255)]
        public string $title,

        #[Required, StringType, Max(10000)]
        public string $content,

        #[Required, In(EmployeeNoteType::values())]
        public EmployeeNoteType $note_type,

        #[Required, In(EmployeeNotePriority::values())]
        public EmployeeNotePriority $priority,

        #[BooleanType]
        public bool $is_private = false,

        #[BooleanType]
        public bool $is_archived = false,

        #[Nullable, ArrayType]
        public ?array $tags = [],

        #[Nullable, ArrayType]
        public ?array $attachments = [],

        #[Nullable]
        public ?Carbon $created_at = null,

        #[Nullable]
        public ?Carbon $updated_at = null,
    ) {}

    public static function fromModel(EmployeeNote $employeeNote): self
    {
        return new self(
            id: $employeeNote->id,
            employee_id: $employeeNote->employee_id,
            user_id: $employeeNote->user_id,
            title: $employeeNote->title,
            content: $employeeNote->content,
            note_type: $employeeNote->note_type,
            priority: $employeeNote->priority,
            is_private: $employeeNote->is_private,
            is_archived: $employeeNote->is_archived,
            tags: $employeeNote->tags,
            attachments: $employeeNote->attachments,
            created_at: $employeeNote->created_at,
            updated_at: $employeeNote->updated_at,
        );
    }

    public static function rules(): array
    {
        return [
            'employee_id' => ['required', 'integer', 'exists:employees,id'],
            'user_id' => ['required', 'integer', 'exists:users,id'],
            'title' => ['required', 'string', 'max:255'],
            'content' => ['required', 'string', 'max:10000'],
            'note_type' => ['required', 'string', 'in:'.implode(',', EmployeeNoteType::values())],
            'priority' => ['required', 'string', 'in:'.implode(',', EmployeeNotePriority::values())],
            'is_private' => ['boolean'],
            'is_archived' => ['boolean'],
            'tags' => ['nullable', 'array'],
            'tags.*' => ['string', 'max:50'],
            'attachments' => ['nullable', 'array'],
            'attachments.*' => ['string', 'max:500'],
        ];
    }

    public static function messages(): array
    {
        return [
            'employee_id.required' => 'Employee ID is required.',
            'employee_id.exists' => 'The selected employee does not exist.',
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
            'attachments.*.string' => 'Each attachment path must be a string.',
            'attachments.*.max' => 'Each attachment path cannot exceed 500 characters.',
        ];
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'employee_id' => $this->employee_id,
            'user_id' => $this->user_id,
            'title' => $this->title,
            'content' => $this->content,
            'note_type' => $this->note_type->value,
            'note_type_label' => $this->note_type->label(),
            'note_type_color' => $this->note_type->color(),
            'note_type_icon' => $this->note_type->icon(),
            'priority' => $this->priority->value,
            'priority_label' => $this->priority->label(),
            'priority_color' => $this->priority->color(),
            'priority_icon' => $this->priority->icon(),
            'is_private' => $this->is_private,
            'is_archived' => $this->is_archived,
            'tags' => $this->tags,
            'attachments' => $this->attachments,
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}
