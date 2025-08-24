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
        Schema::create('customer_wishlists', function (Blueprint $table) {
            $table->id();

            // Customer and Product relationships
            $table->unsignedBigInteger('customer_id');
            $table->unsignedBigInteger('product_id');

            // Wishlist item details
            $table->text('notes')->nullable();
            $table->enum('priority', ['low', 'medium', 'high', 'urgent'])->default('medium');

            // Privacy and visibility
            $table->boolean('is_public')->default(false);
            $table->boolean('is_notified')->default(false);
            $table->timestamp('notification_sent_at')->nullable();

            // Price tracking
            $table->decimal('price_when_added', 10, 2)->nullable();
            $table->decimal('current_price', 10, 2)->nullable();
            $table->boolean('price_drop_notification')->default(true);

            // Timestamps
            $table->timestamp('added_at')->useCurrent();
            $table->timestamps();

            // Foreign key constraints
            $table->foreign('customer_id')->references('id')->on('customers')->onDelete('cascade');
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');

            // Unique constraint to prevent duplicate wishlist items
            $table->unique(['customer_id', 'product_id']);

            // Indexes for performance
            $table->index(['customer_id']);
            $table->index(['product_id']);
            $table->index(['is_public']);
            $table->index(['priority']);
            $table->index(['is_notified']);
            $table->index(['price_drop_notification']);
            $table->index(['added_at']);
            $table->index(['created_at']);
            $table->index(['updated_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customer_wishlists');
    }
};
