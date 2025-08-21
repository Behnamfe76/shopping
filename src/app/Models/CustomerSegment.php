<?php

namespace Fereydooni\Shopping\app\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Carbon;
use Fereydooni\Shopping\app\Enums\SegmentType;
use Fereydooni\Shopping\app\Enums\SegmentStatus;
use Fereydooni\Shopping\app\Enums\SegmentPriority;

class CustomerSegment extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'description',
        'type',
        'status',
        'priority',
        'criteria',
        'conditions',
        'customer_count',
        'last_calculated_at',
        'calculated_by',
        'is_automatic',
        'is_dynamic',
        'is_static',
        'metadata',
        'tags',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'type' => SegmentType::class,
        'status' => SegmentStatus::class,
        'priority' => SegmentPriority::class,
        'criteria' => 'array',
        'conditions' => 'array',
        'customer_count' => 'integer',
        'last_calculated_at' => 'datetime',
        'is_automatic' => 'boolean',
        'is_dynamic' => 'boolean',
        'is_static' => 'boolean',
        'metadata' => 'array',
        'tags' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    protected $attributes = [
        'status' => SegmentStatus::DRAFT,
        'priority' => SegmentPriority::NORMAL,
        'customer_count' => 0,
        'is_automatic' => false,
        'is_dynamic' => false,
        'is_static' => true,
        'criteria' => '[]',
        'conditions' => '[]',
        'metadata' => '[]',
        'tags' => '[]',
    ];

    // Relationships
    public function customers(): BelongsToMany
    {
        return $this->belongsToMany(Customer::class, 'customer_segment_customers')
            ->withPivot(['added_at', 'added_by', 'removed_at', 'removed_by'])
            ->withTimestamps();
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function calculatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'calculated_by');
    }

    public function segmentHistory(): HasMany
    {
        return $this->hasMany(CustomerSegmentHistory::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', SegmentStatus::ACTIVE);
    }

    public function scopeInactive($query)
    {
        return $query->where('status', SegmentStatus::INACTIVE);
    }

    public function scopeDraft($query)
    {
        return $query->where('status', SegmentStatus::DRAFT);
    }

    public function scopeArchived($query)
    {
        return $query->where('status', SegmentStatus::ARCHIVED);
    }

    public function scopeByType($query, SegmentType $type)
    {
        return $query->where('type', $type);
    }

    public function scopeByPriority($query, SegmentPriority $priority)
    {
        return $query->where('priority', $priority);
    }

    public function scopeAutomatic($query)
    {
        return $query->where('is_automatic', true);
    }

    public function scopeManual($query)
    {
        return $query->where('is_automatic', false);
    }

    public function scopeDynamic($query)
    {
        return $query->where('is_dynamic', true);
    }

    public function scopeStatic($query)
    {
        return $query->where('is_static', true);
    }

    public function scopeNeedsRecalculation($query, int $daysAgo = 7)
    {
        return $query->where(function ($q) use ($daysAgo) {
            $q->whereNull('last_calculated_at')
              ->orWhere('last_calculated_at', '<', Carbon::now()->subDays($daysAgo));
        });
    }

    public function scopeByCustomerCount($query, int $minCount, int $maxCount = null)
    {
        $query->where('customer_count', '>=', $minCount);
        
        if ($maxCount) {
            $query->where('customer_count', '<=', $maxCount);
        }
        
        return $query;
    }

    public function scopeWithTag($query, string $tag)
    {
        return $query->whereJsonContains('tags', $tag);
    }

    public function scopeSearch($query, string $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
              ->orWhere('description', 'like', "%{$search}%");
        });
    }

    // Accessors
    public function getDisplayNameAttribute(): string
    {
        return $this->name;
    }

    public function getFullDescriptionAttribute(): string
    {
        return $this->description ?: $this->type->description();
    }

    public function getIsActiveAttribute(): bool
    {
        return $this->status->isActive();
    }

    public function getIsDraftAttribute(): bool
    {
        return $this->status->isDraft();
    }

    public function getIsArchivedAttribute(): bool
    {
        return $this->status->isArchived();
    }

    public function getIsUrgentAttribute(): bool
    {
        return $this->priority->isUrgent();
    }

    public function getNeedsRecalculationAttribute(): bool
    {
        if (!$this->last_calculated_at) {
            return true;
        }

        return $this->last_calculated_at->diffInDays(now()) > 7;
    }

    public function getCustomerCountFormattedAttribute(): string
    {
        return number_format($this->customer_count);
    }

    public function getLastCalculatedFormattedAttribute(): string
    {
        return $this->last_calculated_at 
            ? $this->last_calculated_at->diffForHumans()
            : 'Never';
    }

    // Mutators
    public function setNameAttribute($value)
    {
        $this->attributes['name'] = trim($value);
    }

    public function setDescriptionAttribute($value)
    {
        $this->attributes['description'] = $value ? trim($value) : null;
    }

    public function setTagsAttribute($value)
    {
        if (is_string($value)) {
            $value = json_decode($value, true);
        }
        
        if (is_array($value)) {
            $value = array_filter(array_map('trim', $value));
            $value = array_unique($value);
        }
        
        $this->attributes['tags'] = json_encode($value ?: []);
    }

    // Methods
    public function activate(): bool
    {
        return $this->update(['status' => SegmentStatus::ACTIVE]);
    }

    public function deactivate(): bool
    {
        return $this->update(['status' => SegmentStatus::INACTIVE]);
    }

    public function archive(): bool
    {
        return $this->update(['status' => SegmentStatus::ARCHIVED]);
    }

    public function makeAutomatic(): bool
    {
        return $this->update(['is_automatic' => true]);
    }

    public function makeManual(): bool
    {
        return $this->update(['is_automatic' => false]);
    }

    public function makeDynamic(): bool
    {
        return $this->update([
            'is_dynamic' => true,
            'is_static' => false
        ]);
    }

    public function makeStatic(): bool
    {
        return $this->update([
            'is_static' => true,
            'is_dynamic' => false
        ]);
    }

    public function setPriority(SegmentPriority $priority): bool
    {
        return $this->update(['priority' => $priority]);
    }

    public function updateCustomerCount(int $count): bool
    {
        return $this->update([
            'customer_count' => $count,
            'last_calculated_at' => now()
        ]);
    }

    public function addCustomer(int $customerId, int $userId = null): bool
    {
        $pivotData = [
            'added_at' => now(),
            'added_by' => $userId
        ];

        return $this->customers()->attach($customerId, $pivotData);
    }

    public function removeCustomer(int $customerId, int $userId = null): bool
    {
        $pivotData = [
            'removed_at' => now(),
            'removed_by' => $userId
        ];

        return $this->customers()->updateExistingPivot($customerId, $pivotData);
    }

    public function updateCriteria(array $criteria): bool
    {
        return $this->update(['criteria' => $criteria]);
    }

    public function updateConditions(array $conditions): bool
    {
        return $this->update(['conditions' => $conditions]);
    }

    public function addTag(string $tag): bool
    {
        $tags = $this->tags ?: [];
        if (!in_array($tag, $tags)) {
            $tags[] = $tag;
            return $this->update(['tags' => $tags]);
        }
        return true;
    }

    public function removeTag(string $tag): bool
    {
        $tags = $this->tags ?: [];
        $tags = array_filter($tags, fn($t) => $t !== $tag);
        return $this->update(['tags' => array_values($tags)]);
    }

    public function hasTag(string $tag): bool
    {
        return in_array($tag, $this->tags ?: []);
    }

    public function duplicate(string $newName): self
    {
        $duplicate = $this->replicate();
        $duplicate->name = $newName;
        $duplicate->status = SegmentStatus::DRAFT;
        $duplicate->customer_count = 0;
        $duplicate->last_calculated_at = null;
        $duplicate->calculated_by = null;
        $duplicate->save();

        return $duplicate;
    }

    public function canBeEdited(): bool
    {
        return $this->status->canBeEdited();
    }

    public function canBeDeleted(): bool
    {
        return $this->status->canBeDeleted();
    }

    public function canBeUsed(): bool
    {
        return $this->status->canBeUsed();
    }
}
