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
use Illuminate\Support\Carbon;
use Fereydooni\Shopping\app\Enums\SegmentType;
use Fereydooni\Shopping\app\Enums\SegmentStatus;
use Fereydooni\Shopping\app\Enums\SegmentPriority;
use Fereydooni\Shopping\app\Models\CustomerSegment;

class CustomerSegmentDTO extends Data
{
    public function __construct(
        #[Nullable]
        public ?int $id,

        #[Required, StringType, Max(255)]
        public string $name,

        #[Nullable, StringType, Max(1000)]
        public ?string $description,

        #[Required, In(['demographic', 'behavioral', 'geographic', 'psychographic', 'transactional', 'engagement', 'loyalty', 'custom'])]
        public SegmentType $type,

        #[Required, In(['active', 'inactive', 'draft', 'archived'])]
        public SegmentStatus $status,

        #[Required, In(['low', 'normal', 'high', 'critical'])]
        public SegmentPriority $priority,

        #[Nullable]
        public ?array $criteria,

        #[Nullable]
        public ?array $conditions,

        #[IntegerType, Min(0)]
        public int $customer_count,

        #[Nullable, Date]
        public ?Carbon $last_calculated_at,

        #[Nullable, IntegerType]
        public ?int $calculated_by,

        #[BooleanType]
        public bool $is_automatic,

        #[BooleanType]
        public bool $is_dynamic,

        #[BooleanType]
        public bool $is_static,

        #[Nullable]
        public ?array $metadata,

        #[Nullable]
        public ?array $tags,

        #[Nullable, IntegerType]
        public ?int $created_by,

        #[Nullable, IntegerType]
        public ?int $updated_by,

        #[Nullable]
        public ?Carbon $created_at,

        #[Nullable]
        public ?Carbon $updated_at,

        // Computed fields
        #[Nullable]
        public ?string $display_name = null,

        #[Nullable]
        public ?string $full_description = null,

        #[Nullable]
        public ?bool $is_active = null,

        #[Nullable]
        public ?bool $is_draft = null,

        #[Nullable]
        public ?bool $is_archived = null,

        #[Nullable]
        public ?bool $is_urgent = null,

        #[Nullable]
        public ?bool $needs_recalculation = null,

        #[Nullable]
        public ?string $customer_count_formatted = null,

        #[Nullable]
        public ?string $last_calculated_formatted = null,

        #[Nullable]
        public ?string $type_label = null,

        #[Nullable]
        public ?string $type_color = null,

        #[Nullable]
        public ?string $status_label = null,

        #[Nullable]
        public ?string $status_color = null,

        #[Nullable]
        public ?string $priority_label = null,

        #[Nullable]
        public ?string $priority_color = null,

        // Relationships
        #[Nullable]
        public mixed $customers = null,

        #[Nullable]
        public mixed $created_by_user = null,

        #[Nullable]
        public mixed $updated_by_user = null,

        #[Nullable]
        public mixed $calculated_by_user = null,

        #[Nullable]
        public mixed $segment_history = null,
    ) {
    }

    public static function fromModel(CustomerSegment $customerSegment): static
    {
        return new static(
            id: $customerSegment->id,
            name: $customerSegment->name,
            description: $customerSegment->description,
            type: $customerSegment->type,
            status: $customerSegment->status,
            priority: $customerSegment->priority,
            criteria: $customerSegment->criteria,
            conditions: $customerSegment->conditions,
            customer_count: $customerSegment->customer_count,
            last_calculated_at: $customerSegment->last_calculated_at,
            calculated_by: $customerSegment->calculated_by,
            is_automatic: $customerSegment->is_automatic,
            is_dynamic: $customerSegment->is_dynamic,
            is_static: $customerSegment->is_static,
            metadata: $customerSegment->metadata,
            tags: $customerSegment->tags,
            created_by: $customerSegment->created_by,
            updated_by: $customerSegment->updated_by,
            created_at: $customerSegment->created_at,
            updated_at: $customerSegment->updated_at,

            // Computed fields
            display_name: $customerSegment->display_name,
            full_description: $customerSegment->full_description,
            is_active: $customerSegment->is_active,
            is_draft: $customerSegment->is_draft,
            is_archived: $customerSegment->is_archived,
            is_urgent: $customerSegment->is_urgent,
            needs_recalculation: $customerSegment->needs_recalculation,
            customer_count_formatted: $customerSegment->customer_count_formatted,
            last_calculated_formatted: $customerSegment->last_calculated_formatted,
            type_label: $customerSegment->type->label(),
            type_color: $customerSegment->type->color(),
            status_label: $customerSegment->status->label(),
            status_color: $customerSegment->status->color(),
            priority_label: $customerSegment->priority->label(),
            priority_color: $customerSegment->priority->color(),

            // Relationships
            customers: $customerSegment->customers,
            created_by_user: $customerSegment->createdBy,
            updated_by_user: $customerSegment->updatedBy,
            calculated_by_user: $customerSegment->calculatedBy,
            segment_history: $customerSegment->segmentHistory,
        );
    }

