<?php

namespace Fereydooni\Shopping\database\seeders;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ProviderTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $types = [
            [
                'name' => 'Manufacturer',
                'description' => 'Companies that produce goods',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Distributor',
                'description' => 'Companies that distribute goods from manufacturers',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Wholesaler',
                'description' => 'Companies that sell goods in bulk to retailers',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Retailer',
                'description' => 'Companies that sell goods directly to consumers',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Service Provider',
                'description' => 'Companies that provide services rather than goods',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Logistics',
                'description' => 'Companies that provide transportation and logistics services',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        // Check if provider_types table exists, if not create it
        if (! Schema::hasTable('provider_types')) {
            Schema::create('provider_types', function (Blueprint $table) {
                $table->id();
                $table->string('name')->unique();
                $table->text('description')->nullable();
                $table->timestamps();
            });
        }

        // Insert provider types
        foreach ($types as $type) {
            DB::table('provider_types')->insertOrIgnore($type);
        }
    }
}
