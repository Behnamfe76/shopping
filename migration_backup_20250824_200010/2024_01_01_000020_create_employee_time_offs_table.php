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
        Schema::create('employee_time_offs', function (Blueprint $table) {
            $table->id();

            // Employee and user relationships
            $table->foreignId('employee_id')->constrained('employees')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');

            // Time-off details
            $table->enum('time_off_type', [
                'vacation',
                'sick',
                'personal',
                'bereavement',
                'jury_duty',
                'military',
                'other'
            ]);

            // Date and time fields
            $table->date('start_date');
            $table->date('end_date');
            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();

            // Calculated fields
            $table->decimal('total_hours', 8, 2)->nullable();
            $table->decimal('total_days', 8, 2)->nullable();

            // Request details
            $table->string('reason', 500);
            $table->text('description')->nullable();

            // Status and workflow
            $table->enum('status', [
                'pending',
                'approved',
                'rejected',
                'cancelled'
            ])->default('pending');

            // Approval fields
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('approved_at')->nullable();

            // Rejection fields
            $table->foreignId('rejected_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('rejected_at')->nullable();
            $table->string('rejection_reason', 500)->nullable();

            // Flags
            $table->boolean('is_half_day')->default(false);
            $table->boolean('is_urgent')->default(false);

            // Attachments
            $table->json('attachments')->nullable();

            // Timestamps
            $table->timestamps();
            $table->softDeletes();

            // Indexes for performance
            $table->index(['employee_id', 'start_date']);
            $table->index(['employee_id', 'status']);
            $table->index(['user_id', 'created_at']);
            $table->index(['status', 'created_at']);
            $table->index(['time_off_type', 'status']);
            $table->index(['start_date', 'end_date']);
            $table->index(['approved_by', 'status']);
            $table->index(['rejected_by', 'status']);
            $table->index(['is_urgent', 'status']);
            $table->index(['created_at']);
            $table->index(['updated_at']);

            // Composite indexes for common queries
            $table->index(['employee_id', 'start_date', 'end_date']);
            $table->index(['employee_id', 'status', 'created_at']);
            $table->index(['status', 'time_off_type', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employee_time_offs');
    }
};

