<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Shopping Package Configuration
    |--------------------------------------------------------------------------
    |
    | This file contains the configuration for the shopping package including
    | model configurations, default values, and geographic data settings.
    |
    */

    // User model configuration
    'user_model' => env('SHOPPING_USER_MODEL', 'App\Models\User'),

    // Geographic model configurations
    'geographic_models' => [
        'country_model' => env('SHOPPING_COUNTRY_MODEL', 'App\Models\Country'),
        'province_model' => env('SHOPPING_PROVINCE_MODEL', 'App\Models\Province'),
        'county_model' => env('SHOPPING_COUNTY_MODEL', 'App\Models\County'),
        'city_model' => env('SHOPPING_CITY_MODEL', 'App\Models\City'),
        'village_model' => env('SHOPPING_VILLAGE_MODEL', 'App\Models\Village'),
    ],

    // Geographic default values
    'geographic_defaults' => [
        'country' => env('SHOPPING_DEFAULT_COUNTRY', 'Iran'),
        'province' => env('SHOPPING_DEFAULT_PROVINCE', 'Tehran'),
        'county' => env('SHOPPING_DEFAULT_COUNTY', 'Tehran'),
        'city' => env('SHOPPING_DEFAULT_CITY', 'Tehran'),
        'village' => env('SHOPPING_DEFAULT_VILLAGE', null),
    ],

    // Address configuration
    'address' => [
        'max_addresses_per_user' => env('SHOPPING_MAX_ADDRESSES_PER_USER', 10),
        'require_geographic_data' => env('SHOPPING_REQUIRE_GEOGRAPHIC_DATA', false),
        'cache_geographic_data' => env('SHOPPING_CACHE_GEOGRAPHIC_DATA', true),
        'geographic_cache_ttl' => env('SHOPPING_GEOGRAPHIC_CACHE_TTL', 3600), // 1 hour
    ],

    // Pagination configuration
    'pagination' => [
        'default_per_page' => env('SHOPPING_DEFAULT_PER_PAGE', 15),
        'max_per_page' => env('SHOPPING_MAX_PER_PAGE', 100),
    ],

    // Cache configuration
    'cache' => [
        'enabled' => env('SHOPPING_CACHE_ENABLED', true),
        'prefix' => env('SHOPPING_CACHE_PREFIX', 'shopping'),
        'ttl' => env('SHOPPING_CACHE_TTL', 3600), // 1 hour
    ],

    // API configuration
    'api' => [
        'version' => env('SHOPPING_API_VERSION', 'v1'),
        'rate_limit' => env('SHOPPING_API_RATE_LIMIT', 60), // requests per minute
        'throttle_timeout' => env('SHOPPING_API_THROTTLE_TIMEOUT', 60), // seconds
    ],

    // Validation configuration
    'validation' => [
        'strict_geographic_validation' => env('SHOPPING_STRICT_GEOGRAPHIC_VALIDATION', true),
        'allow_legacy_address_fields' => env('SHOPPING_ALLOW_LEGACY_ADDRESS_FIELDS', true),
    ],

    // Feature flags
    'features' => [
        'geographic_data' => env('SHOPPING_FEATURE_GEOGRAPHIC_DATA', true),
        'address_defaults' => env('SHOPPING_FEATURE_ADDRESS_DEFAULTS', true),
        'address_search' => env('SHOPPING_FEATURE_ADDRESS_SEARCH', true),
        'address_validation' => env('SHOPPING_FEATURE_ADDRESS_VALIDATION', true),
    ],
];
