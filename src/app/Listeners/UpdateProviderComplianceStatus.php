<?php

namespace App\Listeners;

use App\Events\ProviderInsuranceVerified;
use App\Events\ProviderInsuranceExpired;
use App\Events\ProviderInsuranceDeleted;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class UpdateProviderComplianceStatus implements ShouldQueue
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
     * Handle insurance verification event.
     */
    public function handleVerified(ProviderInsuranceVerified $event): void
    {
        try {
            $providerInsurance = $event->providerInsurance;
            $this->updateComplianceStatus($providerInsurance->provider_id, 'verified');

            Log::info('Provider compliance status updated to verified', [
                'provider_id' => $providerInsurance->provider_id,
                'insurance_id' => $providerInsurance->id
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to update provider compliance status for verification', [
                'insurance_id' => $event->providerInsurance->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Handle insurance expiration event.
     */
    public function handleExpired(ProviderInsuranceExpired $event): void
    {
        try {
            $providerInsurance = $event->providerInsurance;
            $this->updateComplianceStatus($providerInsurance->provider_id, 'expired');

            Log::info('Provider compliance status updated to expired', [
                'provider_id' => $providerInsurance->provider_id,
                'insurance_id' => $providerInsurance->id
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to update provider compliance status for expiration', [
                'insurance_id' => $event->providerInsurance->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Handle insurance deletion event.
     */
    public function handleDeleted(ProviderInsuranceDeleted $event): void
    {
        try {
            $providerInsurance = $event->providerInsurance;
            $this->updateComplianceStatus($providerInsurance->provider_id, 'deleted');

            Log::info('Provider compliance status updated to deleted', [
                'provider_id' => $providerInsurance->provider_id,
                'insurance_id' => $providerInsurance->id
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to update provider compliance status for deletion', [
                'insurance_id' => $event->providerInsurance->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Update provider compliance status
     */
    private function updateComplianceStatus(int $providerId, string $status): void
    {
        try {
            DB::table('providers')
                ->where('id', $providerId)
                ->update([
                    'compliance_status' => $status,
                    'compliance_updated_at' => now()
                ]);

            Log::info('Provider compliance status updated in database', [
                'provider_id' => $providerId,
                'status' => $status
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to update provider compliance status in database', [
                'provider_id' => $providerId,
                'status' => $status,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }
}
