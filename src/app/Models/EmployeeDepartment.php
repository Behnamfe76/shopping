<?php

namespace Fereydooni\Shopping\app\Models;

use Fereydooni\Shopping\app\Enums\DepartmentStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class EmployeeDepartment extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'code',
        'description',
        'parent_id',
        'manager_id',
        'location',
        'budget',
        'headcount_limit',
        'is_active',
        'status',
        'metadata',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'budget' => 'decimal:2',
        'headcount_limit' => 'integer',
        'is_active' => 'boolean',
        'status' => DepartmentStatus::class,
        'metadata' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    protected $hidden = [
        'deleted_at',
    ];

    // Relationships
    public function parent(): BelongsTo
    {
        return $this->belongsTo(EmployeeDepartment::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(EmployeeDepartment::class, 'parent_id');
    }

    public function manager(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'manager_id');
    }

    public function employees(): HasMany
    {
        return $this->hasMany(Employee::class, 'department_id');
    }

    public function allChildren(): HasMany
    {
        return $this->children()->with('allChildren');
    }

    public function allParents(): BelongsTo
    {
        return $this->parent()->with('allParents');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true)->where('status', DepartmentStatus::ACTIVE);
    }

    public function scopeInactive($query)
    {
        return $query->where('is_active', false)->orWhere('status', '!=', DepartmentStatus::ACTIVE);
    }

    public function scopeRoot($query)
    {
        return $query->whereNull('parent_id');
    }

    public function scopeByStatus($query, DepartmentStatus $status)
    {
        return $query->where('status', $status);
    }

    public function scopeByLocation($query, string $location)
    {
        return $query->where('location', $location);
    }

    // Accessors
    public function getFullNameAttribute(): string
    {
        if ($this->parent) {
            return $this->parent->full_name.' > '.$this->name;
        }

        return $this->name;
    }

    public function getEmployeeCountAttribute(): int
    {
        return $this->employees()->count();
    }

    public function getBudgetUtilizationAttribute(): float
    {
        if ($this->budget <= 0) {
            return 0;
        }

        // This would need to be calculated based on actual spending
        // For now, returning 0 as placeholder
        return 0;
    }

    public function getHeadcountUtilizationAttribute(): float
    {
        if ($this->headcount_limit <= 0) {
            return 0;
        }

        return ($this->employee_count / $this->headcount_limit) * 100;
    }

    // Methods
    public function isRoot(): bool
    {
        return is_null($this->parent_id);
    }

    public function isLeaf(): bool
    {
        return $this->children()->count() === 0;
    }

    public function hasChildren(): bool
    {
        return $this->children()->count() > 0;
    }

    public function hasParent(): bool
    {
        return ! is_null($this->parent_id);
    }

    public function activate(): bool
    {
        $this->update([
            'is_active' => true,
            'status' => DepartmentStatus::ACTIVE,
        ]);

        return true;
    }

    public function deactivate(): bool
    {
        $this->update([
            'is_active' => false,
            'status' => DepartmentStatus::INACTIVE,
        ]);

        return true;
    }

    public function archive(): bool
    {
        $this->update([
            'is_active' => false,
            'status' => DepartmentStatus::ARCHIVED,
        ]);

        return true;
    }

    public function assignManager(int $managerId): bool
    {
        $this->update(['manager_id' => $managerId]);

        return true;
    }

    public function removeManager(): bool
    {
        $this->update(['manager_id' => null]);

        return true;
    }

    public function moveToParent(?int $newParentId): bool
    {
        $this->update(['parent_id' => $newParentId]);

        return true;
    }

    public function getAncestors(): array
    {
        $ancestors = [];
        $current = $this->parent;

        while ($current) {
            $ancestors[] = $current;
            $current = $current->parent;
        }

        return array_reverse($ancestors);
    }

    public function getDescendants(): array
    {
        $descendants = [];
        $this->collectDescendants($descendants);

        return $descendants;
    }

    private function collectDescendants(array &$descendants): void
    {
        foreach ($this->children as $child) {
            $descendants[] = $child;
            $child->collectDescendants($descendants);
        }
    }

    public function getDepth(): int
    {
        $depth = 0;
        $current = $this->parent;

        while ($current) {
            $depth++;
            $current = $current->parent;
        }

        return $depth;
    }

    public function getLevel(): int
    {
        return $this->getDepth() + 1;
    }
}
