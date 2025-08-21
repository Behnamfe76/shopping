<?php

namespace Fereydooni\Shopping\app\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasFactory, HasRoles;

    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    // Relationships
    public function customers(): HasMany
    {
        return $this->hasMany(Customer::class);
    }

    public function customerSegments(): HasMany
    {
        return $this->hasMany(CustomerSegment::class, 'created_by');
    }

    public function updatedCustomerSegments(): HasMany
    {
        return $this->hasMany(CustomerSegment::class, 'updated_by');
    }

    public function calculatedCustomerSegments(): HasMany
    {
        return $this->hasMany(CustomerSegment::class, 'calculated_by');
    }

    public function segmentHistory(): HasMany
    {
        return $this->hasMany(CustomerSegmentHistory::class, 'performed_by');
    }
}
