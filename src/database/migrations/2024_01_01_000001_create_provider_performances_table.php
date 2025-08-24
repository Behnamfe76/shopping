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
        Schema::create('provider_performances', function (Blueprint $table) {
            $table->id();

            // Provider relationship
            $table->unsignedBigInteger('provider_id');
            $table->foreign('provider_id')->references('id')->on('providers')->onDelete('cascade');

            // Period information
            $table->date('period_start');
            $table->date('period_end');
            $table->enum('period_type', ['daily', 'weekly', 'monthly', 'quarterly', 'yearly']);

            // Order and revenue metrics
            $table->integer('total_orders')->default(0);
            $table->decimal('total_revenue', 15, 2)->default(0.00);
            $table->decimal('average_order_value', 15, 2)->default(0.00);

            // Delivery performance metrics
            $table->decimal('on_time_delivery_rate', 5, 2)->default(0.00); // Percentage
            $table->decimal('return_rate', 5, 2)->default(0.00); // Percentage
            $table->decimal('defect_rate', 5, 2)->default(0.00); // Percentage

            // Customer satisfaction metrics
            $table->decimal('customer_satisfaction_score', 3, 2)->default(0.00); // 1-10 scale
            $table->decimal('response_time_avg', 8, 2)->default(0.00); // Hours

            // Quality and service ratings
            $table->decimal('quality_rating', 3, 2)->default(0.00); // 1-10 scale
            $table->decimal('delivery_rating', 3, 2)->default(0.00); // 1-10 scale
            $table->decimal('communication_rating', 3, 2)->default(0.00); // 1-10 scale

            // Efficiency metrics
            $table->decimal('cost_efficiency_score', 5, 2)->default(0.00); // Percentage
            $table->decimal('inventory_turnover_rate', 8, 2)->default(0.00); // Times per year
            $table->decimal('lead_time_avg', 8, 2)->default(0.00); // Days
            $table->decimal('fill_rate', 5, 2)->default(0.00); // Percentage
            $table->decimal('accuracy_rate', 5, 2)->default(0.00); // Percentage

            // Calculated performance metrics
            $table->decimal('performance_score', 5, 2)->default(0.00); // 0-100 scale
            $table->enum('performance_grade', ['A', 'B', 'C', 'D', 'F'])->default('C');

            // Verification status
            $table->boolean('is_verified')->default(false);
            $table->unsignedBigInteger('verified_by')->nullable();
            $table->timestamp('verified_at')->nullable();

            // Additional information
            $table->text('notes')->nullable();

            // Timestamps
            $table->timestamps();
            $table->softDeletes();

            // Indexes for performance
            $table->index(['provider_id', 'period_start', 'period_end'], 'idx_provider_period');
            $table->index(['period_type', 'period_start'], 'idx_period_type_start');
            $table->index(['performance_grade', 'performance_score'], 'idx_grade_score');
            $table->index(['is_verified', 'verified_at'], 'idx_verification_status');
            $table->index(['on_time_delivery_rate'], 'idx_delivery_rate');
            $table->index(['customer_satisfaction_score'], 'idx_satisfaction_score');
            $table->index(['total_revenue'], 'idx_revenue');
            $table->index(['created_at'], 'idx_created_at');
            $table->index(['updated_at'], 'idx_updated_at');

            // Composite indexes for common queries
            $table->index(['provider_id', 'period_type'], 'idx_provider_period_type');
            $table->index(['provider_id', 'performance_grade'], 'idx_provider_grade');
            $table->index(['period_start', 'period_end'], 'idx_period_range');
            $table->index(['performance_score', 'created_at'], 'idx_score_created');

            // Unique constraint to prevent duplicate performance records
            $table->unique(['provider_id', 'period_start', 'period_end', 'period_type'], 'unique_provider_period');

            // Check constraints for data integrity
            $table->check('total_orders >= 0', 'check_total_orders_positive');
            $table->check('total_revenue >= 0', 'check_total_revenue_positive');
            $table->check('average_order_value >= 0', 'check_avg_order_value_positive');
            $table->check('on_time_delivery_rate >= 0 AND on_time_delivery_rate <= 100', 'check_delivery_rate_range');
            $table->check('return_rate >= 0 AND return_rate <= 100', 'check_return_rate_range');
            $table->check('defect_rate >= 0 AND defect_rate <= 100', 'check_defect_rate_range');
            $table->check('customer_satisfaction_score >= 1 AND customer_satisfaction_score <= 10', 'check_satisfaction_range');
            $table->check('response_time_avg >= 0', 'check_response_time_positive');
            $table->check('quality_rating >= 1 AND quality_rating <= 10', 'check_quality_range');
            $table->check('delivery_rating >= 1 AND delivery_rating <= 10', 'check_delivery_range');
            $table->check('communication_rating >= 1 AND communication_rating <= 10', 'check_communication_range');
            $table->check('cost_efficiency_score >= 0 AND cost_efficiency_score <= 100', 'check_cost_efficiency_range');
            $table->check('inventory_turnover_rate >= 0', 'check_inventory_turnover_positive');
            $table->check('lead_time_avg >= 0', 'check_lead_time_positive');
            $table->check('fill_rate >= 0 AND fill_rate <= 100', 'check_fill_rate_range');
            $table->check('accuracy_rate >= 0 AND accuracy_rate <= 100', 'check_accuracy_rate_range');
            $table->check('performance_score >= 0 AND performance_score <= 100', 'check_performance_score_range');
        });

        // Add foreign key for verified_by after users table exists
        Schema::table('provider_performances', function (Blueprint $table) {
            $table->foreign('verified_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('provider_performances');
    }
};
