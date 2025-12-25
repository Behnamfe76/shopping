<?php

namespace Fereydooni\Shopping\database\seeders;

use Fereydooni\Shopping\app\Enums\CustomerPreferenceType;
use Illuminate\Database\Seeder;

class DefaultCustomerPreferencesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $defaultPreferences = [
            // UI Defaults
            [
                'key' => 'ui.theme',
                'value' => 'light',
                'type' => CustomerPreferenceType::STRING,
                'description' => 'User interface theme preference',
                'category' => 'ui',
                'is_required' => false,
            ],
            [
                'key' => 'ui.language',
                'value' => 'en',
                'type' => CustomerPreferenceType::STRING,
                'description' => 'User interface language preference',
                'category' => 'ui',
                'is_required' => false,
            ],
            [
                'key' => 'ui.currency',
                'value' => 'USD',
                'type' => CustomerPreferenceType::STRING,
                'description' => 'Preferred currency for transactions',
                'category' => 'ui',
                'is_required' => false,
            ],
            [
                'key' => 'ui.timezone',
                'value' => 'UTC',
                'type' => CustomerPreferenceType::STRING,
                'description' => 'User timezone preference',
                'category' => 'ui',
                'is_required' => false,
            ],

            // Notification Defaults
            [
                'key' => 'notifications.email',
                'value' => true,
                'type' => CustomerPreferenceType::BOOLEAN,
                'description' => 'Email notification preference',
                'category' => 'notifications',
                'is_required' => false,
            ],
            [
                'key' => 'notifications.sms',
                'value' => false,
                'type' => CustomerPreferenceType::BOOLEAN,
                'description' => 'SMS notification preference',
                'category' => 'notifications',
                'is_required' => false,
            ],
            [
                'key' => 'notifications.push',
                'value' => true,
                'type' => CustomerPreferenceType::BOOLEAN,
                'description' => 'Push notification preference',
                'category' => 'notifications',
                'is_required' => false,
            ],
            [
                'key' => 'notifications.frequency',
                'value' => 'daily',
                'type' => CustomerPreferenceType::STRING,
                'description' => 'Notification frequency preference',
                'category' => 'notifications',
                'is_required' => false,
            ],

            // Shopping Defaults
            [
                'key' => 'shopping.sort_by',
                'value' => 'price_low_to_high',
                'type' => CustomerPreferenceType::STRING,
                'description' => 'Product sorting preference',
                'category' => 'shopping',
                'is_required' => false,
            ],
            [
                'key' => 'shopping.items_per_page',
                'value' => 20,
                'type' => CustomerPreferenceType::INTEGER,
                'description' => 'Number of items per page',
                'category' => 'shopping',
                'is_required' => false,
            ],
            [
                'key' => 'shopping.show_out_of_stock',
                'value' => false,
                'type' => CustomerPreferenceType::BOOLEAN,
                'description' => 'Show out of stock items preference',
                'category' => 'shopping',
                'is_required' => false,
            ],
            [
                'key' => 'shopping.auto_add_to_cart',
                'value' => false,
                'type' => CustomerPreferenceType::BOOLEAN,
                'description' => 'Auto add to cart preference',
                'category' => 'shopping',
                'is_required' => false,
            ],

            // Privacy Defaults
            [
                'key' => 'privacy.share_data',
                'value' => true,
                'type' => CustomerPreferenceType::BOOLEAN,
                'description' => 'Data sharing preference',
                'category' => 'privacy',
                'is_required' => false,
            ],
            [
                'key' => 'privacy.analytics',
                'value' => true,
                'type' => CustomerPreferenceType::BOOLEAN,
                'description' => 'Analytics tracking preference',
                'category' => 'privacy',
                'is_required' => false,
            ],
            [
                'key' => 'privacy.marketing',
                'value' => false,
                'type' => CustomerPreferenceType::BOOLEAN,
                'description' => 'Marketing communications preference',
                'category' => 'privacy',
                'is_required' => false,
            ],

            // Security Defaults
            [
                'key' => 'security.two_factor',
                'value' => false,
                'type' => CustomerPreferenceType::BOOLEAN,
                'description' => 'Two-factor authentication preference',
                'category' => 'security',
                'is_required' => false,
            ],
            [
                'key' => 'security.session_timeout',
                'value' => 30,
                'type' => CustomerPreferenceType::INTEGER,
                'description' => 'Session timeout in minutes',
                'category' => 'security',
                'is_required' => false,
            ],

            // Advanced Defaults
            [
                'key' => 'advanced.custom_filters',
                'value' => json_encode(['brands' => [], 'categories' => [], 'price_range' => []]),
                'type' => CustomerPreferenceType::JSON,
                'description' => 'Custom filter preferences',
                'category' => 'advanced',
                'is_required' => false,
            ],
            [
                'key' => 'advanced.search_history',
                'value' => json_encode([]),
                'type' => CustomerPreferenceType::JSON,
                'description' => 'Search history preference',
                'category' => 'advanced',
                'is_required' => false,
            ],
            [
                'key' => 'advanced.auto_save',
                'value' => true,
                'type' => CustomerPreferenceType::BOOLEAN,
                'description' => 'Auto-save form data preference',
                'category' => 'advanced',
                'is_required' => false,
            ],
        ];

        // Store default preferences in cache for easy access
        cache()->put('default_customer_preferences', $defaultPreferences, now()->addDays(30));

        $this->command->info('Default customer preferences seeded successfully.');
        $this->command->info('Total default preferences: '.count($defaultPreferences));
    }

    /**
     * Get default preferences for a specific category.
     */
    public static function getDefaultsByCategory(string $category): array
    {
        $defaults = cache()->get('default_customer_preferences', []);

        return array_filter($defaults, function ($pref) use ($category) {
            return $pref['category'] === $category;
        });
    }

    /**
     * Get all default preferences.
     */
    public static function getAllDefaults(): array
    {
        return cache()->get('default_customer_preferences', []);
    }

    /**
     * Get default preference by key.
     */
    public static function getDefaultByKey(string $key): ?array
    {
        $defaults = cache()->get('default_customer_preferences', []);

        foreach ($defaults as $pref) {
            if ($pref['key'] === $key) {
                return $pref;
            }
        }

        return null;
    }
}
