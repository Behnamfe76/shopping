<?php

namespace App\Traits;

use App\Services\ProviderInsuranceService;
use Illuminate\Support\Facades\App;

/**
 * Trait HasProviderInsuranceCompliance
 *
 * Provides insurance compliance checking and monitoring functionality
 * for models that need to manage provider insurance compliance.
 */
trait HasProviderInsuranceCompliance
{
    /**
     * Check provider compliance status
     */
    public function checkProviderCompliance(int $providerId): array
    {
        $activeInsurance = App::make(ProviderInsuranceService::class)->findByProviderAndStatus($providerId, 'active');
        $verifiedInsurance = App::make(ProviderInsuranceService::class)->findByProviderAndStatus($providerId, 'verified');

        $complianceStatus = [
            'is_compliant' => false,
            'active_insurance_count' => $activeInsurance->count(),
            'verified_insurance_count' => $verifiedInsurance->count(),
            'missing_insurance_types' => [],
            'expiring_soon_count' => App::make(ProviderInsuranceService::class)->getExpiringSoonCount($providerId),
            'expired_insurance_count' => App::make(ProviderInsuranceService::class)->getExpiredInsuranceCount($providerId),
            'compliance_score' => 0,
        ];

        // Check if provider has required insurance types
        $requiredTypes = ['general_liability', 'professional_liability', 'workers_compensation'];
        $existingTypes = $activeInsurance->pluck('insurance_type')->toArray();

        $complianceStatus['missing_insurance_types'] = array_diff($requiredTypes, $existingTypes);

        // Calculate compliance score
        $totalRequired = count($requiredTypes);
        $totalActive = $complianceStatus['active_insurance_count'];
        $totalVerified = $complianceStatus['verified_insurance_count'];

        $complianceStatus['compliance_score'] = min(100, (($totalActive + $totalVerified) / ($totalRequired * 2)) * 100);
        $complianceStatus['is_compliant'] = $complianceStatus['compliance_score'] >= 80;

        return $complianceStatus;
    }

    /**
     * Get compliance analytics by provider
     */
    public function getComplianceAnalytics(int $providerId): array
    {
        $compliance = $this->checkProviderCompliance($providerId);

        return [
            'provider_id' => $providerId,
            'compliance_status' => $compliance,
            'insurance_summary' => [
                'total_insurance_count' => App::make(ProviderInsuranceService::class)->getInsuranceCount($providerId),
                'insurance_by_type' => $this->getInsuranceCountByType($providerId),
                'insurance_by_status' => $this->getInsuranceCountByStatus($providerId),
                'coverage_amounts' => $this->getCoverageAmounts($providerId),
            ],
        ];
    }

    /**
     * Get global compliance analytics
     */
    public function getGlobalComplianceAnalytics(): array
    {
        return [
            'total_providers' => App::make(ProviderInsuranceService::class)->getTotalInsuranceCount(),
            'compliant_providers' => 0, // This would need to be calculated
            'non_compliant_providers' => 0, // This would need to be calculated
            'average_compliance_score' => 0, // This would need to be calculated
            'insurance_summary' => [
                'total_active_insurance' => App::make(ProviderInsuranceService::class)->getTotalActiveInsuranceCount(),
                'total_expired_insurance' => App::make(ProviderInsuranceService::class)->getTotalExpiredInsuranceCount(),
                'total_expiring_soon' => App::make(ProviderInsuranceService::class)->getTotalExpiringSoonCount(),
                'total_verified_insurance' => App::make(ProviderInsuranceService::class)->getTotalVerifiedInsuranceCount(),
            ],
        ];
    }

    /**
     * Get insurance count by type for a provider
     */
    private function getInsuranceCountByType(int $providerId): array
    {
        $types = ['general_liability', 'professional_liability', 'product_liability', 'workers_compensation', 'auto_insurance', 'property_insurance', 'cyber_insurance', 'other'];
        $counts = [];

        foreach ($types as $type) {
            $counts[$type] = App::make(ProviderInsuranceService::class)->getInsuranceCountByType($providerId, $type);
        }

        return $counts;
    }

    /**
     * Get insurance count by status for a provider
     */
    private function getInsuranceCountByStatus(int $providerId): array
    {
        $statuses = ['active', 'expired', 'cancelled', 'pending', 'suspended'];
        $counts = [];

        foreach ($statuses as $status) {
            $counts[$status] = App::make(ProviderInsuranceService::class)->getInsuranceCountByStatus($providerId, $status);
        }

        return $counts;
    }

    /**
     * Get coverage amounts for a provider
     */
    private function getCoverageAmounts(int $providerId): array
    {
        return [
            'total_coverage' => App::make(ProviderInsuranceService::class)->getTotalCoverageAmountByProvider($providerId),
            'average_coverage' => App::make(ProviderInsuranceService::class)->getAverageCoverageAmountByProvider($providerId),
        ];
    }

    /**
     * Check if provider meets minimum coverage requirements
     */
    public function meetsMinimumCoverageRequirements(int $providerId): bool
    {
        $totalCoverage = App::make(ProviderInsuranceService::class)->getTotalCoverageAmountByProvider($providerId);
        $minimumRequired = 1000000; // $1M minimum coverage

        return $totalCoverage >= $minimumRequired;
    }

    /**
     * Get compliance recommendations for a provider
     */
    public function getComplianceRecommendations(int $providerId): array
    {
        $compliance = $this->checkProviderCompliance($providerId);
        $recommendations = [];

        if (! empty($compliance['missing_insurance_types'])) {
            $recommendations[] = 'Add missing insurance types: '.implode(', ', $compliance['missing_insurance_types']);
        }

        if ($compliance['expiring_soon_count'] > 0) {
            $recommendations[] = "Renew {$compliance['expiring_soon_count']} insurance policies that are expiring soon";
        }

        if ($compliance['expired_insurance_count'] > 0) {
            $recommendations[] = "Address {$compliance['expired_insurance_count']} expired insurance policies";
        }

        if (! $this->meetsMinimumCoverageRequirements($providerId)) {
            $recommendations[] = 'Increase coverage amounts to meet minimum requirements';
        }

        if (empty($recommendations)) {
            $recommendations[] = 'Provider is compliant with all requirements';
        }

        return $recommendations;
    }
}
