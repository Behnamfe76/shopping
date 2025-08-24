<?php

namespace Fereydooni\Shopping\database\factories;

use Fereydooni\Shopping\app\Models\ProviderNote;
use Fereydooni\Shopping\app\Models\Provider;
use Fereydooni\Shopping\app\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\Fereydooni\Shopping\app\Models\ProviderNote>
 */
class ProviderNoteFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = ProviderNote::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $noteTypes = ['general', 'contract', 'payment', 'quality', 'performance', 'communication', 'other'];
        $priorities = ['low', 'medium', 'high', 'urgent'];

        return [
            'provider_id' => Provider::factory(),
            'user_id' => User::factory(),
            'title' => $this->faker->sentence(3, 6),
            'content' => $this->faker->paragraphs(rand(1, 5), true),
            'note_type' => $this->faker->randomElement($noteTypes),
            'priority' => $this->faker->randomElement($priorities),
            'is_private' => $this->faker->boolean(20), // 20% chance of being private
            'is_archived' => $this->faker->boolean(10), // 10% chance of being archived
            'tags' => $this->faker->boolean(70) ? $this->generateTags() : null,
            'attachments' => $this->faker->boolean(30) ? $this->generateAttachments() : null,
            'created_at' => $this->faker->dateTimeBetween('-1 year', 'now'),
            'updated_at' => function (array $attributes) {
                return $attributes['created_at'];
            },
        ];
    }

    /**
     * Generate realistic tags for provider notes
     */
    private function generateTags(): array
    {
        $tagCategories = [
            'status' => ['pending', 'in-progress', 'completed', 'cancelled'],
            'category' => ['billing', 'quality', 'performance', 'contract', 'communication'],
            'priority' => ['urgent', 'important', 'routine', 'low-priority'],
            'department' => ['sales', 'operations', 'finance', 'hr', 'legal'],
            'custom' => ['follow-up', 'review', 'approval', 'documentation', 'meeting'],
        ];

        $tags = [];
        $numTags = rand(1, 4);

        foreach ($tagCategories as $category => $categoryTags) {
            if (count($tags) >= $numTags) break;

            if ($this->faker->boolean(60)) {
                $tags[] = $this->faker->randomElement($categoryTags);
            }
        }

        // Add some custom tags
        if (count($tags) < $numTags && $this->faker->boolean(40)) {
            $customTags = ['custom-' . $this->faker->word(), 'tag-' . $this->faker->word()];
            $tags = array_merge($tags, array_slice($customTags, 0, $numTags - count($tags)));
        }

        return array_unique($tags);
    }

    /**
     * Generate realistic attachment paths
     */
    private function generateAttachments(): array
    {
        $attachmentTypes = ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'jpg', 'png', 'txt'];
        $numAttachments = rand(1, 3);
        $attachments = [];

        for ($i = 0; $i < $numAttachments; $i++) {
            $type = $this->faker->randomElement($attachmentTypes);
            $filename = $this->faker->slug() . '.' . $type;
            $attachments[] = 'attachments/provider-notes/' . $filename;
        }

        return $attachments;
    }

    /**
     * Indicate that the note is high priority.
     */
    public function highPriority(): static
    {
        return $this->state(fn (array $attributes) => [
            'priority' => 'high',
        ]);
    }

    /**
     * Indicate that the note is urgent priority.
     */
    public function urgent(): static
    {
        return $this->state(fn (array $attributes) => [
            'priority' => 'urgent',
        ]);
    }

    /**
     * Indicate that the note is private.
     */
    public function private(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_private' => true,
        ]);
    }

    /**
     * Indicate that the note is archived.
     */
    public function archived(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_archived' => true,
        ]);
    }

    /**
     * Indicate that the note is a contract note.
     */
    public function contract(): static
    {
        return $this->state(fn (array $attributes) => [
            'note_type' => 'contract',
        ]);
    }

    /**
     * Indicate that the note is a payment note.
     */
    public function payment(): static
    {
        return $this->state(fn (array $attributes) => [
            'note_type' => 'payment',
        ]);
    }

    /**
     * Indicate that the note is a quality note.
     */
    public function quality(): static
    {
        return $this->state(fn (array $attributes) => [
            'note_type' => 'quality',
        ]);
    }

    /**
     * Indicate that the note is a performance note.
     */
    public function performance(): static
    {
        return $this->state(fn (array $attributes) => [
            'note_type' => 'performance',
        ]);
    }

    /**
     * Indicate that the note is a communication note.
     */
    public function communication(): static
    {
        return $this->state(fn (array $attributes) => [
            'note_type' => 'communication',
        ]);
    }

    /**
     * Indicate that the note has many tags.
     */
    public function withManyTags(): static
    {
        return $this->state(fn (array $attributes) => [
            'tags' => $this->generateManyTags(),
        ]);
    }

    /**
     * Indicate that the note has many attachments.
     */
    public function withManyAttachments(): static
    {
        return $this->state(fn (array $attributes) => [
            'attachments' => $this->generateManyAttachments(),
        ]);
    }

    /**
     * Generate many tags for testing
     */
    private function generateManyTags(): array
    {
        $tags = [];
        $numTags = rand(5, 10);

        for ($i = 0; $i < $numTags; $i++) {
            $tags[] = 'tag-' . $this->faker->word() . '-' . $i;
        }

        return $tags;
    }

    /**
     * Generate many attachments for testing
     */
    private function generateManyAttachments(): array
    {
        $attachments = [];
        $numAttachments = rand(4, 8);

        for ($i = 0; $i < $numAttachments; $i++) {
            $type = $this->faker->randomElement(['pdf', 'doc', 'jpg', 'png']);
            $filename = 'attachment-' . $i . '.' . $type;
            $attachments[] = 'attachments/provider-notes/' . $filename;
        }

        return $attachments;
    }
}
