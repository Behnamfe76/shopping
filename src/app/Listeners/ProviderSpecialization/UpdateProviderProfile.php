<?php

namespace Fereydooni\Shopping\App\Listeners\ProviderSpecialization;

use Fereydooni\Shopping\App\Events\ProviderSpecialization\ProviderPrimarySpecializationChanged;
use Fereydooni\Shopping\App\Events\ProviderSpecialization\ProviderSpecializationActivated;
use Fereydooni\Shopping\App\Events\ProviderSpecialization\ProviderSpecializationCreated;
use Fereydooni\Shopping\App\Events\ProviderSpecialization\ProviderSpecializationDeactivated;
use Fereydooni\Shopping\App\Events\ProviderSpecialization\ProviderSpecializationRejected;
use Fereydooni\Shopping\App\Events\ProviderSpecialization\ProviderSpecializationVerified;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class UpdateProviderProfile implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Handle the event.
     */
    public function handle($event): void
    {
        $specialization = $event->specialization;
        $provider = $specialization->provider;

        if (! $provider) {
            return;
        }

        switch (get_class($event)) {
            case ProviderSpecializationCreated::class:
                $this->handleSpecializationCreated($provider, $specialization);
                break;

            case ProviderSpecializationVerified::class:
                $this->handleSpecializationVerified($provider, $specialization);
                break;

            case ProviderSpecializationRejected::class:
                $this->handleSpecializationRejected($provider, $specialization);
                break;

            case ProviderPrimarySpecializationChanged::class:
                $this->handlePrimarySpecializationChanged($provider, $specialization);
                break;

            case ProviderSpecializationActivated::class:
                $this->handleSpecializationActivated($provider, $specialization);
                break;

            case ProviderSpecializationDeactivated::class:
                $this->handleSpecializationDeactivated($provider, $specialization);
                break;
        }
    }

    /**
     * Handle specialization creation.
     */
    protected function handleSpecializationCreated($provider, $specialization): void
    {
        // Update provider's specialization count
        $specializationCount = $provider->specializations()->count();
        $provider->update([
            'specialization_count' => $specializationCount,
        ]);
    }

    /**
     * Handle specialization verification.
     */
    protected function handleSpecializationVerified($provider, $specialization): void
    {
        // Update provider's verified specialization count
        $verifiedCount = $provider->specializations()
            ->where('verification_status', 'verified')
            ->count();

        $provider->update([
            'verified_specialization_count' => $verifiedCount,
        ]);
    }

    /**
     * Handle specialization rejection.
     */
    protected function handleSpecializationRejected($provider, $specialization): void
    {
        // Update provider's rejected specialization count
        $rejectedCount = $provider->specializations()
            ->where('verification_status', 'rejected')
            ->count();

        $provider->update([
            'rejected_specialization_count' => $rejectedCount,
        ]);
    }

    /**
     * Handle primary specialization change.
     */
    protected function handlePrimarySpecializationChanged($provider, $specialization): void
    {
        // Update provider's primary specialization
        $provider->update([
            'primary_specialization' => $specialization->specialization_name,
            'primary_specialization_id' => $specialization->id,
        ]);
    }

    /**
     * Handle specialization activation.
     */
    protected function handleSpecializationActivated($provider, $specialization): void
    {
        // Update provider's active specialization count
        $activeCount = $provider->specializations()
            ->where('is_active', true)
            ->count();

        $provider->update([
            'active_specialization_count' => $activeCount,
        ]);
    }

    /**
     * Handle specialization deactivation.
     */
    protected function handleSpecializationDeactivated($provider, $specialization): void
    {
        // Update provider's active specialization count
        $activeCount = $provider->specializations()
            ->where('is_active', true)
            ->count();

        $provider->update([
            'active_specialization_count' => $activeCount,
        ]);
    }

    /**
     * Handle a job failure.
     */
    public function failed($event, $exception): void
    {
        \Log::error('Failed to update provider profile', [
            'event' => get_class($event),
            'exception' => $exception->getMessage(),
            'specialization_id' => $event->specialization->id ?? null,
        ]);
    }
}
