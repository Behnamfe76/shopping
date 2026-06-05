<?php

namespace Fereydooni\Shopping;

use App\Models\User;
use Fereydooni\Shopping\app\Console\Commands\InstallRoutesCommand;
use Fereydooni\Shopping\app\Console\Commands\ListRoutesCommand;
use Fereydooni\Shopping\app\Console\Commands\SeedCommand;
use Fereydooni\Shopping\app\Console\Commands\SeedRequiredDataCommand;
use Fereydooni\Shopping\app\Console\Commands\UninstallRoutesCommand;
use Fereydooni\Shopping\app\Managers\QueryManager;
use Fereydooni\Shopping\app\Models\Address;
use Fereydooni\Shopping\app\Models\Brand;
use Fereydooni\Shopping\app\Models\Category;
use Fereydooni\Shopping\app\Models\Customer;
use Fereydooni\Shopping\app\Models\CustomerCommunication;
use Fereydooni\Shopping\app\Models\CustomerNote;
use Fereydooni\Shopping\app\Models\CustomerPreference;
use Fereydooni\Shopping\app\Models\CustomerWishlist;
use Fereydooni\Shopping\app\Models\Department;
use Fereydooni\Shopping\app\Models\Employee;
use Fereydooni\Shopping\app\Models\EmployeePosition;
use Fereydooni\Shopping\app\Models\LoyaltyTransaction;
use Fereydooni\Shopping\app\Models\Order;
use Fereydooni\Shopping\app\Models\OrderItem;
use Fereydooni\Shopping\app\Models\OrderStatusHistory;
use Fereydooni\Shopping\app\Models\Product;
use Fereydooni\Shopping\app\Models\ProductAttribute;
use Fereydooni\Shopping\app\Models\ProductAttributeValue;
use Fereydooni\Shopping\app\Models\ProductDiscount;
use Fereydooni\Shopping\app\Models\ProductMeta;
use Fereydooni\Shopping\app\Models\ProductReview;
use Fereydooni\Shopping\app\Models\ProductTag;
use Fereydooni\Shopping\app\Models\ProductVariant;
use Fereydooni\Shopping\app\Models\ProviderInsurance;
use Fereydooni\Shopping\app\Models\ProviderLocation;
use Fereydooni\Shopping\app\Models\ShipmentItem;
use Fereydooni\Shopping\app\Models\Subscription;
use Fereydooni\Shopping\app\Models\Team;
use Fereydooni\Shopping\app\Models\Transaction;
use Fereydooni\Shopping\app\Models\UserSubscription;
use Fereydooni\Shopping\app\Policies\AddressPolicy;
use Fereydooni\Shopping\app\Policies\BrandPolicy;
use Fereydooni\Shopping\app\Policies\CategoryPolicy;
use Fereydooni\Shopping\app\Policies\CustomerCommunicationPolicy;
use Fereydooni\Shopping\app\Policies\CustomerNotePolicy;
use Fereydooni\Shopping\app\Policies\CustomerPolicy;
use Fereydooni\Shopping\app\Policies\CustomerPreferencePolicy;
use Fereydooni\Shopping\app\Policies\CustomerWishlistPolicy;
use Fereydooni\Shopping\app\Policies\DepartmentPolicy;
use Fereydooni\Shopping\app\Policies\EmployeePolicy;
use Fereydooni\Shopping\app\Policies\EmployeePositionPolicy;
use Fereydooni\Shopping\app\Policies\LoyaltyTransactionPolicy;
use Fereydooni\Shopping\app\Policies\OrderItemPolicy;
use Fereydooni\Shopping\app\Policies\OrderPolicy;
use Fereydooni\Shopping\app\Policies\OrderStatusHistoryPolicy;
use Fereydooni\Shopping\app\Policies\ProductAttributePolicy;
use Fereydooni\Shopping\app\Policies\ProductAttributeValuePolicy;
use Fereydooni\Shopping\app\Policies\ProductDiscountPolicy;
use Fereydooni\Shopping\app\Policies\ProductMetaPolicy;
use Fereydooni\Shopping\app\Policies\ProductPolicy;
use Fereydooni\Shopping\app\Policies\ProductReviewPolicy;
use Fereydooni\Shopping\app\Policies\ProductTagPolicy;
use Fereydooni\Shopping\app\Policies\ProductVariantPolicy;
use Fereydooni\Shopping\app\Policies\ProviderInsurancePolicy;
use Fereydooni\Shopping\app\Policies\ProviderLocationPolicy;
use Fereydooni\Shopping\app\Policies\ShipmentItemPolicy;
use Fereydooni\Shopping\app\Policies\SubscriptionPolicy;
use Fereydooni\Shopping\app\Policies\TeamPolicy;
use Fereydooni\Shopping\app\Policies\TransactionPolicy;
use Fereydooni\Shopping\app\Policies\UserPolicy;
use Fereydooni\Shopping\app\Policies\UserSubscriptionPolicy;
use Fereydooni\Shopping\app\Providers\EventServiceProvider;
use Fereydooni\Shopping\app\Repositories\BrandRepository;
use Fereydooni\Shopping\app\Repositories\CategoryRepository;
use Fereydooni\Shopping\app\Repositories\CustomerRepository;
use Fereydooni\Shopping\app\Repositories\DepartmentRepository;
use Fereydooni\Shopping\app\Repositories\EmployeeRepository;
use Fereydooni\Shopping\app\Repositories\Interfaces\BrandRepositoryInterface;
use Fereydooni\Shopping\app\Repositories\Interfaces\CategoryRepositoryInterface;
use Fereydooni\Shopping\app\Repositories\Interfaces\CustomerRepositoryInterface;
use Fereydooni\Shopping\app\Repositories\Interfaces\DepartmentRepositoryInterface;
use Fereydooni\Shopping\app\Repositories\Interfaces\EmployeeRepositoryInterface;
use Fereydooni\Shopping\app\Repositories\Interfaces\ProductAttributeRepositoryInterface;
use Fereydooni\Shopping\app\Repositories\Interfaces\ProductRepositoryInterface;
use Fereydooni\Shopping\app\Repositories\Interfaces\ProductTagRepositoryInterface;
use Fereydooni\Shopping\app\Repositories\Interfaces\TeamRepositoryInterface;
use Fereydooni\Shopping\app\Repositories\Interfaces\UserRepositoryInterface;
use Fereydooni\Shopping\app\Repositories\ProductAttributeRepository;
use Fereydooni\Shopping\app\Repositories\ProductRepository;
use Fereydooni\Shopping\app\Repositories\ProductTagRepository;
use Fereydooni\Shopping\app\Repositories\TeamRepository;
use Fereydooni\Shopping\app\Repositories\UserRepository;
use Fereydooni\Shopping\app\Services\BrandService;
use Fereydooni\Shopping\app\Services\CategoryService;
use Fereydooni\Shopping\app\Services\CustomerService;
use Fereydooni\Shopping\app\Services\DepartmentService;
use Fereydooni\Shopping\app\Services\EmployeeService;
use Fereydooni\Shopping\app\Services\ProductAttributeService;
use Fereydooni\Shopping\app\Services\ProductService;
use Fereydooni\Shopping\app\Services\ProductTagService;
use Fereydooni\Shopping\app\Services\TeamService;
use Fereydooni\Shopping\app\Services\UserService;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class ShoppingServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__.'/config/shopping.php',
            'shopping'
        );

        // Register QueryManager as singleton
        $this->app->singleton(QueryManager::class);

        // // Register Address Repository
        // $this->app->bind(
        //     \Fereydooni\Shopping\app\Repositories\Interfaces\AddressRepositoryInterface::class,
        //     \Fereydooni\Shopping\app\Repositories\AddressRepository::class
        // );

        // Register Category Repository
        $this->app->bind(
            CategoryRepositoryInterface::class,
            CategoryRepository::class
        );

        // Register Category Service
        $this->app->scoped('shopping.category', function ($app) {
            return new CategoryService(
                $app->make(CategoryRepositoryInterface::class)
            );
        });

        // Register ProductTag Facade
        $this->app->singleton('shopping.category.facade', function ($app) {
            return new CategoryService(
                $app->make(CategoryRepositoryInterface::class)
            );
        });

        // Register Department Repository
        $this->app->bind(
            DepartmentRepositoryInterface::class,
            DepartmentRepository::class
        );

        // Register Department Service
        $this->app->scoped('shopping.department', function ($app) {
            return new DepartmentService(
                $app->make(DepartmentRepositoryInterface::class)
            );
        });

        // Register Department Facade
        $this->app->singleton('shopping.department.facade', function ($app) {
            return new DepartmentService(
                $app->make(DepartmentRepositoryInterface::class)
            );
        });

        // Register Team Repository
        $this->app->bind(
            TeamRepositoryInterface::class,
            TeamRepository::class
        );

        // Register Team Service
        $this->app->scoped('shopping.team', function ($app) {
            return new TeamService(
                $app->make(TeamRepositoryInterface::class)
            );
        });

        // Register Team Facade
        $this->app->singleton('shopping.team.facade', function ($app) {
            return new TeamService(
                $app->make(TeamRepositoryInterface::class)
            );
        });

        // Register Brand Repository
        $this->app->bind(
            BrandRepositoryInterface::class,
            BrandRepository::class
        );

        // Register Brand Service
        $this->app->scoped('shopping.brand', function ($app) {
            return new BrandService(
                $app->make(BrandRepositoryInterface::class)
            );
        });

        // Register Brand Facade
        $this->app->singleton('shopping.brand.facade', function ($app) {
            return new BrandService(
                $app->make(BrandRepositoryInterface::class)
            );
        });

        // Register Product Repository
        $this->app->bind(
            ProductRepositoryInterface::class,
            ProductRepository::class
        );

        // Register Product Service
        $this->app->scoped('shopping.product', function ($app) {
            return new ProductService(
                $app->make(ProductRepositoryInterface::class)
            );
        });

        // Register Product Facade
        $this->app->singleton('shopping.product.facade', function ($app) {
            return new ProductService(
                $app->make(ProductRepositoryInterface::class)
            );
        });

        // Register User Service
        $this->app->scoped('shopping.user', function ($app) {
            return new UserService(
                $app->make(UserRepositoryInterface::class)
            );
        });

        // Register User Repository
        $this->app->bind(
            UserRepositoryInterface::class,
            UserRepository::class
        );

        // Register User Facade
        $this->app->singleton('shopping.user.facade', function ($app) {
            return new UserService(
                $app->make(UserRepositoryInterface::class)
            );
        });

        // Register Customer Service
        $this->app->scoped('shopping.customer', function ($app) {
            return new CustomerService(
                $app->make(CustomerRepositoryInterface::class)
            );
        });

        // Register Customer Repository
        $this->app->bind(
            CustomerRepositoryInterface::class,
            CustomerRepository::class
        );

        // Register Customer Facade
        $this->app->singleton('shopping.customer.facade', function ($app) {
            return new CustomerService(
                $app->make(CustomerRepositoryInterface::class)
            );
        });

        // Register Employee Service
        $this->app->scoped('shopping.customer', function ($app) {
            return new EmployeeService(
                $app->make(EmployeeRepositoryInterface::class)
            );
        });

        // Register Employee Repository
        $this->app->bind(
            EmployeeRepositoryInterface::class,
            EmployeeRepository::class
        );

        // Register Employee Facade
        $this->app->singleton('shopping.customer.facade', function ($app) {
            return new EmployeeService(
                $app->make(EmployeeRepositoryInterface::class)
            );
        });

        // // Register Order Repository
        // $this->app->bind(
        //     \Fereydooni\Shopping\app\Repositories\Interfaces\OrderRepositoryInterface::class,
        //     \Fereydooni\Shopping\app\Repositories\OrderRepository::class
        // );

        // // Register Address Service
        // $this->app->scoped('shopping.address', function ($app) {
        //     return new \Fereydooni\Shopping\app\Services\AddressService(
        //         $app->make(\Fereydooni\Shopping\app\Repositories\Interfaces\AddressRepositoryInterface::class)
        //     );
        // });

        // // Register CustomerSegment Service
        // $this->app->scoped('shopping.customer-segment', function ($app) {
        //     return new \Fereydooni\Shopping\app\Services\CustomerSegmentService(
        //         $app->make(\Fereydooni\Shopping\app\Repositories\Interfaces\CustomerSegmentRepositoryInterface::class)
        //     );
        // });

        // // Register CustomerSegment Repository
        // $this->app->bind(
        //     \Fereydooni\Shopping\app\Repositories\Interfaces\CustomerSegmentRepositoryInterface::class,
        //     \Fereydooni\Shopping\app\Repositories\CustomerSegmentRepository::class
        // );

        // // Register CustomerSegment Facade
        // $this->app->singleton('shopping.customer-segment.facade', function ($app) {
        //     return new \Fereydooni\Shopping\app\Services\CustomerSegmentService(
        //         $app->make(\Fereydooni\Shopping\app\Repositories\Interfaces\CustomerSegmentRepositoryInterface::class)
        //     );
        // });

        // // Register CustomerPreference Repository
        // $this->app->bind(
        //     \Fereydooni\Shopping\app\Repositories\Interfaces\CustomerPreferenceRepositoryInterface::class,
        //     \Fereydooni\Shopping\app\Repositories\CustomerPreferenceRepository::class
        // );

        // // Register CustomerPreference Service
        // $this->app->scoped('shopping.customer-preference', function ($app) {
        //     return new \Fereydooni\Shopping\app\Services\CustomerPreferenceService(
        //         $app->make(\Fereydooni\Shopping\app\Repositories\Interfaces\CustomerPreferenceRepositoryInterface::class)
        //     );
        // });

        // // Register ProviderCommunication Repository
        // $this->app->bind(
        //     \Fereydooni\Shopping\app\Repositories\Interfaces\ProviderCommunicationRepositoryInterface::class,
        //     \Fereydooni\Shopping\app\Repositories\ProviderCommunicationRepository::class
        // );

        // // Register ProviderCommunication Service
        // $this->app->scoped('shopping.provider-communication', function ($app) {
        //     return new \Fereydooni\Shopping\app\Services\ProviderCommunicationService(
        //         $app->make(\Fereydooni\Shopping\app\Repositories\Interfaces\ProviderCommunicationRepositoryInterface::class)
        //     );
        // });

        // // Register ProviderCommunication Facade
        // $this->app->singleton('shopping.provider-communication.facade', function ($app) {
        //     return new \Fereydooni\Shopping\app\Services\ProviderCommunicationService(
        //         $app->make(\Fereydooni\Shopping\app\Repositories\Interfaces\ProviderCommunicationRepositoryInterface::class)
        //     );
        // });

        // // Register ProviderLocation Repository
        // $this->app->bind(
        //     \Fereydooni\Shopping\app\Repositories\Interfaces\ProviderLocationRepositoryInterface::class,
        //     \Fereydooni\Shopping\app\Repositories\ProviderLocationRepository::class
        // );

        // // Register ProviderLocation Service
        // $this->app->scoped('shopping.provider-location', function ($app) {
        //     return new \Fereydooni\Shopping\app\Services\ProviderLocationService(
        //         $app->make(\Fereydooni\Shopping\app\Repositories\Interfaces\ProviderLocationRepositoryInterface::class)
        //     );
        // });

        // // Register ProviderLocation Facade
        // $this->app->singleton('shopping.provider-location.facade', function ($app) {
        //     return new \Fereydooni\Shopping\app\Services\ProviderLocationService(
        //         $app->make(\Fereydooni\Shopping\app\Repositories\Interfaces\ProviderLocationRepositoryInterface::class)
        //     );
        // });

        // // Register ProviderInsurance Repository
        // $this->app->bind(
        //     \Fereydooni\Shopping\app\Repositories\Interfaces\ProviderInsuranceRepositoryInterface::class,
        //     \Fereydooni\Shopping\app\Repositories\ProviderInsuranceRepository::class
        // );

        // // Register ProviderInsurance Service
        // $this->app->scoped('shopping.provider-insurance', function ($app) {
        //     return new \Fereydooni\Shopping\app\Services\ProviderInsuranceService(
        //         $app->make(\Fereydooni\Shopping\app\Repositories\Interfaces\ProviderInsuranceRepositoryInterface::class)
        //     );
        // });

        // // Register ProviderInsurance Facade
        // $this->app->singleton('shopping.provider-insurance.facade', function ($app) {
        //     return new \Fereydooni\Shopping\app\Services\ProviderInsuranceService(
        //         $app->make(\Fereydooni\Shopping\app\Repositories\Interfaces\ProviderInsuranceRepositoryInterface::class)
        //     );
        // });

        // // Register CustomerPreference Facade
        // $this->app->singleton('shopping.customer-preference.facade', function ($app) {
        //     return new \Fereydooni\Shopping\app\Services\CustomerPreferenceService(
        //         $app->make(\Fereydooni\Shopping\app\Repositories\Interfaces\CustomerPreferenceRepositoryInterface::class)
        //     );
        // });

        // // Register CustomerWishlist Repository
        // $this->app->bind(
        //     \Fereydooni\Shopping\app\Repositories\Interfaces\CustomerWishlistRepositoryInterface::class,
        //     \Fereydooni\Shopping\app\Repositories\CustomerWishlistRepository::class
        // );

        // // Register CustomerWishlist Service
        // $this->app->scoped('shopping.customer-wishlist', function ($app) {
        //     return new \Fereydooni\Shopping\app\Services\CustomerWishlistService(
        //         $app->make(\Fereydooni\Shopping\app\Repositories\Interfaces\CustomerWishlistRepositoryInterface::class)
        //     );
        // });

        // // Register CustomerWishlist Facade
        // $this->app->singleton('shopping.customer-wishlist.facade', function ($app) {
        //     return new \Fereydooni\Shopping\app\Services\CustomerWishlistService(
        //         $app->make(\Fereydooni\Shopping\app\Repositories\Interfaces\CustomerWishlistRepositoryInterface::class)
        //     );
        // });

        // // Register CustomerNote Repository
        // $this->app->bind(
        //     \Fereydooni\Shopping\app\Repositories\Interfaces\CustomerNoteRepositoryInterface::class,
        //     \Fereydooni\Shopping\app\Repositories\CustomerNoteRepository::class
        // );

        // // Register CustomerNote Service
        // $this->app->scoped('shopping.customer-note', function ($app) {
        //     return new \Fereydooni\Shopping\app\Services\CustomerNoteService(
        //         $app->make(\Fereydooni\Shopping\app\Repositories\Interfaces\CustomerNoteRepositoryInterface::class)
        //     );
        // });

        // // Register CustomerNote Facade
        // $this->app->singleton('shopping.customer-note.facade', function ($app) {
        //     return new \Fereydooni\Shopping\app\Services\CustomerNoteService(
        //         $app->make(\Fereydooni\Shopping\app\Repositories\Interfaces\CustomerNoteRepositoryInterface::class)
        //     );
        // });

        // // Register CustomerCommunication Repository
        // $this->app->bind(
        //     \Fereydooni\Shopping\app\Repositories\Interfaces\CustomerCommunicationRepositoryInterface::class,
        //     \Fereydooni\Shopping\app\Repositories\CustomerCommunicationRepository::class
        // );

        // // Register CustomerCommunication Service
        // $this->app->scoped('shopping.customer-communication', function ($app) {
        //     return new \Fereydooni\Shopping\app\Services\CustomerCommunicationService(
        //         $app->make(\Fereydooni\Shopping\app\Repositories\Interfaces\CustomerCommunicationRepositoryInterface::class)
        //     );
        // });

        // // Register CustomerCommunication Facade
        // $this->app->singleton('shopping.customer-communication.facade', function ($app) {
        //     return new \Fereydooni\Shopping\app\Services\CustomerCommunicationService(
        //         $app->make(\Fereydooni\Shopping\app\Repositories\Interfaces\CustomerCommunicationRepositoryInterface::class)
        //     );
        // });

        // // Register LoyaltyTransaction Repository
        // $this->app->bind(
        //     \Fereydooni\Shopping\app\Repositories\Interfaces\LoyaltyTransactionRepositoryInterface::class,
        //     \Fereydooni\Shopping\app\Repositories\LoyaltyTransactionRepository::class
        // );

        // // Register LoyaltyTransaction Service
        // $this->app->scoped('shopping.loyalty-transaction', function ($app) {
        //     return new \Fereydooni\Shopping\app\Services\LoyaltyTransactionService(
        //         $app->make(\Fereydooni\Shopping\app\Repositories\Interfaces\LoyaltyTransactionRepositoryInterface::class)
        //     );
        // });

        // // Register LoyaltyTransaction Facade
        // $this->app->singleton('shopping.loyalty-transaction.facade', function ($app) {
        //     return new \Fereydooni\Shopping\app\Services\LoyaltyTransactionService(
        //         $app->make(\Fereydooni\Shopping\app\Repositories\Interfaces\LoyaltyTransactionRepositoryInterface::class)
        //     );
        // });

        // // Register Geographic Data Service
        // $this->app->singleton('shopping.geographic', function ($app) {
        //     return new \Fereydooni\Shopping\app\Services\GeographicDataService();
        // });

        // // Register Order Service
        // $this->app->scoped('shopping.order', function ($app) {
        //     return new \Fereydooni\Shopping\app\Services\OrderService(
        //         $app->make(\Fereydooni\Shopping\app\Repositories\Interfaces\OrderRepositoryInterface::class)
        //     );
        // });

        // // Register OrderItem Repository
        // $this->app->bind(
        //     \Fereydooni\Shopping\app\Repositories\Interfaces\OrderItemRepositoryInterface::class,
        //     \Fereydooni\Shopping\app\Repositories\OrderItemRepository::class
        // );

        // // Register OrderItem Service
        // $this->app->scoped('shopping.order-item', function ($app) {
        //     return new \Fereydooni\Shopping\app\Services\OrderItemService(
        //         $app->make(\Fereydooni\Shopping\app\Repositories\Interfaces\OrderItemRepositoryInterface::class)
        //     );
        // });

        // // Register OrderStatusHistory Repository
        // $this->app->bind(
        //     \Fereydooni\Shopping\app\Repositories\Interfaces\OrderStatusHistoryRepositoryInterface::class,
        //     \Fereydooni\Shopping\app\Repositories\OrderStatusHistoryRepository::class
        // );

        // // Register OrderStatusHistory Service
        // $this->app->scoped('shopping.order-status-history', function ($app) {
        //     return new \Fereydooni\Shopping\app\Services\OrderStatusHistoryService(
        //         $app->make(\Fereydooni\Shopping\app\Repositories\Interfaces\OrderStatusHistoryRepositoryInterface::class)
        //     );
        // });

        // Register ProductAttribute Repository
        $this->app->bind(
            ProductAttributeRepositoryInterface::class,
            ProductAttributeRepository::class
        );

        // Register ProductAttribute Service
        $this->app->scoped('shopping.product-attribute', function ($app) {
            return new ProductAttributeService(
                $app->make(ProductAttributeRepositoryInterface::class)
            );
        });

        $this->app->singleton('shopping.product-attribute.facade', function ($app) {
            return new ProductAttributeService(
                $app->make(ProductAttributeRepositoryInterface::class)
            );
        });

        // // Register ProductAttributeValue Repository
        // $this->app->bind(
        //     \Fereydooni\Shopping\app\Repositories\Interfaces\ProductAttributeValueRepositoryInterface::class,
        //     \Fereydooni\Shopping\app\Repositories\ProductAttributeValueRepository::class
        // );

        // // Register ProductAttributeValue Service
        // $this->app->scoped('shopping.product-attribute-value', function ($app) {
        //     return new \Fereydooni\Shopping\app\Services\ProductAttributeValueService(
        //         $app->make(\Fereydooni\Shopping\app\Repositories\Interfaces\ProductAttributeValueRepositoryInterface::class)
        //     );
        // });

        // // Register ProductDiscount Repository
        // $this->app->bind(
        //     \Fereydooni\Shopping\app\Repositories\Interfaces\ProductDiscountRepositoryInterface::class,
        //     \Fereydooni\Shopping\app\Repositories\ProductDiscountRepository::class
        // );

        // // Register ProductDiscount Service
        // $this->app->scoped('shopping.product-discount', function ($app) {
        //     return new \Fereydooni\Shopping\app\Services\ProductDiscountService(
        //         $app->make(\Fereydooni\Shopping\app\Repositories\Interfaces\ProductDiscountRepositoryInterface::class)
        //     );
        // });

        // // Register Facades
        // $this->app->singleton('shopping.order.facade', function ($app) {
        //     return new \Fereydooni\Shopping\app\Services\OrderService(
        //         $app->make(\Fereydooni\Shopping\app\Repositories\Interfaces\OrderRepositoryInterface::class)
        //     );
        // });

        // $this->app->singleton('shopping.order-item.facade', function ($app) {
        //     return new \Fereydooni\Shopping\app\Services\OrderItemService(
        //         $app->make(\Fereydooni\Shopping\app\Repositories\Interfaces\OrderItemRepositoryInterface::class)
        //     );
        // });

        // $this->app->singleton('shopping.order-status-history.facade', function ($app) {
        //     return new \Fereydooni\Shopping\app\Services\OrderStatusHistoryService(
        //         $app->make(\Fereydooni\Shopping\app\Repositories\Interfaces\OrderStatusHistoryRepositoryInterface::class)
        //     );
        // });

        // $this->app->singleton('shopping.product-attribute-value.facade', function ($app) {
        //     return new \Fereydooni\Shopping\app\Services\ProductAttributeValueService(
        //         $app->make(\Fereydooni\Shopping\app\Repositories\Interfaces\ProductAttributeValueRepositoryInterface::class)
        //     );
        // });

        // $this->app->singleton('product-discount-service', function ($app) {
        //     return new \Fereydooni\Shopping\app\Services\ProductDiscountService(
        //         $app->make(\Fereydooni\Shopping\app\Repositories\Interfaces\ProductDiscountRepositoryInterface::class)
        //     );
        // });

        // // Register ProductMeta Repository
        // $this->app->bind(
        //     \Fereydooni\Shopping\app\Repositories\Interfaces\ProductMetaRepositoryInterface::class,
        //     \Fereydooni\Shopping\app\Repositories\ProductMetaRepository::class
        // );

        // // Register ProductMeta Service
        // $this->app->scoped('shopping.product-meta', function ($app) {
        //     return new \Fereydooni\Shopping\app\Services\ProductMetaService(
        //         $app->make(\Fereydooni\Shopping\app\Repositories\Interfaces\ProductMetaRepositoryInterface::class)
        //     );
        // });

        // // Register ProductMeta Facade
        // $this->app->singleton('shopping.product-meta.facade', function ($app) {
        //     return new \Fereydooni\Shopping\app\Services\ProductMetaService(
        //         $app->make(\Fereydooni\Shopping\app\Repositories\Interfaces\ProductMetaRepositoryInterface::class)
        //     );
        // });

        // // Register ProductReview Repository
        // $this->app->bind(
        //     \Fereydooni\Shopping\app\Repositories\Interfaces\ProductReviewRepositoryInterface::class,
        //     \Fereydooni\Shopping\app\Repositories\ProductReviewRepository::class
        // );

        // // Register ProductReview Service
        // $this->app->scoped('shopping.product-review', function ($app) {
        //     return new \Fereydooni\Shopping\app\Services\ProductReviewService(
        //         $app->make(\Fereydooni\Shopping\app\Repositories\Interfaces\ProductReviewRepositoryInterface::class)
        //     );
        // });

        // // Register ProductReview Facade
        // $this->app->singleton('shopping.product-review.facade', function ($app) {
        //     return new \Fereydooni\Shopping\app\Services\ProductReviewService(
        //         $app->make(\Fereydooni\Shopping\app\Repositories\Interfaces\ProductReviewRepositoryInterface::class)
        //     );
        // });

        // Register ProductTag Repository
        $this->app->bind(
            ProductTagRepositoryInterface::class,
            ProductTagRepository::class
        );

        // Register ProductTag Service
        $this->app->scoped('shopping.product-tag', function ($app) {
            return new ProductTagService(
                $app->make(ProductTagRepositoryInterface::class)
            );
        });

        // Register ProductTag Facade
        $this->app->singleton('shopping.product-tag.facade', function ($app) {
            return new ProductTagService(
                $app->make(ProductTagRepositoryInterface::class)
            );
        });

        // // Register ProductVariant Repository
        // $this->app->bind(
        //     \Fereydooni\Shopping\app\Repositories\Interfaces\ProductVariantRepositoryInterface::class,
        //     \Fereydooni\Shopping\app\Repositories\ProductVariantRepository::class
        // );

        // // Register ProductVariant Service
        // $this->app->scoped('shopping.product-variant', function ($app) {
        //     return new \Fereydooni\Shopping\app\Services\ProductVariantService(
        //         $app->make(\Fereydooni\Shopping\app\Repositories\Interfaces\ProductVariantRepositoryInterface::class)
        //     );
        // });

        // // Register ProductVariant Facade
        // $this->app->singleton('shopping.product-variant.facade', function ($app) {
        //     return new \Fereydooni\Shopping\app\Services\ProductVariantService(
        //         $app->make(\Fereydooni\Shopping\app\Repositories\Interfaces\ProductVariantRepositoryInterface::class)
        //     );
        // });

        // // Register Shipment Repository
        // $this->app->bind(
        //     \Fereydooni\Shopping\app\Repositories\Interfaces\ShipmentRepositoryInterface::class,
        //     \Fereydooni\Shopping\app\Repositories\ShipmentRepository::class
        // );

        // // Register Shipment Service
        // $this->app->scoped('shopping.shipment', function ($app) {
        //     return new \Fereydooni\Shopping\app\Services\ShipmentService(
        //         $app->make(\Fereydooni\Shopping\app\Repositories\Interfaces\ShipmentRepositoryInterface::class)
        //     );
        // });

        // // Register Shipment Facade
        // $this->app->singleton('shopping.shipment.facade', function ($app) {
        //     return new \Fereydooni\Shopping\app\Services\ShipmentService(
        //         $app->make(\Fereydooni\Shopping\app\Repositories\Interfaces\ShipmentRepositoryInterface::class)
        //     );
        // });

        // // Register ShipmentItem Repository
        // $this->app->bind(
        //     \Fereydooni\Shopping\app\Repositories\Interfaces\ShipmentItemRepositoryInterface::class,
        //     \Fereydooni\Shopping\app\Repositories\ShipmentItemRepository::class
        // );

        // // Register ShipmentItem Service
        // $this->app->scoped('shopping.shipment-item', function ($app) {
        //     return new \Fereydooni\Shopping\app\Services\ShipmentItemService(
        //         $app->make(\Fereydooni\Shopping\app\Repositories\Interfaces\ShipmentItemRepositoryInterface::class)
        //     );
        // });

        // // Register ShipmentItem Facade
        // $this->app->singleton('shopping.shipment-item.facade', function ($app) {
        //     return new \Fereydooni\Shopping\app\Services\ShipmentItemService(
        //         $app->make(\Fereydooni\Shopping\app\Repositories\Interfaces\ShipmentItemRepositoryInterface::class)
        //     );
        // });

        // // Register Subscription Repository
        // $this->app->bind(
        //     \Fereydooni\Shopping\app\Repositories\Interfaces\SubscriptionRepositoryInterface::class,
        //     \Fereydooni\Shopping\app\Repositories\SubscriptionRepository::class
        // );

        // // Register Subscription Service
        // $this->app->scoped('shopping.subscription', function ($app) {
        //     return new \Fereydooni\Shopping\app\Services\SubscriptionService(
        //         $app->make(\Fereydooni\Shopping\app\Repositories\Interfaces\SubscriptionRepositoryInterface::class)
        //     );
        // });

        // // Register Subscription Facade
        // $this->app->singleton('shopping.subscription.facade', function ($app) {
        //     return new \Fereydooni\Shopping\app\Services\SubscriptionService(
        //         $app->make(\Fereydooni\Shopping\app\Repositories\Interfaces\SubscriptionRepositoryInterface::class)
        //     );
        // });

        // // Register UserSubscription Repository
        // $this->app->bind(
        //     \Fereydooni\Shopping\app\Repositories\Interfaces\UserSubscriptionRepositoryInterface::class,
        //     \Fereydooni\Shopping\app\Repositories\UserSubscriptionRepository::class
        // );

        // // Register UserSubscription Service
        // $this->app->scoped('shopping.user-subscription', function ($app) {
        //     return new \Fereydooni\Shopping\app\Services\UserSubscriptionService(
        //         $app->make(\Fereydooni\Shopping\app\Repositories\Interfaces\UserSubscriptionRepositoryInterface::class)
        //     );
        // });

        // // Register UserSubscription Facade
        // $this->app->singleton('shopping.user-subscription.facade', function ($app) {
        //     return new \Fereydooni\Shopping\app\Services\UserSubscriptionService(
        //         $app->make(\Fereydooni\Shopping\app\Repositories\Interfaces\UserSubscriptionRepositoryInterface::class)
        //     );
        // });

        // // Register Transaction Repository
        // $this->app->bind(
        //     \Fereydooni\Shopping\app\Repositories\Interfaces\TransactionRepositoryInterface::class,
        //     \Fereydooni\Shopping\app\Repositories\TransactionRepository::class
        // );

        // // Register Transaction Service
        // $this->app->scoped('shopping.transaction', function ($app) {
        //     return new \Fereydooni\Shopping\app\Services\TransactionService(
        //         $app->make(\Fereydooni\Shopping\app\Repositories\Interfaces\TransactionRepositoryInterface::class)
        //     );
        // });

        // // Register Transaction Facade
        // $this->app->singleton('shopping.transaction.facade', function ($app) {
        //     return new \Fereydooni\Shopping\app\Services\TransactionService(
        //         $app->make(\Fereydooni\Shopping\app\Repositories\Interfaces\TransactionRepositoryInterface::class)
        //     );
        // });

        // // Register Department Repository
        // $this->app->bind(
        //     \Fereydooni\Shopping\app\Repositories\Interfaces\DepartmentRepositoryInterface::class,
        //     \Fereydooni\Shopping\app\Repositories\DepartmentRepository::class
        // );

        // // Register Department Service
        // $this->app->scoped('shopping.employee-department', function ($app) {
        //     return new \Fereydooni\Shopping\app\Repositories\DepartmentRepository(
        //         $app->make(\Fereydooni\Shopping\app\Repositories\Interfaces\DepartmentRepositoryInterface::class)
        //     );
        // });

        // // Register Department Facade
        // $this->app->singleton('shopping.employee-department.facade', function ($app) {
        //     return new \Fereydooni\Shopping\app\Repositories\DepartmentRepository(
        //         $app->make(\Fereydooni\Shopping\app\Repositories\Interfaces\DepartmentRepositoryInterface::class)
        //     );
        // });

        // // Register EmployeePosition Repository
        // $this->app->bind(
        //     \Fereydooni\Shopping\app\Repositories\Interfaces\EmployeePositionRepositoryInterface::class,
        //     \Fereydooni\Shopping\app\Repositories\EmployeePositionRepository::class
        // );

        // // Register EmployeePosition Service
        // $this->app->scoped('shopping.employee-position', function ($app) {
        //     return new \Fereydooni\Shopping\app\Repositories\EmployeePositionRepository(
        //         $app->make(\Fereydooni\Shopping\app\Repositories\Interfaces\EmployeePositionRepositoryInterface::class)
        //     );
        // });

        // // Register EmployeePosition Facade
        // $this->app->singleton('shopping.employee-position.facade', function ($app) {
        //     return new \Fereydooni\Shopping\app\Repositories\EmployeePositionRepository(
        //         $app->make(\Fereydooni\Shopping\app\Repositories\Interfaces\EmployeePositionRepositoryInterface::class)
        //     );
        // });
    }

    public function boot(): void
    {
        $this->loadTranslationsFrom(__DIR__.'/lang');

        $this->loadMigrationsFrom(__DIR__.'/database/migrations');

        // Load routes conditionally based on configuration
        $this->loadRoutesConditionally();

        // Register console commands
        if ($this->app->runningInConsole()) {
            $this->commands([
                InstallRoutesCommand::class,
                UninstallRoutesCommand::class,
                ListRoutesCommand::class,
                SeedCommand::class,
                SeedRequiredDataCommand::class,
            ]);
        }

        $this->publishes([
            __DIR__.'/config/shopping.php' => config_path('shopping.php'),
        ], 'shopping-config');

        $this->publishes([
            __DIR__.'/database/migrations' => database_path('migrations'),
        ], 'shopping-migrations');

        $this->publishes([
            __DIR__.'/database/seeders/' => database_path('seeders/shopping/'),
        ], 'shopping-seeders');

        // Register event service provider
        $this->app->register(EventServiceProvider::class);

        // Register policies
        $this->registerPolicies();

        // Register facade aliases
        // $this->registerFacadeAliases();
    }

    /**
     * Load routes conditionally based on configuration.
     */
    protected function loadRoutesConditionally(): void
    {
        $config = config('shopping.routes', []);

        // Load API routes if enabled
        if ($config['api'] ?? false) {
            $this->loadRoutesFrom(__DIR__.'/routes/api.php');
        }

        // Load web routes if enabled
        if ($config['web'] ?? false) {
            $this->loadRoutesFrom(__DIR__.'/routes/web.php');
        }
    }

    /**
     * Register the application's policies.
     */
    protected function registerPolicies(): void
    {
        Gate::policy(Team::class, TeamPolicy::class);
        Gate::policy(Address::class, AddressPolicy::class);
        Gate::policy(Customer::class, CustomerPolicy::class);
        Gate::policy(Employee::class, EmployeePolicy::class);
        Gate::policy(User::class, UserPolicy::class);
        // Gate::policy(\Fereydooni\Shopping\app\Models\CustomerSegment::class, \Fereydooni\Shopping\app\Policies\CustomerSegmentPolicy::class);
        Gate::policy(CustomerPreference::class, CustomerPreferencePolicy::class);
        Gate::policy(CustomerWishlist::class, CustomerWishlistPolicy::class);
        Gate::policy(CustomerNote::class, CustomerNotePolicy::class);
        Gate::policy(CustomerCommunication::class, CustomerCommunicationPolicy::class);
        Gate::policy(LoyaltyTransaction::class, LoyaltyTransactionPolicy::class);
        Gate::policy(Category::class, CategoryPolicy::class);
        Gate::policy(Brand::class, BrandPolicy::class);
        Gate::policy(Order::class, OrderPolicy::class);
        Gate::policy(OrderItem::class, OrderItemPolicy::class);
        Gate::policy(OrderStatusHistory::class, OrderStatusHistoryPolicy::class);
        Gate::policy(ProductAttribute::class, ProductAttributePolicy::class);
        Gate::policy(ProductAttributeValue::class, ProductAttributeValuePolicy::class);
        Gate::policy(ProductDiscount::class, ProductDiscountPolicy::class);
        Gate::policy(Product::class, ProductPolicy::class);
        Gate::policy(ProductMeta::class, ProductMetaPolicy::class);
        Gate::policy(ProductReview::class, ProductReviewPolicy::class);
        Gate::policy(ProductTag::class, ProductTagPolicy::class);
        Gate::policy(ProductVariant::class, ProductVariantPolicy::class);
        // Gate::policy(\Fereydooni\Shopping\app\Models\Shipment::class, \Fereydooni\Shopping\app\Policies\ShipmentPolicy::class);
        Gate::policy(ShipmentItem::class, ShipmentItemPolicy::class);
        Gate::policy(Transaction::class, TransactionPolicy::class);
        Gate::policy(Subscription::class, SubscriptionPolicy::class);
        Gate::policy(UserSubscription::class, UserSubscriptionPolicy::class);
        Gate::policy(Department::class, DepartmentPolicy::class);
        Gate::policy(EmployeePosition::class, EmployeePositionPolicy::class);
        Gate::policy(ProviderLocation::class, ProviderLocationPolicy::class);
        Gate::policy(ProviderInsurance::class, ProviderInsurancePolicy::class);
        // Gate::policy(\Fereydooni\Shopping\app\Models\ProviderCommunication::class, \Fereydooni\Shopping\app\Policies\ProviderCommunicationPolicy::class);

        // Register permissions
        $this->registerPermissions();
    }

    /**
     * Register the application's permissions.
     */
    protected function registerPermissions(): void
    {
        // Register EmployeePosition permissions
        // \Fereydooni\Shopping\app\Permissions\EmployeePositionPermissions::register();
    }

    /**
     * Register the application's facade aliases.
     */
    protected function registerFacadeAliases(): void
    {
        // Register EmployeePosition facade alias
        $this->app->alias('shopping.employee-position.facade', app\Facades\EmployeePosition::class);

        // Register ProviderLocation facade alias
        $this->app->alias('shopping.provider-location.facade', app\Facades\ProviderLocation::class);

        // Register ProviderInsurance facade alias
        //        $this->app->alias('shopping.provider-insurance.facade', \Fereydooni\Shopping\app\Facades\ProviderInsurance::class);
        //
        //        // Register ProviderCommunication facade alias
        //        $this->app->alias('shopping.provider-communication.facade', \Fereydooni\Shopping\app\Facades\ProviderCommunication::class);
    }
}
