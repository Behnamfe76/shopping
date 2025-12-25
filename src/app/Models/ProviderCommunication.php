<?php

namespace App\Models;

use App\Enums\CommunicationType;
use App\Enums\Direction;
use App\Enums\Priority;
use App\Enums\Status;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProviderCommunication extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'provider_id',
        'user_id',
        'communication_type',
        'subject',
        'message',
        'direction',
        'status',
        'sent_at',
        'read_at',
        'replied_at',
        'priority',
        'is_urgent',
        'is_archived',
        'attachments',
        'tags',
        'thread_id',
        'parent_id',
        'response_time',
        'satisfaction_rating',
        'notes',
    ];

    protected $casts = [
        'communication_type' => CommunicationType::class,
        'direction' => Direction::class,
        'status' => Status::class,
        'priority' => Priority::class,
        'is_urgent' => 'boolean',
        'is_archived' => 'boolean',
        'attachments' => 'array',
        'tags' => 'array',
        'sent_at' => 'datetime',
        'read_at' => 'datetime',
        'replied_at' => 'datetime',
        'response_time' => 'integer',
        'satisfaction_rating' => 'float',
    ];

    protected $dates = [
        'sent_at',
        'read_at',
        'replied_at',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    // Relationships
    public function provider(): BelongsTo
    {
        return $this->belongsTo(Provider::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(ProviderCommunication::class, 'parent_id');
    }

    public function replies(): HasMany
    {
        return $this->hasMany(ProviderCommunication::class, 'parent_id');
    }

    public function thread(): HasMany
    {
        return $this->hasMany(ProviderCommunication::class, 'thread_id');
    }

    // Scopes
    public function scopeByProvider($query, $providerId)
    {
        return $query->where('provider_id', $providerId);
    }

    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeByType($query, $communicationType)
    {
        return $query->where('communication_type', $communicationType);
    }

    public function scopeByDirection($query, $direction)
    {
        return $query->where('direction', $direction);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeByPriority($query, $priority)
    {
        return $query->where('priority', $priority);
    }

    public function scopeUrgent($query)
    {
        return $query->where('is_urgent', true);
    }

    public function scopeArchived($query)
    {
        return $query->where('is_archived', true);
    }

    public function scopeUnread($query)
    {
        return $query->whereNull('read_at');
    }

    public function scopeUnreplied($query)
    {
        return $query->whereNull('replied_at');
    }

    public function scopeByThread($query, $threadId)
    {
        return $query->where('thread_id', $threadId);
    }

    public function scopeByDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('created_at', [$startDate, $endDate]);
    }

    // Accessors
    public function getIsReadAttribute(): bool
    {
        return ! is_null($this->read_at);
    }

    public function getIsRepliedAttribute(): bool
    {
        return ! is_null($this->replied_at);
    }

    public function getResponseTimeInMinutesAttribute(): ?int
    {
        if (! $this->sent_at || ! $this->replied_at) {
            return null;
        }

        return $this->sent_at->diffInMinutes($this->replied_at);
    }

    public function getDaysSinceSentAttribute(): int
    {
        if (! $this->sent_at) {
            return 0;
        }

        return $this->sent_at->diffInDays(now());
    }

    // Mutators
    public function setTagsAttribute($value)
    {
        if (is_array($value)) {
            $this->attributes['tags'] = json_encode(array_unique($value));
        } else {
            $this->attributes['tags'] = $value;
        }
    }

    public function setAttachmentsAttribute($value)
    {
        if (is_array($value)) {
            $this->attributes['attachments'] = json_encode($value);
        } else {
            $this->attributes['attachments'] = $value;
        }
    }

    // Methods
    public function markAsRead(): bool
    {
        $this->update(['read_at' => now()]);

        return true;
    }

    public function markAsReplied(): bool
    {
        $this->update(['replied_at' => now()]);

        return true;
    }

    public function markAsClosed(): bool
    {
        $this->update(['status' => Status::CLOSED]);

        return true;
    }

    public function archive(): bool
    {
        $this->update(['is_archived' => true]);

        return true;
    }

    public function unarchive(): bool
    {
        $this->update(['is_archived' => false]);

        return true;
    }

    public function setUrgent(): bool
    {
        $this->update(['is_urgent' => true]);

        return true;
    }

    public function unsetUrgent(): bool
    {
        $this->update(['is_urgent' => false]);

        return true;
    }

    public function addTag(string $tag): bool
    {
        $tags = $this->tags ?? [];
        if (! in_array($tag, $tags)) {
            $tags[] = $tag;
            $this->update(['tags' => $tags]);
        }

        return true;
    }

    public function removeTag(string $tag): bool
    {
        $tags = $this->tags ?? [];
        $tags = array_filter($tags, fn ($t) => $t !== $tag);
        $this->update(['tags' => $tags]);

        return true;
    }

    public function addAttachment(string $attachmentPath): bool
    {
        $attachments = $this->attachments ?? [];
        if (! in_array($attachmentPath, $attachments)) {
            $attachments[] = $attachmentPath;
            $this->update(['attachments' => $attachments]);
        }

        return true;
    }

    public function removeAttachment(string $attachmentPath): bool
    {
        $attachments = $this->attachments ?? [];
        $attachments = array_filter($attachments, fn ($a) => $a !== $attachmentPath);
        $this->update(['attachments' => $attachments]);

        return true;
    }

    public function updateSatisfactionRating(float $rating): bool
    {
        if ($rating >= 0 && $rating <= 5) {
            $this->update(['satisfaction_rating' => $rating]);

            return true;
        }

        return false;
    }

    public function calculateResponseTime(): bool
    {
        if ($this->sent_at && $this->replied_at) {
            $responseTime = $this->sent_at->diffInMinutes($this->replied_at);
            $this->update(['response_time' => $responseTime]);

            return true;
        }

        return false;
    }
}
