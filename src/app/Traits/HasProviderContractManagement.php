<?php

namespace Fereydooni\Shopping\App\Traits;

use Carbon\Carbon;
use Fereydooni\Shopping\App\Models\Provider;
use Fereydooni\Shopping\App\Repositories\Interfaces\ProviderRepositoryInterface;
use Illuminate\Support\Facades\Date;

trait HasProviderContractManagement
{
    protected ProviderRepositoryInterface $providerRepository;

    /**
     * Extend provider contract
     */
    public function extendProviderContract(Provider $provider, string $newEndDate): bool
    {
        return $this->providerRepository->update($provider, [
            'contract_end_date' => $newEndDate,
        ]);
    }

    /**
     * Terminate provider contract
     */
    public function terminateProviderContract(Provider $provider, ?string $reason = null): bool
    {
        return $this->providerRepository->update($provider, [
            'contract_end_date' => now(),
            'termination_reason' => $reason,
        ]);
    }

    /**
     * Update contract start date
     */
    public function updateContractStartDate(Provider $provider, string $startDate): bool
    {
        return $this->providerRepository->update($provider, [
            'contract_start_date' => $startDate,
        ]);
    }

    /**
     * Update contract end date
     */
    public function updateContractEndDate(Provider $provider, string $endDate): bool
    {
        return $this->providerRepository->update($provider, [
            'contract_end_date' => $endDate,
        ]);
    }

    /**
     * Get providers with expiring contracts
     */
    public function getProvidersWithExpiringContracts(int $daysAhead = 30): array
    {
        $expiryDate = Carbon::now()->addDays($daysAhead);
        $providers = $this->providerRepository->all();

        return $providers->filter(function ($provider) use ($expiryDate) {
            if (! $provider->contract_end_date) {
                return false;
            }

            return Carbon::parse($provider->contract_end_date)->lte($expiryDate);
        })->toArray();
    }

    /**
     * Get providers with expired contracts
     */
    public function getProvidersWithExpiredContracts(): array
    {
        $providers = $this->providerRepository->all();

        return $providers->filter(function ($provider) {
            if (! $provider->contract_end_date) {
                return false;
            }

            return Carbon::parse($provider->contract_end_date)->lt(now());
        })->toArray();
    }

    /**
     * Get providers with active contracts
     */
    public function getProvidersWithActiveContracts(): array
    {
        $providers = $this->providerRepository->all();

        return $providers->filter(function ($provider) {
            if (! $provider->contract_start_date || ! $provider->contract_end_date) {
                return false;
            }
            $now = now();

            return Carbon::parse($provider->contract_start_date)->lte($now) &&
                   Carbon::parse($provider->contract_end_date)->gt($now);
        })->toArray();
    }

    /**
     * Get providers with upcoming contracts
     */
    public function getProvidersWithUpcomingContracts(int $daysAhead = 30): array
    {
        $startDate = Carbon::now()->addDays($daysAhead);
        $providers = $this->providerRepository->all();

        return $providers->filter(function ($provider) use ($startDate) {
            if (! $provider->contract_start_date) {
                return false;
            }

            return Carbon::parse($provider->contract_start_date)->lte($startDate);
        })->toArray();
    }

    /**
     * Get contract duration for a provider
     */
    public function getProviderContractDuration(Provider $provider): ?int
    {
        if (! $provider->contract_start_date || ! $provider->contract_end_date) {
            return null;
        }

        $start = Carbon::parse($provider->contract_start_date);
        $end = Carbon::parse($provider->contract_end_date);

        return $start->diffInDays($end);
    }

    /**
     * Get days until contract expires
     */
    public function getDaysUntilContractExpires(Provider $provider): ?int
    {
        if (! $provider->contract_end_date) {
            return null;
        }

        $endDate = Carbon::parse($provider->contract_end_date);
        $now = now();

        if ($endDate->lt($now)) {
            return 0; // Already expired
        }

        return $now->diffInDays($endDate);
    }

    /**
     * Get days since contract started
     */
    public function getDaysSinceContractStarted(Provider $provider): ?int
    {
        if (! $provider->contract_start_date) {
            return null;
        }

        $startDate = Carbon::parse($provider->contract_start_date);
        $now = now();

        if ($startDate->gt($now)) {
            return 0; // Contract hasn't started yet
        }

        return $startDate->diffInDays($now);
    }

    /**
     * Check if contract is active
     */
    public function isContractActive(Provider $provider): bool
    {
        if (! $provider->contract_start_date || ! $provider->contract_end_date) {
            return false;
        }

        $now = now();
        $startDate = Carbon::parse($provider->contract_start_date);
        $endDate = Carbon::parse($provider->contract_end_date);

        return $startDate->lte($now) && $endDate->gt($now);
    }

    /**
     * Check if contract is expired
     */
    public function isContractExpired(Provider $provider): bool
    {
        if (! $provider->contract_end_date) {
            return false;
        }

        return Carbon::parse($provider->contract_end_date)->lt(now());
    }

    /**
     * Check if contract is upcoming
     */
    public function isContractUpcoming(Provider $provider): bool
    {
        if (! $provider->contract_start_date) {
            return false;
        }

        return Carbon::parse($provider->contract_start_date)->gt(now());
    }

    /**
     * Get contract summary for a provider
     */
    public function getProviderContractSummary(int $providerId): array
    {
        $provider = $this->providerRepository->find($providerId);

        if (! $provider) {
            return [];
        }

        return [
            'start_date' => $provider->contract_start_date,
            'end_date' => $provider->contract_end_date,
            'is_active' => $this->isContractActive($provider),
            'is_expired' => $this->isContractExpired($provider),
            'is_upcoming' => $this->isContractUpcoming($provider),
            'duration_days' => $this->getProviderContractDuration($provider),
            'days_until_expiry' => $this->getDaysUntilContractExpires($provider),
            'days_since_start' => $this->getDaysSinceContractStarted($provider),
        ];
    }

    /**
     * Get overall contract statistics
     */
    public function getOverallContractStats(): array
    {
        $providers = $this->providerRepository->all();

        $activeContracts = 0;
        $expiredContracts = 0;
        $upcomingContracts = 0;
        $noContracts = 0;

        foreach ($providers as $provider) {
            if ($this->isContractActive($provider)) {
                $activeContracts++;
            } elseif ($this->isContractExpired($provider)) {
                $expiredContracts++;
            } elseif ($this->isContractUpcoming($provider)) {
                $upcomingContracts++;
            } else {
                $noContracts++;
            }
        }

        return [
            'total_providers' => $providers->count(),
            'active_contracts' => $activeContracts,
            'expired_contracts' => $expiredContracts,
            'upcoming_contracts' => $upcomingContracts,
            'no_contracts' => $noContracts,
        ];
    }
}
