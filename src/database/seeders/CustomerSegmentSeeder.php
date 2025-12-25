<?php

namespace Database\Seeders;

use App\Enums\SegmentPriority;
use App\Enums\SegmentStatus;
use App\Enums\SegmentType;
use App\Models\CustomerSegment;
use Illuminate\Database\Seeder;

class CustomerSegmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $segments = [
            [
                'name' => 'High-Value Customers',
                'description' => 'Customers with high purchase value and frequency',
                'type' => SegmentType::BEHAVIORAL,
                'status' => SegmentStatus::ACTIVE,
                'priority' => SegmentPriority::HIGH,
                'criteria' => [
                    [
                        'field' => 'total_spent',
                        'operator' => 'greater_than',
                        'value' => 1000,
                    ],
                    [
                        'field' => 'purchase_frequency',
                        'operator' => 'greater_than',
                        'value' => 5,
                    ],
                ],
                'conditions' => [
                    [
                        'type' => 'purchase_frequency',
                        'value' => 5,
                    ],
                ],
                'is_automatic' => true,
                'is_dynamic' => true,
                'metadata' => [
                    'target_audience' => 'Premium customers',
                    'marketing_strategy' => 'VIP treatment',
                ],
                'tags' => ['premium', 'high-value', 'vip'],
            ],
            [
                'name' => 'New Customers',
                'description' => 'Recently registered customers',
                'type' => SegmentType::BEHAVIORAL,
                'status' => SegmentStatus::ACTIVE,
                'priority' => SegmentPriority::NORMAL,
                'criteria' => [
                    [
                        'field' => 'created_at',
                        'operator' => 'greater_than',
                        'value' => now()->subDays(30)->toDateString(),
                    ],
                ],
                'conditions' => [
                    [
                        'type' => 'registration_date',
                        'value' => 30,
                    ],
                ],
                'is_automatic' => true,
                'is_dynamic' => true,
                'metadata' => [
                    'target_audience' => 'New users',
                    'marketing_strategy' => 'Onboarding',
                ],
                'tags' => ['new', 'onboarding', 'welcome'],
            ],
            [
                'name' => 'Inactive Customers',
                'description' => 'Customers who haven\'t made a purchase in 90 days',
                'type' => SegmentType::BEHAVIORAL,
                'status' => SegmentStatus::ACTIVE,
                'priority' => SegmentPriority::NORMAL,
                'criteria' => [
                    [
                        'field' => 'last_purchase_date',
                        'operator' => 'less_than',
                        'value' => now()->subDays(90)->toDateString(),
                    ],
                ],
                'conditions' => [
                    [
                        'type' => 'inactivity_period',
                        'value' => 90,
                    ],
                ],
                'is_automatic' => true,
                'is_dynamic' => true,
                'metadata' => [
                    'target_audience' => 'Inactive users',
                    'marketing_strategy' => 'Re-engagement',
                ],
                'tags' => ['inactive', 're-engagement', 'winback'],
            ],
            [
                'name' => 'Premium Subscribers',
                'description' => 'Customers with premium subscription plans',
                'type' => SegmentType::TRANSACTIONAL,
                'status' => SegmentStatus::ACTIVE,
                'priority' => SegmentPriority::HIGH,
                'criteria' => [
                    [
                        'field' => 'subscription_type',
                        'operator' => 'equals',
                        'value' => 'premium',
                    ],
                ],
                'conditions' => [
                    [
                        'type' => 'subscription_status',
                        'value' => 'active',
                    ],
                ],
                'is_automatic' => true,
                'is_dynamic' => true,
                'metadata' => [
                    'target_audience' => 'Premium subscribers',
                    'marketing_strategy' => 'Exclusive content',
                ],
                'tags' => ['premium', 'subscription', 'exclusive'],
            ],
            [
                'name' => 'US Customers',
                'description' => 'Customers located in the United States',
                'type' => SegmentType::GEOGRAPHIC,
                'status' => SegmentStatus::ACTIVE,
                'priority' => SegmentPriority::NORMAL,
                'criteria' => [
                    [
                        'field' => 'country',
                        'operator' => 'equals',
                        'value' => 'US',
                    ],
                ],
                'conditions' => [
                    [
                        'type' => 'location',
                        'value' => 'US',
                    ],
                ],
                'is_automatic' => true,
                'is_dynamic' => false,
                'metadata' => [
                    'target_audience' => 'US customers',
                    'marketing_strategy' => 'Local promotions',
                ],
                'tags' => ['us', 'geographic', 'local'],
            ],
            [
                'name' => 'Young Adults',
                'description' => 'Customers aged 18-25',
                'type' => SegmentType::DEMOGRAPHIC,
                'status' => SegmentStatus::ACTIVE,
                'priority' => SegmentPriority::NORMAL,
                'criteria' => [
                    [
                        'field' => 'age',
                        'operator' => 'between',
                        'value' => [18, 25],
                    ],
                ],
                'conditions' => [
                    [
                        'type' => 'age_range',
                        'value' => [18, 25],
                    ],
                ],
                'is_automatic' => true,
                'is_dynamic' => false,
                'metadata' => [
                    'target_audience' => 'Young adults',
                    'marketing_strategy' => 'Trendy products',
                ],
                'tags' => ['young', 'demographic', 'trendy'],
            ],
            [
                'name' => 'Mobile Users',
                'description' => 'Customers who primarily use mobile devices',
                'type' => SegmentType::BEHAVIORAL,
                'status' => SegmentStatus::ACTIVE,
                'priority' => SegmentPriority::NORMAL,
                'criteria' => [
                    [
                        'field' => 'device_type',
                        'operator' => 'equals',
                        'value' => 'mobile',
                    ],
                ],
                'conditions' => [
                    [
                        'type' => 'device_preference',
                        'value' => 'mobile',
                    ],
                ],
                'is_automatic' => true,
                'is_dynamic' => true,
                'metadata' => [
                    'target_audience' => 'Mobile users',
                    'marketing_strategy' => 'Mobile-first experience',
                ],
                'tags' => ['mobile', 'device', 'responsive'],
            ],
            [
                'name' => 'Loyalty Members',
                'description' => 'Customers with high loyalty points',
                'type' => SegmentType::LOYALTY,
                'status' => SegmentStatus::ACTIVE,
                'priority' => SegmentPriority::HIGH,
                'criteria' => [
                    [
                        'field' => 'loyalty_points',
                        'operator' => 'greater_than',
                        'value' => 1000,
                    ],
                ],
                'conditions' => [
                    [
                        'type' => 'loyalty_tier',
                        'value' => 'gold',
                    ],
                ],
                'is_automatic' => true,
                'is_dynamic' => true,
                'metadata' => [
                    'target_audience' => 'Loyal customers',
                    'marketing_strategy' => 'Rewards program',
                ],
                'tags' => ['loyalty', 'rewards', 'gold'],
            ],
            [
                'name' => 'Abandoned Cart',
                'description' => 'Customers who abandoned their shopping cart',
                'type' => SegmentType::BEHAVIORAL,
                'status' => SegmentStatus::ACTIVE,
                'priority' => SegmentPriority::HIGH,
                'criteria' => [
                    [
                        'field' => 'cart_status',
                        'operator' => 'equals',
                        'value' => 'abandoned',
                    ],
                ],
                'conditions' => [
                    [
                        'type' => 'cart_abandonment',
                        'value' => true,
                    ],
                ],
                'is_automatic' => true,
                'is_dynamic' => true,
                'metadata' => [
                    'target_audience' => 'Cart abandoners',
                    'marketing_strategy' => 'Recovery campaigns',
                ],
                'tags' => ['abandoned', 'cart', 'recovery'],
            ],
            [
                'name' => 'Seasonal Buyers',
                'description' => 'Customers who make purchases during specific seasons',
                'type' => SegmentType::BEHAVIORAL,
                'status' => SegmentStatus::ACTIVE,
                'priority' => SegmentPriority::NORMAL,
                'criteria' => [
                    [
                        'field' => 'seasonal_purchase_pattern',
                        'operator' => 'contains',
                        'value' => 'holiday',
                    ],
                ],
                'conditions' => [
                    [
                        'type' => 'seasonal_behavior',
                        'value' => 'holiday',
                    ],
                ],
                'is_automatic' => true,
                'is_dynamic' => true,
                'metadata' => [
                    'target_audience' => 'Seasonal shoppers',
                    'marketing_strategy' => 'Seasonal promotions',
                ],
                'tags' => ['seasonal', 'holiday', 'promotions'],
            ],
        ];

        foreach ($segments as $segmentData) {
            CustomerSegment::create($segmentData);
        }
    }
}
