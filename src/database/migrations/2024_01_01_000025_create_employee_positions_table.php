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
        Schema::create('employee_positions', function (Blueprint $table) {
            $table->id();
            $table->string('title', 255);
            $table->string('code', 50)->unique();
            $table->text('description')->nullable();
            $table->foreignId('department_id')->constrained('employee_departments')->onDelete('cascade');
            $table->enum('level', ['entry', 'junior', 'mid', 'senior', 'lead', 'manager', 'director', 'executive']);
            $table->decimal('salary_min', 10, 2)->nullable();
            $table->decimal('salary_max', 10, 2)->nullable();
            $table->decimal('hourly_rate_min', 8, 2)->nullable();
            $table->decimal('hourly_rate_max', 8, 2)->nullable();
            $table->boolean('is_active')->default(true);
            $table->enum('status', ['active', 'inactive', 'archived', 'hiring', 'frozen'])->default('active');
            $table->json('requirements')->nullable();
            $table->json('responsibilities')->nullable();
            $table->json('skills_required')->nullable();
            $table->integer('experience_required')->nullable();
            $table->json('education_required')->nullable();
            $table->boolean('is_remote')->default(false);
            $table->boolean('is_travel_required')->default(false);
            $table->decimal('travel_percentage', 5, 2)->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Indexes for performance
            $table->index(['department_id']);
            $table->index(['level']);
            $table->index(['status']);
            $table->index(['is_active']);
            $table->index(['is_remote']);
            $table->index(['is_travel_required']);
            $table->index(['salary_min', 'salary_max']);
            $table->index(['hourly_rate_min', 'hourly_rate_max']);
            $table->index(['experience_required']);
            $table->index(['created_at']);
            $table->index(['updated_at']);

            // Full-text search index
            $table->fullText(['title', 'description', 'code']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employee_positions');
    }
};
