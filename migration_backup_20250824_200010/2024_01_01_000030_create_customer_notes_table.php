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
        Schema::create('customer_notes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained('customers')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('title', 255);
            $table->text('content');
            $table->enum('note_type', ['general', 'support', 'sales', 'billing', 'technical', 'complaint', 'feedback', 'follow_up'])->default('general');
            $table->enum('priority', ['low', 'medium', 'high', 'urgent'])->default('medium');
            $table->boolean('is_private')->default(false);
            $table->boolean('is_pinned')->default(false);
            $table->json('tags')->nullable();
            $table->json('attachments')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Indexes for better performance
            $table->index(['customer_id', 'created_at']);
            $table->index(['user_id', 'created_at']);
            $table->index(['note_type', 'created_at']);
            $table->index(['priority', 'created_at']);
            $table->index(['is_private', 'created_at']);
            $table->index(['is_pinned', 'created_at']);
            $table->index(['customer_id', 'note_type']);
            $table->index(['customer_id', 'priority']);
            $table->index(['customer_id', 'is_pinned']);
            $table->index(['customer_id', 'is_private']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customer_notes');
    }
};
