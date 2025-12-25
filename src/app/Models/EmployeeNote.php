<?php

namespace Fereydooni\Shopping\app\Models;

use Fereydooni\Shopping\app\Enums\EmployeeNotePriority;
use Fereydooni\Shopping\app\Enums\EmployeeNoteType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class EmployeeNote extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'employee_id',
        'user_id',
        'title',
        'content',
        'note_type',
        'priority',
        'is_private',
        'is_archived',
        'tags',
        'attachments',
    ];

    protected $casts = [
        'note_type' => EmployeeNoteType::class,
        'priority' => EmployeeNotePriority::class,
        'is_private' => 'boolean',
        'is_archived' => 'boolean',
        'tags' => 'array',
        'attachments' => 'array',
    ];

    protected $attributes = [
        'is_private' => false,
        'is_archived' => false,
        'priority' => EmployeeNotePriority::MEDIUM,
        'note_type' => EmployeeNoteType::GENERAL,
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

    // Scopes
    public function scopePrivate($query)
    {
        return $query->where('is_private', true);
    }

    public function scopePublic($query)
    {
        return $query->where('is_private', false);
    }

    public function scopeArchived($query)
    {
        return $query->where('is_archived', true);
    }

    public function scopeActive($query)
    {
        return $query->where('is_archived', false);
    }

    public function scopeByType($query, EmployeeNoteType $type)
    {
        return $query->where('note_type', $type);
    }

    public function scopeByPriority($query, EmployeeNotePriority $priority)
    {
        return $query->where('priority', $priority);
    }

    public function scopeByEmployee($query, int $employeeId)
    {
        return $query->where('employee_id', $employeeId);
    }

    public function scopeSearch($query, string $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('title', 'like', "%{$search}%")
                ->orWhere('content', 'like', "%{$search}%");
        });
    }
}
