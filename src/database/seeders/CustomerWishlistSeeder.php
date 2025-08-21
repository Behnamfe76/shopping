<?php

namespace Fereydooni\Shopping\database\seeders;

use Fereydooni\Shopping\app\Models\CustomerWishlist;
use Fereydooni\Shopping\app\Models\Customer;
use Fereydooni\Shopping\app\Models\Product;
use Fereydooni\Shopping\app\Enums\WishlistPriority;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CustomerWishlistSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get existing customers and products
        $customers = Customer::all();
        $products = Product::all();

        if ($customers->isEmpty() || $products->isEmpty()) {
            $this->command->warn('No customers or products found. Skipping wishlist seeding.');
            return;
        }

        $wishlistData = [];

        // Create wishlist items for each customer
        foreach ($customers as $customer) {
            // Each customer gets 1-5 random products in their wishlist
            $customerProducts = $products->random(rand(1, 5));
            
            foreach ($customerProducts as $product) {
                $wishlistData[] = [
                    'customer_id' => $customer->id,
                    'product_id' => $product->id,
                    'added_at' => now()->subDays(rand(1, 30)),
                    'notes' => $this->getRandomNotes(),
                    'priority' => WishlistPriority::cases()[array_rand(WishlistPriority::cases())]->value,
                    'is_public' => rand(0, 1),
                    'is_notified' => rand(0, 1),
                    'notification_sent_at' => rand(0, 1) ? now()->subDays(rand(1, 7)) : null,
                    'price_when_added' => $product->price ?? rand(1000, 50000) / 100,
                    'current_price' => $product->price ?? rand(1000, 50000) / 100,
                    'price_drop_notification' => rand(0, 1),
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }

        // Insert wishlist data in chunks
        foreach (array_chunk($wishlistData, 100) as $chunk) {
            DB::table('customer_wishlists')->insert($chunk);
        }

        $this->command->info('Customer wishlist data seeded successfully.');
    }

    /**
     * Get random notes for wishlist items
     */
    private function getRandomNotes(): ?string
    {
        $notes = [
            'Birthday gift idea',
            'For the new apartment',
            'Holiday shopping',
            'Treat myself',
            'Gift for mom',
            'Office decoration',
            'Kitchen upgrade',
            'Bedroom makeover',
            'Garden project',
            'Tech upgrade',
            'Fashion statement',
            'Home improvement',
            'Personal care',
            'Entertainment',
            'Fitness goals',
        ];

        return rand(0, 1) ? $notes[array_rand($notes)] : null;
    }
}
