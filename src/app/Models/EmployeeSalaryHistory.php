<?php

namespace App\Models;

use App\Enums\SalaryChangeType;
use App\Traits\HasEmployeeSalaryHistoryAnalytics;
use App\Traits\HasEmployeeSalaryHistoryApprovalManagement;
use App\Traits\HasEmployeeSalaryHistoryOperations;
use App\Traits\HasEmployeeSalaryHistoryRetroactiveManagement;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class EmployeeSalaryHistory extends Model
{
    use HasEmployeeSalaryHistoryAnalytics;
    use HasEmployeeSalaryHistoryApprovalManagement;
    use HasEmployeeSalaryHistoryOperations;
    use HasEmployeeSalaryHistoryRetroactiveManagement;
    use HasFactory, SoftDeletes;

    protected $table = 'employee_salary_histories';

    protected $fillable = [
        'employee_id',
        'old_salary',
        'new_salary',
        'change_amount',
        'change_percentage',
        'change_type',
        'effective_date',
        'reason',
        'approved_by',
        'approved_at',
        'is_retroactive',
        'retroactive_start_date',
        'retroactive_end_date',
        'notes',
        'attachments',
        'status',
        'rejection_reason',
        'rejected_by',
        'rejected_at',
        'processed_at',
        'metadata',
    ];

    protected $casts = [
        'old_salary' => 'decimal:2',
        'new_salary' => 'decimal:2',
        'change_amount' => 'decimal:2',
        'change_percentage' => 'decimal:4',
        'effective_date' => 'date',
        'approved_at' => 'datetime',
        'is_retroactive' => 'boolean',
        'retroactive_start_date' => 'date',
        'retroactive_end_date' => 'date',
        'processed_at' => 'datetime',
        'rejected_at' => 'datetime',
        'attachments' => 'array',
        'metadata' => 'array',
        'change_type' => SalaryChangeType::class,
    ];

    protected $dates = [
        'effective_date',
        'approved_at',
        'retroactive_start_date',
        'retroactive_end_date',
        'processed_at',
        'rejected_at',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    // Relationships
    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
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
    public function scopeByEmployee($query, $employeeId)
    {
        return $query->where('employee_id', $employeeId);
    }

    public function scopeByChangeType($query, $changeType)
    {
        return $query->where('change_type', $changeType);
    }

    public function scopeByDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('effective_date', [$startDate, $endDate]);
    }

    public function scopeByApprover($query, $approverId)
    {
        return $query->where('approved_by', $approverId);
    }

    public function scopeRetroactive($query)
    {
        return $query->where('is_retroactive', true);
    }

    public function scopePending($query)
    {
        return $query->whereNull('approved_at')->whereNull('rejected_at');
    }

    public function scopeApproved($query)
    {
        return $query->whereNotNull('approved_at');
    }

    public function scopeRejected($query)
    {
        return $query->whereNotNull('rejected_at');
    }

    public function scopeProcessed($query)
    {
        return $query->whereNotNull('processed_at');
    }

    // Accessors
    public function getChangeAmountFormattedAttribute(): string
    {
        return number_format($this->change_amount, 2);
    }

    public function getChangePercentageFormattedAttribute(): string
    {
        return number_format($this->change_percentage, 2).'%';
    }

    public function getOldSalaryFormattedAttribute(): string
    {
        return number_format($this->old_salary, 2);
    }

    public function getNewSalaryFormattedAttribute(): string
    {
        return number_format($this->new_salary, 2);
    }

    public function getEffectiveDateFormattedAttribute(): string
    {
        return $this->effective_date->format('M d, Y');
    }

    public function getIsPendingAttribute(): bool
    {
        return is_null($this->approved_at) && is_null($this->rejected_at);
    }

    public function getIsApprovedAttribute(): bool
    {
        return ! is_null($this->approved_at);
    }

    public function getIsRejectedAttribute(): bool
    {
        return ! is_null($this->rejected_at);
    }

    public function getIsProcessedAttribute(): bool
    {
        return ! is_null($this->processed_at);
    }

    public function getIsRetroactiveAttribute(): bool
    {
        return $this->is_retroactive;
    }

    // Mutators
    public function setChangeAmountAttribute($value)
    {
        $this->attributes['change_amount'] = $value;

        if ($this->old_salary > 0) {
            $this->attributes['change_percentage'] = ($value / $this->old_salary) * 100;
        }
    }

    public function setNewSalaryAttribute($value)
    {
        $this->attributes['new_salary'] = $value;

        if ($this->old_salary > 0) {
            $this->attributes['change_amount'] = $value - $this->old_salary;
            $this->attributes['change_percentage'] = (($value - $this->old_salary) / $this->old_salary) * 100;
        }
    }

    // Methods
    public function calculateChangeAmount(): float
    {
        return $this->new_salary - $this->old_salary;
    }

    public function calculateChangePercentage(): float
    {
        if ($this->old_salary > 0) {
            return (($this->new_salary - $this->old_salary) / $this->old_salary) * 100;
        }

        return 0;
    }

    public function isIncrease(): bool
    {
        return $this->change_amount > 0;
    }

    public function isDecrease(): bool
    {
        return $this->change_amount < 0;
    }

    public function isNoChange(): bool
    {
        return $this->change_amount == 0;
    }

    public function getChangeDescription(): string
    {
        $type = $this->change_type->value;
        $amount = $this->change_amount_formatted;
        $percentage = $this->change_percentage_formatted;

        if ($this->isIncrease()) {
            return "Salary increased by {$amount} ({$percentage})";
        } elseif ($this->isDecrease()) {
            return "Salary decreased by {$amount} ({$percentage})";
        }

        return 'No salary change';
    }

    public function getRetroactivePeriod(): ?string
    {
        if (! $this->is_retroactive) {
            return null;
        }

        $start = $this->retroactive_start_date?->format('M d, Y');
        $end = $this->retroactive_end_date?->format('M d, Y');

        return "{$start} to {$end}";
    }

    public function getRetroactiveDays(): ?int
    {
        if (! $this->is_retroactive || ! $this->retroactive_start_date || ! $this->retroactive_end_date) {
            return null;
        }

        return $this->retroactive_start_date->diffInDays($this->retroactive_end_date) + 1;
    }

    public function getRetroactiveAmount(): ?float
    {
        if (! $this->is_retroactive || ! $this->retroactive_days) {
            return null;
        }

        $dailyRate = $this->change_amount / 365; // Assuming 365 days per year

        return $dailyRate * $this->retroactive_days;
    }

    // Boot method for model events
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->change_amount) && $model->old_salary && $model->new_salary) {
                $model->change_amount = $model->calculateChangeAmount();
            }

            if (empty($model->change_percentage) && $model->old_salary > 0) {
                $model->change_percentage = $model->calculateChangePercentage();
            }
        });

        static::updating(function ($model) {
            if ($model->isDirty('old_salary') || $model->isDirty('new_salary')) {
                $model->change_amount = $model->calculateChangeAmount();
                $model->change_percentage = $model->calculateChangePercentage();
            }
        });
    }
}
