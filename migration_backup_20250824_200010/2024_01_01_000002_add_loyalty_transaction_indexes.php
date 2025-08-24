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
            // Additional composite indexes for better query performance
            $table->index(['customer_id', 'transaction_type', 'status']);
            $table->index(['customer_id', 'created_at', 'status']);
            $table->index(['transaction_type', 'status', 'created_at']);
            $table->index(['points', 'created_at']);
            $table->index(['points_value', 'created_at']);
            
            // Full-text search indexes
            $table->fullText(['description', 'reason']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('loyalty_transactions', function (Blueprint $table) {
            // Drop additional indexes
            $table->dropIndex(['customer_id', 'transaction_type', 'status']);
            $table->dropIndex(['customer_id', 'created_at', 'status']);
            $table->dropIndex(['transaction_type', 'status', 'created_at']);
            $table->dropIndex(['points', 'created_at']);
            $table->dropIndex(['points_value', 'created_at']);
            
            // Drop full-text search indexes
            $table->dropFullText(['description', 'reason']);
        });
    }
};
