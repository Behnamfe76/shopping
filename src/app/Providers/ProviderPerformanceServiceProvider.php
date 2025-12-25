<?php

namespace App\Providers;

use App\Models\ProviderPerformance;
use App\Policies\ProviderPerformancePolicy;
use App\Repositories\Interfaces\ProviderPerformanceRepositoryInterface;
use App\Repositories\ProviderPerformanceRepository;
use App\Services\ProviderPerformanceService;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class ProviderPerformanceServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // Bind repository interface to implementation
        $this->app->bind(
            ProviderPerformanceRepositoryInterface::class,
            ProviderPerformanceRepository::class
        );

        // Bind service
        $this->app->bind(ProviderPerformanceService::class, function ($app) {
            return new ProviderPerformanceService(
                $app->make(ProviderPerformanceRepositoryInterface::class)
            );
        });

        // Register facade
        $this->app->alias(ProviderPerformanceService::class, 'provider-performance');
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Register policies
        Gate::policy(ProviderPerformance::class, ProviderPerformancePolicy::class);

        // Load routes
        $this->loadRoutesFrom(__DIR__.'/../../routes/web.php');

        // Load views
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'provider-performance');

        // Load migrations
        $this->loadMigrationsFrom(__DIR__.'/../../database/migrations');

        // Publish config
        $this->publishes([
            __DIR__.'/../config/provider-performance.php' => config_path('provider-performance.php'),
        ], 'provider-performance-config');

        // Publish views
        $this->publishes([
            __DIR__.'/../resources/views' => resource_path('views/vendor/provider-performance'),
        ], 'provider-performance-views');

        // Publish assets
        $this->publishes([
            __DIR__.'/../resources/assets' => public_path('vendor/provider-performance'),
        ], 'provider-performance-assets');
    }
}
