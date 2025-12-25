<?php

namespace Fereydooni\Shopping\App\Listeners\ProviderSpecialization;

use Fereydooni\Shopping\App\Events\ProviderSpecialization\ProviderPrimarySpecializationChanged;
use Fereydooni\Shopping\App\Events\ProviderSpecialization\ProviderSpecializationActivated;
use Fereydooni\Shopping\App\Events\ProviderSpecialization\ProviderSpecializationDeactivated;
use Fereydooni\Shopping\App\Events\ProviderSpecialization\ProviderSpecializationRejected;
use Fereydooni\Shopping\App\Events\ProviderSpecialization\ProviderSpecializationVerified;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class UpdateProviderSpecializationRecord implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Handle the event.
     */
    public function handle($event): void
    {
        $specialization = $event->specialization;

        DB::transaction(function () use ($event, $specialization) {
            switch (get_class($event)) {
                case ProviderSpecializationVerified::class:
                    $this->handleVerification($specialization, $event->verifiedBy);
                    break;

                case ProviderSpecializationRejected::class:
                    $this->handleRejection($specialization, $event->reason);
                    break;

                case ProviderSpecializationActivated::class:
                    $this->handleActivation($specialization);
                    break;

                case ProviderSpecializationDeactivated::class:
                    $this->handleDeactivation($specialization);
                    break;

                case ProviderPrimarySpecializationChanged::class:
                    $this->handlePrimaryChange($specialization, $event->previousPrimary);
                    break;
            }
        });
    }

    /**
     * Handle specialization verification.
     */
    protected function handleVerification($specialization, int $verifiedBy): void
    {
        $specialization->update([
            'verification_status' => 'verified',
            'verified_at' => Carbon::now(),
            'verified_by' => $verifiedBy,
        ]);
    }

    /**
     * Handle specialization rejection.
     */
    protected function handleRejection($specialization, ?string $reason): void
    {
        $specialization->update([
            'verification_status' => 'rejected',
            'notes' => $reason ? ($specialization->notes."\nRejection reason: ".$reason) : $specialization->notes,
        ]);
    }

    /**
     * Handle specialization activation.
     */
    protected function handleActivation($specialization): void
    {
        $specialization->update([
            'is_active' => true,
        ]);
    }

    /**
     * Handle specialization deactivation.
     */
    protected function handleDeactivation($specialization): void
    {
        $specialization->update([
            'is_active' => false,
        ]);
    }

    /**
     * Handle primary specialization change.
     */
    protected function handlePrimaryChange($specialization, $previousPrimary): void
    {
        // Remove primary status from previous specialization
        if ($previousPrimary) {
            $previousPrimary->update(['is_primary' => false]);
        }

        // Set new primary specialization
        $specialization->update(['is_primary' => true]);
    }

    /**
     * Handle a job failure.
     */
    public function failed($event, $exception): void
    {
        Log::error('Failed to update provider specialization record', [
            'event' => get_class($event),
            'exception' => $exception->getMessage(),
            'specialization_id' => $event->specialization->id ?? null,
        ]);
    }
}
