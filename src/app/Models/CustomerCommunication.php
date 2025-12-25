<?php

namespace Fereydooni\Shopping\app\Models;

use Fereydooni\Shopping\app\Enums\CommunicationChannel;
use Fereydooni\Shopping\app\Enums\CommunicationPriority;
use Fereydooni\Shopping\app\Enums\CommunicationStatus;
use Fereydooni\Shopping\app\Enums\CommunicationType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class CustomerCommunication extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia, SoftDeletes;

    protected $fillable = [
        'customer_id',
        'user_id',
        'communication_type',
        'subject',
        'content',
        'status',
        'priority',
        'channel',
        'scheduled_at',
        'sent_at',
        'delivered_at',
        'opened_at',
        'clicked_at',
        'bounced_at',
        'unsubscribed_at',
        'campaign_id',
        'segment_id',
        'template_id',
        'metadata',
        'tracking_data',
    ];

    protected $casts = [
        'communication_type' => CommunicationType::class,
        'status' => CommunicationStatus::class,
        'priority' => CommunicationPriority::class,
        'channel' => CommunicationChannel::class,
        'scheduled_at' => 'datetime',
        'sent_at' => 'datetime',
        'delivered_at' => 'datetime',
        'opened_at' => 'datetime',
        'clicked_at' => 'datetime',
        'bounced_at' => 'datetime',
        'unsubscribed_at' => 'datetime',
        'metadata' => 'array',
        'tracking_data' => 'array',
    ];

    protected $dates = [
        'scheduled_at',
        'sent_at',
        'delivered_at',
        'opened_at',
        'clicked_at',
        'bounced_at',
        'unsubscribed_at',
    ];

    /**
     * Get the customer that owns the communication.
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * Get the user who sent the communication.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the campaign associated with the communication.
     */
    public function campaign(): BelongsTo
    {
        return $this->belongsTo(Campaign::class);
    }

    /**
     * Get the segment associated with the communication.
     */
    public function segment(): BelongsTo
    {
        return $this->belongsTo(CustomerSegment::class, 'segment_id');
    }

    /**
     * Get the template associated with the communication.
     */
    public function template(): BelongsTo
    {
        return $this->belongsTo(CommunicationTemplate::class, 'template_id');
    }

    /**
     * Register media collections for attachments.
     */
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('attachments')
            ->acceptsMimeTypes(['image/*', 'application/pdf', 'text/plain', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'])
            ->withDisk('public');
    }

    /**
     * Scope a query to only include communications by type.
     */
    public function scopeByType($query, $type)
    {
        return $query->where('communication_type', $type);
    }

    /**
     * Scope a query to only include communications by status.
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope a query to only include communications by priority.
     */
    public function scopeByPriority($query, $priority)
    {
        return $query->where('priority', $priority);
    }

    /**
     * Scope a query to only include communications by channel.
     */
    public function scopeByChannel($query, $channel)
    {
        return $query->where('channel', $channel);
    }

    /**
     * Scope a query to only include scheduled communications.
     */
    public function scopeScheduled($query)
    {
        return $query->where('status', CommunicationStatus::SCHEDULED);
    }

    /**
     * Scope a query to only include sent communications.
     */
    public function scopeSent($query)
    {
        return $query->where('status', CommunicationStatus::SENT);
    }

    /**
     * Scope a query to only include delivered communications.
     */
    public function scopeDelivered($query)
    {
        return $query->where('status', CommunicationStatus::DELIVERED);
    }

    /**
     * Scope a query to only include opened communications.
     */
    public function scopeOpened($query)
    {
        return $query->where('status', CommunicationStatus::OPENED);
    }

    /**
     * Scope a query to only include clicked communications.
     */
    public function scopeClicked($query)
    {
        return $query->where('status', CommunicationStatus::CLICKED);
    }

    /**
     * Scope a query to only include bounced communications.
     */
    public function scopeBounced($query)
    {
        return $query->where('status', CommunicationStatus::BOUNCED);
    }

    /**
     * Scope a query to only include unsubscribed communications.
     */
    public function scopeUnsubscribed($query)
    {
        return $query->where('status', CommunicationStatus::UNSUBSCRIBED);
    }

    /**
     * Check if the communication is scheduled.
     */
    public function isScheduled(): bool
    {
        return $this->status === CommunicationStatus::SCHEDULED;
    }

    /**
     * Check if the communication is sent.
     */
    public function isSent(): bool
    {
        return $this->status === CommunicationStatus::SENT;
    }

    /**
     * Check if the communication is delivered.
     */
    public function isDelivered(): bool
    {
        return $this->status === CommunicationStatus::DELIVERED;
    }

    /**
     * Check if the communication is opened.
     */
    public function isOpened(): bool
    {
        return $this->status === CommunicationStatus::OPENED;
    }

    /**
     * Check if the communication is clicked.
     */
    public function isClicked(): bool
    {
        return $this->status === CommunicationStatus::CLICKED;
    }

    /**
     * Check if the communication is bounced.
     */
    public function isBounced(): bool
    {
        return $this->status === CommunicationStatus::BOUNCED;
    }

    /**
     * Check if the communication is unsubscribed.
     */
    public function isUnsubscribed(): bool
    {
        return $this->status === CommunicationStatus::UNSUBSCRIBED;
    }

    /**
     * Check if the communication is cancelled.
     */
    public function isCancelled(): bool
    {
        return $this->status === CommunicationStatus::CANCELLED;
    }

    /**
     * Check if the communication is failed.
     */
    public function isFailed(): bool
    {
        return $this->status === CommunicationStatus::FAILED;
    }

    /**
     * Get the delivery rate for this communication.
     */
    public function getDeliveryRate(): float
    {
        if (! $this->sent_at) {
            return 0.0;
        }

        return $this->delivered_at ? 100.0 : 0.0;
    }

    /**
     * Get the open rate for this communication.
     */
    public function getOpenRate(): float
    {
        if (! $this->delivered_at) {
            return 0.0;
        }

        return $this->opened_at ? 100.0 : 0.0;
    }

    /**
     * Get the click rate for this communication.
     */
    public function getClickRate(): float
    {
        if (! $this->opened_at) {
            return 0.0;
        }

        return $this->clicked_at ? 100.0 : 0.0;
    }

    /**
     * Get the bounce rate for this communication.
     */
    public function getBounceRate(): float
    {
        if (! $this->sent_at) {
            return 0.0;
        }

        return $this->bounced_at ? 100.0 : 0.0;
    }

    /**
     * Get the unsubscribe rate for this communication.
     */
    public function getUnsubscribeRate(): float
    {
        if (! $this->sent_at) {
            return 0.0;
        }

        return $this->unsubscribed_at ? 100.0 : 0.0;
    }
}
