<?php

namespace App\Traits;

use App\Models\ProviderCertification;
use App\Enums\CertificationCategory;
use App\Enums\CertificationStatus;
use App\Enums\CertificationVerificationStatus;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

trait HasProviderCertificationAnalytics
{
    /**
     * Get comprehensive certification statistics
     */
    public function getCertificationStatistics(): array
    {
        $certifications = $this->certifications();
        $now = Carbon::now();

        return [
            'total' => $certifications->count(),
            'by_status' => [
                'active' => $certifications->where('status', CertificationStatus::ACTIVE)->count(),
                'expired' => $certifications->where('status', CertificationStatus::EXPIRED)->count(),
                'suspended' => $certifications->where('status', CertificationStatus::SUSPENDED)->count(),
                'revoked' => $certifications->where('status', CertificationStatus::REVOKED)->count(),
                'pending_renewal' => $certifications->where('status', CertificationStatus::PENDING_RENEWAL)->count(),
            ],
            'by_verification_status' => [
                'verified' => $certifications->where('verification_status', CertificationVerificationStatus::VERIFIED)->count(),
                'unverified' => $certifications->where('verification_status', CertificationVerificationStatus::UNVERIFIED)->count(),
                'pending' => $certifications->where('verification_status', CertificationVerificationStatus::PENDING)->count(),
                'rejected' => $certifications->where('verification_status', CertificationVerificationStatus::REJECTED)->count(),
                'requires_update' => $certifications->where('verification_status', CertificationVerificationStatus::REQUIRES_UPDATE)->count(),
            ],
            'by_category' => $this->getCertificationCountByCategory(),
            'expiry_analysis' => [
                'expiring_soon_30_days' => $this->getCertificationsExpiringSoon(30)->count(),
                'expiring_soon_60_days' => $this->getCertificationsExpiringSoon(60)->count(),
                'expiring_soon_90_days' => $this->getCertificationsExpiringSoon(90)->count(),
                'expired_this_month' => $certifications->where('expiry_date', '>=', $now->startOfMonth())
                    ->where('expiry_date', '<', $now->startOfMonth()->addMonth())->count(),
                'expired_this_year' => $certifications->where('expiry_date', '>=', $now->startOfYear())
                    ->where('expiry_date', '<', $now->startOfYear()->addYear())->count(),
            ],
            'renewal_analysis' => [
                'renewed_this_month' => $certifications->where('renewal_date', '>=', $now->startOfMonth())->count(),
                'renewed_this_year' => $certifications->where('renewal_date', '>=', $now->startOfYear())->count(),
                'average_renewal_frequency' => $this->getAverageRenewalFrequency(),
            ],
            'credits_analysis' => [
                'total_credits_earned' => $certifications->sum('credits_earned'),
                'average_credits_per_certification' => $certifications->avg('credits_earned'),
                'highest_credits_certification' => $certifications->max('credits_earned'),
            ],
            'issuing_organizations' => $this->getTopIssuingOrganizations(10),
            'recurring_certifications' => $certifications->where('is_recurring', true)->count(),
        ];
    }

    /**
     * Get certification count by category
     */
    public function getCertificationCountByCategory(): array
    {
        $categories = CertificationCategory::cases();
        $counts = [];

        foreach ($categories as $category) {
            $counts[$category->value] = $this->certifications()
                ->where('category', $category->value)
                ->count();
        }

        return $counts;
    }

    /**
     * Get top issuing organizations
     */
    public function getTopIssuingOrganizations(int $limit = 10): array
    {
        return $this->certifications()
            ->select('issuing_organization', DB::raw('count(*) as count'))
            ->whereNotNull('issuing_organization')
            ->groupBy('issuing_organization')
            ->orderBy('count', 'desc')
            ->limit($limit)
            ->pluck('count', 'issuing_organization')
            ->toArray();
    }

    /**
     * Get average renewal frequency
     */
    public function getAverageRenewalFrequency(): ?float
    {
        $renewedCertifications = $this->certifications()
            ->whereNotNull('renewal_date')
            ->whereNotNull('created_at')
            ->get();

        if ($renewedCertifications->isEmpty()) {
            return null;
        }

        $totalDays = 0;
        $count = 0;

        foreach ($renewedCertifications as $certification) {
            $createdDate = Carbon::parse($certification->created_at);
            $renewalDate = Carbon::parse($certification->renewal_date);
            $totalDays += $createdDate->diffInDays($renewalDate);
            $count++;
        }

        return $count > 0 ? round($totalDays / $count, 2) : null;
    }

    /**
     * Get certification trends over time
     */
    public function getCertificationTrends(string $startDate = null, string $endDate = null): array
    {
        $startDate = $startDate ? Carbon::parse($startDate) : Carbon::now()->subYear();
        $endDate = $endDate ? Carbon::parse($endDate) : Carbon::now();

        $trends = [];
        $currentDate = $startDate->copy();

        while ($currentDate <= $endDate) {
            $monthStart = $currentDate->copy()->startOfMonth();
            $monthEnd = $currentDate->copy()->endOfMonth();

            $trends[$currentDate->format('Y-m')] = [
                'new_certifications' => $this->certifications()
                    ->whereBetween('created_at', [$monthStart, $monthEnd])
                    ->count(),
                'renewed_certifications' => $this->certifications()
                    ->whereBetween('renewal_date', [$monthStart, $monthEnd])
                    ->count(),
                'expired_certifications' => $this->certifications()
                    ->whereBetween('expiry_date', [$monthStart, $monthEnd])
                    ->count(),
                'verified_certifications' => $this->certifications()
                    ->whereBetween('verified_at', [$monthStart, $monthEnd])
                    ->count(),
            ];

            $currentDate->addMonth();
        }

        return $trends;
    }

