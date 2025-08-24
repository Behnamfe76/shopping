<?php

namespace Fereydooni\Shopping\App\Traits;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Fereydooni\Shopping\App\Models\ProviderSpecialization;
use Fereydooni\Shopping\App\Enums\SpecializationCategory;
use Fereydooni\Shopping\App\Enums\ProficiencyLevel;
use Fereydooni\Shopping\App\Enums\VerificationStatus;

trait HasProviderSpecializationAnalytics
{
    /**
     * Get comprehensive specialization analytics for the provider.
     */
    public function getSpecializationAnalytics(): array
    {
        return [
            'overview' => $this->getSpecializationOverview(),
            'by_category' => $this->getSpecializationsByCategoryAnalytics(),
            'by_proficiency' => $this->getSpecializationsByProficiencyAnalytics(),
            'by_experience' => $this->getSpecializationsByExperienceAnalytics(),
            'by_verification' => $this->getSpecializationsByVerificationAnalytics(),
            'trends' => $this->getSpecializationTrends(),
            'performance' => $this->getSpecializationPerformanceMetrics(),
            'recommendations' => $this->getSpecializationRecommendations(),
        ];
    }

    /**
     * Get specialization overview statistics.
     */
    public function getSpecializationOverview(): array
    {
        $total = $this->getSpecializationCount();
        $active = $this->getActiveSpecializationCount();
        $verified = $this->getVerifiedSpecializationCount();
        $pending = $this->getPendingSpecializationCount();
        $rejected = $this->rejectedSpecializations()->count();
        $primary = $this->getPrimarySpecializationCount();

        return [
            'total_specializations' => $total,
            'active_specializations' => $active,
            'verified_specializations' => $verified,
            'pending_specializations' => $pending,
            'rejected_specializations' => $rejected,
            'primary_specializations' => $primary,
            'verification_rate' => $total > 0 ? round(($verified / $total) * 100, 2) : 0,
            'active_rate' => $total > 0 ? round(($active / $total) * 100, 2) : 0,
            'pending_rate' => $total > 0 ? round(($pending / $total) * 100, 2) : 0,
            'rejection_rate' => $total > 0 ? round(($rejected / $total) * 100, 2) : 0,
        ];
    }

    /**
     * Get specializations analytics by category.
     */
    public function getSpecializationsByCategoryAnalytics(): array
    {
        $categories = SpecializationCategory::cases();
        $analytics = [];

        foreach ($categories as $category) {
            $specializations = $this->specializationsByCategory($category->value);
            $count = $specializations->count();

            if ($count > 0) {
                $verified = $specializations->where('verification_status', VerificationStatus::VERIFIED)->count();
                $active = $specializations->where('is_active', true)->count();
                $primary = $specializations->where('is_primary', true)->count();
                $avgExperience = $specializations->avg('years_experience');

                $analytics[$category->value] = [
                    'count' => $count,
                    'verified' => $verified,
                    'active' => $active,
                    'primary' => $primary,
                    'verification_rate' => round(($verified / $count) * 100, 2),
                    'active_rate' => round(($active / $count) * 100, 2),
                    'average_experience' => round($avgExperience, 1),
                    'proficiency_distribution' => $this->getProficiencyDistribution($specializations),
                ];
            }
        }

        return $analytics;
    }

    /**
     * Get specializations analytics by proficiency level.
     */
    public function getSpecializationsByProficiencyAnalytics(): array
    {
        $proficiencyLevels = ProficiencyLevel::cases();
        $analytics = [];

        foreach ($proficiencyLevels as $level) {
            $specializations = $this->specializationsByProficiency($level->value);
            $count = $specializations->count();

            if ($count > 0) {
                $verified = $specializations->where('verification_status', VerificationStatus::VERIFIED)->count();
                $active = $specializations->where('is_active', true)->count();
                $primary = $specializations->where('is_primary', true)->count();
                $avgExperience = $specializations->avg('years_experience');

                $analytics[$level->value] = [
                    'count' => $count,
                    'verified' => $verified,
                    'active' => $active,
                    'primary' => $primary,
                    'verification_rate' => round(($verified / $count) * 100, 2),
                    'active_rate' => round(($active / $count) * 100, 2),
                    'average_experience' => round($avgExperience, 1),
                    'category_distribution' => $this->getCategoryDistribution($specializations),
                ];
            }
        }

        return $analytics;
    }

