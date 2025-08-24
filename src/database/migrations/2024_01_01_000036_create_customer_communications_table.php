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
        Schema::create('customer_communications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained('customers')->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('communication_type'); // email, sms, push_notification, in_app, letter, phone_call
            $table->string('subject')->nullable();
            $table->text('content');
            $table->string('status')->default('draft'); // draft, scheduled, sending, sent, delivered, opened, clicked, bounced, unsubscribed, cancelled, failed
            $table->string('priority')->default('normal'); // low, normal, high, urgent
            $table->string('channel')->nullable(); // email, sms, push, web, mobile, mail, phone
            $table->timestamp('scheduled_at')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->timestamp('opened_at')->nullable();
            $table->timestamp('clicked_at')->nullable();
            $table->timestamp('bounced_at')->nullable();
            $table->timestamp('unsubscribed_at')->nullable();
            $table->foreignId('campaign_id')->nullable()->constrained('campaigns')->onDelete('set null');
            $table->foreignId('segment_id')->nullable()->constrained('customer_segments')->onDelete('set null');
            $table->foreignId('template_id')->nullable()->constrained('communication_templates')->onDelete('set null');
            $table->json('metadata')->nullable();
            $table->json('tracking_data')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Customer and user indexes
            $table->index('customer_id');
            $table->index('user_id');

            // Communication type and status indexes
            $table->index('communication_type');
            $table->index('status');
            $table->index('priority');
            $table->index('channel');

            // Date and time indexes
            $table->index('scheduled_at');
            $table->index('sent_at');
            $table->index('delivered_at');
            $table->index('opened_at');
            $table->index('clicked_at');
            $table->index('bounced_at');
            $table->index('unsubscribed_at');
            $table->index('created_at');
            $table->index('updated_at');

            // Campaign and segment indexes
            $table->index('campaign_id');
            $table->index('segment_id');
            $table->index('template_id');

            // Composite indexes for common queries
            $table->index(['customer_id', 'status']);
            $table->index(['customer_id', 'communication_type']);
            $table->index(['customer_id', 'created_at']);
            $table->index(['status', 'scheduled_at']);
            $table->index(['campaign_id', 'status']);
            $table->index(['segment_id', 'status']);
            $table->index(['communication_type', 'status']);
            $table->index(['priority', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customer_communications');
    }
};
