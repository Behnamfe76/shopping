<?php

namespace Fereydooni\Shopping\database\factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Fereydooni\Shopping\app\Models\EmployeeEmergencyContact;
use Fereydooni\Shopping\app\Models\Employee;
use Fereydooni\Shopping\app\Enums\Relationship;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\Fereydooni\Shopping\app\Models\EmployeeEmergencyContact>
 */
class EmployeeEmergencyContactFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = EmployeeEmergencyContact::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $relationships = Relationship::cases();
        $relationship = $this->faker->randomElement($relationships);

        $isPrimary = $this->faker->boolean(20); // 20% chance of being primary

        return [
            'employee_id' => Employee::factory(),
            'contact_name' => $this->faker->name(),
            'relationship' => $relationship,
            'phone_primary' => $this->faker->phoneNumber(),
            'phone_secondary' => $this->faker->optional(0.7)->phoneNumber(),
            'email' => $this->faker->optional(0.8)->safeEmail(),
            'address' => $this->faker->optional(0.9)->streetAddress(),
            'city' => $this->faker->optional(0.9)->city(),
            'state' => $this->faker->optional(0.9)->state(),
            'postal_code' => $this->faker->optional(0.9)->postcode(),
            'country' => $this->faker->optional(0.9)->country(),
            'is_primary' => $isPrimary,
            'is_active' => $this->faker->boolean(90), // 90% chance of being active
            'notes' => $this->faker->optional(0.3)->sentence(),
        ];
    }

    /**
     * Indicate that the contact is a spouse.
     */
    public function spouse(): static
    {
        return $this->state(fn (array $attributes) => [
            'relationship' => Relationship::SPOUSE,
            'contact_name' => $this->faker->name($this->faker->randomElement(['male', 'female'])),
        ]);
    }

    /**
     * Indicate that the contact is a parent.
     */
    public function parent(): static
    {
        return $this->state(fn (array $attributes) => [
            'relationship' => Relationship::PARENT,
            'contact_name' => $this->faker->name($this->faker->randomElement(['male', 'female'])),
        ]);
    }

    /**
     * Indicate that the contact is a child.
     */
    public function child(): static
    {
        return $this->state(fn (array $attributes) => [
            'relationship' => Relationship::CHILD,
            'contact_name' => $this->faker->firstName() . ' ' . $this->faker->lastName(),
        ]);
    }

    /**
     * Indicate that the contact is a sibling.
     */
    public function sibling(): static
    {
        return $this->state(fn (array $attributes) => [
            'relationship' => Relationship::SIBLING,
            'contact_name' => $this->faker->name($this->faker->randomElement(['male', 'female'])),
        ]);
    }

    /**
     * Indicate that the contact is a friend.
     */
    public function friend(): static
    {
        return $this->state(fn (array $attributes) => [
            'relationship' => Relationship::FRIEND,
            'contact_name' => $this->faker->name(),
        ]);
    }

    /**
     * Indicate that the contact is the primary contact.
     */
    public function primary(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_primary' => true,
            'phone_primary' => $this->faker->phoneNumber(), // Ensure primary has phone
        ]);
    }

    /**
     * Indicate that the contact is active.
     */
    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => true,
        ]);
    }

    /**
     * Indicate that the contact is inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }

    /**
     * Indicate that the contact has complete address information.
     */
    public function withCompleteAddress(): static
    {
        return $this->state(fn (array $attributes) => [
            'address' => $this->faker->streetAddress(),
            'city' => $this->faker->city(),
            'state' => $this->faker->state(),
            'postal_code' => $this->faker->postcode(),
            'country' => $this->faker->country(),
        ]);
    }

    /**
     * Indicate that the contact has complete contact information.
     */
    public function withCompleteContactInfo(): static
    {
        return $this->state(fn (array $attributes) => [
            'phone_primary' => $this->faker->phoneNumber(),
            'phone_secondary' => $this->faker->phoneNumber(),
            'email' => $this->faker->safeEmail(),
        ]);
    }

    /**
     * Indicate that the contact has minimal information (just name and primary phone).
     */
    public function minimal(): static
    {
        return $this->state(fn (array $attributes) => [
            'phone_secondary' => null,
            'email' => null,
            'address' => null,
            'city' => null,
            'state' => null,
            'postal_code' => null,
            'country' => null,
            'notes' => null,
        ]);
    }

    /**
     * Indicate that the contact is for a specific employee.
     */
    public function forEmployee(Employee $employee): static
    {
        return $this->state(fn (array $attributes) => [
            'employee_id' => $employee->id,
        ]);
    }

    /**
     * Indicate that the contact has specific relationship.
     */
    public function withRelationship(Relationship $relationship): static
    {
        return $this->state(fn (array $attributes) => [
            'relationship' => $relationship,
        ]);
    }

    /**
     * Indicate that the contact is in a specific city.
     */
    public function inCity(string $city): static
    {
        return $this->state(fn (array $attributes) => [
            'city' => $city,
        ]);
    }

    /**
     * Indicate that the contact is in a specific state.
     */
    public function inState(string $state): static
    {
        return $this->state(fn (array $attributes) => [
            'state' => $state,
        ]);
    }

    /**
     * Indicate that the contact is in a specific country.
     */
    public function inCountry(string $country): static
    {
        return $this->state(fn (array $attributes) => [
            'country' => $country,
        ]);
    }

    /**
     * Indicate that the contact has notes.
     */
    public function withNotes(): static
    {
        return $this->state(fn (array $attributes) => [
            'notes' => $this->faker->sentence(),
        ]);
    }

    /**
     * Indicate that the contact has detailed notes.
     */
    public function withDetailedNotes(): static
    {
        return $this->state(fn (array $attributes) => [
            'notes' => $this->faker->paragraph(),
        ]);
    }

    /**
     * Create a realistic emergency contact scenario.
     */
    public function realistic(): static
    {
        $relationship = $this->faker->randomElement(Relationship::cases());

        $state = $this->state(fn (array $attributes) => [
            'relationship' => $relationship,
        ]);

        // Adjust contact name based on relationship
        switch ($relationship) {
            case Relationship::SPOUSE:
                $state = $state->state(fn (array $attributes) => [
                    'contact_name' => $this->faker->name($this->faker->randomElement(['male', 'female'])),
                    'phone_primary' => $this->faker->phoneNumber(),
                    'phone_secondary' => $this->faker->optional(0.6)->phoneNumber(),
                    'email' => $this->faker->optional(0.8)->safeEmail(),
                ]);
                break;

            case Relationship::PARENT:
                $state = $state->state(fn (array $attributes) => [
                    'contact_name' => $this->faker->name($this->faker->randomElement(['male', 'female'])),
                    'phone_primary' => $this->faker->phoneNumber(),
                    'phone_secondary' => $this->faker->optional(0.4)->phoneNumber(),
                    'email' => $this->faker->optional(0.6)->safeEmail(),
                ]);
                break;

            case Relationship::CHILD:
                $state = $state->state(fn (array $attributes) => [
                    'contact_name' => $this->faker->firstName() . ' ' . $this->faker->lastName(),
                    'phone_primary' => $this->faker->phoneNumber(),
                    'phone_secondary' => $this->faker->optional(0.3)->phoneNumber(),
                    'email' => $this->faker->optional(0.7)->safeEmail(),
                ]);
                break;

            default:
                $state = $state->state(fn (array $attributes) => [
                    'phone_primary' => $this->faker->phoneNumber(),
                    'phone_secondary' => $this->faker->optional(0.5)->phoneNumber(),
                    'email' => $this->faker->optional(0.7)->safeEmail(),
                ]);
        }

        return $state;
    }
}