    /**
     * Get specializations analytics by experience.
     */
    public function getSpecializationsByExperienceAnalytics(): array
    {
        $experienceRanges = [
            'entry' => [0, 2],
            'junior' => [2, 5],
            'mid_level' => [5, 10],
            'senior' => [10, 20],
            'expert' => [20, 50],
        ];

        $analytics = [];

        foreach ($experienceRanges as $level => $range) {
            $specializations = $this->specializationsByExperience($range[0], $range[1]);
            $count = $specializations->count();

            if ($count > 0) {
                $verified = $specializations->where('verification_status', VerificationStatus::VERIFIED)->count();
                $active = $specializations->where('is_active', true)->count();
                $primary = $specializations->where('is_primary', true)->count();

                $analytics[$level] = [
                    'range' => $range,
                    'count' => $count,
                    'verified' => $verified,
                    'active' => $active,
                    'primary' => $primary,
                    'verification_rate' => round(($verified / $count) * 100, 2),
                    'active_rate' => round(($active / $count) * 100, 2),
                    'proficiency_distribution' => $this->getProficiencyDistribution($specializations),
                    'category_distribution' => $this->getCategoryDistribution($specializations),
                ];
            }
        }

        return $analytics;
    }

    /**
     * Get specializations analytics by verification status.
     */
    public function getSpecializationsByVerificationAnalytics(): array
    {
        $statuses = VerificationStatus::cases();
        $analytics = [];

        foreach ($statuses as $status) {
            $specializations = $this->getSpecializationsByVerificationStatus($status->value);
            $count = $specializations->count();

            if ($count > 0) {
                $active = $specializations->where('is_active', true)->count();
                $primary = $specializations->where('is_primary', true)->count();
                $avgExperience = $specializations->avg('years_experience');

                $analytics[$status->value] = [
                    'count' => $count,
                    'active' => $active,
                    'primary' => $primary,
                    'active_rate' => round(($active / $count) * 100, 2),
                    'average_experience' => round($avgExperience, 1),
                    'proficiency_distribution' => $this->getProficiencyDistribution($specializations),
                    'category_distribution' => $this->getCategoryDistribution($specializations),
                ];
            }
        }

        return $analytics;
    }

    /**
     * Get specialization trends over time.
     */
    public function getSpecializationTrends(): array
    {
        $trends = [];
        $months = 6; // Last 6 months

        for ($i = $months - 1; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $startOfMonth = $date->copy()->startOfMonth();
            $endOfMonth = $date->copy()->endOfMonth();

            $created = $this->specializations()
                ->whereBetween('created_at', [$startOfMonth, $endOfMonth])
                ->count();

            $verified = $this->specializations()
                ->whereBetween('verified_at', [$startOfMonth, $endOfMonth])
                ->count();

            $trends[] = [
                'month' => $date->format('Y-m'),
                'month_name' => $date->format('M Y'),
                'created' => $created,
                'verified' => $verified,
                'verification_rate' => $created > 0 ? round(($verified / $created) * 100, 2) : 0,
            ];
        }

        return $trends;
    }

    /**
     * Get specialization performance metrics.
     */
    public function getSpecializationPerformanceMetrics(): array
    {
        $specializations = $this->specializations()->get();

        if ($specializations->isEmpty()) {
            return [
                'total_experience' => 0,
                'average_experience' => 0,
                'experience_distribution' => [],
                'proficiency_score' => 0,
                'verification_score' => 0,
                'completeness_score' => 0,
                'overall_score' => 0,
            ];
        }

        $totalExperience = $specializations->sum('years_experience');
        $averageExperience = $specializations->avg('years_experience');

        // Calculate proficiency score (weighted by experience)
        $proficiencyScore = $this->calculateProficiencyScore($specializations);

        // Calculate verification score
        $verificationScore = $this->calculateVerificationScore($specializations);

        // Calculate completeness score
        $completenessScore = $this->calculateCompletenessScore($specializations);

        // Overall score (weighted average)
        $overallScore = round(($proficiencyScore * 0.4 + $verificationScore * 0.3 + $completenessScore * 0.3), 2);

        return [
            'total_experience' => $totalExperience,
            'average_experience' => round($averageExperience, 1),
            'experience_distribution' => $this->getExperienceDistribution($specializations),
            'proficiency_score' => $proficiencyScore,
            'verification_score' => $verificationScore,
            'completeness_score' => $completenessScore,
            'overall_score' => $overallScore,
        ];
    }

    /**
     * Get specialization recommendations.
     */
    public function getSpecializationRecommendations(): array
    {
        $recommendations = [];
        $analytics = $this->getSpecializationOverview();

        // Check if provider has no specializations
        if ($analytics['total_specializations'] === 0) {
            $recommendations[] = 'Add your first specialization to showcase your expertise.';
            return $recommendations;
        }

        // Check verification rate
        if ($analytics['verification_rate'] < 50) {
            $recommendations[] = 'Submit more specializations for verification to build trust.';
        }

        // Check if no primary specialization
        if ($analytics['primary_specializations'] === 0) {
            $recommendations[] = 'Designate a primary specialization to highlight your main expertise.';
        }

        // Check active rate
        if ($analytics['active_rate'] < 80) {
            $recommendations[] = 'Activate more specializations to increase your visibility.';
        }

        // Check rejection rate
        if ($analytics['rejection_rate'] > 20) {
            $recommendations[] = 'Review rejected specializations and address feedback before resubmitting.';
        }

        // Get category recommendations
        $categoryRecommendations = $this->getCategoryRecommendations();
        $recommendations = array_merge($recommendations, $categoryRecommendations);

        // Get proficiency recommendations
        $proficiencyRecommendations = $this->getProficiencyRecommendations();
        $recommendations = array_merge($recommendations, $proficiencyRecommendations);

        return array_unique($recommendations);
    }

