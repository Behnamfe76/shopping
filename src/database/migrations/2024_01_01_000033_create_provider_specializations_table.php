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
        Schema::create('provider_specializations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('provider_id')->constrained('providers')->onDelete('cascade');
            $table->string('specialization_name', 255);
            $table->enum('category', [
                'medical', 'legal', 'technical', 'financial',
                'educational', 'consulting', 'other'
            ]);
            $table->text('description')->nullable();
            $table->unsignedTinyInteger('years_experience')->default(0);
            $table->enum('proficiency_level', [
                'beginner', 'intermediate', 'advanced', 'expert', 'master'
            ]);
            $table->json('certifications')->nullable();
            $table->boolean('is_primary')->default(false);
            $table->boolean('is_active')->default(true);
            $table->enum('verification_status', [
                'unverified', 'pending', 'verified', 'rejected'
            ])->default('unverified');
            $table->timestamp('verified_at')->nullable();
            $table->foreignId('verified_by')->nullable()->constrained('users')->onDelete('set null');
            $table->json('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Indexes for performance
            $table->index(['provider_id', 'is_primary']);
            $table->index(['provider_id', 'is_active']);
            $table->index(['provider_id', 'verification_status']);
            $table->index(['category', 'proficiency_level']);
            $table->index(['verification_status', 'verified_at']);
            $table->index(['specialization_name']);
            $table->index(['years_experience']);
            $table->index(['created_at']);
            $table->index(['updated_at']);

            // Unique constraint: only one primary specialization per provider
            $table->unique(['provider_id', 'is_primary'], 'unique_provider_primary_specialization');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('provider_specializations');
    }
};
