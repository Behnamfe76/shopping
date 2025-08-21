<?php

namespace Fereydooni\Shopping\app\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Fereydooni\Shopping\app\Enums\CustomerNoteType;
use Fereydooni\Shopping\app\Enums\CustomerNotePriority;
use Illuminate\Support\Facades\Config;

class CustomerNote extends Model implements HasMedia
{
    use SoftDeletes, InteractsWithMedia;

    protected $fillable = [
        'customer_id',
        'user_id',
        'title',
        'content',
        'note_type',
        'priority',
        'is_private',
        'is_pinned',
        'tags',
        'attachments',
    ];

    protected $casts = [
        'note_type' => CustomerNoteType::class,
        'priority' => CustomerNotePriority::class,
        'is_private' => 'boolean',
        'is_pinned' => 'boolean',
        'tags' => 'array',
        'attachments' => 'array',
    ];

    protected $attributes = [
        'note_type' => CustomerNoteType::GENERAL,
        'priority' => CustomerNotePriority::MEDIUM,
        'is_private' => false,
        'is_pinned' => false,
    ];

    // Relationships
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(Config::get('auth.providers.users.model'));
    }

    // Scopes
    public function scopeByCustomer($query, int $customerId)
    {
        return $query->where('customer_id', $customerId);
    }

    public function scopeByUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeByType($query, CustomerNoteType $type)
    {
        return $query->where('note_type', $type);
    }

    public function scopeByPriority($query, CustomerNotePriority $priority)
    {
        return $query->where('priority', $priority);
    }

    public function scopePublic($query)
    {
        return $query->where('is_private', false);
    }

    public function scopePrivate($query)
    {
        return $query->where('is_private', true);
    }

    public function scopePinned($query)
    {
        return $query->where('is_pinned', true);
    }

    public function scopeByDateRange($query, string $startDate, string $endDate)
    {
        return $query->whereBetween('created_at', [$startDate, $endDate]);
    }

    public function scopeByTag($query, string $tag)
    {
        return $query->whereJsonContains('tags', $tag);
    }

    public function scopeSearch($query, string $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('title', 'like', "%{$search}%")
              ->orWhere('content', 'like', "%{$search}%")
              ->orWhereJsonContains('tags', $search);
        });
    }

    // Accessors
    public function getIsHighPriorityAttribute(): bool
    {
        return $this->priority->isHigh();
    }

    public function getIsUrgentAttribute(): bool
    {
        return $this->priority->isUrgent();
    }

    public function getIsPublicAttribute(): bool
    {
        return !$this->is_private;
    }

    public function getHasTagsAttribute(): bool
    {
        return !empty($this->tags);
    }

    public function getHasAttachmentsAttribute(): bool
    {
        return !empty($this->attachments);
    }

    public function getTagCountAttribute(): int
    {
        return is_array($this->tags) ? count($this->tags) : 0;
    }

    public function getAttachmentCountAttribute(): int
    {
        return is_array($this->attachments) ? count($this->attachments) : 0;
    }

    // Methods
    public function pin(): bool
    {
        return $this->update(['is_pinned' => true]);
    }

    public function unpin(): bool
    {
        return $this->update(['is_pinned' => false]);
    }

    public function makePrivate(): bool
    {
        return $this->update(['is_private' => true]);
    }

    public function makePublic(): bool
    {
        return $this->update(['is_private' => false]);
    }

    public function addTag(string $tag): bool
    {
        $tags = $this->tags ?? [];
        if (!in_array($tag, $tags)) {
            $tags[] = $tag;
            return $this->update(['tags' => $tags]);
        }
        return true;
    }

    public function removeTag(string $tag): bool
    {
        $tags = $this->tags ?? [];
        $tags = array_filter($tags, fn($t) => $t !== $tag);
        return $this->update(['tags' => array_values($tags)]);
    }

    public function hasTag(string $tag): bool
    {
        return in_array($tag, $this->tags ?? []);
    }

    public function canBeViewedBy(int $userId): bool
    {
        // Public notes can be viewed by anyone
        if (!$this->is_private) {
            return true;
        }

        // Private notes can only be viewed by the creator or admin
        return $this->user_id === $userId || auth()->user()->hasRole('admin');
    }

    public function canBeEditedBy(int $userId): bool
    {
        // Only the creator or admin can edit notes
        return $this->user_id === $userId || auth()->user()->hasRole('admin');
    }

    public function canBeDeletedBy(int $userId): bool
    {
        // Only the creator or admin can delete notes
        return $this->user_id === $userId || auth()->user()->hasRole('admin');
    }

    // Media Library
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('attachments')
            ->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/gif', 'application/pdf', 'text/plain'])
            ->useDisk('public');
    }
}
