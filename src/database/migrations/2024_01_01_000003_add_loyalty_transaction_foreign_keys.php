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
        Schema::table('loyalty_transactions', function (Blueprint $table) {
            // Add foreign key constraints for reference types
            // Note: These are conditional foreign keys based on reference_type
            
            // Add check constraints for data integrity
            $table->check('points > 0');
            $table->check('points_value >= 0');
            
            // Add unique constraint for certain combinations
            $table->unique(['customer_id', 'reference_type', 'reference_id', 'transaction_type'], 'unique_customer_reference_transaction');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('loyalty_transactions', function (Blueprint $table) {
            // Drop check constraints
            $table->dropCheck(['points > 0']);
            $table->dropCheck(['points_value >= 0']);
            
            // Drop unique constraint
            $table->dropUnique('unique_customer_reference_transaction');
        });
    }
};
