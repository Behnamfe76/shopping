<?php

namespace Fereydooni\Shopping\app\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class CommunicationTemplate extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'name',
        'description',
        'type',
        'subject',
        'content',
        'status',
        'variables',
        'settings',
        'metadata',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'variables' => 'array',
        'settings' => 'array',
        'metadata' => 'array',
    ];

    /**
     * Get the customer communications that use this template.
     */
    public function customerCommunications(): HasMany
    {
        return $this->hasMany(CustomerCommunication::class, 'template_id');
    }

    /**
     * Register media collections for template assets.
     */
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('assets')
            ->acceptsMimeTypes(['image/*', 'application/pdf', 'text/plain'])
            ->withDisk('public');
    }

    /**
     * Scope a query to only include templates by type.
     */
    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Scope a query to only include templates by status.
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope a query to only include active templates.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope a query to only include inactive templates.
     */
    public function scopeInactive($query)
    {
        return $query->where('status', 'inactive');
    }

    /**
     * Scope a query to only include draft templates.
     */
    public function scopeDraft($query)
    {
        return $query->where('status', 'draft');
    }

    /**
     * Get the total number of communications using this template.
     */
    public function getCommunicationsCountAttribute(): int
    {
        return $this->customerCommunications()->count();
    }

    /**
     * Get the total number of sent communications using this template.
     */
    public function getSentCommunicationsCountAttribute(): int
    {
        return $this->customerCommunications()->where('status', 'sent')->count();
    }

    /**
     * Get the total number of delivered communications using this template.
     */
    public function getDeliveredCommunicationsCountAttribute(): int
    {
        return $this->customerCommunications()->where('status', 'delivered')->count();
    }

    /**
     * Get the total number of opened communications using this template.
     */
    public function getOpenedCommunicationsCountAttribute(): int
    {
        return $this->customerCommunications()->where('status', 'opened')->count();
    }

    /**
     * Get the total number of clicked communications using this template.
     */
    public function getClickedCommunicationsCountAttribute(): int
    {
        return $this->customerCommunications()->where('status', 'clicked')->count();
    }

    /**
     * Get the delivery rate for this template.
     */
    public function getDeliveryRateAttribute(): float
    {
        $sentCount = $this->sentCommunicationsCount;
        $deliveredCount = $this->deliveredCommunicationsCount;

        return $sentCount > 0 ? ($deliveredCount / $sentCount) * 100 : 0;
    }

    /**
     * Get the open rate for this template.
     */
    public function getOpenRateAttribute(): float
    {
        $deliveredCount = $this->deliveredCommunicationsCount;
        $openedCount = $this->openedCommunicationsCount;

        return $deliveredCount > 0 ? ($openedCount / $deliveredCount) * 100 : 0;
    }

    /**
     * Get the click rate for this template.
     */
    public function getClickRateAttribute(): float
    {
        $openedCount = $this->openedCommunicationsCount;
        $clickedCount = $this->clickedCommunicationsCount;

        return $openedCount > 0 ? ($clickedCount / $openedCount) * 100 : 0;
    }

    /**
     * Check if the template is active.
     */
    public function getIsActiveAttribute(): bool
    {
        return $this->status === 'active';
    }

    /**
     * Check if the template is inactive.
     */
    public function getIsInactiveAttribute(): bool
    {
        return $this->status === 'inactive';
    }

    /**
     * Check if the template is in draft status.
     */
    public function getIsDraftAttribute(): bool
    {
        return $this->status === 'draft';
    }
}
