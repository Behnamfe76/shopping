# Laravel Shopping Package

A comprehensive Laravel package for e-commerce functionality with complete shopping cart and order management system.

## Features

- 🛍️ **Complete E-commerce Solution** - Product catalog, orders, payments, shipping
- 🔧 **Modern Laravel Architecture** - Repository pattern, service layer, DTOs
- 🚀 **1,000+ API Endpoints** - Comprehensive REST API
- 🎯 **Type Safety** - PHP 8.1+ features and Data Transfer Objects
- 🔐 **Authorization Ready** - Built-in policies and permissions
- 📊 **Analytics & Reporting** - Sales, inventory, and customer insights
- 🔄 **Event-Driven** - Extensible through events and listeners

## Installation

```bash
composer require fereydooni/shopping
```

## Configuration

Publish the configuration file:

```bash
php artisan vendor:publish --tag=shopping-config
```

## Route Installation

The package provides flexible route installation options. By default, only API routes are loaded automatically. You can install routes manually using the provided commands.

### Install Routes

Install API routes only:
```bash
php artisan shopping:install --route=api
```

Install web routes only:
```bash
php artisan shopping:install --route=web
```

Install both API and web routes:
```bash
php artisan shopping:install --route=api --route=web
```

Install with custom prefix:
```bash
php artisan shopping:install --route=api --prefix=store
```

Force overwrite existing routes:
```bash
php artisan shopping:install --route=api --force
```

Interactive installation (will prompt for options):
```bash
php artisan shopping:install
```

### Uninstall Routes

Uninstall API routes:
```bash
php artisan shopping:uninstall --route=api
```

Uninstall web routes:
```bash
php artisan shopping:uninstall --route=web
```

Uninstall both:
```bash
php artisan shopping:uninstall --route=api --route=web
```

Force uninstall without confirmation:
```bash
php artisan shopping:uninstall --route=api --force
```

### List Routes

List all shopping routes:
```bash
php artisan shopping:routes
```

Filter routes by type:
```bash
php artisan shopping:routes --type=api
php artisan shopping:routes --type=web
```

Filter routes by group:
```bash
php artisan shopping:routes --group=products
php artisan shopping:routes --group=orders
```

Filter routes by HTTP method:
```bash
php artisan shopping:routes --method=GET
php artisan shopping:routes --method=POST
```

Output in different formats:
```bash
php artisan shopping:routes --format=json
php artisan shopping:routes --format=csv
```

## Environment Configuration

Add these variables to your `.env` file:

```env
# Route Configuration
SHOPPING_LOAD_API_ROUTES=true
SHOPPING_LOAD_WEB_ROUTES=false
SHOPPING_ROUTE_PREFIX=shopping

# User Model
SHOPPING_USER_MODEL=App\Models\User

# Table Prefix
SHOPPING_TABLE_PREFIX=shp_

# Currency
SHOPPING_CURRENCY=USD

# Tax Rate
SHOPPING_TAX_RATE=0

# Review Settings
SHOPPING_REVIEWS_REQUIRE_APPROVAL=true
SHOPPING_REVIEWS_ALLOW_ANONYMOUS=false
SHOPPING_REVIEWS_MIN_RATING=1
SHOPPING_REVIEWS_MAX_RATING=5

# Subscription Settings
SHOPPING_DEFAULT_TRIAL_DAYS=7
SHOPPING_AUTO_RENEWAL=true
SHOPPING_GRACE_PERIOD_DAYS=3

# Pagination
SHOPPING_PRODUCTS_PER_PAGE=12
SHOPPING_REVIEWS_PER_PAGE=10
```

## Usage

### Service Access

Via dependency injection:
```php
$productService = app('shopping.product');
$orderService = app('shopping.order');
```

Via facade:
```php
use Fereydooni\Shopping\app\Facades\ShoppingProduct;
use Fereydooni\Shopping\app\Facades\ShoppingOrder;

$products = ShoppingProduct::all();
$orders = ShoppingOrder::all();
```

### API Endpoints

After installing routes, you'll have access to:

#### Products
- `GET /api/v1/products` - List products
- `GET /api/v1/products/{id}` - Get product details
- `POST /api/v1/products` - Create product
- `PUT /api/v1/products/{id}` - Update product
- `DELETE /api/v1/products/{id}` - Delete product

#### Orders
- `GET /api/v1/orders` - List orders
- `GET /api/v1/orders/{id}` - Get order details
- `POST /api/v1/orders` - Create order
- `PUT /api/v1/orders/{id}` - Update order

#### Categories
- `GET /api/v1/categories` - List categories
- `GET /api/v1/categories/{slug}` - Get category details
- `POST /api/v1/categories` - Create category

And many more endpoints for all e-commerce functionality.

### Web Routes

After installing web routes, you'll have access to:

- `/shopping/products` - Product catalog
- `/shopping/cart` - Shopping cart
- `/shopping/orders` - Order management
- `/shopping/dashboard` - Customer dashboard

## Models

The package includes 20+ Eloquent models:

- `Product` - Core product entity
- `Order` - Order management
- `Category` - Product categorization
- `Brand` - Product branding
- `Address` - Shipping/billing addresses
- `Shipment` - Shipping tracking
- `Transaction` - Payment processing
- `Subscription` - Recurring billing
- And many more...

## Dependencies

- **Laravel 10/11** - Framework compatibility
- **Spatie Laravel Media Library** - Media management
- **Spatie Laravel Data** - DTOs and data objects
- **Spatie Laravel Permission** - Role-based access control
- **PHP 8.1+** - Modern PHP features

## License

This package is open-sourced software licensed under the [MIT license](LICENSE).

## Support

For support, please open an issue on GitHub or contact the maintainer.
