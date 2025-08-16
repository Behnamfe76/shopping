<?php

namespace Fereydooni\Shopping;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;

class ShoppingServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__ . '/config/shopping.php', 'shopping'
        );

        // Register Category Repository
        $this->app->bind(
            \Fereydooni\Shopping\app\Repositories\Interfaces\CategoryRepositoryInterface::class,
            \Fereydooni\Shopping\app\Repositories\CategoryRepository::class
        );

        // Register Address Repository
        $this->app->bind(
            \Fereydooni\Shopping\app\Repositories\Interfaces\AddressRepositoryInterface::class,
            \Fereydooni\Shopping\app\Repositories\AddressRepository::class
        );

        // Register Brand Repository
        $this->app->bind(
            \Fereydooni\Shopping\app\Repositories\Interfaces\BrandRepositoryInterface::class,
            \Fereydooni\Shopping\app\Repositories\BrandRepository::class
        );

        // Register Order Repository
        $this->app->bind(
            \Fereydooni\Shopping\app\Repositories\Interfaces\OrderRepositoryInterface::class,
            \Fereydooni\Shopping\app\Repositories\OrderRepository::class
        );

        // Register Category Service
        $this->app->scoped('shopping.category', function ($app) {
            return new \Fereydooni\Shopping\app\Services\CategoryService(
                $app->make(\Fereydooni\Shopping\app\Repositories\Interfaces\CategoryRepositoryInterface::class),
                $app->make(\Fereydooni\Shopping\app\Actions\Category\CreateCategoryAction::class),
                $app->make(\Fereydooni\Shopping\app\Actions\Category\UpdateCategoryAction::class),
                $app->make(\Fereydooni\Shopping\app\Actions\Category\DeleteCategoryAction::class),
                $app->make(\Fereydooni\Shopping\app\Actions\Category\MoveCategoryAction::class),
                $app->make(\Fereydooni\Shopping\app\Actions\Category\GetCategoryTreeAction::class),
                $app->make(\Fereydooni\Shopping\app\Actions\Category\SearchCategoriesAction::class)
            );
        });

        // Register Address Service
        $this->app->scoped('shopping.address', function ($app) {
            return new \Fereydooni\Shopping\app\Services\AddressService(
                $app->make(\Fereydooni\Shopping\app\Repositories\Interfaces\AddressRepositoryInterface::class)
            );
        });

        // Register Brand Service
        $this->app->scoped('shopping.brand', function ($app) {
            return new \Fereydooni\Shopping\app\Services\BrandService(
                $app->make(\Fereydooni\Shopping\app\Repositories\Interfaces\BrandRepositoryInterface::class)
            );
        });

        // Register Order Service
        $this->app->scoped('shopping.order', function ($app) {
            return new \Fereydooni\Shopping\app\Services\OrderService(
                $app->make(\Fereydooni\Shopping\app\Repositories\Interfaces\OrderRepositoryInterface::class)
            );
        });

        // Register Facades
        $this->app->singleton('shopping.order.facade', function ($app) {
            return new \Fereydooni\Shopping\app\Services\OrderService(
                $app->make(\Fereydooni\Shopping\app\Repositories\Interfaces\OrderRepositoryInterface::class)
            );
        });
    }

    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__ . '/database/migrations');

        $this->loadRoutesFrom(__DIR__ . '/routes/web.php');
        $this->loadRoutesFrom(__DIR__ . '/routes/api.php');

        $this->loadViewsFrom(__DIR__ . '/resources/views', 'shopping');

        $this->publishes([
            __DIR__ . '/config/shopping.php' => config_path('shopping.php'),
        ], 'shopping-config');

        $this->publishes([
            __DIR__ . '/database/migrations' => database_path('migrations'),
        ], 'shopping-migrations');

        $this->publishes([
            __DIR__ . '/resources/views' => resource_path('views/vendor/shopping'),
        ], 'shopping-views');

        // Register policies
        $this->registerPolicies();
    }

    /**
     * Register the application's policies.
     */
    protected function registerPolicies(): void
    {
        Gate::policy(\Fereydooni\Shopping\app\Models\Address::class, \Fereydooni\Shopping\app\Policies\AddressPolicy::class);
        Gate::policy(\Fereydooni\Shopping\app\Models\Category::class, \Fereydooni\Shopping\app\Policies\CategoryPolicy::class);
        Gate::policy(\Fereydooni\Shopping\app\Models\Brand::class, \Fereydooni\Shopping\app\Policies\BrandPolicy::class);
        Gate::policy(\Fereydooni\Shopping\app\Models\Order::class, \Fereydooni\Shopping\app\Policies\OrderPolicy::class);
    }
}
