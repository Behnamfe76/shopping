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
        Schema::create('employee_salary_histories', function (Blueprint $table) {
            $table->id();

            // Employee relationship
            $table->unsignedBigInteger('employee_id');
            $table->foreign('employee_id')->references('id')->on('employees')->onDelete('cascade');

            // Salary information
            $table->decimal('old_salary', 12, 2)->comment('Previous salary amount');
            $table->decimal('new_salary', 12, 2)->comment('New salary amount');
            $table->decimal('change_amount', 12, 2)->comment('Difference between old and new salary');
            $table->decimal('change_percentage', 8, 4)->comment('Percentage change in salary');

            // Change details
            $table->enum('change_type', [
                'promotion',
                'merit',
                'cost_of_living',
                'market_adjustment',
                'demotion',
                'performance_bonus',
                'retention_bonus',
                'hiring_bonus',
                'severance',
                'salary_freeze',
                'salary_reduction',
                'equity_adjustment',
                'compression_adjustment',
                'geographic_adjustment',
                'skill_adjustment',
                'experience_adjustment',
                'education_adjustment',
                'certification_bonus',
                'language_bonus',
                'shift_differential',
                'overtime_rate',
                'holiday_pay',
                'weekend_pay',
                'night_shift_pay',
                'hazard_pay',
                'travel_pay',
                'relocation_adjustment',
                'other'
            ])->comment('Type of salary change');

            // Effective date and reason
            $table->date('effective_date')->comment('When the salary change takes effect');
            $table->text('reason')->nullable()->comment('Reason for the salary change');

            // Approval workflow
            $table->unsignedBigInteger('approved_by')->nullable()->comment('User who approved the change');
            $table->foreign('approved_by')->references('id')->on('users')->onDelete('set null');
            $table->timestamp('approved_at')->nullable()->comment('When the change was approved');

            $table->unsignedBigInteger('rejected_by')->nullable()->comment('User who rejected the change');
            $table->foreign('rejected_by')->references('id')->on('users')->onDelete('set null');
            $table->timestamp('rejected_at')->nullable()->comment('When the change was rejected');
            $table->text('rejection_reason')->nullable()->comment('Reason for rejection');

            // Status tracking
            $table->enum('status', ['pending', 'approved', 'rejected', 'processed', 'cancelled'])->default('pending');
            $table->timestamp('processed_at')->nullable()->comment('When the change was processed');

            // Retroactive adjustments
            $table->boolean('is_retroactive')->default(false)->comment('Whether this is a retroactive adjustment');
            $table->date('retroactive_start_date')->nullable()->comment('Start date for retroactive period');
            $table->date('retroactive_end_date')->nullable()->comment('End date for retroactive period');

            // Additional information
            $table->text('notes')->nullable()->comment('Additional notes about the change');
            $table->json('attachments')->nullable()->comment('Related documents and attachments');
            $table->json('metadata')->nullable()->comment('Additional metadata for the change');

            // Timestamps
            $table->timestamps();
            $table->softDeletes();

            // Indexes for performance
            $table->index('employee_id');
            $table->index('change_type');
            $table->index('effective_date');
            $table->index('approved_by');
            $table->index('status');
            $table->index('is_retroactive');
            $table->index(['employee_id', 'effective_date']);
            $table->index(['change_type', 'effective_date']);
            $table->index(['employee_id', 'change_type']);
            $table->index(['employee_id', 'status']);
            $table->index(['effective_date', 'status']);

            // Composite indexes for common queries
            $table->index(['employee_id', 'change_type', 'effective_date'], 'emp_sal_hist_emp_chg_eff_date_idx');
            $table->index(['change_type', 'status', 'effective_date'], 'emp_sal_hist_chg_sts_eff_idx');
            $table->index(['employee_id', 'status', 'effective_date'], 'emp_sal_hist_emp_sts_eff_idx');

            // Full-text search index
            $table->fullText(['reason', 'notes']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employee_salary_histories');
    }
};
