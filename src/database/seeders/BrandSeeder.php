<?php

namespace Fereydooni\Shopping\database\seeders;

use Illuminate\Database\Seeder;
use Fereydooni\Shopping\app\Models\Brand;

class BrandSeeder extends Seeder
{
    public function run(): void
    {
        $brands = [
            [
                'name' => 'Apple',
                'slug' => 'apple',
            ],
            [
                'name' => 'Samsung',
                'slug' => 'samsung',
            ],
            [
                'name' => 'Nike',
                'slug' => 'nike',
            ],
            [
                'name' => 'Adidas',
                'slug' => 'adidas',
            ],
            [
                'name' => 'IKEA',
                'slug' => 'ikea',
            ],
            [
                'name' => 'Sony',
                'slug' => 'sony',
            ],
            [
                'name' => 'LG',
                'slug' => 'lg',
            ],
            [
                'name' => 'Dell',
                'slug' => 'dell',
            ],
            [
                'name' => 'HP',
                'slug' => 'hp',
            ],
            [
                'name' => 'Microsoft',
                'slug' => 'microsoft',
            ],
        ];

        foreach ($brands as $brand) {
            Brand::create($brand);
        }
    }
}
