<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('employee_notes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('employees')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('title', 255);
            $table->text('content');
            $table->enum('note_type', [
                'performance',
                'general',
                'warning',
                'praise',
                'incident',
                'training',
                'goal',
                'feedback',
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
            $table->index(['employee_id', 'created_at']);
            $table->index(['user_id', 'created_at']);
            $table->index(['note_type', 'created_at']);
            $table->index(['priority', 'created_at']);
            $table->index(['is_private', 'created_at']);
            $table->index(['is_archived', 'created_at']);
            $table->index('created_at');
            $table->index('updated_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employee_notes');
    }
};
