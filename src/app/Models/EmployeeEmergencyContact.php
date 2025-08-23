<?php

namespace Fereydooni\Shopping\app\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Fereydooni\Shopping\app\Enums\Relationship;

class EmployeeEmergencyContact extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'employee_id',
        'contact_name',
        'relationship',
        'phone_primary',
        'phone_secondary',
        'email',
        'address',
        'city',
        'state',
        'postal_code',
        'country',
        'is_primary',
        'is_active',
        'notes',
    ];

    protected $casts = [
        'relationship' => Relationship::class,
        'is_primary' => 'boolean',
        'is_active' => 'boolean',
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

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeInactive($query)
    {
        return $query->where('is_active', false);
    }

    public function scopePrimary($query)
    {
        return $query->where('is_primary', true);
    }

    public function scopeByEmployee($query, int $employeeId)
    {
        return $query->where('employee_id', $employeeId);
    }

    public function scopeByRelationship($query, string $relationship)
    {
        return $query->where('relationship', $relationship);
    }

    public function scopeByContactName($query, string $contactName)
    {
        return $query->where('contact_name', 'like', "%{$contactName}%");
    }

    public function scopeByPhone($query, string $phone)
    {
        return $query->where(function ($q) use ($phone) {
            $q->where('phone_primary', 'like', "%{$phone}%")
              ->orWhere('phone_secondary', 'like', "%{$phone}%");
        });
    }

    public function scopeByEmail($query, string $email)
    {
        return $query->where('email', 'like', "%{$email}%");
    }

    public function scopeByCity($query, string $city)
    {
        return $query->where('city', 'like', "%{$city}%");
    }

    public function scopeByState($query, string $state)
    {
        return $query->where('state', 'like', "%{$state}%");
    }

    public function scopeByCountry($query, string $country)
    {
        return $query->where('country', 'like', "%{$country}%");
    }

    // Methods
    public function activate(): bool
    {
        return $this->update(['is_active' => true]);
    }

    public function deactivate(): bool
    {
        return $this->update(['is_active' => false]);
    }

    public function setAsPrimary(): bool
    {
        // Remove primary status from other contacts of the same employee
        static::where('employee_id', $this->employee_id)
              ->where('id', '!=', $this->id)
              ->update(['is_primary' => false]);

        return $this->update(['is_primary' => true]);
    }

    public function removePrimary(): bool
    {
        return $this->update(['is_primary' => false]);
    }

    public function getFullAddressAttribute(): string
    {
        $parts = array_filter([
            $this->address,
            $this->city,
            $this->state,
            $this->postal_code,
            $this->country
        ]);

        return implode(', ', $parts);
    }

    public function getDisplayNameAttribute(): string
    {
        return "{$this->contact_name} ({$this->relationship->label()})";
    }

    public function isPrimaryContact(): bool
    {
        return $this->is_primary;
    }

    public function isActiveContact(): bool
    {
        return $this->is_active;
    }

    public function hasValidPhone(): bool
    {
        return !empty($this->phone_primary) || !empty($this->phone_secondary);
    }

    public function hasValidEmail(): bool
    {
        return !empty($this->email) && filter_var($this->email, FILTER_VALIDATE_EMAIL);
    }

    public function hasValidAddress(): bool
    {
        return !empty($this->address) && !empty($this->city) && !empty($this->state);
    }

    public function getContactMethod(): string
    {
        if ($this->hasValidPhone()) {
            return 'phone';
        }
        if ($this->hasValidEmail()) {
            return 'email';
        }
        return 'address';
    }

    public function getPreferredContactMethod(): string
    {
        if ($this->is_primary) {
            return $this->getContactMethod();
        }
        return 'secondary';
    }
}
