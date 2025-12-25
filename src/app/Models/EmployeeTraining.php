<?php

namespace Fereydooni\Shopping\Models;

use Fereydooni\Shopping\Enums\TrainingMethod;
use Fereydooni\Shopping\Enums\TrainingStatus;
use Fereydooni\Shopping\Enums\TrainingType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class EmployeeTraining extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'employee_trainings';

    protected $fillable = [
        'employee_id',
        'training_type',
        'training_name',
        'provider',
        'description',
        'start_date',
        'end_date',
        'completion_date',
        'status',
        'score',
        'grade',
        'certificate_number',
        'certificate_url',
        'hours_completed',
        'total_hours',
        'cost',
        'is_mandatory',
        'is_certification',
        'is_renewable',
        'renewal_date',
        'expiry_date',
        'instructor',
        'location',
        'training_method',
        'materials',
        'notes',
        'attachments',
        'failure_reason',
        'cancellation_reason',
    ];

    protected $casts = [
        'training_type' => TrainingType::class,
        'status' => TrainingStatus::class,
        'training_method' => TrainingMethod::class,
        'start_date' => 'date',
        'end_date' => 'date',
        'completion_date' => 'date',
        'renewal_date' => 'date',
        'expiry_date' => 'date',
        'score' => 'decimal:2',
        'cost' => 'decimal:2',
        'hours_completed' => 'decimal:2',
        'total_hours' => 'decimal:2',
        'is_mandatory' => 'boolean',
        'is_certification' => 'boolean',
        'is_renewable' => 'boolean',
        'materials' => 'array',
        'attachments' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    protected $attributes = [
        'status' => TrainingStatus::NOT_STARTED,
        'is_mandatory' => false,
        'is_certification' => false,
        'is_renewable' => false,
        'hours_completed' => 0.00,
        'score' => 0.00,
    ];

    /**
     * Get the employee that owns the training.
     */
    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    /**
     * Get the completion percentage.
     */
    public function getCompletionPercentageAttribute(): float
    {
        if ($this->total_hours <= 0) {
            return 0;
        }

        return round(($this->hours_completed / $this->total_hours) * 100, 2);
    }

    /**
     * Get the remaining hours.
     */
    public function getRemainingHoursAttribute(): float
    {
        return max(0, $this->total_hours - $this->hours_completed);
    }

    /**
     * Check if training is expired.
     */
    public function isExpired(): bool
    {
        if (! $this->expiry_date) {
            return false;
        }

        return $this->expiry_date->isPast();
    }

    /**
     * Check if training is expiring soon.
     */
    public function isExpiringSoon(int $days = 30): bool
    {
        if (! $this->expiry_date) {
            return false;
        }

        return $this->expiry_date->diffInDays(now()) <= $days;
    }

    /**
     * Check if training can be renewed.
     */
    public function canBeRenewed(): bool
    {
        return $this->is_certification && $this->is_renewable && $this->status === TrainingStatus::COMPLETED;
    }

    /**
     * Check if training is overdue.
     */
    public function isOverdue(): bool
    {
        if (! $this->end_date) {
            return false;
        }

        return $this->end_date->isPast() && $this->status !== TrainingStatus::COMPLETED;
    }

    /**
     * Get days until expiry.
     */
    public function getDaysUntilExpiryAttribute(): ?int
    {
        if (! $this->expiry_date) {
            return null;
        }

        return $this->expiry_date->diffInDays(now(), false);
    }

    /**
     * Get days until start.
     */
    public function getDaysUntilStartAttribute(): ?int
    {
        if (! $this->start_date) {
            return null;
        }

        return $this->start_date->diffInDays(now(), false);
    }

    /**
     * Get days until end.
     */
    public function getDaysUntilEndAttribute(): ?int
    {
        if (! $this->end_date) {
            return null;
        }

        return $this->end_date->diffInDays(now(), false);
    }

    /**
     * Scope for active trainings.
     */
    public function scopeActive($query)
    {
        return $query->whereIn('status', [TrainingStatus::NOT_STARTED, TrainingStatus::IN_PROGRESS]);
    }

    /**
     * Scope for completed trainings.
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', TrainingStatus::COMPLETED);
    }

    /**
     * Scope for mandatory trainings.
     */
    public function scopeMandatory($query)
    {
        return $query->where('is_mandatory', true);
    }

    /**
     * Scope for certifications.
     */
    public function scopeCertifications($query)
    {
        return $query->where('is_certification', true);
    }

    /**
     * Scope for expiring soon.
     */
    public function scopeExpiringSoon($query, int $days = 30)
    {
        return $query->where('expiry_date', '<=', now()->addDays($days))
            ->where('expiry_date', '>', now());
    }

    /**
     * Scope for overdue trainings.
     */
    public function scopeOverdue($query)
    {
        return $query->where('end_date', '<', now())
            ->whereNotIn('status', [TrainingStatus::COMPLETED, TrainingStatus::CANCELLED]);
    }

    /**
     * Boot method for model events.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($training) {
            if (! $training->start_date && $training->status === TrainingStatus::IN_PROGRESS) {
                $training->start_date = now();
            }
        });

        static::updating(function ($training) {
            if ($training->isDirty('status')) {
                if ($training->status === TrainingStatus::IN_PROGRESS && ! $training->start_date) {
                    $training->start_date = now();
                }

                if ($training->status === TrainingStatus::COMPLETED && ! $training->completion_date) {
                    $training->completion_date = now();
                }
            }
        });
    }
}
