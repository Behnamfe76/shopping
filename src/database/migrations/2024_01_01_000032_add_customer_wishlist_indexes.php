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
            // Index for customer queries
            $table->index('customer_id', 'idx_customer_wishlists_customer_id');
            
            // Index for product queries
            $table->index('product_id', 'idx_customer_wishlists_product_id');
            
            // Composite index for customer-product lookups
            $table->unique(['customer_id', 'product_id'], 'idx_customer_wishlists_customer_product_unique');
            
            // Index for priority queries
            $table->index('priority', 'idx_customer_wishlists_priority');
            
            // Index for public/private queries
            $table->index('is_public', 'idx_customer_wishlists_is_public');
            
            // Index for notification queries
            $table->index('is_notified', 'idx_customer_wishlists_is_notified');
            
            // Index for date range queries
            $table->index('added_at', 'idx_customer_wishlists_added_at');
            
            // Index for price tracking
            $table->index('price_when_added', 'idx_customer_wishlists_price_when_added');
            $table->index('current_price', 'idx_customer_wishlists_current_price');
            
            // Index for notification sent date
            $table->index('notification_sent_at', 'idx_customer_wishlists_notification_sent_at');
            
            // Index for price drop notification
            $table->index('price_drop_notification', 'idx_customer_wishlists_price_drop_notification');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customer_wishlists', function (Blueprint $table) {
            $table->dropIndex('idx_customer_wishlists_customer_id');
            $table->dropIndex('idx_customer_wishlists_product_id');
            $table->dropIndex('idx_customer_wishlists_customer_product_unique');
            $table->dropIndex('idx_customer_wishlists_priority');
            $table->dropIndex('idx_customer_wishlists_is_public');
            $table->dropIndex('idx_customer_wishlists_is_notified');
            $table->dropIndex('idx_customer_wishlists_added_at');
            $table->dropIndex('idx_customer_wishlists_price_when_added');
            $table->dropIndex('idx_customer_wishlists_current_price');
            $table->dropIndex('idx_customer_wishlists_notification_sent_at');
            $table->dropIndex('idx_customer_wishlists_price_drop_notification');
        });
    }
};