    /**
     * Get certification performance metrics
     */
    public function getCertificationPerformanceMetrics(): array
    {
        $certifications = $this->certifications();
        $now = Carbon::now();

        return [
            'completion_rate' => $this->getCompletionRate(),
            'verification_rate' => $this->getVerificationRate(),
            'renewal_rate' => $this->getRenewalRate(),
            'expiry_rate' => $this->getExpiryRate(),
            'average_certification_lifespan' => $this->getAverageCertificationLifespan(),
            'certification_health_score' => $this->getCertificationHealthScore(),
        ];
    }

    /**
     * Get completion rate (active vs total)
     */
    public function getCompletionRate(): float
    {
        $total = $this->certifications()->count();
        if ($total === 0) {
            return 0.0;
        }

        $active = $this->certifications()
            ->where('status', CertificationStatus::ACTIVE)
            ->count();

        return round(($active / $total) * 100, 2);
    }

    /**
     * Get verification rate
     */
    public function getVerificationRate(): float
    {
        $total = $this->certifications()->count();
        if ($total === 0) {
            return 0.0;
        }

        $verified = $this->certifications()
            ->where('verification_status', CertificationVerificationStatus::VERIFIED)
            ->count();

        return round(($verified / $total) * 100, 2);
    }

    /**
     * Get renewal rate
     */
    public function getRenewalRate(): float
    {
        $total = $this->certifications()->count();
        if ($total === 0) {
            return 0.0;
        }

        $renewed = $this->certifications()
            ->whereNotNull('renewal_date')
            ->count();

        return round(($renewed / $total) * 100, 2);
    }

    /**
     * Get expiry rate
     */
    public function getExpiryRate(): float
    {
        $total = $this->certifications()->count();
        if ($total === 0) {
            return 0.0;
        }

        $expired = $this->certifications()
            ->where('status', CertificationStatus::EXPIRED)
            ->count();

        return round(($expired / $total) * 100, 2);
    }

    /**
     * Get average certification lifespan
     */
    public function getAverageCertificationLifespan(): ?float
    {
        $certifications = $this->certifications()
            ->whereNotNull('created_at')
            ->whereNotNull('expiry_date')
            ->get();

        if ($certifications->isEmpty()) {
            return null;
        }

        $totalDays = 0;
        $count = 0;

        foreach ($certifications as $certification) {
            $createdDate = Carbon::parse($certification->created_at);
            $expiryDate = Carbon::parse($certification->expiry_date);
            $totalDays += $createdDate->diffInDays($expiryDate);
            $count++;
        }

        return $count > 0 ? round($totalDays / $count, 2) : null;
    }

    /**
     * Get certification health score (0-100)
     */
    public function getCertificationHealthScore(): float
    {
        $score = 0;

        // Completion rate (30 points)
        $score += ($this->getCompletionRate() / 100) * 30;

        // Verification rate (25 points)
        $score += ($this->getVerificationRate() / 100) * 25;

        // Renewal rate (20 points)
        $score += ($this->getRenewalRate() / 100) * 20;

        // Low expiry rate (15 points)
        $expiryRate = $this->getExpiryRate();
        $score += max(0, (100 - $expiryRate) / 100) * 15;

        // Recent activity (10 points)
        $recentActivity = $this->certifications()
            ->where('created_at', '>=', Carbon::now()->subMonths(6))
            ->count();
        $score += min(10, $recentActivity * 2);

        return round($score, 2);
    }

    /**
     * Get certification recommendations
     */
    public function getCertificationRecommendations(): array
    {
        $recommendations = [];

        // Check for expiring certifications
        $expiringSoon = $this->getCertificationsExpiringSoon(30);
        if ($expiringSoon->isNotEmpty()) {
            $recommendations[] = [
                'type' => 'renewal_reminder',
                'priority' => 'high',
                'message' => 'You have ' . $expiringSoon->count() . ' certification(s) expiring within 30 days.',
                'action' => 'Review and renew expiring certifications',
                'count' => $expiringSoon->count(),
            ];
        }

        // Check for unverified certifications
        $unverified = $this->certifications()
            ->where('verification_status', CertificationVerificationStatus::UNVERIFIED)
            ->count();
        if ($unverified > 0) {
            $recommendations[] = [
                'type' => 'verification_needed',
                'priority' => 'medium',
                'message' => 'You have ' . $unverified . ' unverified certification(s).',
                'action' => 'Submit verification documents',
                'count' => $unverified,
            ];
        }

        // Check for expired certifications
        $expired = $this->getExpiredCertifications();
        if ($expired->isNotEmpty()) {
            $recommendations[] = [
                'type' => 'expired_certifications',
                'priority' => 'high',
                'message' => 'You have ' . $expired->count() . ' expired certification(s).',
                'action' => 'Renew or replace expired certifications',
                'count' => $expired->count(),
            ];
        }

        return $recommendations;
    }

    /**
     * Export certification analytics data
     */
    public function exportCertificationAnalytics(): array
    {
        return [
            'provider_id' => $this->id,
            'provider_name' => $this->name ?? 'Unknown',
            'generated_at' => now()->toISOString(),
            'statistics' => $this->getCertificationStatistics(),
            'performance_metrics' => $this->getCertificationPerformanceMetrics(),
            'trends' => $this->getCertificationTrends(),
            'recommendations' => $this->getCertificationRecommendations(),
        ];
    }
}
