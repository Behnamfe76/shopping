<?php

namespace Fereydooni\Shopping\database\seeders;

use Illuminate\Database\Seeder;

class ShoppingDatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            CategoryPermissionSeeder::class,
            AddressPermissionSeeder::class,
            OrderPermissionSeeder::class,
            OrderItemPermissionSeeder::class,
            OrderStatusHistoryPermissionSeeder::class,
            ProductAttributePermissionSeeder::class,
            ProductAttributeValuePermissionSeeder::class,
            ProductDiscountPermissionSeeder::class,
            ProductPermissionSeeder::class,
            ProductMetaPermissionSeeder::class,
            ProductReviewPermissionSeeder::class,
            ProductTagPermissionSeeder::class,
            ProductVariantPermissionSeeder::class,
            CategorySeeder::class,
            BrandSeeder::class,
            ProductAttributeSeeder::class,
            ProductTagSeeder::class,
        ]);
    }
}
