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
        Schema::create('customers', function (Blueprint $table) {
            $table->id();

            // User relationship
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

            // Customer identification
            $table->string('customer_number', 50)->unique();
            $table->string('first_name', 255);
            $table->string('last_name', 255);
            $table->string('email', 255)->unique();
            $table->string('phone', 20)->nullable();

            // Personal information
            $table->date('date_of_birth')->nullable();
            $table->enum('gender', ['male', 'female', 'other', 'prefer_not_to_say'])->nullable();

            // Business information
            $table->string('company_name', 255)->nullable();
            $table->string('tax_id', 100)->nullable();

            // Customer classification
            $table->enum('customer_type', ['individual', 'business', 'wholesale', 'vip'])->default('individual');
            $table->enum('status', ['active', 'inactive', 'suspended', 'pending'])->default('pending');

            // Loyalty program
            $table->integer('loyalty_points')->default(0);

            // Order analytics
            $table->integer('total_orders')->default(0);
            $table->decimal('total_spent', 10, 2)->default(0.00);
            $table->decimal('average_order_value', 10, 2)->default(0.00);
            $table->timestamp('last_order_date')->nullable();
            $table->timestamp('first_order_date')->nullable();

            // Preferences
            $table->string('preferred_payment_method', 100)->nullable();
            $table->string('preferred_shipping_method', 100)->nullable();

            // Marketing preferences
            $table->boolean('marketing_consent')->default(false);
            $table->boolean('newsletter_subscription')->default(false);

            // Additional information
            $table->text('notes')->nullable();
            $table->text('tags')->nullable();

            // Counters
            $table->integer('address_count')->default(0);
            $table->integer('order_count')->default(0);
            $table->integer('review_count')->default(0);
            $table->integer('wishlist_count')->default(0);

            // Timestamps
            $table->timestamps();
            $table->softDeletes();
            $table->timestampEquivalents(); // Automatic!

            // Indexes
            $table->index(['user_id']);
            $table->index(['customer_number']);
            $table->index(['email']);
            $table->index(['phone']);
            $table->index(['status']);
            $table->index(['customer_type']);
            $table->index(['loyalty_points']);
            $table->index(['total_spent']);
            $table->index(['created_at']);
            $table->index(['last_order_date']);
            $table->index(['marketing_consent']);
            $table->index(['newsletter_subscription']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};
