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
        Schema::create('provider_ratings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('provider_id')->constrained('providers')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->decimal('rating_value', 3, 2)->comment('Rating value (1.00 to 5.00)');
            $table->integer('max_rating')->default(5)->comment('Maximum rating scale (5, 10, or 100)');
            $table->enum('category', ['overall', 'quality', 'service', 'pricing', 'communication', 'reliability'])->default('overall');
            $table->string('title', 255)->comment('Rating title/summary');
            $table->text('comment')->comment('Detailed rating comment');
            $table->json('pros')->nullable()->comment('Positive aspects of the service');
            $table->json('cons')->nullable()->comment('Negative aspects of the service');
            $table->boolean('would_recommend')->default(true)->comment('Whether user would recommend the provider');
            $table->json('rating_criteria')->nullable()->comment('Breakdown of rating by specific criteria');
            $table->integer('helpful_votes')->default(0)->comment('Number of helpful votes');
            $table->integer('total_votes')->default(0)->comment('Total number of votes');
            $table->boolean('is_verified')->default(false)->comment('Whether the rating has been verified');
            $table->enum('status', ['pending', 'approved', 'rejected', 'flagged'])->default('pending');
            $table->string('ip_address', 45)->nullable()->comment('IP address of the user who created the rating');
            $table->string('user_agent', 500)->nullable()->comment('User agent string');
            $table->foreignId('moderator_id')->nullable()->constrained('users')->onDelete('set null')->comment('ID of moderator who processed the rating');
            $table->text('moderation_notes')->nullable()->comment('Notes from moderator');
            $table->string('rejection_reason', 500)->nullable()->comment('Reason for rejection if applicable');
            $table->string('flag_reason', 500)->nullable()->comment('Reason for flagging if applicable');
            $table->timestamp('verified_at')->nullable()->comment('When the rating was verified');
            $table->timestamp('moderated_at')->nullable()->comment('When the rating was moderated');
            $table->timestamps();
            $table->softDeletes();

            // Indexes for performance
            $table->index(['provider_id', 'status']);
            $table->index(['provider_id', 'category']);
            $table->index(['user_id', 'created_at']);
            $table->index(['status', 'created_at']);
            $table->index(['rating_value', 'status']);
            $table->index(['is_verified', 'status']);
            $table->index(['created_at']);
            $table->index(['ip_address', 'created_at']);
            $table->index(['helpful_votes', 'total_votes']);
            $table->index(['would_recommend', 'status']);

            // Unique constraint to prevent multiple ratings from same user for same provider
            $table->unique(['provider_id', 'user_id'], 'unique_provider_user_rating');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('provider_ratings');
    }
};
