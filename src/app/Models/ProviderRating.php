<?php

namespace Fereydooni\Shopping\App\Models;

use Fereydooni\Shopping\App\Enums\RatingCategory;
use Fereydooni\Shopping\App\Enums\RatingStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProviderRating extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'provider_id',
        'user_id',
        'rating_value',
        'max_rating',
        'category',
        'title',
        'comment',
        'pros',
        'cons',
        'would_recommend',
        'rating_criteria',
        'helpful_votes',
        'total_votes',
        'is_verified',
        'status',
        'ip_address',
        'user_agent',
        'moderator_id',
        'moderation_notes',
        'rejection_reason',
        'flag_reason',
        'verified_at',
        'moderated_at',
    ];

    protected $casts = [
        'rating_value' => 'decimal:2',
        'max_rating' => 'integer',
        'category' => RatingCategory::class,
        'status' => RatingStatus::class,
        'rating_criteria' => 'array',
        'pros' => 'array',
        'cons' => 'array',
        'would_recommend' => 'boolean',
        'is_verified' => 'boolean',
        'helpful_votes' => 'integer',
        'total_votes' => 'integer',
        'verified_at' => 'datetime',
        'moderated_at' => 'datetime',
    ];

    protected $dates = [
        'verified_at',
        'moderated_at',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $hidden = [
        'ip_address',
        'user_agent',
        'moderator_id',
        'moderation_notes',
        'rejection_reason',
        'flag_reason',
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

    public function moderator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'moderator_id');
    }

    public function votes(): HasMany
    {
        return $this->hasMany(ProviderRatingVote::class);
    }

    // Scopes
    public function scopeApproved(Builder $query): void
    {
        $query->where('status', RatingStatus::APPROVED);
    }

    public function scopePending(Builder $query): void
    {
        $query->where('status', RatingStatus::PENDING);
    }

    public function scopeRejected(Builder $query): void
    {
        $query->where('status', RatingStatus::REJECTED);
    }

    public function scopeFlagged(Builder $query): void
    {
        $query->where('status', RatingStatus::FLAGGED);
    }

    public function scopeVerified(Builder $query): void
    {
        $query->where('is_verified', true);
    }

    public function scopeByCategory(Builder $query, RatingCategory $category): void
    {
        $query->where('category', $category);
    }

    public function scopeByProvider(Builder $query, int $providerId): void
    {
        $query->where('provider_id', $providerId);
    }

    public function scopeByUser(Builder $query, int $userId): void
    {
        $query->where('user_id', $userId);
    }

    public function scopeRecommended(Builder $query): void
    {
        $query->where('would_recommend', true);
    }

    public function scopeByRatingRange(Builder $query, float $minRating, float $maxRating): void
    {
        $query->whereBetween('rating_value', [$minRating, $maxRating]);
    }

    public function scopeByDateRange(Builder $query, string $startDate, string $endDate): void
    {
        $query->whereBetween('created_at', [$startDate, $endDate]);
    }

    // Accessors
    public function getRatingPercentageAttribute(): float
    {
        if ($this->max_rating <= 0) {
            return 0;
        }

        return round(($this->rating_value / $this->max_rating) * 100, 2);
    }

    public function getRatingStarsAttribute(): float
    {
        if ($this->max_rating <= 0) {
            return 0;
        }

        return round(($this->rating_value / $this->max_rating) * 5, 1);
    }

    public function getHelpfulPercentageAttribute(): float
    {
        if ($this->total_votes <= 0) {
            return 0;
        }

        return round(($this->helpful_votes / $this->total_votes) * 100, 2);
    }

    public function getFormattedRatingAttribute(): string
    {
        return match ($this->category) {
            RatingCategory::OVERALL => "{$this->rating_stars}/5 stars",
            RatingCategory::QUALITY => "{$this->rating_value}/{$this->max_rating}",
            RatingCategory::SERVICE => "{$this->rating_value}/{$this->max_rating}",
            default => "{$this->rating_percentage}%",
        };
    }

    public function getStatusBadgeAttribute(): string
    {
        $color = $this->status->getColor();
        $description = $this->status->getDescription();

        return "<span class=\"badge badge-{$color}\">{$description}</span>";
    }

    // Methods
    public function isApproved(): bool
    {
        return $this->status === RatingStatus::APPROVED;
    }

    public function isPending(): bool
    {
        return $this->status === RatingStatus::PENDING;
    }

    public function isRejected(): bool
    {
        return $this->status === RatingStatus::REJECTED;
    }

    public function isFlagged(): bool
    {
        return $this->status === RatingStatus::FLAGGED;
    }

    public function isVerified(): bool
    {
        return $this->is_verified;
    }

    public function isRecommended(): bool
    {
        return $this->would_recommend;
    }

    public function canBeVotedOn(): bool
    {
        return $this->isApproved() && $this->isVerified();
    }

    public function approve(int $moderatorId, ?string $notes = null): bool
    {
        $this->update([
            'status' => RatingStatus::APPROVED,
            'moderator_id' => $moderatorId,
            'moderation_notes' => $notes,
            'moderated_at' => now(),
        ]);

        return true;
    }

    public function reject(int $moderatorId, string $reason, ?string $notes = null): bool
    {
        $this->update([
            'status' => RatingStatus::REJECTED,
            'moderator_id' => $moderatorId,
            'rejection_reason' => $reason,
            'moderation_notes' => $notes,
            'moderated_at' => now(),
        ]);

        return true;
    }

    public function flag(int $moderatorId, string $reason, ?string $notes = null): bool
    {
        $this->update([
            'status' => RatingStatus::FLAGGED,
            'moderator_id' => $moderatorId,
            'flag_reason' => $reason,
            'moderation_notes' => $notes,
            'moderated_at' => now(),
        ]);

        return true;
    }

    public function verify(): bool
    {
        $this->update([
            'is_verified' => true,
            'verified_at' => now(),
        ]);

        return true;
    }

    public function addHelpfulVote(int $userId): bool
    {
        $existingVote = $this->votes()->where('user_id', $userId)->first();

        if ($existingVote) {
            if ($existingVote->is_helpful) {
                return false; // Already voted helpful
            }
            $existingVote->update(['is_helpful' => true]);
        } else {
            $this->votes()->create([
                'user_id' => $userId,
                'is_helpful' => true,
            ]);
        }

        $this->increment('helpful_votes');
        $this->increment('total_votes');

        return true;
    }

    public function removeHelpfulVote(int $userId): bool
    {
        $existingVote = $this->votes()->where('user_id', $userId)->first();

        if (! $existingVote || ! $existingVote->is_helpful) {
            return false;
        }

        $existingVote->update(['is_helpful' => false]);
        $this->decrement('helpful_votes');

        return true;
    }

    public function addVote(int $userId, bool $isHelpful): bool
    {
        $existingVote = $this->votes()->where('user_id', $userId)->first();

        if ($existingVote) {
            $oldHelpful = $existingVote->is_helpful;
            $existingVote->update(['is_helpful' => $isHelpful]);

            if ($oldHelpful && ! $isHelpful) {
                $this->decrement('helpful_votes');
            } elseif (! $oldHelpful && $isHelpful) {
                $this->increment('helpful_votes');
            }
        } else {
            $this->votes()->create([
                'user_id' => $userId,
                'is_helpful' => $isHelpful,
            ]);

            if ($isHelpful) {
                $this->increment('helpful_votes');
            }
            $this->increment('total_votes');
        }

        return true;
    }

    // Static methods
    public static function getAverageRating(int $providerId, ?RatingCategory $category = null): float
    {
        $query = static::where('provider_id', $providerId)
            ->where('status', RatingStatus::APPROVED)
            ->where('is_verified', true);

        if ($category) {
            $query->where('category', $category);
        }

        return $query->avg('rating_value') ?? 0;
    }

    public static function getRatingCount(int $providerId, ?RatingCategory $category = null): int
    {
        $query = static::where('provider_id', $providerId)
            ->where('status', RatingStatus::APPROVED)
            ->where('is_verified', true);

        if ($category) {
            $query->where('category', $category);
        }

        return $query->count();
    }

    public static function getRecommendationPercentage(int $providerId): float
    {
        $total = static::where('provider_id', $providerId)
            ->where('status', RatingStatus::APPROVED)
            ->where('is_verified', true)
            ->count();

        if ($total === 0) {
            return 0;
        }

        $recommended = static::where('provider_id', $providerId)
            ->where('status', RatingStatus::APPROVED)
            ->where('is_verified', true)
            ->where('would_recommend', true)
            ->count();

        return round(($recommended / $total) * 100, 2);
    }
}
