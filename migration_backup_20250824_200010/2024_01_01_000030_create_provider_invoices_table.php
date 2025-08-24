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
        Schema::create('provider_invoices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('provider_id')->constrained('providers')->onDelete('cascade');
            $table->string('invoice_number')->unique();
            $table->date('invoice_date');
            $table->date('due_date');
            $table->decimal('total_amount', 15, 2);
            $table->decimal('subtotal', 15, 2);
            $table->decimal('tax_amount', 15, 2)->default(0);
            $table->decimal('discount_amount', 15, 2)->default(0);
            $table->decimal('shipping_amount', 15, 2)->default(0);
            $table->string('currency', 3)->default('USD');
            $table->enum('status', ['draft', 'sent', 'paid', 'overdue', 'cancelled', 'disputed'])->default('draft');
            $table->enum('payment_terms', ['net_30', 'net_60', 'net_90', 'immediate', 'custom'])->default('net_30');
            $table->string('payment_method')->nullable();
            $table->string('reference_number')->nullable();
            $table->text('notes')->nullable();
            $table->json('attachments')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->boolean('overdue_notice_sent')->default(false);
            $table->timestamps();
            $table->softDeletes();

            // Indexes for performance
            $table->index(['provider_id', 'status']);
            $table->index(['status', 'due_date']);
            $table->index(['invoice_date']);
            $table->index(['due_date']);
            $table->index(['total_amount']);
            $table->index(['payment_terms']);
            $table->index(['currency']);
            $table->index(['created_at']);
            $table->index(['updated_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('provider_invoices');
    }
};

