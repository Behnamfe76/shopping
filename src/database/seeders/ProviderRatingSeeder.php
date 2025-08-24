<?php

namespace Database\Seeders;

use App\Models\ProviderRating;
use App\Models\Provider;
use App\Models\User;
use App\Enums\RatingCategory;
use App\Enums\RatingStatus;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProviderRatingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get existing providers and users
        $providers = Provider::all();
        $users = User::all();

        if ($providers->isEmpty() || $users->isEmpty()) {
            $this->command->warn('No providers or users found. Skipping ProviderRating seeding.');
            return;
        }

        $this->command->info('Seeding ProviderRating data...');

        // Clear existing ratings
        DB::table('provider_ratings')->truncate();

        // Create ratings for each provider
        foreach ($providers as $provider) {
            $this->createProviderRatings($provider, $users);
        }

        $this->command->info('ProviderRating data seeded successfully!');
    }

    /**
     * Create ratings for a specific provider
     */
    protected function createProviderRatings(Provider $provider, $users): void
    {
        $ratingCount = rand(5, 25); // Random number of ratings per provider
        $ratings = [];

        for ($i = 0; $i < $ratingCount; $i++) {
            $user = $users->random();
            $rating = $this->generateRating($provider, $user);
            $ratings[] = $rating;
        }

        // Insert ratings in batches
        foreach (array_chunk($ratings, 100) as $batch) {
            DB::table('provider_ratings')->insert($batch);
        }

        $this->command->info("Created {$ratingCount} ratings for provider: {$provider->name}");
    }

    /**
     * Generate a single rating
     */
    protected function generateRating(Provider $provider, User $user): array
    {
        $ratingValue = $this->generateRealisticRating();
        $category = $this->getRandomCategory();
        $status = $this->getRandomStatus();
        $wouldRecommend = $ratingValue >= 3.5;
        $isVerified = $this->faker->boolean(80);
        $hasModerator = $status !== RatingStatus::PENDING;

        $rating = [
            'provider_id' => $provider->id,
            'user_id' => $user->id,
            'rating_value' => $ratingValue,
            'max_rating' => 5,
            'category' => $category,
            'title' => $this->generateRatingTitle($ratingValue, $category),
            'comment' => $this->generateRatingComment($ratingValue, $category),
            'pros' => $this->generatePros($ratingValue),
            'cons' => $this->generateCons($ratingValue),
            'would_recommend' => $wouldRecommend,
            'rating_criteria' => $this->generateRatingCriteria($ratingValue),
            'helpful_votes' => $this->generateHelpfulVotes($ratingValue),
            'total_votes' => $this->generateTotalVotes($ratingValue),
            'is_verified' => $isVerified,
            'status' => $status,
            'ip_address' => $this->faker->ipv4(),
            'user_agent' => $this->faker->userAgent(),
            'moderator_id' => $hasModerator ? $users->random()->id : null,
            'moderation_notes' => $hasModerator ? $this->faker->sentence() : null,
            'rejection_reason' => $status === RatingStatus::REJECTED ? $this->faker->sentence() : null,
            'flag_reason' => $status === RatingStatus::FLAGGED ? $this->faker->sentence() : null,
            'verified_at' => $isVerified ? $this->faker->dateTimeBetween('-1 year', 'now') : null,
            'moderated_at' => $hasModerator ? $this->faker->dateTimeBetween('-1 year', 'now') : null,
            'created_at' => $this->faker->dateTimeBetween('-1 year', 'now'),
            'updated_at' => $this->faker->dateTimeBetween('-1 year', 'now'),
        ];

        return $rating;
    }

    /**
     * Generate realistic rating value
     */
    protected function generateRealisticRating(): float
    {
        // Generate ratings with a more realistic distribution
        // Most ratings tend to be positive (4-5 stars)
        $rand = $this->faker->randomFloat(2, 0, 1);

        if ($rand < 0.6) {
            // 60% chance of 4-5 stars
            return $this->faker->randomFloat(1, 4.0, 5.0);
        } elseif ($rand < 0.8) {
            // 20% chance of 3-4 stars
            return $this->faker->randomFloat(1, 3.0, 4.0);
        } elseif ($rand < 0.95) {
            // 15% chance of 2-3 stars
            return $this->faker->randomFloat(1, 2.0, 3.0);
        } else {
            // 5% chance of 1-2 stars
            return $this->faker->randomFloat(1, 1.0, 2.0);
        }
    }

    /**
     * Get random category
     */
    protected function getRandomCategory(): string
    {
        $categories = RatingCategory::cases();
        return $this->faker->randomElement($categories)->value;
    }

    /**
     * Get random status
     */
    protected function getRandomStatus(): string
    {
        $statuses = RatingStatus::cases();
        return $this->faker->randomElement($statuses)->value;
    }

    /**
     * Generate rating title
     */
    protected function generateRatingTitle(float $rating, string $category): string
    {
        $titles = [
            'overall' => [
                'Excellent service overall',
                'Great experience',
                'Good but could be better',
                'Disappointing service',
                'Terrible experience',
            ],
            'quality' => [
                'High quality work',
                'Good quality service',
                'Average quality',
                'Poor quality',
                'Very poor quality',
            ],
            'service' => [
                'Outstanding service',
                'Good service',
                'Average service',
                'Poor service',
                'Terrible service',
            ],
            'pricing' => [
                'Great value for money',
                'Fair pricing',
                'Reasonable prices',
                'A bit expensive',
                'Overpriced',
            ],
            'communication' => [
                'Excellent communication',
                'Good communication',
                'Average communication',
                'Poor communication',
                'Terrible communication',
            ],
            'reliability' => [
                'Very reliable',
                'Reliable service',
                'Somewhat reliable',
                'Not very reliable',
                'Unreliable',
            ],
        ];

        $categoryTitles = $titles[$category] ?? $titles['overall'];

        if ($rating >= 4.5) {
            return $categoryTitles[0];
        } elseif ($rating >= 3.5) {
            return $categoryTitles[1];
        } elseif ($rating >= 2.5) {
            return $categoryTitles[2];
        } elseif ($rating >= 1.5) {
            return $categoryTitles[3];
        } else {
            return $categoryTitles[4];
        }
    }

    /**
     * Generate rating comment
     */
    protected function generateRatingComment(float $rating, string $category): string
    {
        $comments = [
            'overall' => [
                'This provider exceeded my expectations in every way. The service was professional, timely, and the results were outstanding.',
                'Overall, I had a good experience with this provider. They delivered what was promised and were easy to work with.',
                'The service was adequate but nothing special. They met the basic requirements but didn\'t go above and beyond.',
                'I was disappointed with the overall experience. The service was below average and didn\'t meet my expectations.',
                'This was a terrible experience. The provider was unprofessional and the results were completely unsatisfactory.',
            ],
            'quality' => [
                'The quality of work was exceptional. Attention to detail was impressive and the final result exceeded expectations.',
                'Good quality work was delivered. The provider maintained consistent standards throughout the project.',
                'Quality was acceptable but not outstanding. Some areas could have been improved.',
                'The quality was disappointing. Several issues were present and the work seemed rushed.',
                'Poor quality throughout. Many mistakes and the work was not up to professional standards.',
            ],
            'service' => [
                'Outstanding service from start to finish. The team was responsive, professional, and went above and beyond.',
                'Good service was provided. The provider was responsive and handled requests professionally.',
                'Service was adequate. They responded to most requests but could have been more proactive.',
                'Service was poor. Slow responses and unprofessional behavior were common.',
                'Terrible service. Unresponsive, unprofessional, and difficult to work with.',
            ],
            'pricing' => [
                'Excellent value for money. The quality and service received far exceeded the cost.',
                'Fair pricing for the service provided. Good value overall.',
                'Pricing was reasonable but not exceptional. About what I expected for this level of service.',
                'A bit expensive for what was delivered. Could have been better value.',
                'Overpriced for the poor quality and service received. Not worth the cost.',
            ],
            'communication' => [
                'Communication was excellent throughout. Clear, timely, and professional responses.',
                'Good communication overall. The provider kept me informed and responded promptly.',
                'Communication was adequate. Most messages were responded to but could have been more proactive.',
                'Communication was poor. Slow responses and unclear information were common.',
                'Terrible communication. Unresponsive and difficult to get clear answers.',
            ],
            'reliability' => [
                'Extremely reliable. They delivered on every promise and met all deadlines.',
                'Reliable service. They generally kept their commitments and met expectations.',
                'Somewhat reliable. Most deadlines were met but there were some delays.',
                'Not very reliable. Several deadlines were missed and promises were broken.',
                'Unreliable. Consistently missed deadlines and failed to deliver on promises.',
            ],
        ];

        $categoryComments = $comments[$category] ?? $comments['overall'];

        if ($rating >= 4.5) {
            return $categoryComments[0];
        } elseif ($rating >= 3.5) {
            return $categoryComments[1];
        } elseif ($rating >= 2.5) {
            return $categoryComments[2];
        } elseif ($rating >= 1.5) {
            return $categoryComments[3];
        } else {
            return $categoryComments[4];
        }
    }

    /**
     * Generate pros based on rating
     */
    protected function generatePros(float $rating): ?string
    {
        if ($rating < 3.0) {
            return null; // No pros for low ratings
        }

        $pros = [
            'Professional team',
            'On-time delivery',
            'High quality work',
            'Good communication',
            'Reasonable pricing',
            'Attention to detail',
            'Responsive service',
            'Expert knowledge',
            'Flexible approach',
            'Customer-focused',
        ];

        $count = $rating >= 4.5 ? 4 : ($rating >= 4.0 ? 3 : 2);
        $selectedPros = $this->faker->randomElements($pros, min($count, count($pros)));

        return json_encode($selectedPros);
    }

    /**
     * Generate cons based on rating
     */
    protected function generateCons(float $rating): ?string
    {
        if ($rating >= 4.0) {
            return null; // No cons for high ratings
        }

        $cons = [
            'Slow response times',
            'Communication issues',
            'Quality concerns',
            'Missed deadlines',
            'Unprofessional behavior',
            'Overpriced service',
            'Lack of attention to detail',
            'Poor customer service',
            'Inconsistent work',
            'Limited flexibility',
        ];

        $count = $rating <= 2.0 ? 3 : 1;
        $selectedCons = $this->faker->randomElements($cons, min($count, count($cons)));

        return json_encode($selectedCons);
    }

    /**
     * Generate rating criteria
     */
    protected function generateRatingCriteria(float $rating): string
    {
        $criteria = [];
        $categories = ['quality', 'service', 'pricing', 'communication', 'reliability'];

        foreach ($categories as $cat) {
            $baseScore = $rating;
            $variation = $this->faker->randomFloat(1, -0.5, 0.5);
            $criteriaScore = max(1, min(5, $baseScore + $variation));

            $criteria[$cat] = [
                'score' => round($criteriaScore, 1),
                'comment' => $this->faker->sentence(),
            ];
        }

        return json_encode($criteria);
    }

    /**
     * Generate helpful votes
     */
    protected function generateHelpfulVotes(float $rating): int
    {
        // Higher ratings tend to get more helpful votes
        $baseVotes = $rating * 10;
        $variation = $this->faker->numberBetween(-5, 15);
        return max(0, (int) ($baseVotes + $variation));
    }

    /**
     * Generate total votes
     */
    protected function generateTotalVotes(float $rating): int
    {
        $helpfulVotes = $this->generateHelpfulVotes($rating);
        $additionalVotes = $this->faker->numberBetween(0, 20);
        return $helpfulVotes + $additionalVotes;
    }

    /**
     * Get faker instance
     */
    protected function getFaker()
    {
        return \Faker\Factory::create();
    }
}
