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
            TransactionPermissionSeeder::class,
            ShipmentPermissionSeeder::class,
            ShipmentItemPermissionSeeder::class,
            ProductAttributePermissionSeeder::class,
            ProductAttributeValuePermissionSeeder::class,
            ProductDiscountPermissionSeeder::class,
            ProductPermissionSeeder::class,
            ProductMetaPermissionSeeder::class,
            ProductReviewPermissionSeeder::class,
            ProductTagPermissionSeeder::class,
            ProductVariantPermissionSeeder::class,
            UserSubscriptionPermissionSeeder::class,
            CustomerPermissionSeeder::class,
            CustomerPreferencePermissionSeeder::class,
            CustomerWishlistPermissionSeeder::class,
            CustomerCommunicationPermissionSeeder::class,
            CategorySeeder::class,
            BrandSeeder::class,
            ProductAttributeSeeder::class,
            ProductTagSeeder::class,
            ProductSeeder::class,
            CustomerSeeder::class,
            DefaultCustomerPreferencesSeeder::class,
            CustomerPreferenceSeeder::class,
            CustomerWishlistSeeder::class,
            WishlistPrioritySeeder::class,
            CustomerCommunicationSeeder::class,
            EmployeePermissionSeeder::class,
            EmployeeSeeder::class,

            // Provider seeders
            ProviderPermissionSeeder::class,
            ProviderTypeSeeder::class,
            ProviderSpecializationSeeder::class,
            ProviderSeeder::class,
            ProviderNoteSeeder::class,
            ProviderInvoiceSeeder::class,
        ]);
    }
}
