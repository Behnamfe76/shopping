<?php

namespace Fereydooni\Shopping\app\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Illuminate\Support\Carbon;

class Campaign extends Model implements HasMedia
{
    use HasFactory, SoftDeletes, InteractsWithMedia;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'name',
        'description',
        'type',
        'status',
        'start_date',
        'end_date',
        'target_audience',
        'settings',
        'metadata',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'target_audience' => 'array',
        'settings' => 'array',
        'metadata' => 'array',
    ];

    /**
     * Get the customer communications for this campaign.
     */
    public function customerCommunications(): HasMany
    {
        return $this->hasMany(CustomerCommunication::class);
    }

    /**
     * Register media collections for campaign assets.
     */
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('assets')
            ->acceptsMimeTypes(['image/*', 'application/pdf', 'text/plain'])
            ->withDisk('public');
    }

    /**
     * Scope a query to only include campaigns by type.
     */
    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Scope a query to only include campaigns by status.
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope a query to only include active campaigns.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope a query to only include draft campaigns.
     */
    public function scopeDraft($query)
    {
        return $query->where('status', 'draft');
    }

    /**
     * Scope a query to only include completed campaigns.
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    /**
     * Scope a query to only include campaigns within a date range.
     */
    public function scopeInDateRange($query, $startDate, $endDate)
    {
        return $query->where(function ($q) use ($startDate, $endDate) {
            $q->whereBetween('start_date', [$startDate, $endDate])
              ->orWhereBetween('end_date', [$startDate, $endDate])
              ->orWhere(function ($subQ) use ($startDate, $endDate) {
                  $subQ->where('start_date', '<=', $startDate)
                       ->where('end_date', '>=', $endDate);
              });
        });
    }

    /**
     * Scope a query to only include currently running campaigns.
     */
    public function scopeCurrentlyRunning($query)
    {
        $now = Carbon::now();
        return $query->where('status', 'active')
                    ->where('start_date', '<=', $now)
                    ->where(function ($q) use ($now) {
                        $q->whereNull('end_date')
                          ->orWhere('end_date', '>=', $now);
                    });
    }

    /**
     * Get the total number of communications for this campaign.
     */
    public function getCommunicationsCountAttribute(): int
    {
        return $this->customerCommunications()->count();
    }

    /**
     * Get the total number of sent communications for this campaign.
     */
    public function getSentCommunicationsCountAttribute(): int
    {
        return $this->customerCommunications()->where('status', 'sent')->count();
    }

    /**
     * Get the total number of delivered communications for this campaign.
     */
    public function getDeliveredCommunicationsCountAttribute(): int
    {
        return $this->customerCommunications()->where('status', 'delivered')->count();
    }

    /**
     * Get the total number of opened communications for this campaign.
     */
    public function getOpenedCommunicationsCountAttribute(): int
    {
        return $this->customerCommunications()->where('status', 'opened')->count();
    }

    /**
     * Get the total number of clicked communications for this campaign.
     */
    public function getClickedCommunicationsCountAttribute(): int
    {
        return $this->customerCommunications()->where('status', 'clicked')->count();
    }

    /**
     * Get the delivery rate for this campaign.
     */
    public function getDeliveryRateAttribute(): float
    {
        $sentCount = $this->sentCommunicationsCount;
        $deliveredCount = $this->deliveredCommunicationsCount;

        return $sentCount > 0 ? ($deliveredCount / $sentCount) * 100 : 0;
    }

    /**
     * Get the open rate for this campaign.
     */
    public function getOpenRateAttribute(): float
    {
        $deliveredCount = $this->delivered_communications_count;
        $openedCount = $this->opened_communications_count;

        return $deliveredCount > 0 ? ($openedCount / $deliveredCount) * 100 : 0;
    }

    /**
     * Get the click rate for this campaign.
     */
    public function getClickRateAttribute(): float
    {
        $openedCount = $this->opened_communications_count;
        $clickedCount = $this->clicked_communications_count;

        return $openedCount > 0 ? ($clickedCount / $openedCount) * 100 : 0;
    }

    /**
     * Check if the campaign is currently active.
     */
    public function getIsActiveAttribute(): bool
    {
        if ($this->status !== 'active') {
            return false;
        }

        $now = Carbon::now();

        if ($this->start_date && $this->start_date->gt($now)) {
            return false;
        }

        if ($this->end_date && $this->end_date->lt($now)) {
            return false;
        }

        return true;
    }

    /**
     * Check if the campaign is scheduled for the future.
     */
    public function getIsScheduledAttribute(): bool
    {
        return $this->status === 'draft' &&
               $this->start_date &&
               $this->start_date->gt(Carbon::now());
    }

    /**
     * Check if the campaign has ended.
     */
    public function getHasEndedAttribute(): bool
    {
        return $this->end_date && $this->end_date->lt(Carbon::now());
    }
}
