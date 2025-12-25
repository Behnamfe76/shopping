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
        Schema::create('provider_notes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('provider_id')->constrained('providers')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('title', 255);
            $table->text('content');
            $table->enum('note_type', [
                'general',
                'contract',
                'payment',
                'quality',
                'performance',
                'communication',
                'other',
            ])->default('general');
            $table->enum('priority', [
                'low',
                'medium',
                'high',
                'urgent',
            ])->default('medium');
            $table->boolean('is_private')->default(false);
            $table->boolean('is_archived')->default(false);
            $table->json('tags')->nullable();
            $table->json('attachments')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Indexes for performance
            $table->index(['provider_id', 'created_at']);
            $table->index(['user_id', 'created_at']);
            $table->index(['note_type', 'created_at']);
            $table->index(['priority', 'created_at']);
            $table->index(['is_private', 'created_at']);
            $table->index(['is_archived', 'created_at']);
            $table->index('created_at');
            $table->index('updated_at');

            // Full-text search indexes
            $table->fullText(['title', 'content']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('provider_notes');
    }
};
