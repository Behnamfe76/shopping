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
        Schema::create('provider_contracts', function (Blueprint $table) {
            $table->id();

            // Basic contract information
            $table->string('contract_number')->unique();
            $table->enum('contract_type', ['service', 'supply', 'distribution', 'partnership', 'other']);
            $table->string('title');
            $table->text('description')->nullable();

            // Contract dates
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->date('renewal_date')->nullable();
            $table->date('termination_date')->nullable();

            // Contract terms and conditions
            $table->json('terms')->nullable();
            $table->json('conditions')->nullable();
            $table->json('renewal_terms')->nullable();

            // Financial information
            $table->decimal('commission_rate', 5, 2)->default(0.00);
            $table->decimal('contract_value', 15, 2)->default(0.00);
            $table->string('currency', 3)->default('USD');
            $table->json('payment_terms')->nullable();

            // Contract status and lifecycle
            $table->enum('status', ['draft', 'active', 'expired', 'terminated', 'suspended', 'pending_renewal'])->default('draft');
            $table->boolean('auto_renewal')->default(false);

            // Signing information
            $table->unsignedBigInteger('signed_by')->nullable();
            $table->timestamp('signed_at')->nullable();
            $table->text('termination_reason')->nullable();

            // Attachments and notes
            $table->json('attachments')->nullable();
            $table->json('notes')->nullable();

            // Relationships
            $table->unsignedBigInteger('provider_id');
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();

            // Timestamps
            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index('provider_id');
            $table->index('contract_type');
            $table->index('status');
            $table->index('start_date');
            $table->index('end_date');
            $table->index('renewal_date');
            $table->index('signed_by');
            $table->index('created_by');
            $table->index('updated_by');
            $table->index(['provider_id', 'status']);
            $table->index(['contract_type', 'status']);
            $table->index(['start_date', 'end_date']);
            $table->index(['status', 'end_date']);

            // Foreign key constraints
            $table->foreign('provider_id')->references('id')->on('providers')->onDelete('cascade');
            $table->foreign('signed_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('updated_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('provider_contracts');
    }
};
