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
        Schema::create('providers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('provider_number')->unique();
            $table->string('company_name');
            $table->string('contact_person');
            $table->string('email')->unique();
            $table->string('phone');
            $table->string('website')->nullable();
            $table->string('tax_id')->nullable();
            $table->string('business_license')->nullable();
            $table->string('provider_type');
            $table->string('status')->default('pending');
            $table->decimal('rating', 3, 2)->nullable();
            $table->integer('total_orders')->default(0);
            $table->decimal('total_spent', 15, 2)->default(0);
            $table->decimal('average_order_value', 15, 2)->default(0);
            $table->timestamp('last_order_date')->nullable();
            $table->timestamp('first_order_date')->nullable();
            $table->string('payment_terms')->nullable();
            $table->decimal('credit_limit', 15, 2)->default(0);
            $table->decimal('current_balance', 15, 2)->default(0);
            $table->text('address');
            $table->string('city');
            $table->string('state');
            $table->string('postal_code');
            $table->string('country');
            $table->string('bank_name')->nullable();
            $table->string('bank_account_number')->nullable();
            $table->string('bank_routing_number')->nullable();
            $table->text('contact_notes')->nullable();
            $table->json('specializations')->nullable();
            $table->json('certifications')->nullable();
            $table->json('insurance_info')->nullable();
            $table->timestamp('contract_start_date')->nullable();
            $table->timestamp('contract_end_date')->nullable();
            $table->decimal('commission_rate', 5, 4)->default(0);
            $table->decimal('discount_rate', 5, 4)->default(0);
            $table->json('shipping_methods')->nullable();
            $table->json('payment_methods')->nullable();
            $table->decimal('quality_rating', 3, 2)->nullable();
            $table->decimal('delivery_rating', 3, 2)->nullable();
            $table->decimal('communication_rating', 3, 2)->nullable();
            $table->integer('response_time')->nullable(); // in hours
            $table->decimal('on_time_delivery_rate', 5, 2)->nullable(); // percentage
            $table->decimal('return_rate', 5, 2)->nullable(); // percentage
            $table->decimal('defect_rate', 5, 2)->nullable(); // percentage
            $table->string('suspension_reason')->nullable();
            $table->string('blacklist_reason')->nullable();
            $table->string('rejection_reason')->nullable();
            $table->string('termination_reason')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('providers');
    }
};
