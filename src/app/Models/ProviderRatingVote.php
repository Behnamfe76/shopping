<?php

namespace Fereydooni\Shopping\App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProviderRatingVote extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'provider_rating_id',
        'user_id',
        'is_helpful',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'is_helpful' => 'boolean',
    ];

    protected $hidden = [
        'ip_address',
        'user_agent',
    ];

    // Relationships
    public function rating(): BelongsTo
    {
        return $this->belongsTo(ProviderRating::class, 'provider_rating_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Methods
    public function isHelpful(): bool
    {
        return $this->is_helpful;
    }

    public function isNotHelpful(): bool
    {
        return ! $this->is_helpful;
    }

    public function toggle(): bool
    {
        $this->update(['is_helpful' => ! $this->is_helpful]);

        return $this->is_helpful;
    }
}
