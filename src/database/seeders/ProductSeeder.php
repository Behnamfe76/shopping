<?php

namespace Fereydooni\Shopping\database\seeders;

use Fereydooni\Shopping\app\Enums\ProductStatus;
use Fereydooni\Shopping\app\Enums\ProductType;
use Fereydooni\Shopping\app\Models\Brand;
use Fereydooni\Shopping\app\Models\Category;
use Fereydooni\Shopping\app\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        // Get some categories and brands to associate with products
        $categories = Category::limit(10)->get();
        $brands = Brand::limit(10)->get();

        if ($categories->isEmpty() || $brands->isEmpty()) {
            $this->command->warn('Please make sure CategorySeeder and BrandSeeder have been run first.');

            return;
        }

        $products = [
            [
                'sku' => 'IP14-PRO-256-SG',
                'title' => 'iPhone 14 Pro 256GB Space Gray',
                'slug' => 'iphone-14-pro-256gb-space-gray',
                'description' => 'The most Pro iPhone yet. Featuring the Dynamic Island, Always-On display, and the most advanced camera system ever on iPhone.',
                'weight' => 206.00,
                'dimensions' => '147.5 × 71.5 × 7.85 mm',
                'status' => ProductStatus::PUBLISHED,
                'product_type' => ProductType::PHYSICAL,
            ],
            [
                'sku' => 'SM-S23U-512-BLK',
                'title' => 'Samsung Galaxy S23 Ultra 512GB Black',
                'slug' => 'samsung-galaxy-s23-ultra-512gb-black',
                'description' => 'The ultimate smartphone experience with S Pen, advanced camera system, and all-day battery life.',
                'weight' => 234.00,
                'dimensions' => '163.4 × 78.1 × 8.9 mm',
                'status' => ProductStatus::PUBLISHED,
                'product_type' => ProductType::PHYSICAL,
            ],
            [
                'sku' => 'MBA-M2-256-SLV',
                'title' => 'MacBook Air M2 256GB Silver',
                'slug' => 'macbook-air-m2-256gb-silver',
                'description' => 'Supercharged by M2 chip. Incredibly thin and light design with all-day battery life.',
                'weight' => 1240.00,
                'dimensions' => '304.1 × 215 × 11.3 mm',
                'status' => ProductStatus::PUBLISHED,
                'product_type' => ProductType::PHYSICAL,
            ],
            [
                'sku' => 'NKE-AIR-MAX-90-WHT',
                'title' => 'Nike Air Max 90 White',
                'slug' => 'nike-air-max-90-white',
                'description' => 'Classic Nike Air Max 90 with iconic visible Air cushioning and timeless design.',
                'weight' => 450.00,
                'dimensions' => '320 × 120 × 110 mm',
                'status' => ProductStatus::PUBLISHED,
                'product_type' => ProductType::PHYSICAL,
            ],
            [
                'sku' => 'SPFY-PREM-1M',
                'title' => 'Spotify Premium Subscription',
                'slug' => 'spotify-premium-subscription',
                'description' => 'Enjoy ad-free music streaming with offline downloads and high-quality audio.',
                'weight' => 0.00,
                'dimensions' => null,
                'status' => ProductStatus::PUBLISHED,
                'product_type' => ProductType::SUBSCRIPTION,
            ],
            [
                'sku' => 'ADOBE-CC-1Y',
                'title' => 'Adobe Creative Cloud All Apps',
                'slug' => 'adobe-creative-cloud-all-apps',
                'description' => 'Complete creative suite including Photoshop, Illustrator, Premiere Pro, and more.',
                'weight' => 0.00,
                'dimensions' => null,
                'status' => ProductStatus::PUBLISHED,
                'product_type' => ProductType::DIGITAL,
            ],
            [
                'sku' => 'LVS-JEANS-BLU-32',
                'title' => 'Levi\'s 501 Original Jeans Blue 32W',
                'slug' => 'levis-501-original-jeans-blue-32w',
                'description' => 'The original blue jean since 1873. Classic straight fit with button fly.',
                'weight' => 680.00,
                'dimensions' => '810 × 320 × 25 mm',
                'status' => ProductStatus::PUBLISHED,
                'product_type' => ProductType::PHYSICAL,
            ],
            [
                'sku' => 'SONY-WH1000XM5-BLK',
                'title' => 'Sony WH-1000XM5 Wireless Headphones Black',
                'slug' => 'sony-wh-1000xm5-wireless-headphones-black',
                'description' => 'Industry-leading noise canceling with premium sound quality and smart features.',
                'weight' => 250.00,
                'dimensions' => '270 × 203 × 95 mm',
                'status' => ProductStatus::PUBLISHED,
                'product_type' => ProductType::PHYSICAL,
            ],
            [
                'sku' => 'KINDLE-UNL-1M',
                'title' => 'Kindle Unlimited Subscription',
                'slug' => 'kindle-unlimited-subscription',
                'description' => 'Access to over 2 million titles, thousands of audiobooks, and select magazine subscriptions.',
                'weight' => 0.00,
                'dimensions' => null,
                'status' => ProductStatus::PUBLISHED,
                'product_type' => ProductType::SUBSCRIPTION,
            ],
            [
                'sku' => 'NFLX-PREM-1M',
                'title' => 'Netflix Premium Plan',
                'slug' => 'netflix-premium-plan',
                'description' => 'Watch unlimited movies and TV shows in Ultra HD on up to 4 screens at the same time.',
                'weight' => 0.00,
                'dimensions' => null,
                'status' => ProductStatus::PUBLISHED,
                'product_type' => ProductType::DIGITAL,
            ],
            [
                'sku' => 'ADIDAS-UB22-BLK-42',
                'title' => 'Adidas Ultraboost 22 Black Size 42',
                'slug' => 'adidas-ultraboost-22-black-size-42',
                'description' => 'Premium running shoes with responsive Boost cushioning and Primeknit upper.',
                'weight' => 320.00,
                'dimensions' => '290 × 110 × 95 mm',
                'status' => ProductStatus::PUBLISHED,
                'product_type' => ProductType::PHYSICAL,
            ],
            [
                'sku' => 'DRAFT-PROD-001',
                'title' => 'Draft Product Example',
                'slug' => 'draft-product-example',
                'description' => 'This is a draft product that is not yet ready for publication.',
                'weight' => 100.00,
                'dimensions' => '100 × 100 × 100 mm',
                'status' => ProductStatus::DRAFT,
                'product_type' => ProductType::PHYSICAL,
            ],
            [
                'sku' => 'ARCH-PROD-001',
                'title' => 'Archived Product Example',
                'slug' => 'archived-product-example',
                'description' => 'This product has been archived and is no longer available.',
                'weight' => 150.00,
                'dimensions' => '150 × 150 × 150 mm',
                'status' => ProductStatus::ARCHIVED,
                'product_type' => ProductType::PHYSICAL,
            ],
        ];

        foreach ($products as $productData) {
            // Assign random category and brand
            $productData['category_id'] = $categories->random()->id;
            $productData['brand_id'] = $brands->random()->id;

            Product::create($productData);
        }

        $this->command->info('Products seeded successfully!');
    }
}
