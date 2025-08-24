<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('loyalty_transactions', function (Blueprint $table) {
            // Add unique constraint for certain combinations
            $table->unique(
                ['customer_id', 'reference_type', 'reference_id', 'transaction_type'],
                'unique_customer_reference_transaction'
            );
        });

        // Add check constraints (raw SQL, since Laravel doesn't support check() on MySQL/Postgres)
        DB::statement('ALTER TABLE loyalty_transactions ADD CONSTRAINT chk_points CHECK (points > 0)');
        DB::statement('ALTER TABLE loyalty_transactions ADD CONSTRAINT chk_points_value CHECK (points_value >= 0)');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('loyalty_transactions', function (Blueprint $table) {
            // Drop unique constraint
            $table->dropUnique('unique_customer_reference_transaction');
        });

        // Drop check constraints
        DB::statement('ALTER TABLE loyalty_transactions DROP CONSTRAINT chk_points');
        DB::statement('ALTER TABLE loyalty_transactions DROP CONSTRAINT chk_points_value');
    }
};

