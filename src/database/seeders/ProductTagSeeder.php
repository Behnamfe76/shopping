<?php

namespace Fereydooni\Shopping\database\seeders;

use Illuminate\Database\Seeder;
use Fereydooni\Shopping\app\Models\ProductTag;

class ProductTagSeeder extends Seeder
{
    public function run(): void
    {
        $tags = [
            [
                'name' => 'New Arrival',
                'slug' => 'new-arrival',
                'description' => 'Latest products just arrived',
                'color' => '#28a745',
                'icon' => 'star',
                'is_active' => true,
                'is_featured' => true,
                'sort_order' => 1,
            ],
            [
                'name' => 'Best Seller',
                'slug' => 'best-seller',
                'description' => 'Our most popular products',
                'color' => '#ffc107',
                'icon' => 'fire',
                'is_active' => true,
                'is_featured' => true,
                'sort_order' => 2,
            ],
            [
                'name' => 'Sale',
                'slug' => 'sale',
                'description' => 'Products on special offer',
                'color' => '#dc3545',
                'icon' => 'percent',
                'is_active' => true,
                'is_featured' => false,
                'sort_order' => 3,
            ],
            [
                'name' => 'Featured',
                'slug' => 'featured',
                'description' => 'Handpicked featured products',
                'color' => '#007bff',
                'icon' => 'heart',
                'is_active' => true,
                'is_featured' => true,
                'sort_order' => 4,
            ],
            [
                'name' => 'Trending',
                'slug' => 'trending',
                'description' => 'Currently trending products',
                'color' => '#fd7e14',
                'icon' => 'trending-up',
                'is_active' => true,
                'is_featured' => false,
                'sort_order' => 5,
            ],
            [
                'name' => 'Limited Edition',
                'slug' => 'limited-edition',
                'description' => 'Exclusive limited edition items',
                'color' => '#6f42c1',
                'icon' => 'crown',
                'is_active' => true,
                'is_featured' => false,
                'sort_order' => 6,
            ],
            [
                'name' => 'Premium',
                'slug' => 'premium',
                'description' => 'High-end premium products',
                'color' => '#e83e8c',
                'icon' => 'gem',
                'is_active' => true,
                'is_featured' => false,
                'sort_order' => 7,
            ],
            [
                'name' => 'Budget Friendly',
                'slug' => 'budget-friendly',
                'description' => 'Affordable options for everyone',
                'color' => '#20c997',
                'icon' => 'dollar-sign',
                'is_active' => true,
                'is_featured' => false,
                'sort_order' => 8,
            ],
            [
                'name' => 'Eco Friendly',
                'slug' => 'eco-friendly',
                'description' => 'Environmentally conscious products',
                'color' => '#198754',
                'icon' => 'leaf',
                'is_active' => true,
                'is_featured' => false,
                'sort_order' => 9,
            ],
            [
                'name' => 'Wireless',
                'slug' => 'wireless',
                'description' => 'Wireless technology products',
                'color' => '#6c757d',
                'icon' => 'wifi',
                'is_active' => true,
                'is_featured' => false,
                'sort_order' => 10,
            ],
            [
                'name' => 'Waterproof',
                'slug' => 'waterproof',
                'description' => 'Water-resistant products',
                'color' => '#0dcaf0',
                'icon' => 'droplet',
                'is_active' => true,
                'is_featured' => false,
                'sort_order' => 11,
            ],
            [
                'name' => 'Lightweight',
                'slug' => 'lightweight',
                'description' => 'Light and portable products',
                'color' => '#adb5bd',
                'icon' => 'feather',
                'is_active' => true,
                'is_featured' => false,
                'sort_order' => 12,
            ],
            [
                'name' => 'Durable',
                'slug' => 'durable',
                'description' => 'Built to last products',
                'color' => '#495057',
                'icon' => 'shield',
                'is_active' => true,
                'is_featured' => false,
                'sort_order' => 13,
            ],
            [
                'name' => 'Fast Charging',
                'slug' => 'fast-charging',
                'description' => 'Quick charging technology',
                'color' => '#fd7e14',
                'icon' => 'zap',
                'is_active' => true,
                'is_featured' => false,
                'sort_order' => 14,
            ],
            [
                'name' => 'High Performance',
                'slug' => 'high-performance',
                'description' => 'Top performance products',
                'color' => '#dc3545',
                'icon' => 'activity',
                'is_active' => true,
                'is_featured' => false,
                'sort_order' => 15,
            ],
        ];

        foreach ($tags as $tag) {
            ProductTag::create($tag);
        }
    }
}
