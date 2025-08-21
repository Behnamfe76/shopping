<?php

namespace Fereydooni\Shopping\database\seeders;

use Fereydooni\Shopping\app\Enums\WishlistPriority;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class WishlistPrioritySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $priorities = [
            [
                'name' => 'Low',
                'value' => WishlistPriority::LOW->value,
                'description' => 'Low priority items - nice to have',
                'color' => '#6c757d',
                'sort_order' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Medium',
                'value' => WishlistPriority::MEDIUM->value,
                'description' => 'Medium priority items - would like to have',
                'color' => '#ffc107',
                'sort_order' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'High',
                'value' => WishlistPriority::HIGH->value,
                'description' => 'High priority items - really want',
                'color' => '#fd7e14',
                'sort_order' => 3,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Urgent',
                'value' => WishlistPriority::URGENT->value,
                'description' => 'Urgent priority items - need immediately',
                'color' => '#dc3545',
                'sort_order' => 4,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        // Check if wishlist_priorities table exists
        if (DB::getSchemaBuilder()->hasTable('wishlist_priorities')) {
            DB::table('wishlist_priorities')->insert($priorities);
            $this->command->info('Wishlist priority data seeded successfully.');
        } else {
            $this->command->warn('wishlist_priorities table does not exist. Skipping priority seeding.');
        }
    }
}
