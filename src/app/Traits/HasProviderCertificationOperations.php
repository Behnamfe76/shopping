<?php

namespace Fereydooni\Shopping\App\Traits;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Fereydooni\Shopping\App\Models\ProviderCertification;
use Fereydooni\Shopping\App\DTOs\ProviderCertificationDTO;
use Fereydooni\Shopping\App\Enums\CertificationStatus;
use Fereydooni\Shopping\App\Enums\VerificationStatus;
use Carbon\Carbon;

trait HasProviderCertificationOperations
{
    /**
     * Get all certifications for the provider.
     */
    public function certifications(): Collection
    {
        return $this->hasMany(ProviderCertification::class, 'provider_id')->get();
    }

    /**
     * Get active certifications for the provider.
     */
    public function activeCertifications(): Collection
    {
        return $this->hasMany(ProviderCertification::class, 'provider_id')
            ->where('status', CertificationStatus::ACTIVE)
            ->get();
    }

    /**
     * Get expired certifications for the provider.
     */
    public function expiredCertifications(): Collection
    {
        return $this->hasMany(ProviderCertification::class, 'provider_id')
            ->where('status', CertificationStatus::EXPIRED)
            ->get();
    }

    /**
     * Get verified certifications for the provider.
     */
    public function verifiedCertifications(): Collection
    {
        return $this->hasMany(ProviderCertification::class, 'provider_id')
            ->where('verification_status', VerificationStatus::VERIFIED)
            ->get();
    }

    /**
     * Get unverified certifications for the provider.
     */
    public function unverifiedCertifications(): Collection
    {
        return $this->hasMany(ProviderCertification::class, 'provider_id')
            ->where('verification_status', VerificationStatus::UNVERIFIED)
            ->get();
    }

    /**
     * Get certifications expiring soon for the provider.
     */
    public function expiringSoonCertifications(int $days = 30): Collection
    {
        return $this->hasMany(ProviderCertification::class, 'provider_id')
            ->where('status', CertificationStatus::ACTIVE)
            ->where('expiry_date', '<=', Carbon::now()->addDays($days))
            ->where('expiry_date', '>', Carbon::now())
            ->get();
    }

    /**
     * Get certifications by category for the provider.
     */
    public function certificationsByCategory(string $category): Collection
    {
        return $this->hasMany(ProviderCertification::class, 'provider_id')
            ->where('category', $category)
            ->get();
    }

    /**
     * Get certifications by status for the provider.
     */
    public function certificationsByStatus(string $status): Collection
    {
        return $this->hasMany(ProviderCertification::class, 'provider_id')
            ->where('status', $status)
            ->get();
    }

    /**
     * Get certifications by verification status for the provider.
     */
    public function certificationsByVerificationStatus(string $verificationStatus): Collection
    {
        return $this->hasMany(ProviderCertification::class, 'provider_id')
            ->where('verification_status', $verificationStatus)
            ->get();
    }

