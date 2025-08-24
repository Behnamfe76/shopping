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
        Schema::create('customer_preferences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained('customers')->onDelete('cascade');
            $table->string('preference_key', 255)->index();
            $table->text('preference_value')->nullable();
            $table->enum('preference_type', ['string', 'integer', 'float', 'boolean', 'json', 'array', 'object'])->default('string');
            $table->boolean('is_active')->default(true)->index();
            $table->text('description')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Composite indexes for efficient querying
            $table->unique(['customer_id', 'preference_key'], 'customer_preference_unique');
            $table->index(['customer_id', 'preference_type'], 'customer_preference_type_index');
            $table->index(['customer_id', 'is_active'], 'customer_preference_active_index');
            $table->index(['preference_key', 'is_active'], 'preference_key_active_index');
            $table->index(['preference_type', 'is_active'], 'preference_type_active_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customer_preferences');
    }
};
