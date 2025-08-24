<?php

namespace Fereydooni\Shopping\app\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProviderInsuranceStatisticsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        $data = parent::toArray($request);

        // Add computed statistics
        $data['total_coverage_formatted'] = $this->formatCurrency($data['total_coverage'] ?? 0);
        $data['average_coverage_formatted'] = $this->formatCurrency($data['average_coverage'] ?? 0);
        $data['expiring_soon_percentage'] = $this->calculateExpiringSoonPercentage();
        $data['verification_rate'] = $this->calculateVerificationRate();
        $data['compliance_rate'] = $this->calculateComplianceRate();

        // Add chart data for frontend visualization
        $data['charts'] = [
            'status_distribution' => $this->getStatusDistribution(),
            'type_distribution' => $this->getTypeDistribution(),
            'verification_status_distribution' => $this->getVerificationStatusDistribution(),
            'monthly_trends' => $this->getMonthlyTrends(),
            'coverage_distribution' => $this->getCoverageDistribution()
        ];

        // Add summary metrics
        $data['summary'] = [
            'total_policies' => $data['total_count'] ?? 0,
            'active_policies' => $data['active_count'] ?? 0,
            'expired_policies' => $data['expired_count'] ?? 0,
            'pending_verification' => $data['pending_verification_count'] ?? 0,
            'verified_policies' => $data['verified_count'] ?? 0,
            'total_providers' => $data['unique_providers'] ?? 0,
            'average_policies_per_provider' => $this->calculateAveragePoliciesPerProvider(),
            'renewal_rate' => $this->calculateRenewalRate()
        ];

        return $data;
    }

    /**
     * Format currency values.
     */
    protected function formatCurrency(float $amount): string
    {
        return '$' . number_format($amount, 2);
    }

    /**
     * Calculate percentage of insurance expiring soon.
     */
    protected function calculateExpiringSoonPercentage(): float
    {
        $total = $this->resource['total_count'] ?? 0;
        $expiringSoon = $this->resource['expiring_soon_count'] ?? 0;

        if ($total === 0) return 0.0;

        return round(($expiringSoon / $total) * 100, 2);
    }

    /**
     * Calculate verification rate.
     */
    protected function calculateVerificationRate(): float
    {
        $total = $this->resource['total_count'] ?? 0;
        $verified = $this->resource['verified_count'] ?? 0;

        if ($total === 0) return 0.0;

        return round(($verified / $total) * 100, 2);
    }

    /**
     * Calculate compliance rate.
     */
    protected function calculateComplianceRate(): float
    {
        $total = $this->resource['total_count'] ?? 0;
        $active = $this->resource['active_count'] ?? 0;

        if ($total === 0) return 0.0;

        return round(($active / $total) * 100, 2);
    }

    /**
     * Calculate average policies per provider.
     */
    protected function calculateAveragePoliciesPerProvider(): float
    {
        $totalPolicies = $this->resource['total_count'] ?? 0;
        $uniqueProviders = $this->resource['unique_providers'] ?? 0;

        if ($uniqueProviders === 0) return 0.0;

        return round($totalPolicies / $uniqueProviders, 2);
    }

    /**
     * Calculate renewal rate.
     */
    protected function calculateRenewalRate(): float
    {
        $total = $this->resource['total_count'] ?? 0;
        $renewed = $this->resource['renewed_count'] ?? 0;

        if ($total === 0) return 0.0;

        return round(($renewed / $total) * 100, 2);
    }

    /**
     * Get status distribution for charts.
     */
    protected function getStatusDistribution(): array
    {
        $statuses = [
            'active' => $this->resource['active_count'] ?? 0,
            'expired' => $this->resource['expired_count'] ?? 0,
            'cancelled' => $this->resource['cancelled_count'] ?? 0,
            'pending' => $this->resource['pending_count'] ?? 0,
            'suspended' => $this->resource['suspended_count'] ?? 0
        ];

        return array_map(function ($count, $status) {
            return [
                'label' => ucfirst($status),
                'value' => $count,
                'color' => $this->getStatusColor($status)
            ];
        }, $statuses, array_keys($statuses));
    }

    /**
     * Get type distribution for charts.
     */
    protected function getTypeDistribution(): array
    {
        $types = $this->resource['type_distribution'] ?? [];

        return array_map(function ($count, $type) {
            return [
                'label' => $this->getTypeLabel($type),
                'value' => $count,
                'color' => $this->getTypeColor($type)
            ];
        }, $types, array_keys($types));
    }

    /**
     * Get verification status distribution for charts.
     */
    protected function getVerificationStatusDistribution(): array
    {
        $statuses = [
            'pending' => $this->resource['pending_verification_count'] ?? 0,
            'verified' => $this->resource['verified_count'] ?? 0,
            'rejected' => $this->resource['rejected_count'] ?? 0,
            'expired' => $this->resource['verification_expired_count'] ?? 0
        ];

        return array_map(function ($count, $status) {
            return [
                'label' => $this->getVerificationStatusLabel($status),
                'value' => $count,
                'color' => $this->getVerificationStatusColor($status)
            ];
        }, $statuses, array_keys($statuses));
    }

    /**
     * Get monthly trends data.
     */
    protected function getMonthlyTrends(): array
    {
        $trends = $this->resource['monthly_trends'] ?? [];

        return array_map(function ($data, $month) {
            return [
                'month' => $month,
                'new_policies' => $data['new_policies'] ?? 0,
                'expired_policies' => $data['expired_policies'] ?? 0,
                'total_coverage' => $data['total_coverage'] ?? 0
            ];
        }, $trends, array_keys($trends));
    }

    /**
     * Get coverage distribution for charts.
     */
    protected function getCoverageDistribution(): array
    {
        $ranges = [
            '0-100k' => ['min' => 0, 'max' => 100000],
            '100k-500k' => ['min' => 100000, 'max' => 500000],
            '500k-1M' => ['min' => 500000, 'max' => 1000000],
            '1M-5M' => ['min' => 1000000, 'max' => 5000000],
            '5M+' => ['min' => 5000000, 'max' => null]
        ];

        $distribution = [];
        foreach ($ranges as $label => $range) {
            $count = $this->getCoverageRangeCount($range['min'], $range['max']);
            $distribution[] = [
                'label' => $label,
                'value' => $count,
                'color' => $this->getCoverageRangeColor($label)
            ];
        }

        return $distribution;
    }

    /**
     * Get count for coverage range.
     */
    protected function getCoverageRangeCount(float $min, ?float $max): int
    {
        // This would typically come from the analytics data
        // For now, return a placeholder
        return 0;
    }

    /**
     * Get status color for charts.
     */
    protected function getStatusColor(string $status): string
    {
        return match ($status) {
            'active' => '#10B981',
            'expired' => '#EF4444',
            'cancelled' => '#6B7280',
            'pending' => '#F59E0B',
            'suspended' => '#8B5CF6',
            default => '#6B7280'
        };
    }

    /**
     * Get type color for charts.
     */
    protected function getTypeColor(string $type): string
    {
        $colors = [
            '#3B82F6', '#10B981', '#F59E0B', '#EF4444',
            '#8B5CF6', '#06B6D4', '#84CC16', '#F97316'
        ];

        $index = crc32($type) % count($colors);
        return $colors[$index];
    }

    /**
     * Get verification status color for charts.
     */
    protected function getVerificationStatusColor(string $status): string
    {
        return match ($status) {
            'pending' => '#F59E0B',
            'verified' => '#10B981',
            'rejected' => '#EF4444',
            'expired' => '#6B7280',
            default => '#6B7280'
        };
    }

    /**
     * Get coverage range color for charts.
     */
    protected function getCoverageRangeColor(string $range): string
    {
        return match ($range) {
            '0-100k' => '#3B82F6',
            '100k-500k' => '#10B981',
            '500k-1M' => '#F59E0B',
            '1M-5M' => '#F97316',
            '5M+' => '#8B5CF6',
            default => '#6B7280'
        };
    }

    /**
     * Get type label.
     */
    protected function getTypeLabel(string $type): string
    {
        return match ($type) {
            'general_liability' => 'General Liability',
            'professional_liability' => 'Professional Liability',
            'product_liability' => 'Product Liability',
            'workers_compensation' => 'Workers Compensation',
            'auto_insurance' => 'Auto Insurance',
            'property_insurance' => 'Property Insurance',
            'cyber_insurance' => 'Cyber Insurance',
            'other' => 'Other',
            default => ucfirst(str_replace('_', ' ', $type))
        };
    }

    /**
     * Get verification status label.
     */
    protected function getVerificationStatusLabel(string $status): string
    {
        return match ($status) {
            'pending' => 'Pending Verification',
            'verified' => 'Verified',
            'rejected' => 'Rejected',
            'expired' => 'Verification Expired',
            default => ucfirst($status)
        };
    }
}
