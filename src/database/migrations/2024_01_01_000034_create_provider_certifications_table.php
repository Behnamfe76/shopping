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
        Schema::create('provider_certifications', function (Blueprint $table) {
            $table->id();

            // Provider relationship
            $table->unsignedBigInteger('provider_id');
            $table->foreign('provider_id')
                  ->references('id')
                  ->on('providers')
                  ->onDelete('cascade');

            // Certification details
            $table->string('certification_name', 255);
            $table->string('certification_number', 100)->unique();
            $table->string('issuing_organization', 255);
            $table->enum('category', [
                'professional',
                'technical',
                'safety',
                'compliance',
                'educational',
                'industry_specific',
                'other'
            ]);
            $table->text('description')->nullable();

            // Dates
            $table->date('issue_date');
            $table->date('expiry_date')->nullable();
            $table->date('renewal_date')->nullable();

            // Status and verification
            $table->enum('status', [
                'active',
                'expired',
                'suspended',
                'revoked',
                'pending_renewal'
            ])->default('active');

            $table->enum('verification_status', [
                'unverified',
                'pending',
                'verified',
                'rejected',
                'requires_update'
            ])->default('unverified');

            // Verification details
            $table->string('verification_url', 500)->nullable();
            $table->string('attachment_path', 500)->nullable();

            // Credits and renewal
            $table->integer('credits_earned')->nullable();
            $table->boolean('is_recurring')->default(false);
            $table->integer('renewal_period')->nullable(); // in months

            // JSON fields for flexible data
            $table->json('renewal_requirements')->nullable();
            $table->json('notes')->nullable();

            // Verification tracking
            $table->timestamp('verified_at')->nullable();
            $table->unsignedBigInteger('verified_by')->nullable();
            $table->foreign('verified_by')
                  ->references('id')
                  ->on('users')
                  ->onDelete('set null');

            // Timestamps
            $table->timestamps();
            $table->softDeletes();

            // Indexes for performance
            $table->index('provider_id');
            $table->index('category');
            $table->index('status');
            $table->index('verification_status');
            $table->index('issue_date');
            $table->index('expiry_date');
            $table->index('verified_at');
            $table->index('verified_by');
            $table->index('is_recurring');

            // Composite indexes for common queries
            $table->index(['provider_id', 'status']);
            $table->index(['provider_id', 'category']);
            $table->index(['provider_id', 'verification_status']);
            $table->index(['status', 'expiry_date']);
            $table->index(['verification_status', 'verified_at']);

            // Full-text search index
            $table->fullText(['certification_name', 'certification_number', 'issuing_organization', 'description'], 'provider_certs_fulltext_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('provider_certifications');
    }
};
