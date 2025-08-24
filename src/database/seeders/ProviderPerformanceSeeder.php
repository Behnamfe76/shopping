<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use App\Models\ProviderPerformance;
use App\Models\Provider;
use App\Enums\PerformanceGrade;
use App\Enums\PeriodType;
use Carbon\Carbon;
use Faker\Factory as Faker;

class ProviderPerformanceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Skip if no providers exist
        if (!Schema::hasTable('providers') || Provider::count() == 0) {
            $this->command->warn('No providers found. Skipping provider performance seeding.');
            return;
        }

        $this->command->info('Seeding provider performance data...');

        // Get all providers
        $providers = Provider::all();

        if ($providers->isEmpty()) {
            $this->command->warn('No providers available for seeding.');
            return;
        }

        // Seed performance data for each provider
        foreach ($providers as $provider) {
            $this->seedProviderPerformance($provider);
        }

        // Seed additional historical data
        $this->seedHistoricalData($providers);

        // Seed benchmark data
        $this->seedBenchmarkData();

        $this->command->info('Provider performance data seeded successfully!');
    }

    /**
     * Seed performance data for a specific provider.
     */
    protected function seedProviderPerformance(Provider $provider): void
    {
        $faker = Faker::create();

        // Generate performance data for the last 12 months
        for ($i = 0; $i < 12; $i++) {
            $periodStart = Carbon::now()->subMonths($i)->startOfMonth();
            $periodEnd = Carbon::now()->subMonths($i)->endOfMonth();

            // Generate realistic performance metrics
            $metrics = $this->generateRealisticMetrics($faker, $provider, $periodStart);

            // Create performance record
            ProviderPerformance::create([
                'provider_id' => $provider->id,
                'period_start' => $periodStart,
                'period_end' => $periodEnd,
                'period_type' => PeriodType::MONTHLY,
                'total_orders' => $metrics['total_orders'],
                'total_revenue' => $metrics['total_revenue'],
                'average_order_value' => $metrics['average_order_value'],
                'on_time_delivery_rate' => $metrics['on_time_delivery_rate'],
                'return_rate' => $metrics['return_rate'],
                'defect_rate' => $metrics['defect_rate'],
                'customer_satisfaction_score' => $metrics['customer_satisfaction_score'],
                'response_time_avg' => $metrics['response_time_avg'],
                'quality_rating' => $metrics['quality_rating'],
                'delivery_rating' => $metrics['delivery_rating'],
                'communication_rating' => $metrics['communication_rating'],
                'cost_efficiency_score' => $metrics['cost_efficiency_score'],
                'inventory_turnover_rate' => $metrics['inventory_turnover_rate'],
                'lead_time_avg' => $metrics['lead_time_avg'],
                'fill_rate' => $metrics['fill_rate'],
                'accuracy_rate' => $metrics['accuracy_rate'],
                'performance_score' => $metrics['performance_score'],
                'performance_grade' => $metrics['performance_grade'],
                'is_verified' => $faker->boolean(80), // 80% chance of being verified
                'verified_by' => $faker->boolean(70) ? $faker->numberBetween(1, 10) : null,
                'verified_at' => $faker->boolean(70) ? $faker->dateTimeBetween('-6 months', 'now') : null,
                'notes' => $faker->boolean(30) ? $faker->sentence() : null,
            ]);
        }

        // Generate quarterly performance data
        for ($i = 0; $i < 4; $i++) {
            $quarterStart = Carbon::now()->subQuarters($i)->startOfQuarter();
            $quarterEnd = Carbon::now()->subQuarters($i)->endOfQuarter();

            $metrics = $this->generateRealisticMetrics($faker, $provider, $quarterStart, 'quarterly');

            ProviderPerformance::create([
                'provider_id' => $provider->id,
                'period_start' => $quarterStart,
                'period_end' => $quarterEnd,
                'period_type' => PeriodType::QUARTERLY,
                'total_orders' => $metrics['total_orders'],
                'total_revenue' => $metrics['total_revenue'],
                'average_order_value' => $metrics['average_order_value'],
                'on_time_delivery_rate' => $metrics['on_time_delivery_rate'],
                'return_rate' => $metrics['return_rate'],
                'defect_rate' => $metrics['defect_rate'],
                'customer_satisfaction_score' => $metrics['customer_satisfaction_score'],
                'response_time_avg' => $metrics['response_time_avg'],
                'quality_rating' => $metrics['quality_rating'],
                'delivery_rating' => $metrics['delivery_rating'],
                'communication_rating' => $metrics['communication_rating'],
                'cost_efficiency_score' => $metrics['cost_efficiency_score'],
                'inventory_turnover_rate' => $metrics['inventory_turnover_rate'],
                'lead_time_avg' => $metrics['lead_time_avg'],
                'fill_rate' => $metrics['fill_rate'],
                'accuracy_rate' => $metrics['accuracy_rate'],
                'performance_score' => $metrics['performance_score'],
                'performance_grade' => $metrics['performance_grade'],
                'is_verified' => $faker->boolean(90), // 90% chance of being verified for quarterly data
                'verified_by' => $faker->boolean(80) ? $faker->numberBetween(1, 10) : null,
                'verified_at' => $faker->boolean(80) ? $faker->dateTimeBetween('-6 months', 'now') : null,
                'notes' => $faker->boolean(40) ? $faker->sentence() : null,
            ]);
        }

        // Generate yearly performance data
        for ($i = 0; $i < 2; $i++) {
            $yearStart = Carbon::now()->subYears($i)->startOfYear();
            $yearEnd = Carbon::now()->subYears($i)->endOfYear();

            $metrics = $this->generateRealisticMetrics($faker, $provider, $yearStart, 'yearly');

            ProviderPerformance::create([
                'provider_id' => $provider->id,
                'period_start' => $yearStart,
                'period_end' => $yearEnd,
                'period_type' => PeriodType::YEARLY,
                'total_orders' => $metrics['total_orders'],
                'total_revenue' => $metrics['total_revenue'],
                'average_order_value' => $metrics['average_order_value'],
                'on_time_delivery_rate' => $metrics['on_time_delivery_rate'],
                'return_rate' => $metrics['return_rate'],
                'defect_rate' => $metrics['defect_rate'],
                'customer_satisfaction_score' => $metrics['customer_satisfaction_score'],
                'response_time_avg' => $metrics['response_time_avg'],
                'quality_rating' => $metrics['quality_rating'],
                'delivery_rating' => $metrics['delivery_rating'],
                'communication_rating' => $metrics['communication_rating'],
                'cost_efficiency_score' => $metrics['cost_efficiency_score'],
                'inventory_turnover_rate' => $metrics['inventory_turnover_rate'],
                'lead_time_avg' => $metrics['lead_time_avg'],
                'fill_rate' => $metrics['fill_rate'],
                'accuracy_rate' => $metrics['accuracy_rate'],
                'performance_score' => $metrics['performance_score'],
                'performance_grade' => $metrics['performance_grade'],
                'is_verified' => true, // Yearly data is always verified
                'verified_by' => $faker->numberBetween(1, 10),
                'verified_at' => $faker->dateTimeBetween('-6 months', 'now'),
                'notes' => $faker->boolean(50) ? $faker->sentence() : null,
            ]);
        }
    }

    /**
     * Generate realistic performance metrics.
     */
    protected function generateRealisticMetrics($faker, Provider $provider, Carbon $periodStart, string $periodType = 'monthly'): array
    {
        // Base metrics that vary by provider and period
        $baseOrders = $faker->numberBetween(50, 500);
        $baseRevenue = $faker->numberBetween(5000, 50000);

        // Adjust for period type
        $multiplier = match($periodType) {
            'monthly' => 1,
            'quarterly' => 3,
            'yearly' => 12,
            default => 1,
        };

        $totalOrders = $baseOrders * $multiplier;
        $totalRevenue = $baseRevenue * $multiplier;
        $averageOrderValue = $totalRevenue / $totalOrders;

        // Generate realistic performance metrics
        $onTimeDeliveryRate = $faker->randomFloat(2, 85, 98);
        $returnRate = $faker->randomFloat(2, 1, 8);
        $defectRate = $faker->randomFloat(2, 0.5, 5);
        $customerSatisfactionScore = $faker->randomFloat(2, 6.5, 9.5);
        $responseTimeAvg = $faker->randomFloat(2, 2, 24);
        $qualityRating = $faker->randomFloat(2, 7.0, 9.5);
        $deliveryRating = $faker->randomFloat(2, 7.0, 9.5);
        $communicationRating = $faker->randomFloat(2, 7.0, 9.5);
        $costEfficiencyScore = $faker->randomFloat(2, 75, 95);
        $inventoryTurnoverRate = $faker->randomFloat(2, 4, 12);
        $leadTimeAvg = $faker->randomFloat(2, 3, 14);
        $fillRate = $faker->randomFloat(2, 90, 99);
        $accuracyRate = $faker->randomFloat(2, 92, 99);

        // Calculate performance score
        $performanceScore = $this->calculatePerformanceScore([
            'on_time_delivery_rate' => $onTimeDeliveryRate,
            'customer_satisfaction_score' => $customerSatisfactionScore,
            'quality_rating' => $qualityRating,
            'delivery_rating' => $deliveryRating,
            'communication_rating' => $communicationRating,
            'cost_efficiency_score' => $costEfficiencyScore,
        ]);

        // Determine performance grade
        $performanceGrade = $this->determinePerformanceGrade($performanceScore);

        return [
            'total_orders' => $totalOrders,
            'total_revenue' => $totalRevenue,
            'average_order_value' => $averageOrderValue,
            'on_time_delivery_rate' => $onTimeDeliveryRate,
            'return_rate' => $returnRate,
            'defect_rate' => $defectRate,
            'customer_satisfaction_score' => $customerSatisfactionScore,
            'response_time_avg' => $responseTimeAvg,
            'quality_rating' => $qualityRating,
            'delivery_rating' => $deliveryRating,
            'communication_rating' => $communicationRating,
            'cost_efficiency_score' => $costEfficiencyScore,
            'inventory_turnover_rate' => $inventoryTurnoverRate,
            'lead_time_avg' => $leadTimeAvg,
            'fill_rate' => $fillRate,
            'accuracy_rate' => $accuracyRate,
            'performance_score' => $performanceScore,
            'performance_grade' => $performanceGrade,
        ];
    }

    /**
     * Calculate performance score based on metrics.
     */
    protected function calculatePerformanceScore(array $metrics): float
    {
        $weights = [
            'on_time_delivery_rate' => 0.20,
            'customer_satisfaction_score' => 0.25,
            'quality_rating' => 0.20,
            'delivery_rating' => 0.15,
            'communication_rating' => 0.10,
            'cost_efficiency_score' => 0.10,
        ];

        $score = 0;
        foreach ($weights as $metric => $weight) {
            $value = $metrics[$metric];
            if ($metric === 'customer_satisfaction_score') {
                $score += ($value / 10) * 100 * $weight;
            } else {
                $score += $value * $weight;
            }
        }

        return round($score, 2);
    }

    /**
     * Determine performance grade based on score.
     */
    protected function determinePerformanceGrade(float $score): PerformanceGrade
    {
        if ($score >= 90) return PerformanceGrade::A;
        if ($score >= 80) return PerformanceGrade::B;
        if ($score >= 70) return PerformanceGrade::C;
        if ($score >= 60) return PerformanceGrade::D;
        return PerformanceGrade::F;
    }

    /**
     * Seed additional historical data.
     */
    protected function seedHistoricalData($providers): void
    {
        $faker = Faker::create();

        // Generate some weekly performance data for recent periods
        foreach ($providers->take(5) as $provider) { // Only for first 5 providers
            for ($i = 0; $i < 8; $i++) { // Last 8 weeks
                $weekStart = Carbon::now()->subWeeks($i)->startOfWeek();
                $weekEnd = Carbon::now()->subWeeks($i)->endOfWeek();

                $metrics = $this->generateRealisticMetrics($faker, $provider, $weekStart, 'weekly');

                ProviderPerformance::create([
                    'provider_id' => $provider->id,
                    'period_start' => $weekStart,
                    'period_end' => $weekEnd,
                    'period_type' => PeriodType::WEEKLY,
                    'total_orders' => $metrics['total_orders'] / 4, // Weekly orders
                    'total_revenue' => $metrics['total_revenue'] / 4, // Weekly revenue
                    'average_order_value' => $metrics['average_order_value'],
                    'on_time_delivery_rate' => $metrics['on_time_delivery_rate'],
                    'return_rate' => $metrics['return_rate'],
                    'defect_rate' => $metrics['defect_rate'],
                    'customer_satisfaction_score' => $metrics['customer_satisfaction_score'],
                    'response_time_avg' => $metrics['response_time_avg'],
                    'quality_rating' => $metrics['quality_rating'],
                    'delivery_rating' => $metrics['delivery_rating'],
                    'communication_rating' => $metrics['communication_rating'],
                    'cost_efficiency_score' => $metrics['cost_efficiency_score'],
                    'inventory_turnover_rate' => $metrics['inventory_turnover_rate'],
                    'lead_time_avg' => $metrics['lead_time_avg'],
                    'fill_rate' => $metrics['fill_rate'],
                    'accuracy_rate' => $metrics['accuracy_rate'],
                    'performance_score' => $metrics['performance_score'],
                    'performance_grade' => $metrics['performance_grade'],
                    'is_verified' => $faker->boolean(60),
                    'verified_by' => $faker->boolean(60) ? $faker->numberBetween(1, 10) : null,
                    'verified_at' => $faker->boolean(60) ? $faker->dateTimeBetween('-2 months', 'now') : null,
                    'notes' => $faker->boolean(20) ? $faker->sentence() : null,
                ]);
            }
        }
    }

    /**
     * Seed benchmark data.
     */
    protected function seedBenchmarkData(): void
    {
        // This would typically seed industry benchmarks and standards
        // For now, we'll just log that benchmarks would be seeded
        $this->command->info('Benchmark data seeding would be implemented here.');
    }

    /**
     * Truncate all provider performance data.
     */
    public function truncate(): void
    {
        if (Schema::hasTable('provider_performances')) {
            DB::table('provider_performances')->truncate();
            $this->command->info('Provider performance data truncated.');
        }
    }
}
