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
        Schema::create('communication_templates', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('type'); // email, sms, push_notification, etc.
            $table->string('subject')->nullable(); // For email templates
            $table->text('content'); // Template content with placeholders
            $table->string('status')->default('active'); // active, inactive, draft
            $table->json('variables')->nullable(); // Available template variables
            $table->json('settings')->nullable(); // Template-specific settings
            $table->json('metadata')->nullable(); // Additional metadata
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('communication_templates');
    }
};
