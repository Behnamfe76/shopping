<?php

namespace Fereydooni\Shopping\app\Models;

use Fereydooni\Shopping\app\Enums\TeamStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Team extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'code',
        'description',
        'department_id',
        'location',
        'member_limit',
        'is_active',
        'status',
        'metadata',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'member_limit' => 'integer',
        'is_active' => 'boolean',
        'status' => TeamStatus::class,
        'metadata' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    protected $hidden = [
        'deleted_at',
    ];

    // Relationships
    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class, 'department_id');
    }

    public function members(): BelongsToMany
    {
        return $this->belongsToMany(Employee::class, 'team_members', 'team_id', 'employee_id')
            ->withPivot(['is_manager', 'joined_at', 'left_at', 'metadata'])
            ->withTimestamps();
    }

    public function managers(): BelongsToMany
    {
        return $this->belongsToMany(Employee::class, 'team_members', 'team_id', 'employee_id')
            ->withPivot(['is_manager', 'joined_at', 'left_at', 'metadata'])
            ->wherePivot('is_manager', true)
            ->withTimestamps();
    }

    public function activeMembers(): BelongsToMany
    {
        return $this->members()->whereNull('team_members.left_at');
    }

    public function activeManagers(): BelongsToMany
    {
        return $this->managers()->whereNull('team_members.left_at');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true)->where('status', TeamStatus::ACTIVE);
    }

    public function scopeInactive($query)
    {
        return $query->where('is_active', false)->orWhere('status', '!=', TeamStatus::ACTIVE);
    }

    public function scopeByStatus($query, TeamStatus $status)
    {
        return $query->where('status', $status);
    }

    public function scopeByDepartment($query, int $departmentId)
    {
        return $query->where('department_id', $departmentId);
    }

    public function scopeByLocation($query, string $location)
    {
        return $query->where('location', $location);
    }

    // Accessors
    public function getFullNameAttribute(): string
    {
        if ($this->department) {
            return $this->department->name.' > '.$this->name;
        }

        return $this->name;
    }

    public function getMemberCountAttribute(): int
    {
        return $this->activeMembers()->count();
    }

    public function getManagerCountAttribute(): int
    {
        return $this->activeManagers()->count();
    }

    public function getMemberUtilizationAttribute(): float
    {
        if ($this->member_limit <= 0) {
            return 0;
        }

        return ($this->member_count / $this->member_limit) * 100;
    }

    // Methods
    public function hasMembers(): bool
    {
        return $this->activeMembers()->count() > 0;
    }

    public function hasManagers(): bool
    {
        return $this->activeManagers()->count() > 0;
    }

    public function isMember(int $employeeId): bool
    {
        return $this->activeMembers()->where('employees.id', $employeeId)->exists();
    }

    public function isManager(int $employeeId): bool
    {
        return $this->activeManagers()->where('employees.id', $employeeId)->exists();
    }

    public function activate(): bool
    {
        $this->update([
            'is_active' => true,
            'status' => TeamStatus::ACTIVE,
        ]);

        return true;
    }

    public function deactivate(): bool
    {
        $this->update([
            'is_active' => false,
            'status' => TeamStatus::INACTIVE,
        ]);

        return true;
    }

    public function archive(): bool
    {
        $this->update([
            'is_active' => false,
            'status' => TeamStatus::ARCHIVED,
        ]);

        return true;
    }

    public function addMember(int $employeeId, bool $isManager = false, ?string $joinedAt = null): bool
    {
        if ($this->isMember($employeeId)) {
            return false;
        }

        $this->members()->attach($employeeId, [
            'is_manager' => $isManager,
            'joined_at' => $joinedAt ?? now()->toDateString(),
        ]);

        return true;
    }

    public function removeMember(int $employeeId): bool
    {
        if (! $this->isMember($employeeId)) {
            return false;
        }

        // Mark as left instead of deleting
        $this->members()->updateExistingPivot($employeeId, [
            'left_at' => now()->toDateString(),
        ]);

        return true;
    }

    public function promoteToManager(int $employeeId): bool
    {
        if (! $this->isMember($employeeId)) {
            return false;
        }

        $this->members()->updateExistingPivot($employeeId, [
            'is_manager' => true,
        ]);

        return true;
    }

    public function demoteFromManager(int $employeeId): bool
    {
        if (! $this->isManager($employeeId)) {
            return false;
        }

        // Ensure at least one manager remains
        if ($this->manager_count <= 1) {
            throw new \InvalidArgumentException('Team must have at least one manager');
        }

        $this->members()->updateExistingPivot($employeeId, [
            'is_manager' => false,
        ]);

        return true;
    }

    public function changeManager(int $oldManagerId, int $newManagerId): bool
    {
        if (! $this->isManager($oldManagerId)) {
            return false;
        }

        if (! $this->isMember($newManagerId)) {
            return false;
        }

        // Demote old manager
        $this->members()->updateExistingPivot($oldManagerId, [
            'is_manager' => false,
        ]);

        // Promote new manager
        $this->members()->updateExistingPivot($newManagerId, [
            'is_manager' => true,
        ]);

        return true;
    }
}