    /**
     * Add a new certification to the provider.
     */
    public function addCertification(array $data): ProviderCertification
    {
        try {
            DB::beginTransaction();

            $data['provider_id'] = $this->id;
            $certification = ProviderCertification::create($data);

            // Update provider's certifications array if it exists
            if (isset($this->certifications)) {
                $currentCertifications = $this->certifications ?? [];
                $currentCertifications[] = $certification->id;
                $this->update(['certifications' => $currentCertifications]);
            }

            DB::commit();

            Log::info('Certification added to provider', [
                'provider_id' => $this->id,
                'certification_id' => $certification->id,
                'certification_name' => $certification->certification_name,
            ]);

            return $certification;

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to add certification to provider', [
                'provider_id' => $this->id,
                'error' => $e->getMessage(),
                'data' => $data,
            ]);
            throw $e;
        }
    }

    /**
     * Add a new certification to the provider and return DTO.
     */
    public function addCertificationDTO(array $data): ProviderCertificationDTO
    {
        $certification = $this->addCertification($data);
        return ProviderCertificationDTO::fromModel($certification);
    }

    /**
     * Update a certification for the provider.
     */
    public function updateCertification(int $certificationId, array $data): bool
    {
        try {
            $certification = $this->certifications()->firstWhere('id', $certificationId);

            if (!$certification) {
                throw new \Exception("Certification not found for this provider");
            }

            $updated = $certification->update($data);

            if ($updated) {
                Log::info('Certification updated for provider', [
                    'provider_id' => $this->id,
                    'certification_id' => $certificationId,
                    'data' => $data,
                ]);
            }

            return $updated;

        } catch (\Exception $e) {
            Log::error('Failed to update certification for provider', [
                'provider_id' => $this->id,
                'certification_id' => $certificationId,
                'error' => $e->getMessage(),
                'data' => $data,
            ]);
            throw $e;
        }
    }

    /**
     * Update a certification for the provider and return DTO.
     */
    public function updateCertificationDTO(int $certificationId, array $data): ?ProviderCertificationDTO
    {
        $updated = $this->updateCertification($certificationId, $data);

        if ($updated) {
            $certification = $this->certifications()->firstWhere('id', $certificationId);
            return ProviderCertificationDTO::fromModel($certification);
        }

        return null;
    }

    /**
     * Remove a certification from the provider.
     */
    public function removeCertification(int $certificationId): bool
    {
        try {
            $certification = $this->certifications()->firstWhere('id', $certificationId);

            if (!$certification) {
                throw new \Exception("Certification not found for this provider");
            }

            $deleted = $certification->delete();

            if ($deleted) {
                // Update provider's certifications array if it exists
                if (isset($this->certifications)) {
                    $currentCertifications = $this->certifications ?? [];
                    $currentCertifications = array_filter($currentCertifications, fn($id) => $id != $certificationId);
                    $this->update(['certifications' => array_values($currentCertifications)]);
                }

                Log::info('Certification removed from provider', [
                    'provider_id' => $this->id,
                    'certification_id' => $certificationId,
                ]);
            }

            return $deleted;

        } catch (\Exception $e) {
            Log::error('Failed to remove certification from provider', [
                'provider_id' => $this->id,
                'certification_id' => $certificationId,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * Get certification count for the provider.
     */
    public function getCertificationCount(): int
    {
        return $this->hasMany(ProviderCertification::class, 'provider_id')->count();
    }

    /**
     * Get certification count by category for the provider.
     */
    public function getCertificationCountByCategory(string $category): int
    {
        return $this->hasMany(ProviderCertification::class, 'provider_id')
            ->where('category', $category)
            ->count();
    }

    /**
     * Get certification count by status for the provider.
     */
    public function getCertificationCountByStatus(string $status): int
    {
        return $this->hasMany(ProviderCertification::class, 'provider_id')
            ->where('status', $status)
            ->count();
    }

    /**
     * Get certification count by verification status for the provider.
     */
    public function getCertificationCountByVerificationStatus(string $verificationStatus): int
    {
        return $this->hasMany(ProviderCertification::class, 'provider_id')
            ->where('verification_status', $verificationStatus)
            ->count();
    }

    /**
     * Check if provider has active certifications.
     */
    public function hasActiveCertifications(): bool
    {
        return $this->hasMany(ProviderCertification::class, 'provider_id')
            ->where('status', CertificationStatus::ACTIVE)
            ->exists();
    }

    /**
     * Check if provider has verified certifications.
     */
    public function hasVerifiedCertifications(): bool
    {
        return $this->hasMany(ProviderCertification::class, 'provider_id')
            ->where('verification_status', VerificationStatus::VERIFIED)
            ->exists();
    }

    /**
     * Check if provider has certifications expiring soon.
     */
    public function hasCertificationsExpiringSoon(int $days = 30): bool
    {
        return $this->hasMany(ProviderCertification::class, 'provider_id')
            ->where('status', CertificationStatus::ACTIVE)
            ->where('expiry_date', '<=', Carbon::now()->addDays($days))
            ->where('expiry_date', '>', Carbon::now())
            ->exists();
    }

    /**
     * Get provider's certification statistics.
     */
    public function getCertificationStatistics(): array
    {
        return [
            'total' => $this->getCertificationCount(),
            'active' => $this->getCertificationCountByStatus(CertificationStatus::ACTIVE->value),
            'expired' => $this->getCertificationCountByStatus(CertificationStatus::EXPIRED->value),
            'verified' => $this->getCertificationCountByVerificationStatus(VerificationStatus::VERIFIED->value),
            'unverified' => $this->getCertificationCountByVerificationStatus(VerificationStatus::UNVERIFIED->value),
            'pending_verification' => $this->getCertificationCountByVerificationStatus(VerificationStatus::PENDING->value),
            'expiring_soon' => $this->expiringSoonCertifications()->count(),
        ];
    }

    /**
     * Search certifications for the provider.
     */
    public function searchCertifications(string $query): Collection
    {
        return $this->hasMany(ProviderCertification::class, 'provider_id')
            ->where(function ($q) use ($query) {
                $q->where('certification_name', 'like', "%{$query}%")
                  ->orWhere('certification_number', 'like', "%{$query}%")
                  ->orWhere('issuing_organization', 'like', "%{$query}%")
                  ->orWhere('description', 'like', "%{$query}%");
            })
            ->get();
    }

    /**
     * Get certifications as DTOs for the provider.
     */
    public function getCertificationsDTO(): Collection
    {
        return $this->certifications()->map(function ($certification) {
            return ProviderCertificationDTO::fromModel($certification);
        });
    }

    /**
     * Get active certifications as DTOs for the provider.
     */
    public function getActiveCertificationsDTO(): Collection
    {
        return $this->activeCertifications()->map(function ($certification) {
            return ProviderCertificationDTO::fromModel($certification);
        });
    }

    /**
     * Get verified certifications as DTOs for the provider.
     */
    public function getVerifiedCertificationsDTO(): Collection
    {
        return $this->verifiedCertifications()->map(function ($certification) {
            return ProviderCertificationDTO::fromModel($certification);
        });
    }

    /**
     * Get expiring soon certifications as DTOs for the provider.
     */
    public function getExpiringSoonCertificationsDTO(int $days = 30): Collection
    {
        return $this->expiringSoonCertifications($days)->map(function ($certification) {
            return ProviderCertificationDTO::fromModel($certification);
        });
    }
}
