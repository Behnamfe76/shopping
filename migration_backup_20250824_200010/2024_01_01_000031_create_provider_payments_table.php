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
        Schema::create('provider_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('provider_id')->constrained('providers')->onDelete('cascade');
            $table->foreignId('invoice_id')->nullable()->constrained('provider_invoices')->onDelete('set null');
            $table->string('payment_number', 50)->unique();
            $table->date('payment_date');
            $table->decimal('amount', 15, 2);
            $table->string('currency', 3)->default('USD');
            $table->enum('payment_method', [
                'bank_transfer',
                'check',
                'credit_card',
                'wire_transfer',
                'cash',
                'other'
            ]);
            $table->string('reference_number', 255)->nullable();
            $table->string('transaction_id', 255)->nullable();
            $table->enum('status', [
                'pending',
                'processed',
                'completed',
                'failed',
                'cancelled',
                'refunded'
            ])->default('pending');
            $table->text('notes')->nullable();
            $table->json('attachments')->nullable();
            $table->foreignId('processed_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('processed_at')->nullable();
            $table->timestamp('reconciled_at')->nullable();
            $table->text('reconciliation_notes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Indexes for performance
            $table->index(['provider_id', 'status']);
            $table->index(['invoice_id', 'status']);
            $table->index(['payment_date']);
            $table->index(['status']);
            $table->index(['payment_method']);
            $table->index(['currency']);
            $table->index(['processed_by']);
            $table->index(['reconciled_at']);
            $table->index(['created_at']);
            $table->index(['updated_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('provider_payments');
    }
};
