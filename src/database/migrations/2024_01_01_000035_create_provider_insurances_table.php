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
        Schema::create('provider_insurances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('provider_id')->constrained('providers')->onDelete('cascade');
            $table->enum('insurance_type', [
                'general_liability',
                'professional_liability',
                'product_liability',
                'workers_compensation',
                'auto_insurance',
                'property_insurance',
                'cyber_insurance',
                'other'
            ]);
            $table->string('policy_number')->unique();
            $table->string('provider_name');
            $table->decimal('coverage_amount', 15, 2);
            $table->date('start_date');
            $table->date('end_date');
            $table->enum('status', [
                'active',
                'expired',
                'cancelled',
                'pending',
                'suspended'
            ])->default('pending');
            $table->json('documents')->nullable();
            $table->enum('verification_status', [
                'unverified',
                'pending',
                'verified',
                'rejected',
                'requires_update'
            ])->default('unverified');
            $table->foreignId('verified_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('verified_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Indexes for performance
            $table->index(['provider_id', 'insurance_type']);
            $table->index(['provider_id', 'status']);
            $table->index(['provider_id', 'verification_status']);
            $table->index(['status', 'end_date']);
            $table->index(['verification_status', 'verified_at']);
            $table->index('policy_number');
            $table->index('provider_name');
            $table->index('coverage_amount');
            $table->index('start_date');
            $table->index('end_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('provider_insurances');
    }
};
