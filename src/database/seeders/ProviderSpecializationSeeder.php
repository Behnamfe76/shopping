<?php

namespace Fereydooni\Shopping\database\seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

class ProviderSpecializationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $specializations = [
            [
                'name' => 'Electronics',
                'description' => 'Electronic devices and components',
                'category' => 'Technology',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Clothing & Apparel',
                'description' => 'Fashion and clothing items',
                'category' => 'Fashion',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Home & Garden',
                'description' => 'Home improvement and garden supplies',
                'category' => 'Home',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Automotive',
                'description' => 'Automotive parts and accessories',
                'category' => 'Transportation',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Health & Beauty',
                'description' => 'Health and beauty products',
                'category' => 'Health',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Sports & Outdoors',
                'description' => 'Sports equipment and outdoor gear',
                'category' => 'Sports',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Books & Media',
                'description' => 'Books, movies, and media content',
                'category' => 'Entertainment',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Food & Beverage',
                'description' => 'Food and beverage products',
                'category' => 'Food',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        // Check if provider_specializations table exists, if not create it
        if (!Schema::hasTable('provider_specializations')) {
            Schema::create('provider_specializations', function (Blueprint $table) {
                $table->id();
                $table->string('name')->unique();
                $table->text('description')->nullable();
                $table->string('category')->nullable();
                $table->timestamps();
            });
        }

        // Insert provider specializations
        foreach ($specializations as $specialization) {
            DB::table('provider_specializations')->insertOrIgnore($specialization);
        }
    }
}
