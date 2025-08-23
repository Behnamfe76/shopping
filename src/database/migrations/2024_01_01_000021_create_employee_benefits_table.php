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
        Schema::create('employee_benefits', function (Blueprint $table) {
            $table->id();

            // Employee relationship
            $table->foreignId('employee_id')->constrained('employees')->onDelete('cascade');

            // Benefit details
            $table->enum('benefit_type', ['health', 'dental', 'vision', 'life', 'disability', 'retirement', 'other']);
            $table->string('benefit_name', 255);
            $table->string('provider', 255);
            $table->string('plan_id', 100)->nullable();

            // Dates
            $table->date('enrollment_date')->nullable();
            $table->date('effective_date');
            $table->date('end_date')->nullable();

            // Status and coverage
            $table->enum('status', ['enrolled', 'pending', 'terminated', 'cancelled'])->default('pending');
            $table->enum('coverage_level', ['individual', 'family', 'employee_plus_spouse', 'employee_plus_children']);

            // Cost information
            $table->decimal('premium_amount', 10, 2);
            $table->decimal('employee_contribution', 10, 2);
            $table->decimal('employer_contribution', 10, 2);
            $table->decimal('total_cost', 10, 2);

            // Additional cost details
            $table->decimal('deductible', 10, 2)->nullable();
            $table->decimal('co_pay', 10, 2)->nullable();
            $table->decimal('co_insurance', 5, 2)->nullable(); // Percentage
            $table->decimal('max_out_of_pocket', 10, 2)->nullable();

            // Network and status
            $table->enum('network_type', ['ppo', 'hmo', 'epo', 'pos', 'hdhp']);
            $table->boolean('is_active')->default(true);

            // Additional information
            $table->text('notes')->nullable();
            $table->json('documents')->nullable();

            // Timestamps
            $table->timestamps();
            $table->softDeletes();

            // Indexes for performance
            $table->index(['employee_id', 'benefit_type']);
            $table->index(['employee_id', 'status']);
            $table->index(['benefit_type', 'status']);
            $table->index(['effective_date']);
            $table->index(['end_date']);
            $table->index(['enrollment_date']);
            $table->index(['is_active']);
            $table->index(['provider']);
            $table->index(['coverage_level']);
            $table->index(['network_type']);

            // Composite indexes for common queries
            $table->index(['employee_id', 'is_active', 'status']);
            $table->index(['benefit_type', 'is_active', 'status']);
            $table->index(['effective_date', 'end_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employee_benefits');
    }
};
