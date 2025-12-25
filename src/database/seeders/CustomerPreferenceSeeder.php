<?php

namespace Fereydooni\Shopping\database\seeders;

use Fereydooni\Shopping\app\Enums\CustomerPreferenceType;
use Fereydooni\Shopping\app\Models\Customer;
use Fereydooni\Shopping\app\Models\CustomerPreference;
use Illuminate\Database\Seeder;

class CustomerPreferenceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $customers = Customer::all();

        if ($customers->isEmpty()) {
            $this->command->warn('No customers found. Please run CustomerSeeder first.');

            return;
        }

        $preferences = [
            // UI Preferences
            [
                'key' => 'ui.theme',
                'value' => 'light',
                'type' => CustomerPreferenceType::STRING,
                'description' => 'User interface theme preference',
                'metadata' => ['category' => 'ui', 'default' => 'light'],
            ],
            [
                'key' => 'ui.language',
                'value' => 'en',
                'type' => CustomerPreferenceType::STRING,
                'description' => 'User interface language preference',
                'metadata' => ['category' => 'ui', 'default' => 'en'],
            ],
            [
                'key' => 'ui.currency',
                'value' => 'USD',
                'type' => CustomerPreferenceType::STRING,
                'description' => 'Preferred currency for transactions',
                'metadata' => ['category' => 'ui', 'default' => 'USD'],
            ],
            [
                'key' => 'ui.timezone',
                'value' => 'UTC',
                'type' => CustomerPreferenceType::STRING,
                'description' => 'User timezone preference',
                'metadata' => ['category' => 'ui', 'default' => 'UTC'],
            ],

            // Notification Preferences
            [
                'key' => 'notifications.email',
                'value' => true,
                'type' => CustomerPreferenceType::BOOLEAN,
                'description' => 'Email notification preference',
                'metadata' => ['category' => 'notifications', 'default' => true],
            ],
            [
                'key' => 'notifications.sms',
                'value' => false,
                'type' => CustomerPreferenceType::BOOLEAN,
                'description' => 'SMS notification preference',
                'metadata' => ['category' => 'notifications', 'default' => false],
            ],
            [
                'key' => 'notifications.push',
                'value' => true,
                'type' => CustomerPreferenceType::BOOLEAN,
                'description' => 'Push notification preference',
                'metadata' => ['category' => 'notifications', 'default' => true],
            ],
            [
                'key' => 'notifications.frequency',
                'value' => 'daily',
                'type' => CustomerPreferenceType::STRING,
                'description' => 'Notification frequency preference',
                'metadata' => ['category' => 'notifications', 'default' => 'daily'],
            ],

            // Shopping Preferences
            [
                'key' => 'shopping.sort_by',
                'value' => 'price_low_to_high',
                'type' => CustomerPreferenceType::STRING,
                'description' => 'Product sorting preference',
                'metadata' => ['category' => 'shopping', 'default' => 'price_low_to_high'],
            ],
            [
                'key' => 'shopping.items_per_page',
                'value' => 20,
                'type' => CustomerPreferenceType::INTEGER,
                'description' => 'Number of items per page',
                'metadata' => ['category' => 'shopping', 'default' => 20],
            ],
            [
                'key' => 'shopping.show_out_of_stock',
                'value' => false,
                'type' => CustomerPreferenceType::BOOLEAN,
                'description' => 'Show out of stock items preference',
                'metadata' => ['category' => 'shopping', 'default' => false],
            ],
            [
                'key' => 'shopping.auto_add_to_cart',
                'value' => false,
                'type' => CustomerPreferenceType::BOOLEAN,
                'description' => 'Auto add to cart preference',
                'metadata' => ['category' => 'shopping', 'default' => false],
            ],

            // Privacy Preferences
            [
                'key' => 'privacy.share_data',
                'value' => true,
                'type' => CustomerPreferenceType::BOOLEAN,
                'description' => 'Data sharing preference',
                'metadata' => ['category' => 'privacy', 'default' => true],
            ],
            [
                'key' => 'privacy.analytics',
                'value' => true,
                'type' => CustomerPreferenceType::BOOLEAN,
                'description' => 'Analytics tracking preference',
                'metadata' => ['category' => 'privacy', 'default' => true],
            ],
            [
                'key' => 'privacy.marketing',
                'value' => false,
                'type' => CustomerPreferenceType::BOOLEAN,
                'description' => 'Marketing communications preference',
                'metadata' => ['category' => 'privacy', 'default' => false],
            ],

            // Advanced Preferences
            [
                'key' => 'advanced.custom_filters',
                'value' => json_encode(['brands' => [], 'categories' => [], 'price_range' => []]),
                'type' => CustomerPreferenceType::JSON,
                'description' => 'Custom filter preferences',
                'metadata' => ['category' => 'advanced', 'default' => '{}'],
            ],
            [
                'key' => 'advanced.search_history',
                'value' => json_encode([]),
                'type' => CustomerPreferenceType::JSON,
                'description' => 'Search history preference',
                'metadata' => ['category' => 'advanced', 'default' => '[]'],
            ],
        ];

        $createdCount = 0;

        foreach ($customers as $customer) {
            // Create 5-10 random preferences for each customer
            $customerPreferences = collect($preferences)->random(rand(5, 10));

            foreach ($customerPreferences as $pref) {
                // Randomize some values for variety
                $value = $this->randomizeValue($pref['value'], $pref['type']);

                CustomerPreference::create([
                    'customer_id' => $customer->id,
                    'preference_key' => $pref['key'],
                    'preference_value' => $value,
                    'preference_type' => $pref['type'],
                    'is_active' => rand(0, 10) > 1, // 90% chance of being active
                    'description' => $pref['description'],
                    'metadata' => $pref['metadata'],
                ]);

                $createdCount++;
            }
        }

        $this->command->info("Created {$createdCount} customer preferences for {$customers->count()} customers.");
    }

    /**
     * Randomize preference values for variety.
     */
    private function randomizeValue($value, CustomerPreferenceType $type): mixed
    {
        return match ($type) {
            CustomerPreferenceType::BOOLEAN => (bool) rand(0, 1),
            CustomerPreferenceType::INTEGER => is_numeric($value) ? rand(1, 100) : $value,
            CustomerPreferenceType::STRING => $this->randomizeStringValue($value),
            CustomerPreferenceType::JSON => $value, // Keep JSON as is for now
            default => $value
        };
    }

    /**
     * Randomize string values.
     */
    private function randomizeStringValue(string $value): string
    {
        $variations = [
            'ui.theme' => ['light', 'dark', 'auto'],
            'ui.language' => ['en', 'es', 'fr', 'de'],
            'ui.currency' => ['USD', 'EUR', 'GBP', 'CAD'],
            'ui.timezone' => ['UTC', 'EST', 'PST', 'GMT'],
            'notifications.frequency' => ['immediate', 'hourly', 'daily', 'weekly'],
            'shopping.sort_by' => ['price_low_to_high', 'price_high_to_low', 'name_a_z', 'name_z_a', 'newest'],
            'shopping.items_per_page' => ['10', '20', '50', '100'],
        ];

        $key = array_search($value, array_column($variations, 0));

        if ($key !== false && isset($variations[$key])) {
            return $variations[$key][array_rand($variations[$key])];
        }

        return $value;
    }
}
