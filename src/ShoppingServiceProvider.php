<?php

namespace Fereydooni\Shopping;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;

class ShoppingServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__ . '/config/shopping.php',
            'shopping'
        );

        // Register Address Repository
        $this->app->bind(
            \Fereydooni\Shopping\app\Repositories\Interfaces\AddressRepositoryInterface::class,
            \Fereydooni\Shopping\app\Repositories\AddressRepository::class
        );

        // Register Customer Repository
        $this->app->bind(
            \Fereydooni\Shopping\app\Repositories\Interfaces\CustomerRepositoryInterface::class,
            \Fereydooni\Shopping\app\Repositories\CustomerRepository::class
        );

        // Register Category Repository
        $this->app->bind(
            \Fereydooni\Shopping\app\Repositories\Interfaces\CategoryRepositoryInterface::class,
            \Fereydooni\Shopping\app\Repositories\CategoryRepository::class
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
                $app->make(\Fereydooni\Shopping\app\Repositories\Interfaces\CategoryRepositoryInterface::class)
            );
        });

        // Register Address Service
        $this->app->scoped('shopping.address', function ($app) {
            return new \Fereydooni\Shopping\app\Services\AddressService(
                $app->make(\Fereydooni\Shopping\app\Repositories\Interfaces\AddressRepositoryInterface::class)
            );
        });

        // Register Customer Service
        $this->app->scoped('shopping.customer', function ($app) {
            return new \Fereydooni\Shopping\app\Services\CustomerService(
                $app->make(\Fereydooni\Shopping\app\Repositories\Interfaces\CustomerRepositoryInterface::class)
            );
        });

        // Register CustomerSegment Repository
        $this->app->bind(
            \Fereydooni\Shopping\app\Repositories\Interfaces\CustomerSegmentRepositoryInterface::class,
            \Fereydooni\Shopping\app\Repositories\CustomerSegmentRepository::class
        );

        // Register CustomerSegment Service
        $this->app->scoped('shopping.customer-segment', function ($app) {
            return new \Fereydooni\Shopping\app\Services\CustomerSegmentService(
                $app->make(\Fereydooni\Shopping\app\Repositories\Interfaces\CustomerSegmentRepositoryInterface::class)
            );
        });

        // Register CustomerSegment Facade
        $this->app->singleton('shopping.customer-segment.facade', function ($app) {
            return new \Fereydooni\Shopping\app\Services\CustomerSegmentService(
                $app->make(\Fereydooni\Shopping\app\Repositories\Interfaces\CustomerSegmentRepositoryInterface::class)
            );
        });

        // Register CustomerPreference Repository
        $this->app->bind(
            \Fereydooni\Shopping\app\Repositories\Interfaces\CustomerPreferenceRepositoryInterface::class,
            \Fereydooni\Shopping\app\Repositories\CustomerPreferenceRepository::class
        );

        // Register CustomerPreference Service
        $this->app->scoped('shopping.customer-preference', function ($app) {
            return new \Fereydooni\Shopping\app\Services\CustomerPreferenceService(
                $app->make(\Fereydooni\Shopping\app\Repositories\Interfaces\CustomerPreferenceRepositoryInterface::class)
            );
        });

        // Register CustomerPreference Facade
        $this->app->singleton('shopping.customer-preference.facade', function ($app) {
            return new \Fereydooni\Shopping\app\Services\CustomerPreferenceService(
                $app->make(\Fereydooni\Shopping\app\Repositories\Interfaces\CustomerPreferenceRepositoryInterface::class)
            );
        });

        // Register CustomerWishlist Repository
        $this->app->bind(
            \Fereydooni\Shopping\app\Repositories\Interfaces\CustomerWishlistRepositoryInterface::class,
            \Fereydooni\Shopping\app\Repositories\CustomerWishlistRepository::class
        );

        // Register CustomerWishlist Service
        $this->app->scoped('shopping.customer-wishlist', function ($app) {
            return new \Fereydooni\Shopping\app\Services\CustomerWishlistService(
                $app->make(\Fereydooni\Shopping\app\Repositories\Interfaces\CustomerWishlistRepositoryInterface::class)
            );
        });

        // Register CustomerWishlist Facade
        $this->app->singleton('shopping.customer-wishlist.facade', function ($app) {
            return new \Fereydooni\Shopping\app\Services\CustomerWishlistService(
                $app->make(\Fereydooni\Shopping\app\Repositories\Interfaces\CustomerWishlistRepositoryInterface::class)
            );
        });

        // Register CustomerNote Repository
        $this->app->bind(
            \Fereydooni\Shopping\app\Repositories\Interfaces\CustomerNoteRepositoryInterface::class,
            \Fereydooni\Shopping\app\Repositories\CustomerNoteRepository::class
        );

        // Register CustomerNote Service
        $this->app->scoped('shopping.customer-note', function ($app) {
            return new \Fereydooni\Shopping\app\Services\CustomerNoteService(
                $app->make(\Fereydooni\Shopping\app\Repositories\Interfaces\CustomerNoteRepositoryInterface::class)
            );
        });

        // Register CustomerNote Facade
        $this->app->singleton('shopping.customer-note.facade', function ($app) {
            return new \Fereydooni\Shopping\app\Services\CustomerNoteService(
                $app->make(\Fereydooni\Shopping\app\Repositories\Interfaces\CustomerNoteRepositoryInterface::class)
            );
        });

        // Register CustomerCommunication Repository
        $this->app->bind(
            \Fereydooni\Shopping\app\Repositories\Interfaces\CustomerCommunicationRepositoryInterface::class,
            \Fereydooni\Shopping\app\Repositories\CustomerCommunicationRepository::class
        );

        // Register CustomerCommunication Service
        $this->app->scoped('shopping.customer-communication', function ($app) {
            return new \Fereydooni\Shopping\app\Services\CustomerCommunicationService(
                $app->make(\Fereydooni\Shopping\app\Repositories\Interfaces\CustomerCommunicationRepositoryInterface::class)
            );
        });

        // Register CustomerCommunication Facade
        $this->app->singleton('shopping.customer-communication.facade', function ($app) {
            return new \Fereydooni\Shopping\app\Services\CustomerCommunicationService(
                $app->make(\Fereydooni\Shopping\app\Repositories\Interfaces\CustomerCommunicationRepositoryInterface::class)
            );
        });

        // Register LoyaltyTransaction Repository
        $this->app->bind(
            \Fereydooni\Shopping\app\Repositories\Interfaces\LoyaltyTransactionRepositoryInterface::class,
            \Fereydooni\Shopping\app\Repositories\LoyaltyTransactionRepository::class
        );

        // Register LoyaltyTransaction Service
        $this->app->scoped('shopping.loyalty-transaction', function ($app) {
            return new \Fereydooni\Shopping\app\Services\LoyaltyTransactionService(
                $app->make(\Fereydooni\Shopping\app\Repositories\Interfaces\LoyaltyTransactionRepositoryInterface::class)
            );
        });

        // Register LoyaltyTransaction Facade
        $this->app->singleton('shopping.loyalty-transaction.facade', function ($app) {
            return new \Fereydooni\Shopping\app\Services\LoyaltyTransactionService(
                $app->make(\Fereydooni\Shopping\app\Repositories\Interfaces\LoyaltyTransactionRepositoryInterface::class)
            );
        });

        // Register Geographic Data Service
        $this->app->singleton('shopping.geographic', function ($app) {
            return new \Fereydooni\Shopping\app\Services\GeographicDataService();
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

        // Register OrderItem Repository
        $this->app->bind(
            \Fereydooni\Shopping\app\Repositories\Interfaces\OrderItemRepositoryInterface::class,
            \Fereydooni\Shopping\app\Repositories\OrderItemRepository::class
        );

        // Register OrderItem Service
        $this->app->scoped('shopping.order-item', function ($app) {
            return new \Fereydooni\Shopping\app\Services\OrderItemService(
                $app->make(\Fereydooni\Shopping\app\Repositories\Interfaces\OrderItemRepositoryInterface::class)
            );
        });

        // Register OrderStatusHistory Repository
        $this->app->bind(
            \Fereydooni\Shopping\app\Repositories\Interfaces\OrderStatusHistoryRepositoryInterface::class,
            \Fereydooni\Shopping\app\Repositories\OrderStatusHistoryRepository::class
        );

        // Register OrderStatusHistory Service
        $this->app->scoped('shopping.order-status-history', function ($app) {
            return new \Fereydooni\Shopping\app\Services\OrderStatusHistoryService(
                $app->make(\Fereydooni\Shopping\app\Repositories\Interfaces\OrderStatusHistoryRepositoryInterface::class)
            );
        });

        // Register ProductAttribute Repository
        $this->app->bind(
            \Fereydooni\Shopping\app\Repositories\Interfaces\ProductAttributeRepositoryInterface::class,
            \Fereydooni\Shopping\app\Repositories\ProductAttributeRepository::class
        );

        // Register ProductAttribute Service
        $this->app->scoped('shopping.product-attribute', function ($app) {
            return new \Fereydooni\Shopping\app\Services\ProductAttributeService(
                $app->make(\Fereydooni\Shopping\app\Repositories\Interfaces\ProductAttributeRepositoryInterface::class)
            );
        });

        // Register ProductAttributeValue Repository
        $this->app->bind(
            \Fereydooni\Shopping\app\Repositories\Interfaces\ProductAttributeValueRepositoryInterface::class,
            \Fereydooni\Shopping\app\Repositories\ProductAttributeValueRepository::class
        );

        // Register ProductAttributeValue Service
        $this->app->scoped('shopping.product-attribute-value', function ($app) {
            return new \Fereydooni\Shopping\app\Services\ProductAttributeValueService(
                $app->make(\Fereydooni\Shopping\app\Repositories\Interfaces\ProductAttributeValueRepositoryInterface::class)
            );
        });

        // Register ProductDiscount Repository
        $this->app->bind(
            \Fereydooni\Shopping\app\Repositories\Interfaces\ProductDiscountRepositoryInterface::class,
            \Fereydooni\Shopping\app\Repositories\ProductDiscountRepository::class
        );

        // Register ProductDiscount Service
        $this->app->scoped('shopping.product-discount', function ($app) {
            return new \Fereydooni\Shopping\app\Services\ProductDiscountService(
                $app->make(\Fereydooni\Shopping\app\Repositories\Interfaces\ProductDiscountRepositoryInterface::class)
            );
        });

        // Register Facades
        $this->app->singleton('shopping.order.facade', function ($app) {
            return new \Fereydooni\Shopping\app\Services\OrderService(
                $app->make(\Fereydooni\Shopping\app\Repositories\Interfaces\OrderRepositoryInterface::class)
            );
        });

        $this->app->singleton('shopping.order-item.facade', function ($app) {
            return new \Fereydooni\Shopping\app\Services\OrderItemService(
                $app->make(\Fereydooni\Shopping\app\Repositories\Interfaces\OrderItemRepositoryInterface::class)
            );
        });

        $this->app->singleton('shopping.order-status-history.facade', function ($app) {
            return new \Fereydooni\Shopping\app\Services\OrderStatusHistoryService(
                $app->make(\Fereydooni\Shopping\app\Repositories\Interfaces\OrderStatusHistoryRepositoryInterface::class)
            );
        });

        $this->app->singleton('shopping.product-attribute.facade', function ($app) {
            return new \Fereydooni\Shopping\app\Services\ProductAttributeService(
                $app->make(\Fereydooni\Shopping\app\Repositories\Interfaces\ProductAttributeRepositoryInterface::class)
            );
        });

        $this->app->singleton('shopping.product-attribute-value.facade', function ($app) {
            return new \Fereydooni\Shopping\app\Services\ProductAttributeValueService(
                $app->make(\Fereydooni\Shopping\app\Repositories\Interfaces\ProductAttributeValueRepositoryInterface::class)
            );
        });

        $this->app->singleton('product-discount-service', function ($app) {
            return new \Fereydooni\Shopping\app\Services\ProductDiscountService(
                $app->make(\Fereydooni\Shopping\app\Repositories\Interfaces\ProductDiscountRepositoryInterface::class)
            );
        });

        // Register Customer Facade
        $this->app->singleton('shopping.customer.facade', function ($app) {
            return new \Fereydooni\Shopping\app\Services\CustomerService(
                $app->make(\Fereydooni\Shopping\app\Repositories\Interfaces\CustomerRepositoryInterface::class)
            );
        });

        // Register Product Repository
        $this->app->bind(
            \Fereydooni\Shopping\app\Repositories\Interfaces\ProductRepositoryInterface::class,
            \Fereydooni\Shopping\app\Repositories\ProductRepository::class
        );

        // Register Product Service
        $this->app->scoped('shopping.product', function ($app) {
            return new \Fereydooni\Shopping\app\Services\ProductService(
                $app->make(\Fereydooni\Shopping\app\Repositories\Interfaces\ProductRepositoryInterface::class)
            );
        });

        // Register Product Facade
        $this->app->singleton('shopping.product.facade', function ($app) {
            return new \Fereydooni\Shopping\app\Services\ProductService(
                $app->make(\Fereydooni\Shopping\app\Repositories\Interfaces\ProductRepositoryInterface::class)
            );
        });

        // Register ProductMeta Repository
        $this->app->bind(
            \Fereydooni\Shopping\app\Repositories\Interfaces\ProductMetaRepositoryInterface::class,
            \Fereydooni\Shopping\app\Repositories\ProductMetaRepository::class
        );

        // Register ProductMeta Service
        $this->app->scoped('shopping.product-meta', function ($app) {
            return new \Fereydooni\Shopping\app\Services\ProductMetaService(
                $app->make(\Fereydooni\Shopping\app\Repositories\Interfaces\ProductMetaRepositoryInterface::class)
            );
        });

        // Register ProductMeta Facade
        $this->app->singleton('shopping.product-meta.facade', function ($app) {
            return new \Fereydooni\Shopping\app\Services\ProductMetaService(
                $app->make(\Fereydooni\Shopping\app\Repositories\Interfaces\ProductMetaRepositoryInterface::class)
            );
        });

        // Register ProductReview Repository
        $this->app->bind(
            \Fereydooni\Shopping\app\Repositories\Interfaces\ProductReviewRepositoryInterface::class,
            \Fereydooni\Shopping\app\Repositories\ProductReviewRepository::class
        );

        // Register ProductReview Service
        $this->app->scoped('shopping.product-review', function ($app) {
            return new \Fereydooni\Shopping\app\Services\ProductReviewService(
                $app->make(\Fereydooni\Shopping\app\Repositories\Interfaces\ProductReviewRepositoryInterface::class)
            );
        });

        // Register ProductReview Facade
        $this->app->singleton('shopping.product-review.facade', function ($app) {
            return new \Fereydooni\Shopping\app\Services\ProductReviewService(
                $app->make(\Fereydooni\Shopping\app\Repositories\Interfaces\ProductReviewRepositoryInterface::class)
            );
        });

        // Register ProductTag Repository
        $this->app->bind(
            \Fereydooni\Shopping\app\Repositories\Interfaces\ProductTagRepositoryInterface::class,
            \Fereydooni\Shopping\app\Repositories\ProductTagRepository::class
        );

        // Register ProductTag Service
        $this->app->scoped('shopping.product-tag', function ($app) {
            return new \Fereydooni\Shopping\app\Services\ProductTagService(
                $app->make(\Fereydooni\Shopping\app\Repositories\Interfaces\ProductTagRepositoryInterface::class)
            );
        });

        // Register ProductTag Facade
        $this->app->singleton('shopping.product-tag.facade', function ($app) {
            return new \Fereydooni\Shopping\app\Services\ProductTagService(
                $app->make(\Fereydooni\Shopping\app\Repositories\Interfaces\ProductTagRepositoryInterface::class)
            );
        });

        // Register ProductVariant Repository
        $this->app->bind(
            \Fereydooni\Shopping\app\Repositories\Interfaces\ProductVariantRepositoryInterface::class,
            \Fereydooni\Shopping\app\Repositories\ProductVariantRepository::class
        );

        // Register ProductVariant Service
        $this->app->scoped('shopping.product-variant', function ($app) {
            return new \Fereydooni\Shopping\app\Services\ProductVariantService(
                $app->make(\Fereydooni\Shopping\app\Repositories\Interfaces\ProductVariantRepositoryInterface::class)
            );
        });

        // Register ProductVariant Facade
        $this->app->singleton('shopping.product-variant.facade', function ($app) {
            return new \Fereydooni\Shopping\app\Services\ProductVariantService(
                $app->make(\Fereydooni\Shopping\app\Repositories\Interfaces\ProductVariantRepositoryInterface::class)
            );
        });

        // Register Shipment Repository
        $this->app->bind(
            \Fereydooni\Shopping\app\Repositories\Interfaces\ShipmentRepositoryInterface::class,
            \Fereydooni\Shopping\app\Repositories\ShipmentRepository::class
        );

        // Register Shipment Service
        $this->app->scoped('shopping.shipment', function ($app) {
            return new \Fereydooni\Shopping\app\Services\ShipmentService(
                $app->make(\Fereydooni\Shopping\app\Repositories\Interfaces\ShipmentRepositoryInterface::class)
            );
        });

        // Register Shipment Facade
        $this->app->singleton('shopping.shipment.facade', function ($app) {
            return new \Fereydooni\Shopping\app\Services\ShipmentService(
                $app->make(\Fereydooni\Shopping\app\Repositories\Interfaces\ShipmentRepositoryInterface::class)
            );
        });

        // Register ShipmentItem Repository
        $this->app->bind(
            \Fereydooni\Shopping\app\Repositories\Interfaces\ShipmentItemRepositoryInterface::class,
            \Fereydooni\Shopping\app\Repositories\ShipmentItemRepository::class
        );

        // Register ShipmentItem Service
        $this->app->scoped('shopping.shipment-item', function ($app) {
            return new \Fereydooni\Shopping\app\Services\ShipmentItemService(
                $app->make(\Fereydooni\Shopping\app\Repositories\Interfaces\ShipmentItemRepositoryInterface::class)
            );
        });

        // Register ShipmentItem Facade
        $this->app->singleton('shopping.shipment-item.facade', function ($app) {
            return new \Fereydooni\Shopping\app\Services\ShipmentItemService(
                $app->make(\Fereydooni\Shopping\app\Repositories\Interfaces\ShipmentItemRepositoryInterface::class)
            );
        });

        // Register Subscription Repository
        $this->app->bind(
            \Fereydooni\Shopping\app\Repositories\Interfaces\SubscriptionRepositoryInterface::class,
            \Fereydooni\Shopping\app\Repositories\SubscriptionRepository::class
        );

        // Register Subscription Service
        $this->app->scoped('shopping.subscription', function ($app) {
            return new \Fereydooni\Shopping\app\Services\SubscriptionService(
                $app->make(\Fereydooni\Shopping\app\Repositories\Interfaces\SubscriptionRepositoryInterface::class)
            );
        });

        // Register Subscription Facade
        $this->app->singleton('shopping.subscription.facade', function ($app) {
            return new \Fereydooni\Shopping\app\Services\SubscriptionService(
                $app->make(\Fereydooni\Shopping\app\Repositories\Interfaces\SubscriptionRepositoryInterface::class)
            );
        });

        // Register UserSubscription Repository
        $this->app->bind(
            \Fereydooni\Shopping\app\Repositories\Interfaces\UserSubscriptionRepositoryInterface::class,
            \Fereydooni\Shopping\app\Repositories\UserSubscriptionRepository::class
        );

        // Register UserSubscription Service
        $this->app->scoped('shopping.user-subscription', function ($app) {
            return new \Fereydooni\Shopping\app\Services\UserSubscriptionService(
                $app->make(\Fereydooni\Shopping\app\Repositories\Interfaces\UserSubscriptionRepositoryInterface::class)
            );
        });

        // Register UserSubscription Facade
        $this->app->singleton('shopping.user-subscription.facade', function ($app) {
            return new \Fereydooni\Shopping\app\Services\UserSubscriptionService(
                $app->make(\Fereydooni\Shopping\app\Repositories\Interfaces\UserSubscriptionRepositoryInterface::class)
            );
        });

        // Register Transaction Repository
        $this->app->bind(
            \Fereydooni\Shopping\app\Repositories\Interfaces\TransactionRepositoryInterface::class,
            \Fereydooni\Shopping\app\Repositories\TransactionRepository::class
        );

        // Register Transaction Service
        $this->app->scoped('shopping.transaction', function ($app) {
            return new \Fereydooni\Shopping\app\Services\TransactionService(
                $app->make(\Fereydooni\Shopping\app\Repositories\Interfaces\TransactionRepositoryInterface::class)
            );
        });

        // Register Transaction Facade
        $this->app->singleton('shopping.transaction.facade', function ($app) {
            return new \Fereydooni\Shopping\app\Services\TransactionService(
                $app->make(\Fereydooni\Shopping\app\Repositories\Interfaces\TransactionRepositoryInterface::class)
            );
        });
    }

    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__ . '/database/migrations');

        // Load routes conditionally based on configuration
        $this->loadRoutesConditionally();

        $this->loadViewsFrom(__DIR__ . '/resources/views', 'shopping');

        // Register console commands
        if ($this->app->runningInConsole()) {
            $this->commands([
                \Fereydooni\Shopping\app\Console\Commands\InstallRoutesCommand::class,
                \Fereydooni\Shopping\app\Console\Commands\UninstallRoutesCommand::class,
                \Fereydooni\Shopping\app\Console\Commands\ListRoutesCommand::class,
            ]);
        }

        $this->publishes([
            __DIR__ . '/config/shopping.php' => config_path('shopping.php'),
        ], 'shopping-config');

        $this->publishes([
            __DIR__ . '/database/migrations' => database_path('migrations'),
        ], 'shopping-migrations');

        $this->publishes([
            __DIR__ . '/resources/views' => resource_path('views/vendor/shopping'),
        ], 'shopping-views');

        $this->publishes([
            __DIR__ . '/database/seeders/' => database_path('seeders/shopping/'),
        ], 'shopping-seeders');

        // Register event service provider
        $this->app->register(\Fereydooni\Shopping\app\Providers\EventServiceProvider::class);

        // Register policies
        $this->registerPolicies();
    }

    /**
     * Load routes conditionally based on configuration.
     */
    protected function loadRoutesConditionally(): void
    {
        $config = config('shopping.routes', []);

        // Load API routes if enabled
        if ($config['api'] ?? true) {
            $this->loadRoutesFrom(__DIR__ . '/routes/api.php');
        }

        // Load web routes if enabled
        if ($config['web'] ?? false) {
            $this->loadRoutesFrom(__DIR__ . '/routes/web.php');
        }
    }

    /**
     * Register the application's policies.
     */
    protected function registerPolicies(): void
    {
        Gate::policy(\Fereydooni\Shopping\app\Models\Address::class, \Fereydooni\Shopping\app\Policies\AddressPolicy::class);
        Gate::policy(\Fereydooni\Shopping\app\Models\Customer::class, \Fereydooni\Shopping\app\Policies\CustomerPolicy::class);
        Gate::policy(\Fereydooni\Shopping\app\Models\CustomerSegment::class, \Fereydooni\Shopping\app\Policies\CustomerSegmentPolicy::class);
        Gate::policy(\Fereydooni\Shopping\app\Models\CustomerPreference::class, \Fereydooni\Shopping\app\Policies\CustomerPreferencePolicy::class);
        Gate::policy(\Fereydooni\Shopping\app\Models\CustomerWishlist::class, \Fereydooni\Shopping\app\Policies\CustomerWishlistPolicy::class);
        Gate::policy(\Fereydooni\Shopping\app\Models\CustomerNote::class, \Fereydooni\Shopping\app\Policies\CustomerNotePolicy::class);
        Gate::policy(\Fereydooni\Shopping\app\Models\CustomerCommunication::class, \Fereydooni\Shopping\app\Policies\CustomerCommunicationPolicy::class);
        Gate::policy(\Fereydooni\Shopping\app\Models\LoyaltyTransaction::class, \Fereydooni\Shopping\app\Policies\LoyaltyTransactionPolicy::class);
        Gate::policy(\Fereydooni\Shopping\app\Models\Category::class, \Fereydooni\Shopping\app\Policies\CategoryPolicy::class);
        Gate::policy(\Fereydooni\Shopping\app\Models\Brand::class, \Fereydooni\Shopping\app\Policies\BrandPolicy::class);
        Gate::policy(\Fereydooni\Shopping\app\Models\Order::class, \Fereydooni\Shopping\app\Policies\OrderPolicy::class);
        Gate::policy(\Fereydooni\Shopping\app\Models\OrderItem::class, \Fereydooni\Shopping\app\Policies\OrderItemPolicy::class);
        Gate::policy(\Fereydooni\Shopping\app\Models\OrderStatusHistory::class, \Fereydooni\Shopping\app\Policies\OrderStatusHistoryPolicy::class);
        Gate::policy(\Fereydooni\Shopping\app\Models\ProductAttribute::class, \Fereydooni\Shopping\app\Policies\ProductAttributePolicy::class);
        Gate::policy(\Fereydooni\Shopping\app\Models\ProductAttributeValue::class, \Fereydooni\Shopping\app\Policies\ProductAttributeValuePolicy::class);
        Gate::policy(\Fereydooni\Shopping\app\Models\ProductDiscount::class, \Fereydooni\Shopping\app\Policies\ProductDiscountPolicy::class);
        Gate::policy(\Fereydooni\Shopping\app\Models\Product::class, \Fereydooni\Shopping\app\Policies\ProductPolicy::class);
        Gate::policy(\Fereydooni\Shopping\app\Models\ProductMeta::class, \Fereydooni\Shopping\app\Policies\ProductMetaPolicy::class);
        Gate::policy(\Fereydooni\Shopping\app\Models\ProductReview::class, \Fereydooni\Shopping\app\Policies\ProductReviewPolicy::class);
        Gate::policy(\Fereydooni\Shopping\app\Models\ProductTag::class, \Fereydooni\Shopping\app\Policies\ProductTagPolicy::class);
        Gate::policy(\Fereydooni\Shopping\app\Models\ProductVariant::class, \Fereydooni\Shopping\app\Policies\ProductVariantPolicy::class);
        Gate::policy(\Fereydooni\Shopping\app\Models\Shipment::class, \Fereydooni\Shopping\app\Policies\ShipmentPolicy::class);
        Gate::policy(\Fereydooni\Shopping\app\Models\ShipmentItem::class, \Fereydooni\Shopping\app\Policies\ShipmentItemPolicy::class);
        Gate::policy(\Fereydooni\Shopping\app\Models\Transaction::class, \Fereydooni\Shopping\app\Policies\TransactionPolicy::class);
        Gate::policy(\Fereydooni\Shopping\app\Models\Subscription::class, \Fereydooni\Shopping\app\Policies\SubscriptionPolicy::class);
        Gate::policy(\Fereydooni\Shopping\app\Models\UserSubscription::class, \Fereydooni\Shopping\app\Policies\UserSubscriptionPolicy::class);
    }
}
