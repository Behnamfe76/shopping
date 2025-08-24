<?php

namespace Database\Factories;

use App\Models\ProviderRating;
use App\Models\Provider;
use App\Models\User;
use App\Enums\RatingCategory;
use App\Enums\RatingStatus;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ProviderRating>
 */
class ProviderRatingFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = ProviderRating::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $ratingValue = $this->faker->randomFloat(1, 1, 5);
        $maxRating = 5;
        $category = $this->faker->randomElement(RatingCategory::cases());
        $status = $this->faker->randomElement(RatingStatus::cases());
        $wouldRecommend = $ratingValue >= 3.5;

        return [
            'provider_id' => Provider::factory(),
            'user_id' => User::factory(),
            'rating_value' => $ratingValue,
            'max_rating' => $maxRating,
            'category' => $category,
            'title' => $this->faker->sentence(3, 6),
            'comment' => $this->faker->paragraph(3, 5),
            'pros' => $this->faker->boolean(70) ? $this->faker->sentences(2, 3) : null,
            'cons' => $this->faker->boolean(30) ? $this->faker->sentences(1, 2) : null,
            'would_recommend' => $wouldRecommend,
            'rating_criteria' => $this->generateRatingCriteria($ratingValue),
            'helpful_votes' => $this->faker->numberBetween(0, 50),
            'total_votes' => $this->faker->numberBetween(0, 100),
            'is_verified' => $this->faker->boolean(80),
            'status' => $status,
            'ip_address' => $this->faker->ipv4(),
            'user_agent' => $this->faker->userAgent(),
            'moderator_id' => $status !== RatingStatus::PENDING ? User::factory() : null,
            'moderation_notes' => $status !== RatingStatus::PENDING ? $this->faker->sentence() : null,
            'rejection_reason' => $status === RatingStatus::REJECTED ? $this->faker->sentence() : null,
            'flag_reason' => $status === RatingStatus::FLAGGED ? $this->faker->sentence() : null,
            'verified_at' => $this->faker->boolean(80) ? $this->faker->dateTimeBetween('-1 year', 'now') : null,
            'moderated_at' => $status !== RatingStatus::PENDING ? $this->faker->dateTimeBetween('-1 year', 'now') : null,
            'created_at' => $this->faker->dateTimeBetween('-1 year', 'now'),
            'updated_at' => $this->faker->dateTimeBetween('-1 year', 'now'),
        ];
    }

    /**
     * Generate rating criteria based on rating value
     */
    protected function generateRatingCriteria(float $ratingValue): array
    {
        $criteria = [];
        $categories = ['quality', 'service', 'pricing', 'communication', 'reliability'];

        foreach ($categories as $cat) {
            // Generate criteria score based on main rating with some variation
            $baseScore = $ratingValue;
            $variation = $this->faker->randomFloat(1, -1, 1);
            $criteriaScore = max(1, min(5, $baseScore + $variation));

            $criteria[$cat] = [
                'score' => round($criteriaScore, 1),
                'comment' => $this->faker->sentence(),
            ];
        }

        return $criteria;
    }

    /**
     * Indicate that the rating is for a specific provider.
     */
    public function forProvider(Provider $provider): static
    {
        return $this->state(fn (array $attributes) => [
            'provider_id' => $provider->id,
        ]);
    }

    /**
     * Indicate that the rating is from a specific user.
     */
    public function fromUser(User $user): static
    {
        return $this->state(fn (array $attributes) => [
            'user_id' => $user->id,
        ]);
    }

    /**
     * Indicate that the rating is for a specific category.
     */
    public function forCategory(RatingCategory $category): static
    {
        return $this->state(fn (array $attributes) => [
            'category' => $category,
        ]);
    }

    /**
     * Indicate that the rating has a specific status.
     */
    public function withStatus(RatingStatus $status): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => $status,
        ]);
    }

    /**
     * Indicate that the rating is verified.
     */
    public function verified(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_verified' => true,
            'verified_at' => $this->faker->dateTimeBetween('-1 year', 'now'),
        ]);
    }

    /**
     * Indicate that the rating is not verified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_verified' => false,
            'verified_at' => null,
        ]);
    }

    /**
     * Indicate that the rating is approved.
     */
    public function approved(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => RatingStatus::APPROVED,
            'moderated_at' => $this->faker->dateTimeBetween('-1 year', 'now'),
        ]);
    }

    /**
     * Indicate that the rating is pending.
     */
    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => RatingStatus::PENDING,
            'moderated_at' => null,
        ]);
    }

    /**
     * Indicate that the rating is rejected.
     */
    public function rejected(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => RatingStatus::REJECTED,
            'moderated_at' => $this->faker->dateTimeBetween('-1 year', 'now'),
            'rejection_reason' => $this->faker->sentence(),
        ]);
    }

    /**
     * Indicate that the rating is flagged.
     */
    public function flagged(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => RatingStatus::FLAGGED,
            'moderated_at' => $this->faker->dateTimeBetween('-1 year', 'now'),
            'flag_reason' => $this->faker->sentence(),
        ]);
    }

    /**
     * Indicate that the rating is recommended.
     */
    public function recommended(): static
    {
        return $this->state(fn (array $attributes) => [
            'would_recommend' => true,
            'rating_value' => $this->faker->randomFloat(1, 4, 5),
        ]);
    }

    /**
     * Indicate that the rating is not recommended.
     */
    public function notRecommended(): static
    {
        return $this->state(fn (array $attributes) => [
            'would_recommend' => false,
            'rating_value' => $this->faker->randomFloat(1, 1, 3),
        ]);
    }

    /**
     * Indicate that the rating has high helpful votes.
     */
    public function highlyHelpful(): static
    {
        return $this->state(fn (array $attributes) => [
            'helpful_votes' => $this->faker->numberBetween(20, 100),
            'total_votes' => $this->faker->numberBetween(25, 120),
        ]);
    }

    /**
     * Indicate that the rating has low helpful votes.
     */
    public function lowHelpful(): static
    {
        return $this->state(fn (array $attributes) => [
            'helpful_votes' => $this->faker->numberBetween(0, 10),
            'total_votes' => $this->faker->numberBetween(5, 20),
        ]);
    }

    /**
     * Indicate that the rating is recent.
     */
    public function recent(): static
    {
        return $this->state(fn (array $attributes) => [
            'created_at' => $this->faker->dateTimeBetween('-1 month', 'now'),
            'updated_at' => $this->faker->dateTimeBetween('-1 month', 'now'),
        ]);
    }

    /**
     * Indicate that the rating is old.
     */
    public function old(): static
    {
        return $this->state(fn (array $attributes) => [
            'created_at' => $this->faker->dateTimeBetween('-2 years', '-1 year'),
            'updated_at' => $this->faker->dateTimeBetween('-2 years', '-1 year'),
        ]);
    }

    /**
     * Generate a positive rating.
     */
    public function positive(): static
    {
        return $this->state(fn (array $attributes) => [
            'rating_value' => $this->faker->randomFloat(1, 4, 5),
            'would_recommend' => true,
            'pros' => $this->faker->sentences(3, 4),
            'cons' => null,
        ]);
    }

    /**
     * Generate a negative rating.
     */
    public function negative(): static
    {
        return $this->state(fn (array $attributes) => [
            'rating_value' => $this->faker->randomFloat(1, 1, 2.5),
            'would_recommend' => false,
            'pros' => null,
            'cons' => $this->faker->sentences(2, 3),
        ]);
    }

    /**
     * Generate a neutral rating.
     */
    public function neutral(): static
    {
        return $this->state(fn (array $attributes) => [
            'rating_value' => $this->faker->randomFloat(1, 2.5, 3.5),
            'would_recommend' => $this->faker->boolean(),
            'pros' => $this->faker->boolean(60) ? $this->faker->sentences(1, 2) : null,
            'cons' => $this->faker->boolean(40) ? $this->faker->sentences(1, 2) : null,
        ]);
    }
}
