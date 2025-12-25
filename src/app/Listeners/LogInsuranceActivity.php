<?php

namespace App\Listeners;

use App\Events\ProviderInsuranceCreated;
use App\Events\ProviderInsuranceDeleted;
use App\Events\ProviderInsuranceDocumentUploaded;
use App\Events\ProviderInsuranceExpired;
use App\Events\ProviderInsuranceRenewed;
use App\Events\ProviderInsuranceUpdated;
use App\Events\ProviderInsuranceVerified;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class LogInsuranceActivity implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle insurance created event.
     */
    public function handleCreated(ProviderInsuranceCreated $event): void
    {
        $this->logActivity('created', $event->providerInsurance, $event->user);
    }

    /**
     * Handle insurance updated event.
     */
    public function handleUpdated(ProviderInsuranceUpdated $event): void
    {
        $this->logActivity('updated', $event->providerInsurance, $event->user, $event->changes);
    }

    /**
     * Handle insurance deleted event.
     */
    public function handleDeleted(ProviderInsuranceDeleted $event): void
    {
        $this->logActivity('deleted', $event->providerInsurance, $event->deletedBy, null, $event->reason);
    }

    /**
     * Handle insurance verified event.
     */
    public function handleVerified(ProviderInsuranceVerified $event): void
    {
        $this->logActivity('verified', $event->providerInsurance, $event->verifier, $event->verificationDetails);
    }

    /**
     * Handle insurance expired event.
     */
    public function handleExpired(ProviderInsuranceExpired $event): void
    {
        $this->logActivity('expired', $event->providerInsurance, null, ['expiration_date' => $event->expirationDate]);
    }

    /**
     * Handle insurance renewed event.
     */
    public function handleRenewed(ProviderInsuranceRenewed $event): void
    {
        $this->logActivity('renewed', $event->providerInsurance, null, $event->renewalDetails);
    }

    /**
     * Handle insurance document uploaded event.
     */
    public function handleDocumentUploaded(ProviderInsuranceDocumentUploaded $event): void
    {
        $this->logActivity('document_uploaded', $event->providerInsurance, $event->uploader, $event->documentInfo);
    }

    /**
     * Log insurance activity
     */
    private function logActivity(string $action, $providerInsurance, $userId = null, array $details = [], ?string $reason = null): void
    {
        try {
            $activityData = [
                'action' => $action,
                'provider_insurance_id' => $providerInsurance->id,
                'provider_id' => $providerInsurance->provider_id,
                'user_id' => $userId,
                'details' => json_encode($details),
                'reason' => $reason,
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'created_at' => now(),
            ];

            // Log to database
            DB::table('insurance_activity_logs')->insert($activityData);

            // Log to application log
            Log::info("Insurance activity logged: {$action}", [
                'insurance_id' => $providerInsurance->id,
                'provider_id' => $providerInsurance->provider_id,
                'user_id' => $userId,
                'details' => $details,
                'reason' => $reason,
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to log insurance activity', [
                'action' => $action,
                'insurance_id' => $providerInsurance->id,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
