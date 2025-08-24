<?php

namespace App\Listeners\ProviderCertification;

use App\Events\ProviderCertification\ProviderCertificationCreated;
use App\Events\ProviderCertification\ProviderCertificationUpdated;
use App\Events\ProviderCertification\ProviderCertificationVerified;
use App\Events\ProviderCertification\ProviderCertificationRejected;
use App\Events\ProviderCertification\ProviderCertificationExpired;
use App\Events\ProviderCertification\ProviderCertificationRenewed;
use App\Events\ProviderCertification\ProviderCertificationSuspended;
use App\Events\ProviderCertification\ProviderCertificationRevoked;
use App\Notifications\ProviderCertification\CertificationAdded;
use App\Notifications\ProviderCertification\CertificationVerified;
use App\Notifications\ProviderCertification\CertificationRejected;
use App\Notifications\ProviderCertification\CertificationExpiring;
use App\Notifications\ProviderCertification\CertificationExpired;
use App\Notifications\ProviderCertification\CertificationRenewed;
use App\Notifications\ProviderCertification\CertificationSuspended;
use App\Notifications\ProviderCertification\CertificationRevoked;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class SendProviderCertificationNotification implements ShouldQueue
{
    use InteractsWithQueue;

    public $queue = 'notifications';

    /**
     * Handle the event.
     */
    public function handle($event): void
    {
        try {
            $provider = $event->certification->provider;

            if (!$provider) {
                Log::warning('Provider not found for certification notification', [
                    'certification_id' => $event->certification->id
                ]);
                return;
            }

            switch (get_class($event)) {
                case ProviderCertificationCreated::class:
                    $provider->notify(new CertificationAdded($event->certification));
                    break;

                case ProviderCertificationVerified::class:
                    $provider->notify(new CertificationVerified($event->certification));
                    break;

                case ProviderCertificationRejected::class:
                    $provider->notify(new CertificationRejected($event->certification, $event->reason));
                    break;

                case ProviderCertificationExpired::class:
                    $provider->notify(new CertificationExpired($event->certification));
                    break;

                case ProviderCertificationRenewed::class:
                    $provider->notify(new CertificationRenewed($event->certification));
                    break;

                case ProviderCertificationSuspended::class:
                    $provider->notify(new CertificationSuspended($event->certification, $event->reason));
                    break;

                case ProviderCertificationRevoked::class:
                    $provider->notify(new CertificationRevoked($event->certification, $event->reason));
                    break;
            }

            Log::info('Provider certification notification sent successfully', [
                'event' => get_class($event),
                'certification_id' => $event->certification->id,
                'provider_id' => $provider->id
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to send provider certification notification', [
                'event' => get_class($event),
                'certification_id' => $event->certification->id ?? null,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            throw $e;
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed($event, \Throwable $exception): void
    {
        Log::error('Provider certification notification job failed', [
            'event' => get_class($event),
            'certification_id' => $event->certification->id ?? null,
            'error' => $exception->getMessage()
        ]);
    }
}
