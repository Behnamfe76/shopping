<?php

namespace Fereydooni\Shopping\App\Listeners\ProviderSpecialization;

use Fereydooni\Shopping\App\Events\ProviderSpecialization\ProviderPrimarySpecializationChanged;
use Fereydooni\Shopping\App\Events\ProviderSpecialization\ProviderSpecializationActivated;
use Fereydooni\Shopping\App\Events\ProviderSpecialization\ProviderSpecializationCreated;
use Fereydooni\Shopping\App\Events\ProviderSpecialization\ProviderSpecializationDeactivated;
use Fereydooni\Shopping\App\Events\ProviderSpecialization\ProviderSpecializationRejected;
use Fereydooni\Shopping\App\Events\ProviderSpecialization\ProviderSpecializationUpdated;
use Fereydooni\Shopping\App\Events\ProviderSpecialization\ProviderSpecializationVerified;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class LogProviderSpecializationActivity implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Handle the event.
     */
    public function handle($event): void
    {
        $specialization = $event->specialization;
        $providerId = $specialization->provider_id;
        $specializationName = $specialization->specialization_name;

        $logData = [
            'specialization_id' => $specialization->id,
            'provider_id' => $providerId,
            'specialization_name' => $specializationName,
            'action' => $this->getActionFromEvent($event),
            'timestamp' => now()->toISOString(),
        ];

        // Add event-specific data
        switch (get_class($event)) {
            case ProviderSpecializationUpdated::class:
                $logData['changes'] = $event->changes;
                break;

            case ProviderSpecializationVerified::class:
                $logData['verified_by'] = $event->verifiedBy;
                break;

            case ProviderSpecializationRejected::class:
                $logData['reason'] = $event->reason;
                break;

            case ProviderPrimarySpecializationChanged::class:
                $logData['previous_primary_id'] = $event->previousPrimary?->id;
                $logData['previous_primary_name'] = $event->previousPrimary?->specialization_name;
                break;
        }

        Log::info('Provider Specialization Activity', $logData);
    }

    /**
     * Get action description from event.
     */
    protected function getActionFromEvent($event): string
    {
        switch (get_class($event)) {
            case ProviderSpecializationCreated::class:
                return 'created';
            case ProviderSpecializationUpdated::class:
                return 'updated';
            case ProviderSpecializationVerified::class:
                return 'verified';
            case ProviderSpecializationRejected::class:
                return 'rejected';
            case ProviderSpecializationActivated::class:
                return 'activated';
            case ProviderSpecializationDeactivated::class:
                return 'deactivated';
            case ProviderPrimarySpecializationChanged::class:
                return 'primary_changed';
            default:
                return 'unknown';
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed($event, $exception): void
    {
        Log::error('Failed to log provider specialization activity', [
            'event' => get_class($event),
            'exception' => $exception->getMessage(),
            'specialization_id' => $event->specialization->id ?? null,
        ]);
    }
}
