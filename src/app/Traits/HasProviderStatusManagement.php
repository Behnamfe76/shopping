<?php

namespace App\Traits;

use Fereydooni\Shopping\App\Enums\ProviderStatus;
use Fereydooni\Shopping\App\Models\Provider;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Fereydooni\Shopping\App\Repositories\Interfaces\ProviderRepositoryInterface;

trait HasProviderStatusManagement
{
    protected ProviderRepositoryInterface $providerRepository;

    /**
     * Activate a provider
     */
    public function activateProvider(Provider $provider): bool
    {
        return $this->providerRepository->update($provider, ['status' => ProviderStatus::ACTIVE]);
    }

    /**
     * Deactivate a provider
     */
    public function deactivateProvider(Provider $provider): bool
    {
        return $this->providerRepository->update($provider, ['status' => ProviderStatus::INACTIVE]);
    }

    /**
     * Suspend a provider
     */
    public function suspendProvider(Provider $provider, string $reason = null): bool
    {
        return $this->providerRepository->update($provider, [
            'status' => ProviderStatus::SUSPENDED,
            'suspension_reason' => $reason
        ]);
    }

    /**
     * Unsuspend a provider
     */
    public function unsuspendProvider(Provider $provider): bool
    {
        return $this->providerRepository->update($provider, [
            'status' => ProviderStatus::ACTIVE,
            'suspension_reason' => null
        ]);
    }

    /**
     * Blacklist a provider
     */
    public function blacklistProvider(Provider $provider, string $reason = null): bool
    {
        return $this->providerRepository->update($provider, [
            'status' => ProviderStatus::BLACKLISTED,
            'blacklist_reason' => $reason
        ]);
    }

    /**
     * Remove provider from blacklist
     */
    public function removeFromBlacklist(Provider $provider): bool
    {
        return $this->providerRepository->update($provider, [
            'status' => ProviderStatus::INACTIVE,
            'blacklist_reason' => null
        ]);
    }

    /**
     * Set provider status to pending
     */
    public function setProviderPending(Provider $provider): bool
    {
        return $this->providerRepository->update($provider, ['status' => ProviderStatus::PENDING]);
    }

    /**
     * Approve pending provider
     */
    public function approveProvider(Provider $provider): bool
    {
        return $this->providerRepository->update($provider, ['status' => ProviderStatus::ACTIVE]);
    }

    /**
     * Reject pending provider
     */
    public function rejectProvider(Provider $provider, string $reason = null): bool
    {
        return $this->providerRepository->update($provider, [
            'status' => ProviderStatus::INACTIVE,
            'rejection_reason' => $reason
        ]);
    }

    /**
     * Get providers by status
     */
    public function getProvidersByStatus(ProviderStatus $status): Collection
    {
        return $this->where('status', $status)->get();
    }

    /**
     * Get active providers
     */
    public function getActiveProviders(): Collection
    {
        return $this->where('status', ProviderStatus::ACTIVE)->get();
    }

    /**
     * Get inactive providers
     */
    public function getInactiveProviders(): Collection
    {
        return $this->where('status', ProviderStatus::INACTIVE)->get();
    }

    /**
     * Get suspended providers
     */
    public function getSuspendedProviders(): Collection
    {
        return $this->where('status', ProviderStatus::SUSPENDED)->get();
    }

    /**
     * Get pending providers
     */
    public function getPendingProviders(): Collection
    {
        return $this->where('status', ProviderStatus::PENDING)->get();
    }

    /**
     * Get blacklisted providers
     */
    public function getBlacklistedProviders(): Collection
    {
        return $this->where('status', ProviderStatus::BLACKLISTED)->get();
    }

    /**
     * Get rejected providers
     */
    public function getRejectedProviders(): Collection
    {
        return $this->where('status', ProviderStatus::REJECTED)->get();
    }

    /**
     * Scope query to active providers
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', ProviderStatus::ACTIVE);
    }

    /**
     * Scope query to inactive providers
     */
    public function scopeInactive(Builder $query): Builder
    {
        return $query->where('status', ProviderStatus::INACTIVE);
    }

    /**
     * Scope query to suspended providers
     */
    public function scopeSuspended(Builder $query): Builder
    {
        return $query->where('status', ProviderStatus::SUSPENDED);
    }

    /**
     * Scope query to pending providers
     */
    public function scopePending(Builder $query): Builder
    {
        return $query->where('status', ProviderStatus::PENDING);
    }

    /**
     * Scope query to blacklisted providers
     */
    public function scopeBlacklisted(Builder $query): Builder
    {
        return $query->where('status', ProviderStatus::BLACKLISTED);
    }

    /**
     * Scope query to rejected providers
     */
    public function scopeRejected(Builder $query): Builder
    {
        return $query->where('status', ProviderStatus::REJECTED);
    }

    /**
     * Check if provider is active
     */
    public function isActive(): bool
    {
        return $this->status === ProviderStatus::ACTIVE;
    }

    /**
     * Check if provider is inactive
     */
    public function isInactive(): bool
    {
        return $this->status === ProviderStatus::INACTIVE;
    }

    /**
     * Check if provider is suspended
     */
    public function isSuspended(): bool
    {
        return $this->status === ProviderStatus::SUSPENDED;
    }

    /**
     * Check if provider is pending
     */
    public function isPending(): bool
    {
        return $this->status === ProviderStatus::PENDING;
    }

    /**
     * Check if provider is blacklisted
     */
    public function isBlacklisted(): bool
    {
        return $this->status === ProviderStatus::BLACKLISTED;
    }

    /**
     * Check if provider is rejected
     */
    public function isRejected(): bool
    {
        return $this->status === ProviderStatus::REJECTED;
    }

    /**
     * Get status change history
     */
    public function getStatusHistory(): Collection
    {
        // This would typically query a status_history table
        // For now, return empty collection
        return collect();
    }

    /**
     * Get providers that need status review
     */
    public function getProvidersNeedingReview(): Collection
    {
        return $this->whereIn('status', [
            ProviderStatus::PENDING,
            ProviderStatus::SUSPENDED
        ])->get();
    }

    /**
     * Get providers with expiring contracts
     */
    public function getProvidersWithExpiringContracts(int $daysAhead = 30): Collection
    {
        $expiryDate = now()->addDays($daysAhead);
        return $this->where('contract_end_date', '<=', $expiryDate)
            ->where('status', ProviderStatus::ACTIVE)
            ->get();
    }
}