    /**
     * Get category-specific recommendations.
     */
    protected function getCategoryRecommendations(): array
    {
        $recommendations = [];
        $categoryAnalytics = $this->getSpecializationsByCategoryAnalytics();

        foreach ($categoryAnalytics as $category => $data) {
            if ($data['verification_rate'] < 30) {
                $recommendations[] = "Focus on getting more {$category} specializations verified.";
            }

            if ($data['count'] === 1 && $data['verification_status'] === 'rejected') {
                $recommendations[] = "Consider adding more {$category} specializations to diversify your expertise.";
            }
        }

        return $recommendations;
    }

    /**
     * Get proficiency-specific recommendations.
     */
    protected function getProficiencyRecommendations(): array
    {
        $recommendations = [];
        $proficiencyAnalytics = $this->getSpecializationsByProficiencyAnalytics();

        $beginnerCount = $proficiencyAnalytics['beginner']['count'] ?? 0;
        $expertCount = $proficiencyAnalytics['expert']['count'] ?? 0;
        $masterCount = $proficiencyAnalytics['master']['count'] ?? 0;

        if ($beginnerCount > ($expertCount + $masterCount)) {
            $recommendations[] = 'Focus on developing higher proficiency specializations to showcase expertise.';
        }

        if ($expertCount === 0 && $masterCount === 0) {
            $recommendations[] = 'Consider developing expert-level specializations to demonstrate advanced skills.';
        }

        return $recommendations;
    }

    /**
     * Calculate proficiency score.
     */
    protected function calculateProficiencyScore(Collection $specializations): float
    {
        $proficiencyWeights = [
            'beginner' => 1,
            'intermediate' => 2,
            'advanced' => 3,
            'expert' => 4,
            'master' => 5,
        ];

        $totalScore = 0;
        $totalWeight = 0;

        foreach ($specializations as $specialization) {
            $weight = $proficiencyWeights[$specialization->proficiency_level->value] ?? 1;
            $experienceMultiplier = min($specialization->years_experience / 10, 2); // Cap at 2x
            $totalScore += $weight * $experienceMultiplier;
            $totalWeight += $experienceMultiplier;
        }

        return $totalWeight > 0 ? round(($totalScore / $totalWeight) * 20, 2) : 0; // Scale to 0-100
    }

    /**
     * Calculate verification score.
     */
    protected function calculateVerificationScore(Collection $specializations): float
    {
        $verified = $specializations->where('verification_status', VerificationStatus::VERIFIED)->count();
        $total = $specializations->count();

        return $total > 0 ? round(($verified / $total) * 100, 2) : 0;
    }

    /**
     * Calculate completeness score.
     */
    protected function calculateCompletenessScore(Collection $specializations): float
    {
        $totalFields = 0;
        $filledFields = 0;

        foreach ($specializations as $specialization) {
            $fields = [
                'specialization_name',
                'category',
                'description',
                'proficiency_level',
                'years_experience',
            ];

            foreach ($fields as $field) {
                $totalFields++;
                if (!empty($specialization->$field)) {
                    $filledFields++;
                }
            }

            // Bonus for certifications
            if (!empty($specialization->certifications)) {
                $filledFields++;
            }
            $totalFields++;
        }

        return $totalFields > 0 ? round(($filledFields / $totalFields) * 100, 2) : 0;
    }

    /**
     * Get proficiency distribution.
     */
    protected function getProficiencyDistribution(Collection $specializations): array
    {
        $distribution = [];
        $proficiencyLevels = ProficiencyLevel::cases();

        foreach ($proficiencyLevels as $level) {
            $count = $specializations->where('proficiency_level', $level)->count();
            if ($count > 0) {
                $distribution[$level->value] = $count;
            }
        }

        return $distribution;
    }

    /**
     * Get category distribution.
     */
    protected function getCategoryDistribution(Collection $specializations): array
    {
        $distribution = [];
        $categories = SpecializationCategory::cases();

        foreach ($categories as $category) {
            $count = $specializations->where('category', $category)->count();
            if ($count > 0) {
                $distribution[$category->value] = $count;
            }
        }

        return $distribution;
    }

    /**
     * Get experience distribution.
     */
    protected function getExperienceDistribution(Collection $specializations): array
    {
        $ranges = [
            '0-2 years' => [0, 2],
            '2-5 years' => [2, 5],
            '5-10 years' => [5, 10],
            '10-20 years' => [10, 20],
            '20+ years' => [20, 50],
        ];

        $distribution = [];

        foreach ($ranges as $label => $range) {
            $count = $specializations->whereBetween('years_experience', $range)->count();
            if ($count > 0) {
                $distribution[$label] = $count;
            }
        }

        return $distribution;
    }
}
