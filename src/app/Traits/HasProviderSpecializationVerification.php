<?php

namespace Fereydooni\Shopping\App\Traits;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Exception;
use Fereydooni\Shopping\App\Models\ProviderSpecialization;
use Fereydooni\Shopping\App\Enums\VerificationStatus;

trait HasProviderSpecializationVerification
{
    /**
     * Submit a specialization for verification.
     */
    public function submitSpecializationForVerification(int $specializationId): bool
    {
        try {
            $specialization = $this->specializations()->findOrFail($specializationId);

            if ($specialization->verification_status === VerificationStatus::VERIFIED) {
                throw new Exception('Specialization is already verified.');
            }

            if ($specialization->verification_status === VerificationStatus::PENDING) {
                throw new Exception('Specialization is already pending verification.');
            }

            $result = $specialization->markAsPending();

            if ($result && method_exists($this, 'fireEvent')) {
                $this->fireEvent('specialization.submitted_for_verification', $specialization);
            }

            return $result;
        } catch (Exception $e) {
            Log::error("Failed to submit specialization {$specializationId} for verification: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Verify a specialization.
     */
    public function verifySpecialization(int $specializationId, int $verifiedBy = null): bool
    {
        try {
            $specialization = $this->specializations()->findOrFail($specializationId);

            if ($specialization->verification_status === VerificationStatus::VERIFIED) {
                throw new Exception('Specialization is already verified.');
            }

            $verifiedBy = $verifiedBy ?? Auth::id();

            if (!$verifiedBy) {
                throw new Exception('Verifier ID is required.');
            }

            $result = $specialization->verify($verifiedBy);

            if ($result && method_exists($this, 'fireEvent')) {
                $this->fireEvent('specialization.verified', $specialization);
            }

            return $result;
        } catch (Exception $e) {
            Log::error("Failed to verify specialization {$specializationId}: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Reject a specialization.
     */
    public function rejectSpecialization(int $specializationId, string $reason = null): bool
    {
        try {
            $specialization = $this->specializations()->findOrFail($specializationId);

            if ($specialization->verification_status === VerificationStatus::REJECTED) {
                throw new Exception('Specialization is already rejected.');
            }

            $result = $specialization->reject($reason);

            if ($result && method_exists($this, 'fireEvent')) {
                $this->fireEvent('specialization.rejected', $specialization);
            }

            return $result;
        } catch (Exception $e) {
            Log::error("Failed to reject specialization {$specializationId}: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Request verification for all unverified specializations.
     */
    public function requestVerificationForAll(): array
    {
        $results = [];
        $unverifiedSpecializations = $this->unverifiedSpecializations();

        foreach ($unverifiedSpecializations as $specialization) {
            try {
                $result = $this->submitSpecializationForVerification($specialization->id);
                $results[$specialization->id] = [
                    'success' => $result,
                    'message' => $result ? 'Verification requested' : 'Failed to request verification'
                ];
            } catch (Exception $e) {
                $results[$specialization->id] = [
                    'success' => false,
                    'message' => $e->getMessage()
                ];
            }
        }

        return $results;
    }

    /**
     * Get specializations that need verification.
     */
    public function getSpecializationsNeedingVerification(): Collection
    {
        return $this->specializations()
            ->whereIn('verification_status', [
                VerificationStatus::UNVERIFIED,
                VerificationStatus::PENDING
            ])
            ->get();
    }

    /**
     * Get specializations that are overdue for verification.
     */
    public function getOverdueSpecializations(int $daysThreshold = 30): Collection
    {
        return $this->specializations()
            ->where('verification_status', VerificationStatus::PENDING)
            ->where('created_at', '<=', now()->subDays($daysThreshold))
            ->get();
    }

    /**
     * Get specializations that need renewal.
     */
    public function getSpecializationsNeedingRenewal(int $renewalThresholdDays = 365): Collection
    {
        return $this->specializations()
            ->where('verification_status', VerificationStatus::VERIFIED)
            ->where('verified_at', '<=', now()->subDays($renewalThresholdDays))
            ->get();
    }

    /**
     * Check if a specialization can be verified.
     */
    public function canVerifySpecialization(int $specializationId): bool
    {
        $specialization = $this->specializations()->find($specializationId);

        if (!$specialization) {
            return false;
        }

        return in_array($specialization->verification_status, [
            VerificationStatus::UNVERIFIED,
            VerificationStatus::PENDING
        ]);
    }

    /**
     * Check if a specialization can be rejected.
     */
    public function canRejectSpecialization(int $specializationId): bool
    {
        $specialization = $this->specializations()->find($specializationId);

        if (!$specialization) {
            return false;
        }

        return in_array($specialization->verification_status, [
            VerificationStatus::UNVERIFIED,
            VerificationStatus::PENDING
        ]);
    }

    /**
     * Get verification statistics for the provider.
     */
    public function getVerificationStatistics(): array
    {
        $total = $this->getSpecializationCount();
        $verified = $this->getVerifiedSpecializationCount();
        $pending = $this->getPendingSpecializationCount();
        $rejected = $this->rejectedSpecializations()->count();
        $unverified = $this->getUnverifiedSpecializationCount();

        return [
            'total' => $total,
            'verified' => $verified,
            'pending' => $pending,
            'rejected' => $rejected,
            'unverified' => $unverified,
            'verification_rate' => $total > 0 ? round(($verified / $total) * 100, 2) : 0,
            'pending_rate' => $total > 0 ? round(($pending / $total) * 100, 2) : 0,
            'rejection_rate' => $total > 0 ? round(($rejected / $total) * 100, 2) : 0,
        ];
    }

    /**
     * Get verification timeline for a specialization.
     */
    public function getSpecializationVerificationTimeline(int $specializationId): array
    {
        $specialization = $this->specializations()->findOrFail($specializationId);

        $timeline = [
            [
                'event' => 'created',
                'timestamp' => $specialization->created_at,
                'description' => 'Specialization created'
            ]
        ];

        if ($specialization->verification_status === VerificationStatus::PENDING) {
            $timeline[] = [
                'event' => 'submitted_for_verification',
                'timestamp' => $specialization->updated_at,
                'description' => 'Submitted for verification'
            ];
        }

        if ($specialization->verification_status === VerificationStatus::VERIFIED) {
            $timeline[] = [
                'event' => 'verified',
                'timestamp' => $specialization->verified_at,
                'description' => 'Verified by ' . ($specialization->verifiedBy ? $specialization->verifiedBy->name : 'Unknown')
            ];
        }

        if ($specialization->verification_status === VerificationStatus::REJECTED) {
            $timeline[] = [
                'event' => 'rejected',
                'timestamp' => $specialization->updated_at,
                'description' => 'Rejected'
            ];
        }

        return $timeline;
    }

    /**
     * Get verification requirements for a specialization.
     */
    public function getSpecializationVerificationRequirements(int $specializationId): array
    {
        $specialization = $this->specializations()->findOrFail($specializationId);

        $requirements = [
            'basic_info' => [
                'specialization_name' => !empty($specialization->specialization_name),
                'category' => !empty($specialization->category),
                'description' => !empty($specialization->description),
                'proficiency_level' => !empty($specialization->proficiency_level),
            ],
            'experience' => [
                'years_experience' => $specialization->years_experience > 0,
                'experience_validation' => $specialization->years_experience <= 50,
            ],
            'certifications' => [
                'has_certifications' => !empty($specialization->certifications),
                'certification_count' => is_array($specialization->certifications) ? count($specialization->certifications) : 0,
            ],
            'completeness' => [
                'all_fields_filled' => $this->isSpecializationComplete($specialization),
                'description_length' => strlen($specialization->description ?? '') >= 50,
            ]
        ];

        $requirements['overall_score'] = $this->calculateVerificationScore($requirements);

        return $requirements;
    }

    /**
     * Check if a specialization is complete for verification.
     */
    protected function isSpecializationComplete(ProviderSpecialization $specialization): bool
    {
        $requiredFields = [
            'specialization_name',
            'category',
            'description',
            'proficiency_level',
            'years_experience'
        ];

        foreach ($requiredFields as $field) {
            if (empty($specialization->$field)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Calculate verification score based on requirements.
     */
    protected function calculateVerificationScore(array $requirements): float
    {
        $totalChecks = 0;
        $passedChecks = 0;

        foreach ($requirements as $category => $checks) {
            if ($category === 'overall_score') {
                continue;
            }

            foreach ($checks as $check => $passed) {
                $totalChecks++;
                if ($passed) {
                    $passedChecks++;
                }
            }
        }

        return $totalChecks > 0 ? round(($passedChecks / $totalChecks) * 100, 2) : 0;
    }

    /**
     * Get specializations by verification status.
     */
    public function getSpecializationsByVerificationStatus(string $status): Collection
    {
        return $this->specializations()->where('verification_status', $status)->get();
    }

    /**
     * Get specializations that were verified by a specific user.
     */
    public function getSpecializationsVerifiedBy(int $userId): Collection
    {
        return $this->specializations()->where('verified_by', $userId)->get();
    }

    /**
     * Get the date when a specialization was last verified.
     */
    public function getLastVerificationDate(int $specializationId): ?string
    {
        $specialization = $this->specializations()->find($specializationId);
        return $specialization?->verified_at?->toDateString();
    }

    /**
     * Get the number of days since a specialization was verified.
     */
    public function getDaysSinceVerification(int $specializationId): ?int
    {
        $specialization = $this->specializations()->find($specializationId);
        return $specialization?->getDaysSinceVerification();
    }

    /**
     * Check if a specialization needs renewal.
     */
    public function needsRenewal(int $specializationId, int $renewalThresholdDays = 365): bool
    {
        $specialization = $this->specializations()->find($specializationId);
        return $specialization?->needsRenewal($renewalThresholdDays) ?? false;
    }

    /**
     * Get specializations that are expiring soon.
     */
    public function getExpiringSpecializations(int $daysThreshold = 30): Collection
    {
        return $this->specializations()
            ->where('verification_status', VerificationStatus::VERIFIED)
            ->where('verified_at', '<=', now()->subDays(365 - $daysThreshold))
            ->where('verified_at', '>', now()->subDays(365))
            ->get();
    }
}
