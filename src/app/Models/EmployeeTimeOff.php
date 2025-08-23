<?php

namespace Fereydooni\Shopping\app\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Fereydooni\Shopping\app\Enums\TimeOffType;
use Fereydooni\Shopping\app\Enums\TimeOffStatus;

class EmployeeTimeOff extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'employee_id',
        'user_id',
        'time_off_type',
        'start_date',
        'end_date',
        'start_time',
        'end_time',
        'total_hours',
        'total_days',
        'reason',
        'description',
        'status',
        'approved_by',
        'approved_at',
        'rejected_by',
        'rejected_at',
        'rejection_reason',
        'is_half_day',
        'is_urgent',
        'attachments',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'time_off_type' => TimeOffType::class,
        'status' => TimeOffStatus::class,
        'approved_at' => 'datetime',
        'rejected_at' => 'datetime',
        'is_half_day' => 'boolean',
        'is_urgent' => 'boolean',
        'attachments' => 'array',
        'total_hours' => 'decimal:2',
        'total_days' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    protected $hidden = [
        'deleted_at',
    ];

    // Relationships
    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function rejector(): BelongsTo
    {
        return $this->belongsTo(User::class, 'rejected_by');
    }

    // Scopes
    public function scopeByEmployee($query, int $employeeId)
    {
        return $query->where('employee_id', $employeeId);
    }

    public function scopeByUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeByStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    public function scopeByTimeOffType($query, string $timeOffType)
    {
        return $query->where('time_off_type', $timeOffType);
    }

    public function scopeByDateRange($query, string $startDate, string $endDate)
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

    public function scopePending($query)
    {
        return $query->where('status', TimeOffStatus::PENDING);
    }

    public function scopeApproved($query)
    {
        return $query->where('status', TimeOffStatus::APPROVED);
    }

    public function scopeRejected($query)
    {
        return $query->where('status', TimeOffStatus::REJECTED);
    }

    public function scopeCancelled($query)
    {
        return $query->where('status', TimeOffStatus::CANCELLED);
    }

    public function scopeUrgent($query)
    {
        return $query->where('is_urgent', true);
    }

    public function scopeUpcoming($query, string $date = null)
    {
        $date = $date ?: now()->format('Y-m-d');
        return $query->where('start_date', '>=', $date);
    }

    public function scopePast($query, string $date = null)
    {
        $date = $date ?: now()->format('Y-m-d');
        return $query->where('end_date', '<', $date);
    }

    public function scopeByApprover($query, int $approverId)
    {
        return $query->where('approved_by', $approverId);
    }

    public function scopeOverlapping($query, int $employeeId, string $startDate, string $endDate)
    {
        return $query->where('employee_id', $employeeId)
                    ->where(function ($q) use ($startDate, $endDate) {
                        $q->whereBetween('start_date', [$startDate, $endDate])
                          ->orWhereBetween('end_date', [$startDate, $endDate])
                          ->orWhere(function ($subQ) use ($startDate, $endDate) {
                              $subQ->where('start_date', '<=', $startDate)
                                    ->where('end_date', '>=', $endDate);
                          });
                    });
    }

    // Accessors
    public function getDurationInDaysAttribute(): int
    {
        return $this->start_date->diffInDays($this->end_date) + 1;
    }

    public function getDurationInHoursAttribute(): int
    {
        return $this->duration_in_days * 8; // Assuming 8-hour workday
    }

    public function getFormattedStartDateAttribute(): string
    {
        return $this->start_date->format('M j, Y');
    }

    public function getFormattedEndDateAttribute(): string
    {
        return $this->end_date->format('M j, Y');
    }

    public function getFormattedStartTimeAttribute(): ?string
    {
        return $this->start_time ? $this->start_time->format('g:i A') : null;
    }

    public function getFormattedEndTimeAttribute(): ?string
    {
        return $this->end_time ? $this->end_time->format('g:i A') : null;
    }

    public function getStatusLabelAttribute(): string
    {
        return $this->status->label();
    }

    public function getStatusColorAttribute(): string
    {
        return $this->status->color();
    }

    public function getTimeOffTypeLabelAttribute(): string
    {
        return $this->time_off_type->label();
    }

    public function getTimeOffTypeColorAttribute(): string
    {
        return $this->time_off_type->color();
    }

    // Mutators
    public function setStartDateAttribute($value)
    {
        $this->attributes['start_date'] = Carbon::parse($value);
    }

    public function setEndDateAttribute($value)
    {
        $this->attributes['end_date'] = Carbon::parse($value);
    }

    public function setStartTimeAttribute($value)
    {
        $this->attributes['start_time'] = $value ? Carbon::parse($value) : null;
    }

    public function setEndTimeAttribute($value)
    {
        $this->attributes['end_time'] = $value ? Carbon::parse($value) : null;
    }

    // Methods
    public function isPending(): bool
    {
        return $this->status === TimeOffStatus::PENDING;
    }

    public function isApproved(): bool
    {
        return $this->status === TimeOffStatus::APPROVED;
    }

    public function isRejected(): bool
    {
        return $this->status === TimeOffStatus::REJECTED;
    }

    public function isCancelled(): bool
    {
        return $this->status === TimeOffStatus::CANCELLED;
    }

    public function canBeModified(): bool
    {
        return $this->status->canBeModified();
    }

    public function requiresApproval(): bool
    {
        return $this->time_off_type->requiresApproval();
    }

    public function isPaid(): bool
    {
        return $this->time_off_type->isPaid();
    }

    public function isOverlapping(string $startDate, string $endDate): bool
    {
        $requestStart = Carbon::parse($startDate);
        $requestEnd = Carbon::parse($endDate);

        return $this->start_date->lte($requestEnd) && $this->end_date->gte($requestStart);
    }

    public function calculateTotalDays(): float
    {
        if ($this->total_days !== null) {
            return $this->total_days;
        }

        $totalDays = $this->duration_in_days;

        if ($this->is_half_day) {
            $totalDays = $totalDays * 0.5;
        }

        return $totalDays;
    }

    public function calculateTotalHours(): float
    {
        if ($this->total_hours !== null) {
            return $this->total_hours;
        }

        $totalDays = $this->calculateTotalDays();

        // Assuming 8-hour workday
        return $totalDays * 8;
    }

    public function approve(int $approvedBy): bool
    {
        $this->update([
            'status' => TimeOffStatus::APPROVED,
            'approved_by' => $approvedBy,
            'approved_at' => now(),
        ]);

        return true;
    }

    public function reject(int $rejectedBy, string $reason = null): bool
    {
        $this->update([
            'status' => TimeOffStatus::REJECTED,
            'rejected_by' => $rejectedBy,
            'rejected_at' => now(),
            'rejection_reason' => $reason,
        ]);

        return true;
    }

    public function cancel(string $reason = null): bool
    {
        $this->update([
            'status' => TimeOffStatus::CANCELLED,
            'rejection_reason' => $reason,
        ]);

        return true;
    }

    public function markAsUrgent(): bool
    {
        $this->update(['is_urgent' => true]);
        return true;
    }

    public function removeUrgentFlag(): bool
    {
        $this->update(['is_urgent' => false]);
        return true;
    }

    public function getTotalDaysUsed(): float
    {
        return $this->calculateTotalDays();
    }

    public function getTotalHoursUsed(): float
    {
        return $this->calculateTotalHours();
    }

    public function toArray(): array
    {
        $array = parent::toArray();

        $array['duration_in_days'] = $this->duration_in_days;
        $array['duration_in_hours'] = $this->duration_in_hours;
        $array['formatted_start_date'] = $this->formatted_start_date;
        $array['formatted_end_date'] = $this->formatted_end_date;
        $array['formatted_start_time'] = $this->formatted_start_time;
        $array['formatted_end_time'] = $this->formatted_end_time;
        $array['status_label'] = $this->status_label;
        $array['status_color'] = $this->status_color;
        $array['time_off_type_label'] = $this->time_off_type_label;
        $array['time_off_type_color'] = $this->time_off_type_color;
        $array['can_be_modified'] = $this->canBeModified();
        $array['requires_approval'] = $this->requiresApproval();
        $array['is_paid'] = $this->isPaid();

        return $array;
    }
}

