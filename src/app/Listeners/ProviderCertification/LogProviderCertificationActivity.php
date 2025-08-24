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
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class LogProviderCertificationActivity implements ShouldQueue
{
    use InteractsWithQueue;

    public $queue = 'logging';

    /**
     * Handle the event.
     */
    public function handle($event): void
    {
        try {
            $certification = $event->certification;
            $userId = Auth::id() ?? 'system';

            $logData = [
                'certification_id' => $certification->id,
                'provider_id' => $certification->provider_id,
                'certification_name' => $certification->certification_name,
                'user_id' => $userId,
                'timestamp' => now()->toISOString(),
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ];

            switch (get_class($event)) {
                case ProviderCertificationCreated::class:
                    $this->logActivity('created', $logData);
                    break;

                case ProviderCertificationUpdated::class:
                    $logData['changes'] = $event->changes ?? [];
                    $this->logActivity('updated', $logData);
                    break;

                case ProviderCertificationVerified::class:
                    $logData['verified_by'] = $event->verifiedBy->id ?? null;
                    $this->logActivity('verified', $logData);
                    break;

                case ProviderCertificationRejected::class:
                    $logData['rejected_by'] = $userId;
                    $logData['reason'] = $event->reason;
                    $this->logActivity('rejected', $logData);
                    break;

                case ProviderCertificationExpired::class:
                    $this->logActivity('expired', $logData);
                    break;

                case ProviderCertificationRenewed::class:
                    $logData['new_expiry_date'] = $event->newExpiryDate;
                    $this->logActivity('renewed', $logData);
                    break;

                case ProviderCertificationSuspended::class:
                    $logData['suspended_by'] = $userId;
                    $logData['reason'] = $event->reason;
                    $this->logActivity('suspended', $logData);
                    break;

                case ProviderCertificationRevoked::class:
                    $logData['revoked_by'] = $userId;
                    $logData['reason'] = $event->reason;
                    $this->logActivity('revoked', $logData);
                    break;
            }

        } catch (\Exception $e) {
            Log::error('Failed to log provider certification activity', [
                'event' => get_class($event),
                'certification_id' => $event->certification->id ?? null,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Log the activity with appropriate level and context.
     */
    private function logActivity(string $action, array $data): void
    {
        $message = "Provider certification {$action}";

        switch ($action) {
            case 'created':
            case 'verified':
            case 'renewed':
                Log::info($message, $data);
                break;

            case 'updated':
                Log::info($message, $data);
                break;

            case 'rejected':
            case 'suspended':
            case 'revoked':
                Log::warning($message, $data);
                break;

            case 'expired':
                Log::notice($message, $data);
                break;

            default:
                Log::info($message, $data);
                break;
        }

        // Also log to a dedicated certification activity log
        Log::channel('certification_activity')->info($message, $data);
    }

    /**
     * Handle a job failure.
     */
    public function failed($event, \Throwable $exception): void
    {
        Log::error('Provider certification activity logging job failed', [
            'event' => get_class($event),
            'certification_id' => $event->certification->id ?? null,
            'error' => $exception->getMessage()
        ]);
    }
}
