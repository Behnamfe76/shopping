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
