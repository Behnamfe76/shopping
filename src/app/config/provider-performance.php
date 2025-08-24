<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Provider Performance Configuration
    |--------------------------------------------------------------------------
    |
    | This file contains configuration options for the Provider Performance
    | module including thresholds, weights, and other settings.
    |
    */

    /*
    |--------------------------------------------------------------------------
    | Performance Score Weights
    |--------------------------------------------------------------------------
    |
    | Weights used to calculate the overall performance score.
    | All weights should sum to 1.0 (100%).
    |
    */
    'score_weights' => [
        'on_time_delivery_rate' => 0.20,      // 20%
        'customer_satisfaction_score' => 0.25, // 25%
        'quality_rating' => 0.20,             // 20%
        'delivery_rating' => 0.15,            // 15%
        'communication_rating' => 0.10,       // 10%
        'cost_efficiency_score' => 0.10,      // 10%
    ],

    /*
    |--------------------------------------------------------------------------
    | Performance Grade Thresholds
    |--------------------------------------------------------------------------
    |
    | Score thresholds for determining performance grades.
    |
    */
    'grade_thresholds' => [
        'A' => 90.0,
        'B' => 80.0,
        'C' => 70.0,
        'D' => 60.0,
        'F' => 0.0,
    ],

    /*
    |--------------------------------------------------------------------------
    | Alert Thresholds
    |--------------------------------------------------------------------------
    |
    | Thresholds that trigger performance alerts.
    |
    */
    'alert_thresholds' => [
        'critical' => [
            'on_time_delivery_rate' => 85.0,
            'customer_satisfaction_score' => 6.0,
            'quality_rating' => 6.5,
            'return_rate' => 8.0,
            'defect_rate' => 5.0,
        ],
        'warning' => [
            'on_time_delivery_rate' => 90.0,
            'customer_satisfaction_score' => 7.0,
            'quality_rating' => 7.5,
            'return_rate' => 5.0,
            'defect_rate' => 3.0,
        ],
        'info' => [
            'on_time_delivery_rate' => 95.0,
            'customer_satisfaction_score' => 8.0,
            'quality_rating' => 8.5,
            'return_rate' => 2.0,
            'defect_rate' => 1.0,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Benchmark Settings
    |--------------------------------------------------------------------------
    |
    | Settings for performance benchmarking.
    |
    */
    'benchmarks' => [
        'industry_averages' => [
            'on_time_delivery_rate' => 92.0,
            'customer_satisfaction_score' => 7.8,
            'quality_rating' => 8.2,
            'return_rate' => 3.5,
            'defect_rate' => 2.1,
            'response_time_avg' => 8.5,
            'lead_time_avg' => 7.2,
        ],
        'top_performer_threshold' => 95.0,
        'bottom_performer_threshold' => 70.0,
    ],

    /*
    |--------------------------------------------------------------------------
    | Cache Settings
    |--------------------------------------------------------------------------
    |
    | Cache configuration for performance data.
    |
    */
    'cache' => [
        'enabled' => env('PROVIDER_PERFORMANCE_CACHE_ENABLED', true),
        'ttl' => env('PROVIDER_PERFORMANCE_CACHE_TTL', 3600), // 1 hour
        'prefix' => 'provider_performance_',
        'tags' => ['provider_performance', 'analytics'],
    ],

    /*
    |--------------------------------------------------------------------------
    | Pagination Settings
    |--------------------------------------------------------------------------
    |
    | Default pagination settings for performance listings.
    |
    */
    'pagination' => [
        'default_per_page' => 15,
        'max_per_page' => 100,
        'available_per_page' => [15, 25, 50, 100],
    ],

    /*
    |--------------------------------------------------------------------------
    | Export Settings
    |--------------------------------------------------------------------------
    |
    | Settings for data export functionality.
    |
    */
    'export' => [
        'formats' => ['csv', 'xlsx', 'pdf'],
        'max_records' => 10000,
        'chunk_size' => 1000,
        'timeout' => 300, // 5 minutes
    ],

    /*
    |--------------------------------------------------------------------------
    | Notification Settings
    |--------------------------------------------------------------------------
    |
    | Settings for performance-related notifications.
    |
    */
    'notifications' => [
        'enabled' => env('PROVIDER_PERFORMANCE_NOTIFICATIONS_ENABLED', true),
        'channels' => ['mail', 'database', 'slack'],
        'recipients' => [
            'admins' => true,
            'managers' => true,
            'analysts' => true,
            'supervisors' => true,
            'qa_specialists' => true,
        ],
        'frequency' => [
            'daily_summary' => '09:00',
            'weekly_report' => 'monday 09:00',
            'monthly_analysis' => 'first day of month 09:00',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Workflow Settings
    |--------------------------------------------------------------------------
    |
    | Settings for performance review workflows.
    |
    */
    'workflows' => [
        'auto_verification' => [
            'enabled' => env('PROVIDER_PERFORMANCE_AUTO_VERIFY', false),
            'score_threshold' => 85.0,
            'grade_threshold' => 'B',
        ],
        'approval_required' => [
            'score_threshold' => 95.0,
            'grade_threshold' => 'A',
        ],
        'review_required' => [
            'score_threshold' => 70.0,
            'grade_threshold' => 'C',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Analytics Settings
    |--------------------------------------------------------------------------
    |
    | Settings for performance analytics and reporting.
    |
    */
    'analytics' => [
        'trend_analysis' => [
            'min_data_points' => 3,
            'forecast_periods' => 3,
            'confidence_level' => 0.95,
        ],
        'comparison' => [
            'peer_group_size' => 10,
            'industry_benchmark_weight' => 0.3,
            'historical_weight' => 0.7,
        ],
        'insights' => [
            'min_improvement' => 5.0,
            'min_degradation' => 3.0,
            'correlation_threshold' => 0.7,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Validation Settings
    |--------------------------------------------------------------------------
    |
    | Validation rules and constraints.
    |
    */
    'validation' => [
        'numeric_ranges' => [
            'on_time_delivery_rate' => [0, 100],
            'customer_satisfaction_score' => [1, 10],
            'quality_rating' => [1, 10],
            'delivery_rating' => [1, 10],
            'communication_rating' => [1, 10],
            'cost_efficiency_score' => [0, 100],
            'return_rate' => [0, 100],
            'defect_rate' => [0, 100],
        ],
        'date_constraints' => [
            'max_period_duration' => 365, // days
            'min_period_duration' => 1,   // days
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Feature Flags
    |--------------------------------------------------------------------------
    |
    | Enable/disable specific features.
    |
    */
    'features' => [
        'real_time_updates' => env('PROVIDER_PERFORMANCE_REALTIME', true),
        'advanced_analytics' => env('PROVIDER_PERFORMANCE_ADVANCED_ANALYTICS', true),
        'machine_learning' => env('PROVIDER_PERFORMANCE_ML', false),
        'api_access' => env('PROVIDER_PERFORMANCE_API', true),
        'bulk_operations' => env('PROVIDER_PERFORMANCE_BULK', true),
        'audit_logging' => env('PROVIDER_PERFORMANCE_AUDIT', true),
    ],
];
