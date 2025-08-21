<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('customer_preferences', function (Blueprint $table) {
            // Add additional indexes for better performance
            $table->index(['created_at'], 'customer_preference_created_at_index');
            $table->index(['updated_at'], 'customer_preference_updated_at_index');
            $table->index(['customer_id', 'preference_key', 'is_active'], 'customer_preference_composite_index');
            $table->index(['preference_key', 'preference_type'], 'preference_key_type_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customer_preferences', function (Blueprint $table) {
            $table->dropIndex('customer_preference_created_at_index');
            $table->dropIndex('customer_preference_updated_at_index');
            $table->dropIndex('customer_preference_composite_index');
            $table->dropIndex('preference_key_type_index');
        });
    }
};
