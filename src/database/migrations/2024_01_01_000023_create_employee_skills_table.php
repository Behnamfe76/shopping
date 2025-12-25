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
        Schema::create('employee_skills', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('employees')->onDelete('cascade');
            $table->string('skill_name', 255);
            $table->enum('skill_category', [
                'technical',
                'soft_skills',
                'languages',
                'tools',
                'methodologies',
                'certifications',
                'other',
            ]);
            $table->enum('proficiency_level', [
                'beginner',
                'intermediate',
                'advanced',
                'expert',
                'master',
            ]);
            $table->unsignedInteger('years_experience')->default(0);
            $table->boolean('certification_required')->default(false);
            $table->string('certification_name', 255)->nullable();
            $table->date('certification_date')->nullable();
            $table->date('certification_expiry')->nullable();
            $table->boolean('is_verified')->default(false);
            $table->foreignId('verified_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('verified_at')->nullable();
            $table->boolean('is_active')->default(true);
            $table->boolean('is_primary')->default(false);
            $table->boolean('is_required')->default(false);
            $table->text('skill_description')->nullable();
            $table->json('keywords')->nullable();
            $table->json('tags')->nullable();
            $table->text('notes')->nullable();
            $table->json('attachments')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Indexes for performance
            $table->index(['employee_id', 'skill_category']);
            $table->index(['employee_id', 'proficiency_level']);
            $table->index(['employee_id', 'is_verified']);
            $table->index(['employee_id', 'is_primary']);
            $table->index(['employee_id', 'is_active']);
            $table->index(['skill_category', 'proficiency_level']);
            $table->index(['is_verified', 'verified_at']);
            $table->index(['certification_expiry']);
            $table->index(['skill_name']);
            $table->index(['created_at']);
            $table->index(['updated_at']);

            // Full-text search index for skill name and description
            $table->fullText(['skill_name', 'skill_description']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employee_skills');
    }
};
