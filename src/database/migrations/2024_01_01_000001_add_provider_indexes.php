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
        Schema::table('providers', function (Blueprint $table) {
            // Basic search indexes
            $table->index('company_name');
            $table->index('contact_person');
            $table->index('email');
            $table->index('phone');
            $table->index('tax_id');
            $table->index('business_license');

            // Status and type indexes
            $table->index('status');
            $table->index('provider_type');

            // Location indexes
            $table->index('city');
            $table->index('state');
            $table->index('country');
            $table->index(['city', 'state', 'country']);

            // Rating and performance indexes
            $table->index('rating');
            $table->index('quality_rating');
            $table->index('delivery_rating');
            $table->index('communication_rating');
            $table->index('total_orders');
            $table->index('total_spent');
            $table->index('average_order_value');

            // Financial indexes
            $table->index('credit_limit');
            $table->index('current_balance');
            $table->index('commission_rate');
            $table->index('discount_rate');

            // Contract indexes
            $table->index('contract_start_date');
            $table->index('contract_end_date');

            // Date indexes
            $table->index('last_order_date');
            $table->index('first_order_date');
            $table->index('created_at');
            $table->index('updated_at');

            // Composite indexes for common queries
            $table->index(['status', 'provider_type']);
            $table->index(['status', 'rating']);
            $table->index(['provider_type', 'rating']);
            $table->index(['city', 'status']);
            $table->index(['rating', 'total_orders']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('providers', function (Blueprint $table) {
            // Drop all indexes
            $table->dropIndex(['company_name']);
            $table->dropIndex(['contact_person']);
            $table->dropIndex(['email']);
            $table->dropIndex(['phone']);
            $table->dropIndex(['tax_id']);
            $table->dropIndex(['business_license']);
            $table->dropIndex(['status']);
            $table->dropIndex(['provider_type']);
            $table->dropIndex(['city']);
            $table->dropIndex(['state']);
            $table->dropIndex(['country']);
            $table->dropIndex(['city', 'state', 'country']);
            $table->dropIndex(['rating']);
            $table->dropIndex(['quality_rating']);
            $table->dropIndex(['delivery_rating']);
            $table->dropIndex(['communication_rating']);
            $table->dropIndex(['total_orders']);
            $table->dropIndex(['total_spent']);
            $table->dropIndex(['average_order_value']);
            $table->dropIndex(['credit_limit']);
            $table->dropIndex(['current_balance']);
            $table->dropIndex(['commission_rate']);
            $table->dropIndex(['discount_rate']);
            $table->dropIndex(['contract_start_date']);
            $table->dropIndex(['contract_end_date']);
            $table->dropIndex(['last_order_date']);
            $table->dropIndex(['first_order_date']);
            $table->dropIndex(['created_at']);
            $table->dropIndex(['updated_at']);
            $table->dropIndex(['status', 'provider_type']);
            $table->dropIndex(['status', 'rating']);
            $table->dropIndex(['provider_type', 'rating']);
            $table->dropIndex(['city', 'status']);
            $table->dropIndex(['rating', 'total_orders']);
        });
    }
};
