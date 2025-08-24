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
        Schema::create('employees', function (Blueprint $table) {
            $table->id();

            // User relationship
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

            // Employee identification
            $table->string('employee_number', 50)->unique();
            $table->string('first_name', 100);
            $table->string('last_name', 100);
            $table->string('email', 255)->unique();
            $table->string('phone', 20)->nullable();

            // Personal information
            $table->date('date_of_birth')->nullable();
            $table->enum('gender', ['male', 'female', 'other', 'prefer_not_to_say'])->nullable();

            // Employment information
            $table->date('hire_date');
            $table->date('termination_date')->nullable();
            $table->enum('status', ['active', 'inactive', 'terminated', 'pending', 'on_leave'])->default('pending');
            $table->enum('employment_type', ['full_time', 'part_time', 'contract', 'temporary', 'intern', 'freelance'])->default('full_time');
            $table->string('department', 100);
            $table->string('position', 100);

            // Hierarchy
            $table->unsignedBigInteger('manager_id')->nullable();
            $table->foreign('manager_id')->references('id')->on('employees')->onDelete('set null');

            // Compensation
            $table->decimal('salary', 10, 2)->nullable();
            $table->decimal('hourly_rate', 8, 2)->nullable();

            // Performance
            $table->decimal('performance_rating', 3, 2)->nullable();
            $table->date('last_review_date')->nullable();
            $table->date('next_review_date')->nullable();

            // Address information
            $table->string('address', 255)->nullable();
            $table->string('city', 100)->nullable();
            $table->string('state', 100)->nullable();
            $table->string('postal_code', 20)->nullable();
            $table->string('country', 100)->nullable();

            // Emergency contact
            $table->string('emergency_contact_name', 100)->nullable();
            $table->string('emergency_contact_phone', 20)->nullable();
            $table->string('emergency_contact_relationship', 50)->nullable();

            // Banking information
            $table->string('bank_name', 100)->nullable();
            $table->string('bank_account_number', 50)->nullable();
            $table->string('bank_routing_number', 20)->nullable();

            // Tax information
            $table->string('tax_id', 50)->nullable();
            $table->string('social_security_number', 20)->nullable();

            // Time off tracking
            $table->integer('vacation_days_total')->default(0);
            $table->integer('vacation_days_used')->default(0);
            $table->integer('sick_days_total')->default(0);
            $table->integer('sick_days_used')->default(0);

            // Benefits
            $table->boolean('benefits_enrolled')->default(false);

            // Skills and development
            $table->json('skills')->nullable();
            $table->json('certifications')->nullable();
            $table->json('training_completed')->nullable();

            // Notes
            $table->text('notes')->nullable();

            // Timestamps
            $table->timestamps();

            // Indexes
            $table->index(['status']);
            $table->index(['employment_type']);
            $table->index(['department']);
            $table->index(['position']);
            $table->index(['manager_id']);
            $table->index(['hire_date']);
            $table->index(['salary']);
            $table->index(['performance_rating']);
            $table->index(['email']);
            $table->index(['employee_number']);

            // Additional indexes for better query performance
            $table->index('user_id');
            $table->index('phone');
            $table->index('termination_date');
            $table->index('last_review_date');
            $table->index('next_review_date');
            $table->index('created_at');
            $table->index('updated_at');

            // Composite indexes for common queries
            $table->index(['status', 'department']);
            $table->index(['status', 'employment_type']);
            $table->index(['department', 'position']);
            $table->index(['hire_date', 'status']);
            $table->index(['performance_rating', 'status']);
            $table->index(['manager_id', 'status']);

            // Full-text search indexes
            $table->fullText(['first_name', 'last_name', 'email']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};

