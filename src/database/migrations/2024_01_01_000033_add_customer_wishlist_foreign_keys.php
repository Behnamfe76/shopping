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
        Schema::table('customer_wishlists', function (Blueprint $table) {
            // Foreign key for customer relationship
            $table->foreign('customer_id', 'fk_customer_wishlists_customer_id')
                  ->references('id')
                  ->on('customers')
                  ->onDelete('cascade')
                  ->onUpdate('cascade');
            
            // Foreign key for product relationship
            $table->foreign('product_id', 'fk_customer_wishlists_product_id')
                  ->references('id')
                  ->on('products')
                  ->onDelete('cascade')
                  ->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customer_wishlists', function (Blueprint $table) {
            $table->dropForeign('fk_customer_wishlists_customer_id');
            $table->dropForeign('fk_customer_wishlists_product_id');
        });
    }
};
