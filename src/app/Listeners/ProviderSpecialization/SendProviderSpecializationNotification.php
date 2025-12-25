<?php

namespace Fereydooni\Shopping\App\Listeners\ProviderSpecialization;

use Fereydooni\Shopping\App\Events\ProviderSpecialization\ProviderPrimarySpecializationChanged;
use Fereydooni\Shopping\App\Events\ProviderSpecialization\ProviderSpecializationActivated;
use Fereydooni\Shopping\App\Events\ProviderSpecialization\ProviderSpecializationCreated;
use Fereydooni\Shopping\App\Events\ProviderSpecialization\ProviderSpecializationDeactivated;
use Fereydooni\Shopping\App\Events\ProviderSpecialization\ProviderSpecializationRejected;
use Fereydooni\Shopping\App\Events\ProviderSpecialization\ProviderSpecializationVerified;
use Fereydooni\Shopping\App\Notifications\ProviderSpecialization\PrimarySpecializationChanged;
use Fereydooni\Shopping\App\Notifications\ProviderSpecialization\SpecializationAdded;
use Fereydooni\Shopping\App\Notifications\ProviderSpecialization\SpecializationRejected;
use Fereydooni\Shopping\App\Notifications\ProviderSpecialization\SpecializationStatusChanged;
use Fereydooni\Shopping\App\Notifications\ProviderSpecialization\SpecializationVerified;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendProviderSpecializationNotification implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Handle the event.
     */
    public function handle($event): void
    {
        switch (get_class($event)) {
            case ProviderSpecializationCreated::class:
                $this->handleSpecializationCreated($event);
                break;

            case ProviderSpecializationVerified::class:
                $this->handleSpecializationVerified($event);
                break;

            case ProviderSpecializationRejected::class:
                $this->handleSpecializationRejected($event);
                break;

            case ProviderPrimarySpecializationChanged::class:
                $this->handlePrimarySpecializationChanged($event);
                break;

            case ProviderSpecializationActivated::class:
            case ProviderSpecializationDeactivated::class:
                $this->handleStatusChanged($event);
                break;
        }
    }

    /**
     * Handle specialization created event.
     */
    protected function handleSpecializationCreated(ProviderSpecializationCreated $event): void
    {
        $specialization = $event->specialization;
        $provider = $specialization->provider;

        if ($provider && $provider->user) {
            $provider->user->notify(new SpecializationAdded($specialization));
        }
    }

    /**
     * Handle specialization verified event.
     */
    protected function handleSpecializationVerified(ProviderSpecializationVerified $event): void
    {
        $specialization = $event->specialization;
        $provider = $specialization->provider;

        if ($provider && $provider->user) {
            $provider->user->notify(new SpecializationVerified($specialization));
        }
    }

    /**
     * Handle specialization rejected event.
     */
    protected function handleSpecializationRejected(ProviderSpecializationRejected $event): void
    {
        $specialization = $event->specialization;
        $provider = $specialization->provider;

        if ($provider && $provider->user) {
            $provider->user->notify(new SpecializationRejected($specialization, $event->reason));
        }
    }

    /**
     * Handle primary specialization changed event.
     */
    protected function handlePrimarySpecializationChanged(ProviderPrimarySpecializationChanged $event): void
    {
        $specialization = $event->specialization;
        $provider = $specialization->provider;

        if ($provider && $provider->user) {
            $provider->user->notify(new PrimarySpecializationChanged($specialization, $event->previousPrimary));
        }
    }

    /**
     * Handle status changed event.
     */
    protected function handleStatusChanged($event): void
    {
        $specialization = $event->specialization;
        $provider = $specialization->provider;

        if ($provider && $provider->user) {
            $status = $event instanceof ProviderSpecializationActivated ? 'activated' : 'deactivated';
            $provider->user->notify(new SpecializationStatusChanged($specialization, $status));
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed($event, $exception): void
    {
        // Log the failure
        \Log::error('Failed to send provider specialization notification', [
            'event' => get_class($event),
            'exception' => $exception->getMessage(),
            'specialization_id' => $event->specialization->id ?? null,
        ]);
    }
}
