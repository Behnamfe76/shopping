<?php

namespace Fereydooni\Shopping\app\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Fereydooni\Shopping\app\Enums\ReviewStatus;
use Illuminate\Support\Carbon;

class ProductReview extends Model
{
    protected $fillable = [
        'product_id',
        'user_id',
        'rating',
        'review',
        'status',
        'title',
        'pros',
        'cons',
        'verified_purchase',
        'helpful_votes',
        'total_votes',
        'sentiment_score',
        'moderation_status',
        'moderation_notes',
        'is_featured',
        'is_verified',
        'review_date',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'rating' => 'integer',
        'verified_purchase' => 'boolean',
        'helpful_votes' => 'integer',
        'total_votes' => 'integer',
        'sentiment_score' => 'float',
        'is_featured' => 'boolean',
        'is_verified' => 'boolean',
        'review_date' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'status' => ReviewStatus::class,
    ];

    protected $attributes = [
        'status' => ReviewStatus::PENDING,
        'verified_purchase' => false,
        'helpful_votes' => 0,
        'total_votes' => 0,
        'sentiment_score' => 0.0,
        'is_featured' => false,
        'is_verified' => false,
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(config('shopping.user_model', 'App\Models\User'));
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(config('shopping.user_model', 'App\Models\User'), 'created_by');
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(config('shopping.user_model', 'App\Models\User'), 'updated_by');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', ReviewStatus::APPROVED);
    }

    public function scopePending($query)
    {
        return $query->where('status', ReviewStatus::PENDING);
    }

    public function scopeRejected($query)
    {
        return $query->where('status', ReviewStatus::REJECTED);
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopeVerified($query)
    {
        return $query->where('is_verified', true);
    }

    public function scopeByRating($query, int $rating)
    {
        return $query->where('rating', $rating);
    }

    public function scopeByProduct($query, int $productId)
    {
        return $query->where('product_id', $productId);
    }

    public function scopeByUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeRecent($query, int $days = 30)
    {
        return $query->where('created_at', '>=', Carbon::now()->subDays($days));
    }

    public function scopePopular($query)
    {
        return $query->orderBy('helpful_votes', 'desc');
    }

    public function scopePositive($query)
    {
        return $query->where('sentiment_score', '>', 0.3);
    }

    public function scopeNegative($query)
    {
        return $query->where('sentiment_score', '<', -0.3);
    }

    public function scopeNeutral($query)
    {
        return $query->whereBetween('sentiment_score', [-0.3, 0.3]);
    }

    public function isApproved(): bool
    {
        return $this->status === ReviewStatus::APPROVED;
    }

    public function isPending(): bool
    {
        return $this->status === ReviewStatus::PENDING;
    }

    public function isRejected(): bool
    {
        return $this->status === ReviewStatus::REJECTED;
    }

    public function isFeatured(): bool
    {
        return $this->is_featured;
    }

    public function isVerified(): bool
    {
        return $this->is_verified;
    }

    public function isVerifiedPurchase(): bool
    {
        return $this->verified_purchase;
    }

    public function getHelpfulPercentage(): float
    {
        if ($this->total_votes === 0) {
            return 0.0;
        }

        return round(($this->helpful_votes / $this->total_votes) * 100, 2);
    }

    public function getSentimentLabel(): string
    {
        if ($this->sentiment_score > 0.3) {
            return 'Positive';
        } elseif ($this->sentiment_score < -0.3) {
            return 'Negative';
        } else {
            return 'Neutral';
        }
    }
}
