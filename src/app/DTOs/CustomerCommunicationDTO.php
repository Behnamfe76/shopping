<?php

namespace Fereydooni\Shopping\app\DTOs;

use Fereydooni\Shopping\app\Enums\CommunicationChannel;
use Fereydooni\Shopping\app\Enums\CommunicationPriority;
use Fereydooni\Shopping\app\Enums\CommunicationStatus;
use Fereydooni\Shopping\app\Enums\CommunicationType;
use Fereydooni\Shopping\app\Models\Customer;
use Fereydooni\Shopping\app\Models\CustomerCommunication;
use Fereydooni\Shopping\app\Models\CustomerSegment;
use Fereydooni\Shopping\app\Models\User;
use Illuminate\Support\Carbon;
use Spatie\LaravelData\Attributes\Validation\ArrayType;
use Spatie\LaravelData\Attributes\Validation\Date;
use Spatie\LaravelData\Attributes\Validation\In;
use Spatie\LaravelData\Attributes\Validation\IntegerType;
use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Attributes\Validation\Nullable;
use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Attributes\Validation\StringType;
use Spatie\LaravelData\Data;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class CustomerCommunicationDTO extends Data
{
    public function __construct(
        #[Nullable]
        public ?int $id,

        #[Required, IntegerType]
        public int $customer_id,

        #[Required, IntegerType]
        public int $user_id,

        #[Required, StringType, In(['email', 'sms', 'push_notification', 'in_app', 'letter', 'phone_call'])]
        public CommunicationType $communication_type,

        #[Required, StringType, Max(255)]
        public string $subject,

        #[Required, StringType, Max(10000)]
        public string $content,

        #[Required, StringType, In(['draft', 'scheduled', 'sending', 'sent', 'delivered', 'opened', 'clicked', 'bounced', 'unsubscribed', 'cancelled', 'failed'])]
        public CommunicationStatus $status,

        #[Required, StringType, In(['low', 'normal', 'high', 'urgent'])]
        public CommunicationPriority $priority,

        #[Required, StringType, In(['email', 'sms', 'push', 'web', 'mobile', 'mail', 'phone'])]
        public CommunicationChannel $channel,

        #[Nullable, Date]
        public ?Carbon $scheduled_at,

        #[Nullable, Date]
        public ?Carbon $sent_at,

        #[Nullable, Date]
        public ?Carbon $delivered_at,

        #[Nullable, Date]
        public ?Carbon $opened_at,

        #[Nullable, Date]
        public ?Carbon $clicked_at,

        #[Nullable, Date]
        public ?Carbon $bounced_at,

        #[Nullable, Date]
        public ?Carbon $unsubscribed_at,

        #[Nullable, IntegerType]
        public ?int $campaign_id,

        #[Nullable, IntegerType]
        public ?int $segment_id,

        #[Nullable, IntegerType]
        public ?int $template_id,

        #[Nullable, ArrayType]
        public ?array $metadata,

        #[Nullable, ArrayType]
        public ?array $attachments,

        #[Nullable, ArrayType]
        public ?array $tracking_data,

        #[Nullable]
        public ?Carbon $created_at,

        #[Nullable]
        public ?Carbon $updated_at,

        // Relationships
        #[Nullable]
        public ?Customer $customer,

        #[Nullable]
        public ?User $user,

        #[Nullable]
        public ?CustomerSegment $segment,

        #[Nullable]
        public ?array $media
    ) {}

    public static function fromModel(CustomerCommunication $communication): self
    {
        return new self(
            id: $communication->id,
            customer_id: $communication->customer_id,
            user_id: $communication->user_id,
            communication_type: $communication->communication_type,
            subject: $communication->subject,
            content: $communication->content,
            status: $communication->status,
            priority: $communication->priority,
            channel: $communication->channel,
            scheduled_at: $communication->scheduled_at,
            sent_at: $communication->sent_at,
            delivered_at: $communication->delivered_at,
            opened_at: $communication->opened_at,
            clicked_at: $communication->clicked_at,
            bounced_at: $communication->bounced_at,
            unsubscribed_at: $communication->unsubscribed_at,
            campaign_id: $communication->campaign_id,
            segment_id: $communication->segment_id,
            template_id: $communication->template_id,
            metadata: $communication->metadata,
            attachments: $communication->attachments,
            tracking_data: $communication->tracking_data,
            created_at: $communication->created_at,
            updated_at: $communication->updated_at,
            customer: $communication->customer,
            user: $communication->user,
            segment: $communication->segment,
            media: $communication->getMedia('attachments')->map(fn (Media $media) => [
                'id' => $media->id,
                'name' => $media->name,
                'file_name' => $media->file_name,
                'mime_type' => $media->mime_type,
                'size' => $media->size,
                'url' => $media->getUrl(),
                'created_at' => $media->created_at,
            ])->toArray()
        );
    }

    public static function rules(): array
    {
        return [
            'customer_id' => ['required', 'integer', 'exists:customers,id'],
            'user_id' => ['required', 'integer', 'exists:users,id'],
            'communication_type' => ['required', 'string', 'in:email,sms,push_notification,in_app,letter,phone_call'],
            'subject' => ['required', 'string', 'max:255'],
            'content' => ['required', 'string', 'max:10000'],
            'status' => ['required', 'string', 'in:draft,scheduled,sending,sent,delivered,opened,clicked,bounced,unsubscribed,cancelled,failed'],
            'priority' => ['required', 'string', 'in:low,normal,high,urgent'],
            'channel' => ['required', 'string', 'in:email,sms,push,web,mobile,mail,phone'],
            'scheduled_at' => ['nullable', 'date', 'after:now'],
            'sent_at' => ['nullable', 'date'],
            'delivered_at' => ['nullable', 'date'],
            'opened_at' => ['nullable', 'date'],
            'clicked_at' => ['nullable', 'date'],
            'bounced_at' => ['nullable', 'date'],
            'unsubscribed_at' => ['nullable', 'date'],
            'campaign_id' => ['nullable', 'integer', 'exists:campaigns,id'],
            'segment_id' => ['nullable', 'integer', 'exists:customer_segments,id'],
            'template_id' => ['nullable', 'integer', 'exists:communication_templates,id'],
            'metadata' => ['nullable', 'array'],
            'attachments' => ['nullable', 'array'],
            'tracking_data' => ['nullable', 'array'],
        ];
    }

    public static function messages(): array
    {
        return [
            'customer_id.required' => 'Customer ID is required.',
            'customer_id.exists' => 'The selected customer does not exist.',
            'user_id.required' => 'User ID is required.',
            'user_id.exists' => 'The selected user does not exist.',
            'communication_type.required' => 'Communication type is required.',
            'communication_type.in' => 'Invalid communication type.',
            'subject.required' => 'Subject is required.',
            'subject.max' => 'Subject cannot exceed 255 characters.',
            'content.required' => 'Content is required.',
            'content.max' => 'Content cannot exceed 10,000 characters.',
            'status.required' => 'Status is required.',
            'status.in' => 'Invalid status.',
            'priority.required' => 'Priority is required.',
            'priority.in' => 'Invalid priority.',
            'channel.required' => 'Channel is required.',
            'channel.in' => 'Invalid channel.',
            'scheduled_at.after' => 'Scheduled date must be in the future.',
            'campaign_id.exists' => 'The selected campaign does not exist.',
            'segment_id.exists' => 'The selected segment does not exist.',
            'template_id.exists' => 'The selected template does not exist.',
        ];
    }
}
