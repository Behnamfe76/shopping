<?php

namespace App\Traits;

use App\Enums\CertificationStatus;
use App\Models\ProviderCertification;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

trait HasProviderCertificationRenewalManagement
{
    /**
     * Renew a provider certification
     */
    public function renewCertification(
        ProviderCertification $certification,
        string $newExpiryDate,
        ?string $newCertificationNumber = null,
        ?string $notes = null
    ): bool {
        try {
            DB::beginTransaction();

            $oldExpiryDate = $certification->expiry_date;

            $updateData = [
                'expiry_date' => $newExpiryDate,
                'renewal_date' => now(),
                'status' => CertificationStatus::ACTIVE,
            ];

            if ($newCertificationNumber) {
                $updateData['certification_number'] = $newCertificationNumber;
            }

            if ($notes) {
                $updateData['notes'] = $notes;
            }

            $certification->update($updateData);

            DB::commit();

            // Fire renewal event
            event(new \App\Events\ProviderCertification\ProviderCertificationRenewed($certification, $oldExpiryDate));

            Log::info('Provider certification renewed', [
                'certification_id' => $certification->id,
                'provider_id' => $certification->provider_id,
                'old_expiry_date' => $oldExpiryDate,
                'new_expiry_date' => $newExpiryDate,
            ]);

            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to renew provider certification', [
                'certification_id' => $certification->id,
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }

    /**
     * Check if certification is expiring soon
     */
    public function isCertificationExpiringSoon(
        ProviderCertification $certification,
        int $days = 30
    ): bool {
        if (! $certification->expiry_date) {
            return false;
        }

        $expiryDate = Carbon::parse($certification->expiry_date);
        $thresholdDate = Carbon::now()->addDays($days);

        return $expiryDate->lte($thresholdDate) && $expiryDate->isFuture();
    }

    /**
     * Check if certification is expired
     */
    public function isCertificationExpired(ProviderCertification $certification): bool
    {
        if (! $certification->expiry_date) {
            return false;
        }

        return Carbon::parse($certification->expiry_date)->isPast();
    }

    /**
     * Mark certification as expired
     */
    public function markCertificationAsExpired(ProviderCertification $certification): bool
    {
        try {
            $certification->update([
                'status' => CertificationStatus::EXPIRED,
            ]);

            // Fire expiration event
            event(new \App\Events\ProviderCertification\ProviderCertificationExpired($certification));

            Log::info('Provider certification marked as expired', [
                'certification_id' => $certification->id,
                'provider_id' => $certification->provider_id,
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error('Failed to mark certification as expired', [
                'certification_id' => $certification->id,
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }

    /**
     * Mark certification as pending renewal
     */
    public function markCertificationAsPendingRenewal(ProviderCertification $certification): bool
    {
        try {
            $certification->update([
                'status' => CertificationStatus::PENDING_RENEWAL,
            ]);

            Log::info('Provider certification marked as pending renewal', [
                'certification_id' => $certification->id,
                'provider_id' => $certification->provider_id,
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error('Failed to mark certification as pending renewal', [
                'certification_id' => $certification->id,
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }

    /**
     * Get certifications expiring soon
     */
    public function getCertificationsExpiringSoon(int $days = 30): Collection
    {
        $thresholdDate = Carbon::now()->addDays($days);

        return $this->certifications()
            ->where('expiry_date', '<=', $thresholdDate)
            ->where('expiry_date', '>', Carbon::now())
            ->where('status', '!=', CertificationStatus::EXPIRED)
            ->orderBy('expiry_date', 'asc')
            ->get();
    }

    /**
     * Get expired certifications
     */
    public function getExpiredCertifications(): Collection
    {
        return $this->certifications()
            ->where('expiry_date', '<', Carbon::now())
            ->where('status', '!=', CertificationStatus::EXPIRED)
            ->orderBy('expiry_date', 'asc')
            ->get();
    }

    /**
     * Get certifications pending renewal
     */
    public function getCertificationsPendingRenewal(): Collection
    {
        return $this->certifications()
            ->where('status', CertificationStatus::PENDING_RENEWAL)
            ->orderBy('expiry_date', 'asc')
            ->get();
    }

    /**
     * Get active certifications
     */
    public function getActiveCertifications(): Collection
    {
        return $this->certifications()
            ->where('status', CertificationStatus::ACTIVE)
            ->where('expiry_date', '>', Carbon::now())
            ->orderBy('expiry_date', 'asc')
            ->get();
    }

    /**
     * Process expiration for all certifications
     */
    public function processExpirations(): int
    {
        $expiredCertifications = $this->getExpiredCertifications();
        $processedCount = 0;

        foreach ($expiredCertifications as $certification) {
            if ($this->markCertificationAsExpired($certification)) {
                $processedCount++;
            }
        }

        Log::info('Expiration processing completed', [
            'provider_id' => $this->id,
            'total_expired' => $expiredCertifications->count(),
            'processed' => $processedCount,
        ]);

        return $processedCount;
    }

    /**
     * Process renewal reminders
     */
    public function processRenewalReminders(int $days = 30): int
    {
        $expiringSoon = $this->getCertificationsExpiringSoon($days);
        $reminderCount = 0;

        foreach ($expiringSoon as $certification) {
            if ($this->markCertificationAsPendingRenewal($certification)) {
                $reminderCount++;
            }
        }

        Log::info('Renewal reminders processed', [
            'provider_id' => $this->id,
            'total_expiring_soon' => $expiringSoon->count(),
            'reminders_sent' => $reminderCount,
        ]);

        return $reminderCount;
    }

    /**
     * Get renewal statistics
     */
    public function getRenewalStatistics(): array
    {
        $certifications = $this->certifications();
        $now = Carbon::now();

        return [
            'total' => $certifications->count(),
            'active' => $certifications->where('status', CertificationStatus::ACTIVE)->count(),
            'expired' => $certifications->where('status', CertificationStatus::EXPIRED)->count(),
            'pending_renewal' => $certifications->where('status', CertificationStatus::PENDING_RENEWAL)->count(),
            'expiring_soon_30_days' => $this->getCertificationsExpiringSoon(30)->count(),
            'expiring_soon_60_days' => $this->getCertificationsExpiringSoon(60)->count(),
            'expiring_soon_90_days' => $this->getCertificationsExpiringSoon(90)->count(),
            'renewed_this_month' => $certifications->where('renewal_date', '>=', $now->startOfMonth())->count(),
            'renewed_this_year' => $certifications->where('renewal_date', '>=', $now->startOfYear())->count(),
        ];
    }

    /**
     * Get renewal history
     */
    public function getRenewalHistory(): Collection
    {
        return $this->certifications()
            ->whereNotNull('renewal_date')
            ->orderBy('renewal_date', 'desc')
            ->get();
    }

    /**
     * Check if provider has any active certifications
     */
    public function hasActiveCertifications(): bool
    {
        return $this->getActiveCertifications()->isNotEmpty();
    }

    /**
     * Check if provider has any expired certifications
     */
    public function hasExpiredCertifications(): bool
    {
        return $this->getExpiredCertifications()->isNotEmpty();
    }

    /**
     * Check if provider has any certifications expiring soon
     */
    public function hasCertificationsExpiringSoon(int $days = 30): bool
    {
        return $this->getCertificationsExpiringSoon($days)->isNotEmpty();
    }

    /**
     * Get next certification to expire
     */
    public function getNextExpiringCertification(): ?ProviderCertification
    {
        return $this->certifications()
            ->where('expiry_date', '>', Carbon::now())
            ->where('status', '!=', CertificationStatus::EXPIRED)
            ->orderBy('expiry_date', 'asc')
            ->first();
    }

    /**
     * Get days until next certification expires
     */
    public function getDaysUntilNextExpiration(): ?int
    {
        $nextExpiring = $this->getNextExpiringCertification();

        if (! $nextExpiring || ! $nextExpiring->expiry_date) {
            return null;
        }

        return Carbon::now()->diffInDays($nextExpiring->expiry_date, false);
    }
}
