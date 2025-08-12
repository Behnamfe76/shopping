<?php

namespace Fereydooni\Shopping;

use Illuminate\Support\ServiceProvider;

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
    }

    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__ . '/database/migrations');

        $this->loadRoutesFrom(__DIR__ . '/routes/web.php');

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
    }
}
