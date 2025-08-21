<?php

namespace Fereydooni\Shopping\database\factories;

use Fereydooni\Shopping\app\Models\CustomerPreference;
use Fereydooni\Shopping\app\Models\Customer;
use Fereydooni\Shopping\app\Enums\CustomerPreferenceType;
use Illuminate\Database\Eloquent\Factories\Factory;

class CustomerPreferenceFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     */
    protected $model = CustomerPreference::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        $preferenceTypes = [
            'ui.theme' => ['light', 'dark', 'auto'],
            'ui.language' => ['en', 'es', 'fr', 'de'],
            'ui.currency' => ['USD', 'EUR', 'GBP', 'CAD'],
            'ui.timezone' => ['UTC', 'EST', 'PST', 'GMT'],
            'notifications.email' => [true, false],
            'notifications.sms' => [true, false],
            'notifications.push' => [true, false],
            'notifications.frequency' => ['immediate', 'hourly', 'daily', 'weekly'],
            'shopping.sort_by' => ['price_low_to_high', 'price_high_to_low', 'name_a_z', 'name_z_a', 'newest'],
            'shopping.items_per_page' => [10, 20, 50, 100],
            'shopping.show_out_of_stock' => [true, false],
            'shopping.auto_add_to_cart' => [true, false],
            'privacy.share_data' => [true, false],
            'privacy.analytics' => [true, false],
            'privacy.marketing' => [true, false],
            'security.two_factor' => [true, false],
            'security.session_timeout' => [15, 30, 60, 120],
        ];

        $preferenceKey = $this->faker->randomElement(array_keys($preferenceTypes));
        $preferenceValue = $this->faker->randomElement($preferenceTypes[$preferenceKey]);

        // Determine type based on value
        $type = match(true) {
            is_bool($preferenceValue) => CustomerPreferenceType::BOOLEAN,
            is_int($preferenceValue) => CustomerPreferenceType::INTEGER,
            is_float($preferenceValue) => CustomerPreferenceType::FLOAT,
            is_string($preferenceValue) => CustomerPreferenceType::STRING,
            is_array($preferenceValue) => CustomerPreferenceType::JSON,
            default => CustomerPreferenceType::STRING
        };

        return [
            'customer_id' => Customer::factory(),
            'preference_key' => $preferenceKey,
            'preference_value' => $preferenceValue,
            'preference_type' => $type,
            'is_active' => $this->faker->boolean(90), // 90% chance of being active
            'description' => $this->faker->sentence(),
            'metadata' => [
                'category' => $this->getCategoryFromKey($preferenceKey),
                'default' => $preferenceValue,
                'created_by' => 'factory',
                'version' => '1.0'
            ],
        ];
    }

    /**
     * Indicate that the preference is active.
     */
    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => true,
        ]);
    }

    /**
     * Indicate that the preference is inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }

    /**
     * Create a UI preference.
     */
    public function ui(): static
    {
        return $this->state(fn (array $attributes) => [
            'preference_key' => $this->faker->randomElement([
                'ui.theme',
                'ui.language',
                'ui.currency',
                'ui.timezone'
            ]),
            'preference_type' => CustomerPreferenceType::STRING,
        ]);
    }

    /**
     * Create a notification preference.
     */
    public function notification(): static
    {
        return $this->state(fn (array $attributes) => [
            'preference_key' => $this->faker->randomElement([
                'notifications.email',
                'notifications.sms',
                'notifications.push',
                'notifications.frequency'
            ]),
            'preference_type' => $this->faker->randomElement([
                CustomerPreferenceType::BOOLEAN,
                CustomerPreferenceType::STRING
            ]),
        ]);
    }

    /**
     * Create a shopping preference.
     */
    public function shopping(): static
    {
        return $this->state(fn (array $attributes) => [
            'preference_key' => $this->faker->randomElement([
                'shopping.sort_by',
                'shopping.items_per_page',
                'shopping.show_out_of_stock',
                'shopping.auto_add_to_cart'
            ]),
            'preference_type' => $this->faker->randomElement([
                CustomerPreferenceType::STRING,
                CustomerPreferenceType::INTEGER,
                CustomerPreferenceType::BOOLEAN
            ]),
        ]);
    }

    /**
     * Create a privacy preference.
     */
    public function privacy(): static
    {
        return $this->state(fn (array $attributes) => [
            'preference_key' => $this->faker->randomElement([
                'privacy.share_data',
                'privacy.analytics',
                'privacy.marketing'
            ]),
            'preference_type' => CustomerPreferenceType::BOOLEAN,
        ]);
    }

    /**
     * Get category from preference key.
     */
    private function getCategoryFromKey(string $key): string
    {
        $parts = explode('.', $key);
        return $parts[0] ?? 'general';
    }
}
