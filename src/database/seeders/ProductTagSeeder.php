<?php

namespace Fereydooni\Shopping\database\seeders;

use Illuminate\Database\Seeder;
use Fereydooni\Shopping\app\Models\ProductTag;

class ProductTagSeeder extends Seeder
{
    public function run(): void
    {
        $tags = [
            ['name' => 'New Arrival', 'slug' => 'new-arrival'],
            ['name' => 'Best Seller', 'slug' => 'best-seller'],
            ['name' => 'Sale', 'slug' => 'sale'],
            ['name' => 'Featured', 'slug' => 'featured'],
            ['name' => 'Trending', 'slug' => 'trending'],
            ['name' => 'Limited Edition', 'slug' => 'limited-edition'],
            ['name' => 'Premium', 'slug' => 'premium'],
            ['name' => 'Budget Friendly', 'slug' => 'budget-friendly'],
            ['name' => 'Eco Friendly', 'slug' => 'eco-friendly'],
            ['name' => 'Wireless', 'slug' => 'wireless'],
            ['name' => 'Waterproof', 'slug' => 'waterproof'],
            ['name' => 'Lightweight', 'slug' => 'lightweight'],
            ['name' => 'Durable', 'slug' => 'durable'],
            ['name' => 'Fast Charging', 'slug' => 'fast-charging'],
            ['name' => 'High Performance', 'slug' => 'high-performance'],
        ];

        foreach ($tags as $tag) {
            ProductTag::create($tag);
        }
    }
}
