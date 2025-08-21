<?php

namespace Fereydooni\Shopping\app\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Carbon;

class CustomerSegmentHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_segment_id',
        'action',
        'description',
        'old_values',
        'new_values',
        'performed_by',
        'performed_at',
        'metadata',
    ];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
        'performed_at' => 'datetime',
        'metadata' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    protected $attributes = [
        'performed_at' => 'now',
        'metadata' => '[]',
    ];

    // Relationships
    public function customerSegment(): BelongsTo
    {
        return $this->belongsTo(CustomerSegment::class);
    }

    public function performedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'performed_by');
    }

    // Scopes
    public function scopeByAction($query, string $action)
    {
        return $query->where('action', $action);
    }

    public function scopeByPerformer($query, int $userId)
    {
        return $query->where('performed_by', $userId);
    }

    public function scopeRecent($query, int $days = 30)
    {
        return $query->where('performed_at', '>=', Carbon::now()->subDays($days));
    }

    // Accessors
    public function getActionLabelAttribute(): string
    {
        return ucfirst(str_replace('_', ' ', $this->action));
    }

    public function getPerformedAtFormattedAttribute(): string
    {
        return $this->performed_at->diffForHumans();
    }

    public function getChangesAttribute(): array
    {
        if (!$this->old_values || !$this->new_values) {
            return [];
        }

        $changes = [];
        foreach ($this->new_values as $key => $newValue) {
            $oldValue = $this->old_values[$key] ?? null;
            if ($oldValue !== $newValue) {
                $changes[$key] = [
                    'old' => $oldValue,
                    'new' => $newValue,
                ];
            }
        }

        return $changes;
    }

    // Methods
    public static function logAction(
        CustomerSegment $segment,
        string $action,
        string $description,
        array $oldValues = [],
        array $newValues = [],
        int $performedBy = null,
        array $metadata = []
    ): self {
        return self::create([
            'customer_segment_id' => $segment->id,
            'action' => $action,
            'description' => $description,
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'performed_by' => $performedBy,
            'performed_at' => now(),
            'metadata' => $metadata,
        ]);
    }
}