    public static function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'type' => ['required', 'string', 'in:demographic,behavioral,geographic,psychographic,transactional,engagement,loyalty,custom'],
            'status' => ['required', 'string', 'in:active,inactive,draft,archived'],
            'priority' => ['required', 'string', 'in:low,normal,high,critical'],
            'criteria' => ['nullable', 'array'],
            'conditions' => ['nullable', 'array'],
            'customer_count' => ['integer', 'min:0'],
            'last_calculated_at' => ['nullable', 'date'],
            'calculated_by' => ['nullable', 'integer', 'exists:users,id'],
            'is_automatic' => ['boolean'],
            'is_dynamic' => ['boolean'],
            'is_static' => ['boolean'],
            'metadata' => ['nullable', 'array'],
            'tags' => ['nullable', 'array'],
            'created_by' => ['nullable', 'integer', 'exists:users,id'],
            'updated_by' => ['nullable', 'integer', 'exists:users,id'],
        ];
    }

    public static function messages(): array
    {
        return [
            'name.required' => 'Segment name is required.',
            'name.string' => 'Segment name must be a string.',
            'name.max' => 'Segment name cannot exceed 255 characters.',
            'description.string' => 'Description must be a string.',
            'description.max' => 'Description cannot exceed 1000 characters.',
            'type.required' => 'Segment type is required.',
            'type.in' => 'Invalid segment type selected.',
            'status.required' => 'Segment status is required.',
            'status.in' => 'Invalid segment status selected.',
            'priority.required' => 'Segment priority is required.',
            'priority.in' => 'Invalid segment priority selected.',
            'criteria.array' => 'Criteria must be an array.',
            'conditions.array' => 'Conditions must be an array.',
            'customer_count.integer' => 'Customer count must be an integer.',
            'customer_count.min' => 'Customer count cannot be negative.',
            'last_calculated_at.date' => 'Last calculated date must be a valid date.',
            'calculated_by.integer' => 'Calculated by must be a valid user ID.',
            'calculated_by.exists' => 'The selected user does not exist.',
            'is_automatic.boolean' => 'Is automatic must be true or false.',
            'is_dynamic.boolean' => 'Is dynamic must be true or false.',
            'is_static.boolean' => 'Is static must be true or false.',
            'metadata.array' => 'Metadata must be an array.',
            'tags.array' => 'Tags must be an array.',
            'created_by.integer' => 'Created by must be a valid user ID.',
            'created_by.exists' => 'The selected user does not exist.',
            'updated_by.integer' => 'Updated by must be a valid user ID.',
            'updated_by.exists' => 'The selected user does not exist.',
        ];
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'type' => $this->type->value,
            'status' => $this->status->value,
            'priority' => $this->priority->value,
            'criteria' => $this->criteria,
            'conditions' => $this->conditions,
            'customer_count' => $this->customer_count,
            'last_calculated_at' => $this->last_calculated_at?->toISOString(),
            'calculated_by' => $this->calculated_by,
            'is_automatic' => $this->is_automatic,
            'is_dynamic' => $this->is_dynamic,
            'is_static' => $this->is_static,
            'metadata' => $this->metadata,
            'tags' => $this->tags,
            'created_by' => $this->created_by,
            'updated_by' => $this->updated_by,
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),

            // Computed fields
            'display_name' => $this->display_name,
            'full_description' => $this->full_description,
            'is_active' => $this->is_active,
            'is_draft' => $this->is_draft,
            'is_archived' => $this->is_archived,
            'is_urgent' => $this->is_urgent,
            'needs_recalculation' => $this->needs_recalculation,
            'customer_count_formatted' => $this->customer_count_formatted,
            'last_calculated_formatted' => $this->last_calculated_formatted,
            'type_label' => $this->type_label,
            'type_color' => $this->type_color,
            'status_label' => $this->status_label,
            'status_color' => $this->status_color,
            'priority_label' => $this->priority_label,
            'priority_color' => $this->priority_color,

            // Relationships
            'customers' => $this->customers,
            'created_by_user' => $this->created_by_user,
            'updated_by_user' => $this->updated_by_user,
            'calculated_by_user' => $this->calculated_by_user,
            'segment_history' => $this->segment_history,
        ];
    }
}
