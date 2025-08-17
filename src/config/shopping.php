<?php

return [
    /*
    |--------------------------------------------------------------------------
    | User Model
    |--------------------------------------------------------------------------
    |
    | This is the user model that will be used for relationships.
    | You can change this to use your own User model.
    |
    */
    'user_model' => env('SHOPPING_USER_MODEL', 'App\Models\User'),

    /*
    |--------------------------------------------------------------------------
    | Table Prefix
    |--------------------------------------------------------------------------
    |
    | This prefix will be added to all table names to avoid conflicts
    | with existing tables in your application.
    |
    */
    'table_prefix' => env('SHOPPING_TABLE_PREFIX', 'shp_'),

    /*
    |--------------------------------------------------------------------------
    | Currency
    |--------------------------------------------------------------------------
    |
    | Default currency for the shopping system.
    |
    */
    'currency' => env('SHOPPING_CURRENCY', 'USD'),

    /*
    |--------------------------------------------------------------------------
    | Tax Rate
    |--------------------------------------------------------------------------
    |
    | Default tax rate as a percentage (e.g., 10 for 10%).
    |
    */
    'tax_rate' => env('SHOPPING_TAX_RATE', 0),

    /*
    |--------------------------------------------------------------------------
    | Shipping Methods
    |--------------------------------------------------------------------------
    |
    | Available shipping methods and their costs.
    |
    */
    'shipping_methods' => [
        'standard' => [
            'name' => 'Standard Shipping',
            'cost' => 5.99,
            'delivery_days' => '3-5 business days',
        ],
        'express' => [
            'name' => 'Express Shipping',
            'cost' => 12.99,
            'delivery_days' => '1-2 business days',
        ],
        'overnight' => [
            'name' => 'Overnight Shipping',
            'cost' => 24.99,
            'delivery_days' => 'Next business day',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Payment Gateways
    |--------------------------------------------------------------------------
    |
    | Available payment gateways configuration.
    |
    */
    'payment_gateways' => [
        'stripe' => [
            'enabled' => env('SHOPPING_STRIPE_ENABLED', false),
            'public_key' => env('STRIPE_PUBLIC_KEY'),
            'secret_key' => env('STRIPE_SECRET_KEY'),
        ],
        'paypal' => [
            'enabled' => env('SHOPPING_PAYPAL_ENABLED', false),
            'client_id' => env('PAYPAL_CLIENT_ID'),
            'client_secret' => env('PAYPAL_CLIENT_SECRET'),
            'mode' => env('PAYPAL_MODE', 'sandbox'),
        ],
    ],



    /*
    |--------------------------------------------------------------------------
    | Pagination
    |--------------------------------------------------------------------------
    |
    | Default pagination settings for product listings.
    |
    */
    'pagination' => [
        'products_per_page' => env('SHOPPING_PRODUCTS_PER_PAGE', 12),
        'reviews_per_page' => env('SHOPPING_REVIEWS_PER_PAGE', 10),
    ],

    /*
    |--------------------------------------------------------------------------
    | Review Settings
    |--------------------------------------------------------------------------
    |
    | Configuration for product reviews.
    |
    */
    'reviews' => [
        'require_approval' => env('SHOPPING_REVIEWS_REQUIRE_APPROVAL', true),
        'allow_anonymous' => env('SHOPPING_REVIEWS_ALLOW_ANONYMOUS', false),
        'min_rating' => env('SHOPPING_REVIEWS_MIN_RATING', 1),
        'max_rating' => env('SHOPPING_REVIEWS_MAX_RATING', 5),
    ],

    /*
    |--------------------------------------------------------------------------
    | Subscription Settings
    |--------------------------------------------------------------------------
    |
    | Configuration for subscription products.
    |
    */
    'subscriptions' => [
        'default_trial_days' => env('SHOPPING_DEFAULT_TRIAL_DAYS', 7),
        'auto_renewal' => env('SHOPPING_AUTO_RENEWAL', true),
        'grace_period_days' => env('SHOPPING_GRACE_PERIOD_DAYS', 3),
    ],

    /*
    |--------------------------------------------------------------------------
    | Route Settings
    |--------------------------------------------------------------------------
    |
    | Configuration for route loading and installation.
    |
    */
    'routes' => [
        'api' => env('SHOPPING_LOAD_API_ROUTES', false),
        'web' => env('SHOPPING_LOAD_WEB_ROUTES', false),
        'prefix' => env('SHOPPING_ROUTE_PREFIX', 'shopping'),
        'middleware' => [
            'api' => ['auth:sanctum', 'throttle:60,1'],
            'web' => ['web'],
        ],
    ],
];
