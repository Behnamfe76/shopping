<?php

namespace Fereydooni\Shopping\App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends \App\Models\User
{
    public function getRouteKeyName(): string
    {
        return 'uuid';
    }

    /**
     * Get the payments processed by this user.
     */
    public function processedPayments(): HasMany
    {
        return $this->hasMany(ProviderPayment::class, 'processed_by');
    }
}
