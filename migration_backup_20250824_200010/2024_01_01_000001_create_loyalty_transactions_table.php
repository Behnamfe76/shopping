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
        Schema::create('loyalty_transactions', function (Blueprint $table) {
            $table->id();
            
            // Customer and user relationships
            $table->foreignId('customer_id')->constrained('customers')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            
            // Transaction details
            $table->enum('transaction_type', ['earned', 'redeemed', 'expired', 'reversed', 'bonus', 'adjustment']);
            $table->integer('points');
            $table->decimal('points_value', 10, 2);
            
            // Reference information
            $table->enum('reference_type', ['order', 'product', 'campaign', 'manual', 'system']);
            $table->unsignedBigInteger('reference_id')->nullable();
            
            // Description and reason
            $table->text('description')->nullable();
            $table->text('reason')->nullable();
            
            // Status and timestamps
            $table->enum('status', ['pending', 'completed', 'failed', 'reversed', 'expired'])->default('pending');
            $table->timestamp('expires_at')->nullable();
            $table->timestamp('reversed_at')->nullable();
            $table->foreignId('reversed_by')->nullable()->constrained('users')->onDelete('set null');
            
            // Metadata for additional data
            $table->json('metadata')->nullable();
            
            // Timestamps
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes for performance
            $table->index(['customer_id', 'created_at']);
            $table->index(['transaction_type', 'status']);
            $table->index(['reference_type', 'reference_id']);
            $table->index(['expires_at']);
            $table->index(['reversed_at']);
            $table->index(['user_id', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('loyalty_transactions');
    }
};
