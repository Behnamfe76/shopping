<?php

namespace Fereydooni\Shopping\database\factories;

use Fereydooni\Shopping\app\Enums\EmployeeNotePriority;
use Fereydooni\Shopping\app\Enums\EmployeeNoteType;
use Fereydooni\Shopping\app\Models\Employee;
use Fereydooni\Shopping\app\Models\EmployeeNote;
use Fereydooni\Shopping\app\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class EmployeeNoteFactory extends Factory
{
    protected $model = EmployeeNote::class;

    public function definition(): array
    {
        return [
            'employee_id' => Employee::factory(),
            'user_id' => User::factory(),
            'title' => $this->faker->sentence(3, 6),
            'content' => $this->faker->paragraphs(rand(1, 3), true),
            'note_type' => $this->faker->randomElement(EmployeeNoteType::cases()),
            'priority' => $this->faker->randomElement(EmployeeNotePriority::cases()),
            'is_private' => $this->faker->boolean(20), // 20% chance of being private
            'is_archived' => $this->faker->boolean(10), // 10% chance of being archived
            'tags' => $this->faker->randomElements([
                'performance', 'training', 'feedback', 'goal', 'incident',
                'praise', 'warning', 'general', 'important', 'urgent',
            ], rand(0, 3)),
            'attachments' => [],
        ];
    }

    public function performance(): static
    {
        return $this->state(fn (array $attributes) => [
            'note_type' => EmployeeNoteType::PERFORMANCE,
            'priority' => EmployeeNotePriority::HIGH,
            'tags' => ['performance', 'review', 'evaluation'],
        ]);
    }

    public function training(): static
    {
        return $this->state(fn (array $attributes) => [
            'note_type' => EmployeeNoteType::TRAINING,
            'priority' => EmployeeNotePriority::MEDIUM,
            'tags' => ['training', 'learning', 'development'],
        ]);
    }

    public function warning(): static
    {
        return $this->state(fn (array $attributes) => [
            'note_type' => EmployeeNoteType::WARNING,
            'priority' => EmployeeNotePriority::HIGH,
            'is_private' => true,
            'tags' => ['warning', 'disciplinary', 'concern'],
        ]);
    }

    public function praise(): static
    {
        return $this->state(fn (array $attributes) => [
            'note_type' => EmployeeNoteType::PRAISE,
            'priority' => EmployeeNotePriority::MEDIUM,
            'is_private' => false,
            'tags' => ['praise', 'recognition', 'achievement'],
        ]);
    }

    public function incident(): static
    {
        return $this->state(fn (array $attributes) => [
            'note_type' => EmployeeNoteType::INCIDENT,
            'priority' => EmployeeNotePriority::URGENT,
            'is_private' => true,
            'tags' => ['incident', 'safety', 'urgent'],
        ]);
    }

    public function goal(): static
    {
        return $this->state(fn (array $attributes) => [
            'note_type' => EmployeeNoteType::GOAL,
            'priority' => EmployeeNotePriority::MEDIUM,
            'is_private' => false,
            'tags' => ['goal', 'planning', 'objective'],
        ]);
    }

    public function feedback(): static
    {
        return $this->state(fn (array $attributes) => [
            'note_type' => EmployeeNoteType::FEEDBACK,
            'priority' => EmployeeNotePriority::LOW,
            'is_private' => false,
            'tags' => ['feedback', 'suggestion', 'improvement'],
        ]);
    }

    public function general(): static
    {
        return $this->state(fn (array $attributes) => [
            'note_type' => EmployeeNoteType::GENERAL,
            'priority' => EmployeeNotePriority::LOW,
            'is_private' => false,
            'tags' => ['general', 'information'],
        ]);
    }

    public function urgent(): static
    {
        return $this->state(fn (array $attributes) => [
            'priority' => EmployeeNotePriority::URGENT,
            'tags' => array_merge($this->faker->randomElements([
                'urgent', 'important', 'critical',
            ], rand(1, 2)), $this->faker->randomElements([
                'performance', 'training', 'feedback', 'goal', 'incident',
                'praise', 'warning', 'general',
            ], rand(0, 1))),
        ]);
    }

    public function highPriority(): static
    {
        return $this->state(fn (array $attributes) => [
            'priority' => EmployeeNotePriority::HIGH,
            'tags' => array_merge($this->faker->randomElements([
                'important', 'high-priority',
            ], rand(1, 2)), $this->faker->randomElements([
                'performance', 'training', 'feedback', 'goal', 'incident',
                'praise', 'warning', 'general',
            ], rand(0, 1))),
        ]);
    }

    public function private(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_private' => true,
            'tags' => array_merge($this->faker->randomElements([
                'private', 'confidential',
            ], rand(1, 2)), $this->faker->randomElements([
                'performance', 'training', 'feedback', 'goal', 'incident',
                'praise', 'warning', 'general',
            ], rand(0, 1))),
        ]);
    }

    public function archived(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_archived' => true,
            'tags' => array_merge($this->faker->randomElements([
                'archived', 'historical',
            ], rand(1, 2)), $this->faker->randomElements([
                'performance', 'training', 'feedback', 'goal', 'incident',
                'praise', 'warning', 'general',
            ], rand(0, 1))),
        ]);
    }
}
