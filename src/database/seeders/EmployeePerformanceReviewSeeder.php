<?php

namespace Database\Seeders;

use App\Models\Employee;
use App\Models\EmployeePerformanceReview;
use App\Models\User;
use Carbon\Carbon;
use Faker\Factory as Faker;
use Illuminate\Database\Seeder;

class EmployeePerformanceReviewSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();

        // Get existing employees and users
        $employees = Employee::all();
        $users = User::all();

        if ($employees->isEmpty() || $users->isEmpty()) {
            $this->command->warn('No employees or users found. Please seed employees and users first.');

            return;
        }

        $this->command->info('Seeding employee performance reviews...');

        // Create sample performance reviews
        $this->createSampleReviews($faker, $employees, $users);

        // Create historical reviews
        $this->createHistoricalReviews($faker, $employees, $users);

        // Create pending reviews
        $this->createPendingReviews($faker, $employees, $users);

        // Create overdue reviews
        $this->createOverdueReviews($faker, $employees, $users);

        $this->command->info('Employee performance reviews seeded successfully!');
    }

    /**
     * Create sample performance reviews
     */
    protected function createSampleReviews($faker, $employees, $users): void
    {
        $statuses = ['draft', 'submitted', 'pending_approval', 'approved', 'rejected'];
        $ratingScales = [1.0, 1.5, 2.0, 2.5, 3.0, 3.5, 4.0, 4.5, 5.0];

        foreach ($employees->take(20) as $employee) {
            $reviewer = $users->random();
            $status = $faker->randomElement($statuses);
            $rating = $faker->randomElement($ratingScales);
            $performanceScore = $this->calculatePerformanceScore($rating);

            $review = EmployeePerformanceReview::create([
                'employee_id' => $employee->id,
                'reviewer_id' => $reviewer->id,
                'review_period_start' => $faker->dateTimeBetween('-6 months', '-1 month'),
                'review_period_end' => $faker->dateTimeBetween('-1 month', 'now'),
                'review_date' => $faker->dateTimeBetween('-1 month', 'now'),
                'next_review_date' => $faker->dateTimeBetween('now', '+6 months'),
                'overall_rating' => $rating,
                'performance_score' => $performanceScore,
                'goals_achieved' => $this->generateSampleGoals($faker, true),
                'goals_missed' => $this->generateSampleGoals($faker, false),
                'strengths' => $this->generateSampleStrengths($faker),
                'areas_for_improvement' => $this->generateSampleImprovementAreas($faker),
                'recommendations' => $this->generateSampleRecommendations($faker),
                'employee_comments' => $faker->optional(0.7)->paragraph(),
                'reviewer_comments' => $faker->optional(0.8)->paragraph(),
                'status' => $status,
                'is_approved' => $status === 'approved',
                'approved_by' => $status === 'approved' ? $users->random()->id : null,
                'approved_at' => $status === 'approved' ? $faker->dateTimeBetween('-1 month', 'now') : null,
            ]);

            $this->command->line("Created review {$review->id} for employee {$employee->id} with status {$status}");
        }
    }

    /**
     * Create historical reviews
     */
    protected function createHistoricalReviews($faker, $employees, $users): void
    {
        $statuses = ['approved', 'rejected'];
        $ratingScales = [1.0, 1.5, 2.0, 2.5, 3.0, 3.5, 4.0, 4.5, 5.0];

        foreach ($employees->take(15) as $employee) {
            // Create reviews for the past 2 years
            for ($year = 2; $year >= 1; $year--) {
                $reviewer = $users->random();
                $status = $faker->randomElement($statuses);
                $rating = $faker->randomElement($ratingScales);
                $performanceScore = $this->calculatePerformanceScore($rating);

                $reviewPeriodStart = Carbon::now()->subYears($year)->startOfYear();
                $reviewPeriodEnd = Carbon::now()->subYears($year)->endOfYear();
                $reviewDate = $faker->dateTimeBetween($reviewPeriodStart, $reviewPeriodEnd);

                $review = EmployeePerformanceReview::create([
                    'employee_id' => $employee->id,
                    'reviewer_id' => $reviewer->id,
                    'review_period_start' => $reviewPeriodStart,
                    'review_period_end' => $reviewPeriodEnd,
                    'review_date' => $reviewDate,
                    'next_review_date' => $reviewDate->copy()->addMonths(6),
                    'overall_rating' => $rating,
                    'performance_score' => $performanceScore,
                    'goals_achieved' => $this->generateSampleGoals($faker, true),
                    'goals_missed' => $this->generateSampleGoals($faker, false),
                    'strengths' => $this->generateSampleStrengths($faker),
                    'areas_for_improvement' => $this->generateSampleImprovementAreas($faker),
                    'recommendations' => $this->generateSampleRecommendations($faker),
                    'employee_comments' => $faker->optional(0.6)->paragraph(),
                    'reviewer_comments' => $faker->optional(0.7)->paragraph(),
                    'status' => $status,
                    'is_approved' => $status === 'approved',
                    'approved_by' => $status === 'approved' ? $users->random()->id : null,
                    'approved_at' => $status === 'approved' ? $reviewDate : null,
                ]);

                $this->command->line("Created historical review {$review->id} for employee {$employee->id} for year ".$reviewPeriodStart->year);
            }
        }
    }

    /**
     * Create pending reviews
     */
    protected function createPendingReviews($faker, $employees, $users): void
    {
        $statuses = ['draft', 'submitted', 'pending_approval'];
        $ratingScales = [1.0, 1.5, 2.0, 2.5, 3.0, 3.5, 4.0, 4.5, 5.0];

        foreach ($employees->take(10) as $employee) {
            $reviewer = $users->random();
            $status = $faker->randomElement($statuses);
            $rating = $faker->randomElement($ratingScales);
            $performanceScore = $this->calculatePerformanceScore($rating);

            $review = EmployeePerformanceReview::create([
                'employee_id' => $employee->id,
                'reviewer_id' => $reviewer->id,
                'review_period_start' => $faker->dateTimeBetween('-3 months', '-1 month'),
                'review_period_end' => $faker->dateTimeBetween('-1 month', 'now'),
                'review_date' => $faker->dateTimeBetween('-1 month', 'now'),
                'next_review_date' => $faker->dateTimeBetween('now', '+3 months'),
                'overall_rating' => $rating,
                'performance_score' => $performanceScore,
                'goals_achieved' => $this->generateSampleGoals($faker, true),
                'goals_missed' => $this->generateSampleGoals($faker, false),
                'strengths' => $this->generateSampleStrengths($faker),
                'areas_for_improvement' => $this->generateSampleImprovementAreas($faker),
                'recommendations' => $this->generateSampleRecommendations($faker),
                'employee_comments' => $faker->optional(0.5)->paragraph(),
                'reviewer_comments' => $faker->optional(0.6)->paragraph(),
                'status' => $status,
                'is_approved' => false,
                'approved_by' => null,
                'approved_at' => null,
            ]);

            $this->command->line("Created pending review {$review->id} for employee {$employee->id} with status {$status}");
        }
    }

    /**
     * Create overdue reviews
     */
    protected function createOverdueReviews($faker, $employees, $users): void
    {
        $ratingScales = [1.0, 1.5, 2.0, 2.5, 3.0, 3.5, 4.0, 4.5, 5.0];

        foreach ($employees->take(5) as $employee) {
            $reviewer = $users->random();
            $rating = $faker->randomElement($ratingScales);
            $performanceScore = $this->calculatePerformanceScore($rating);

            $review = EmployeePerformanceReview::create([
                'employee_id' => $employee->id,
                'reviewer_id' => $reviewer->id,
                'review_period_start' => $faker->dateTimeBetween('-6 months', '-3 months'),
                'review_period_end' => $faker->dateTimeBetween('-3 months', '-1 month'),
                'review_date' => $faker->dateTimeBetween('-3 months', '-1 month'),
                'next_review_date' => $faker->dateTimeBetween('-2 months', '-1 week'),
                'overall_rating' => $rating,
                'performance_score' => $performanceScore,
                'goals_achieved' => $this->generateSampleGoals($faker, true),
                'goals_missed' => $this->generateSampleGoals($faker, false),
                'strengths' => $this->generateSampleStrengths($faker),
                'areas_for_improvement' => $this->generateSampleImprovementAreas($faker),
                'recommendations' => $this->generateSampleRecommendations($faker),
                'employee_comments' => $faker->optional(0.4)->paragraph(),
                'reviewer_comments' => $faker->optional(0.5)->paragraph(),
                'status' => 'overdue',
                'is_approved' => false,
                'approved_by' => null,
                'approved_at' => null,
            ]);

            $this->command->line("Created overdue review {$review->id} for employee {$employee->id}");
        }
    }

    /**
     * Calculate performance score based on rating
     */
    protected function calculatePerformanceScore(float $rating): float
    {
        // Convert 1-5 rating to 0-100 scale
        $baseScore = ($rating - 1) * 25; // 0-100 scale

        // Add some variation
        $variation = rand(-5, 5);

        return max(0, min(100, $baseScore + $variation));
    }

    /**
     * Generate sample goals
     */
    protected function generateSampleGoals($faker, bool $achieved): array
    {
        $goalTypes = [
            'Complete project deliverables',
            'Improve technical skills',
            'Enhance communication',
            'Increase productivity',
            'Develop leadership skills',
            'Learn new technologies',
            'Improve time management',
            'Enhance team collaboration',
            'Achieve sales targets',
            'Improve customer satisfaction',
            'Reduce errors and bugs',
            'Increase code quality',
            'Complete training programs',
            'Mentor junior team members',
            'Implement process improvements',
        ];

        $numGoals = $faker->numberBetween(2, 5);
        $selectedGoals = $faker->randomElements($goalTypes, $numGoals);

        $goals = [];
        foreach ($selectedGoals as $goal) {
            $goals[] = [
                'description' => $goal,
                'target_date' => $faker->dateTimeBetween('-6 months', 'now')->format('Y-m-d'),
                'priority' => $faker->randomElement(['low', 'medium', 'high']),
                'achieved' => $achieved,
                'achievement_date' => $achieved ? $faker->dateTimeBetween('-6 months', 'now')->format('Y-m-d') : null,
                'notes' => $faker->optional(0.7)->sentence(),
            ];
        }

        return $goals;
    }

    /**
     * Generate sample strengths
     */
    protected function generateSampleStrengths($faker): string
    {
        $strengths = [
            'Excellent technical skills and problem-solving abilities',
            'Strong communication and interpersonal skills',
            'Reliable and consistent performance',
            'Great team player and collaborator',
            'Self-motivated and takes initiative',
            'Adapts quickly to new situations and technologies',
            'Strong attention to detail and quality',
            'Excellent time management and organization skills',
            'Creative thinking and innovative approach',
            'Strong analytical and critical thinking skills',
            'Proactive in identifying and solving problems',
            'Excellent customer service orientation',
            'Strong leadership and mentoring abilities',
            'Consistent delivery of high-quality work',
            'Great learning ability and continuous improvement mindset',
        ];

        $numStrengths = $faker->numberBetween(3, 6);
        $selectedStrengths = $faker->randomElements($strengths, $numStrengths);

        return implode("\n• ", array_merge(['• '.$selectedStrengths[0]], array_slice($selectedStrengths, 1)));
    }

    /**
     * Generate sample improvement areas
     */
    protected function generateSampleImprovementAreas($faker): string
    {
        $improvementAreas = [
            'Could improve time management and prioritization',
            'Needs to enhance public speaking and presentation skills',
            'Could benefit from additional technical training',
            'Should work on delegating tasks more effectively',
            'Needs to improve conflict resolution skills',
            'Could enhance strategic thinking and planning abilities',
            'Should work on providing more constructive feedback',
            'Needs to improve stress management and work-life balance',
            'Could benefit from cross-functional training',
            'Should work on improving documentation skills',
            'Needs to enhance problem-solving under pressure',
            'Could improve customer relationship management',
            'Should work on innovation and creative thinking',
            'Needs to enhance project management skills',
            'Could benefit from leadership development training',
        ];

        $numAreas = $faker->numberBetween(2, 4);
        $selectedAreas = $faker->randomElements($improvementAreas, $numAreas);

        return implode("\n• ", array_merge(['• '.$selectedAreas[0]], array_slice($selectedAreas, 1)));
    }

    /**
     * Generate sample recommendations
     */
    protected function generateSampleRecommendations($faker): string
    {
        $recommendations = [
            'Continue current performance and maintain high standards',
            'Consider taking on more challenging projects',
            'Enroll in advanced training programs',
            'Take on mentoring responsibilities for junior team members',
            'Participate in cross-functional projects',
            'Consider pursuing professional certifications',
            'Take on leadership roles in team projects',
            'Continue developing technical expertise',
            'Focus on building stronger client relationships',
            'Consider rotational assignments to broaden experience',
            'Participate in innovation and improvement initiatives',
            'Continue developing soft skills and emotional intelligence',
            'Take on strategic planning responsibilities',
            'Consider international assignments or projects',
            'Focus on building industry knowledge and expertise',
        ];

        $numRecommendations = $faker->numberBetween(3, 5);
        $selectedRecommendations = $faker->randomElements($recommendations, $numRecommendations);

        return implode("\n• ", array_merge(['• '.$selectedRecommendations[0]], array_slice($selectedRecommendations, 1)));
    }

    /**
     * Create a comprehensive review for demonstration
     */
    public function createComprehensiveReview(int $employeeId, int $reviewerId): EmployeePerformanceReview
    {
        $faker = Faker::create();

        return EmployeePerformanceReview::create([
            'employee_id' => $employeeId,
            'reviewer_id' => $reviewerId,
            'review_period_start' => Carbon::now()->subMonths(6)->startOfMonth(),
            'review_period_end' => Carbon::now()->endOfMonth(),
            'review_date' => Carbon::now(),
            'next_review_date' => Carbon::now()->addMonths(6),
            'overall_rating' => 4.5,
            'performance_score' => 92.5,
            'goals_achieved' => [
                [
                    'description' => 'Complete major project deliverables',
                    'target_date' => Carbon::now()->subMonth()->format('Y-m-d'),
                    'priority' => 'high',
                    'achieved' => true,
                    'achievement_date' => Carbon::now()->subMonth()->format('Y-m-d'),
                    'notes' => 'Successfully delivered all project milestones on time',
                ],
                [
                    'description' => 'Improve technical skills',
                    'target_date' => Carbon::now()->subMonth()->format('Y-m-d'),
                    'priority' => 'medium',
                    'achieved' => true,
                    'achievement_date' => Carbon::now()->subMonth()->format('Y-m-d'),
                    'notes' => 'Completed advanced training and obtained certification',
                ],
            ],
            'goals_missed' => [
                [
                    'description' => 'Enhance team collaboration',
                    'target_date' => Carbon::now()->subMonth()->format('Y-m-d'),
                    'priority' => 'medium',
                    'achieved' => false,
                    'achievement_date' => null,
                    'notes' => 'Limited opportunities due to remote work constraints',
                ],
            ],
            'strengths' => "• Excellent technical skills and problem-solving abilities\n• Strong communication and interpersonal skills\n• Reliable and consistent performance\n• Great team player and collaborator\n• Self-motivated and takes initiative",
            'areas_for_improvement' => "• Could improve time management and prioritization\n• Needs to enhance public speaking skills\n• Should work on delegating tasks more effectively",
            'recommendations' => "• Continue current performance and maintain high standards\n• Consider taking on more challenging projects\n• Enroll in advanced training programs\n• Take on mentoring responsibilities",
            'employee_comments' => 'I am satisfied with my performance this period and look forward to taking on new challenges.',
            'reviewer_comments' => 'Outstanding performance with clear areas for continued growth and development.',
            'status' => 'approved',
            'is_approved' => true,
            'approved_by' => $reviewerId,
            'approved_at' => Carbon::now(),
        ]);
    }
}
