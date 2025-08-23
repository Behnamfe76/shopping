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
        Schema::create('employee_trainings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('employees')->onDelete('cascade');
            $table->enum('training_type', [
                'technical',
                'soft_skills', 
                'compliance',
                'safety',
                'leadership',
                'product',
                'other'
            ]);
            $table->string('training_name');
            $table->string('provider');
            $table->text('description')->nullable();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->date('completion_date')->nullable();
            $table->enum('status', [
                'not_started',
                'in_progress',
                'completed',
                'failed',
                'cancelled'
            ])->default('not_started');
            $table->decimal('score', 5, 2)->nullable();
            $table->string('grade', 10)->nullable();
            $table->string('certificate_number', 100)->nullable();
            $table->string('certificate_url', 500)->nullable();
            $table->decimal('hours_completed', 8, 2)->default(0.00);
            $table->decimal('total_hours', 8, 2)->nullable();
            $table->decimal('cost', 10, 2)->nullable();
            $table->boolean('is_mandatory')->default(false);
            $table->boolean('is_certification')->default(false);
            $table->boolean('is_renewable')->default(false);
            $table->date('renewal_date')->nullable();
            $table->date('expiry_date')->nullable();
            $table->string('instructor', 255)->nullable();
            $table->string('location', 255)->nullable();
            $table->enum('training_method', [
                'in_person',
                'online',
                'hybrid',
                'self_study',
                'workshop',
                'seminar'
            ]);
            $table->json('materials')->nullable();
            $table->text('notes')->nullable();
            $table->json('attachments')->nullable();
            $table->string('failure_reason', 500)->nullable();
            $table->string('cancellation_reason', 500)->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Indexes for performance
            $table->index(['employee_id', 'status']);
            $table->index(['training_type', 'status']);
            $table->index(['status', 'start_date']);
            $table->index(['is_mandatory', 'status']);
            $table->index(['is_certification', 'expiry_date']);
            $table->index(['provider']);
            $table->index(['instructor']);
            $table->index(['training_method']);
            $table->index(['start_date', 'end_date']);
            $table->index(['completion_date']);
            $table->index(['score']);
            $table->index(['created_at']);
            $table->index(['updated_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employee_trainings');
    }
};
