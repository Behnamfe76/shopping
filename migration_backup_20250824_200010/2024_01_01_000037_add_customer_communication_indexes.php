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
        Schema::table('customer_communications', function (Blueprint $table) {
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
        Schema::table('customer_communications', function (Blueprint $table) {
            // Drop all indexes
            $table->dropIndex(['customer_id']);
            $table->dropIndex(['user_id']);
            $table->dropIndex(['communication_type']);
            $table->dropIndex(['status']);
            $table->dropIndex(['priority']);
            $table->dropIndex(['channel']);
            $table->dropIndex(['scheduled_at']);
            $table->dropIndex(['sent_at']);
            $table->dropIndex(['delivered_at']);
            $table->dropIndex(['opened_at']);
            $table->dropIndex(['clicked_at']);
            $table->dropIndex(['bounced_at']);
            $table->dropIndex(['unsubscribed_at']);
            $table->dropIndex(['created_at']);
            $table->dropIndex(['updated_at']);
            $table->dropIndex(['campaign_id']);
            $table->dropIndex(['segment_id']);
            $table->dropIndex(['template_id']);
            $table->dropIndex(['customer_id', 'status']);
            $table->dropIndex(['customer_id', 'communication_type']);
            $table->dropIndex(['customer_id', 'created_at']);
            $table->dropIndex(['status', 'scheduled_at']);
            $table->dropIndex(['campaign_id', 'status']);
            $table->dropIndex(['segment_id', 'status']);
            $table->dropIndex(['communication_type', 'status']);
            $table->dropIndex(['priority', 'status']);
        });
    }
};
