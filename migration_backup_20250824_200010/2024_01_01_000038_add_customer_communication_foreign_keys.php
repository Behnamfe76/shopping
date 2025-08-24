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
            // Add foreign key constraints if they don't exist
            if (!Schema::hasColumn('customer_communications', 'campaign_id')) {
                $table->foreignId('campaign_id')->nullable()->constrained('campaigns')->onDelete('set null');
            }
            
            if (!Schema::hasColumn('customer_communications', 'segment_id')) {
                $table->foreignId('segment_id')->nullable()->constrained('customer_segments')->onDelete('set null');
            }
            
            if (!Schema::hasColumn('customer_communications', 'template_id')) {
                $table->foreignId('template_id')->nullable()->constrained('communication_templates')->onDelete('set null');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customer_communications', function (Blueprint $table) {
            // Drop foreign key constraints
            $table->dropForeign(['campaign_id']);
            $table->dropForeign(['segment_id']);
            $table->dropForeign(['template_id']);
            
            // Drop columns
            $table->dropColumn(['campaign_id', 'segment_id', 'template_id']);
        });
    }
};
