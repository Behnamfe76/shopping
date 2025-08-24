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
        Schema::create('provider_communications', function (Blueprint $table) {
            $table->id();

            // Provider and User relationships
            $table->unsignedBigInteger('provider_id');
            $table->unsignedBigInteger('user_id');

            // Communication details
            $table->enum('communication_type', [
                'email', 'phone', 'chat', 'sms', 'video_call', 'in_person',
                'support_ticket', 'complaint', 'inquiry', 'order_update',
                'payment_notification', 'quality_issue', 'delivery_update',
                'contract_discussion', 'general'
            ]);
            $table->string('subject', 255);
            $table->text('message');
            $table->enum('direction', ['inbound', 'outbound']);
            $table->enum('status', [
                'draft', 'sent', 'delivered', 'read', 'replied',
                'closed', 'archived', 'failed'
            ])->default('draft');

            // Timestamps
            $table->timestamp('sent_at')->nullable();
            $table->timestamp('read_at')->nullable();
            $table->timestamp('replied_at')->nullable();

            // Priority and urgency
            $table->enum('priority', ['low', 'normal', 'high', 'urgent'])->default('normal');
            $table->boolean('is_urgent')->default(false);
            $table->boolean('is_archived')->default(false);

            // Threading and organization
            $table->string('thread_id', 255)->nullable();
            $table->unsignedBigInteger('parent_id')->nullable();

            // Metadata
            $table->json('attachments')->nullable();
            $table->json('tags')->nullable();
            $table->integer('response_time')->nullable()->comment('Response time in minutes');
            $table->decimal('satisfaction_rating', 2, 1)->nullable()->comment('Rating from 0.0 to 5.0');
            $table->text('notes')->nullable();

            // Standard timestamps
            $table->timestamps();
            $table->softDeletes();

            // Indexes for performance
            $table->index('provider_id');
            $table->index('user_id');
            $table->index('communication_type');
            $table->index('direction');
            $table->index('status');
            $table->index('priority');
            $table->index('is_urgent');
            $table->index('is_archived');
            $table->index('thread_id');
            $table->index('parent_id');
            $table->index('sent_at');
            $table->index('read_at');
            $table->index('replied_at');
            $table->index('created_at');
            $table->index('updated_at');

            // Composite indexes for common queries
            $table->index(['provider_id', 'status']);
            $table->index(['provider_id', 'communication_type']);
            $table->index(['provider_id', 'direction']);
            $table->index(['provider_id', 'priority']);
            $table->index(['provider_id', 'is_urgent']);
            $table->index(['provider_id', 'is_archived']);
            $table->index(['user_id', 'status']);
            $table->index(['user_id', 'communication_type']);
            $table->index(['thread_id', 'created_at']);
            $table->index(['parent_id', 'created_at']);

            // Full-text search indexes
            $table->fullText(['subject', 'message', 'notes']);

            // Foreign key constraints
            $table->foreign('provider_id')
                  ->references('id')
                  ->on('providers')
                  ->onDelete('cascade');

            $table->foreign('user_id')
                  ->references('id')
                  ->on('users')
                  ->onDelete('cascade');

            $table->foreign('parent_id')
                  ->references('id')
                  ->on('provider_communications')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('provider_communications');
    }
};
