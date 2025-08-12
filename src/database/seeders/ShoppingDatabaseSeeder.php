<?php

namespace Fereydooni\Shopping\database\seeders;

use Illuminate\Database\Seeder;

class ShoppingDatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            CategorySeeder::class,
            BrandSeeder::class,
            ProductAttributeSeeder::class,
            ProductTagSeeder::class,
        ]);
    }
}
