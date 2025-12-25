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
        Schema::create('customer_segments', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->text('description')->nullable();
            $table->enum('type', [
                'demographic',
                'behavioral',
                'geographic',
                'psychographic',
                'transactional',
                'engagement',
                'loyalty',
                'custom',
            ]);
            $table->enum('status', [
                'active',
                'inactive',
                'draft',
                'archived',
            ])->default('active');
            $table->enum('priority', [
                'low',
                'normal',
                'high',
                'critical',
            ])->default('normal');
            $table->json('criteria')->nullable();
            $table->json('conditions')->nullable();
            $table->integer('customer_count')->default(0);
            $table->timestamp('last_calculated_at')->nullable();
            $table->unsignedBigInteger('calculated_by')->nullable();
            $table->boolean('is_automatic')->default(false);
            $table->boolean('is_dynamic')->default(false);
            $table->json('metadata')->nullable();
            $table->json('tags')->nullable();
            $table->timestamps();

            $table->index(['type', 'status']);
            $table->index(['priority', 'status']);
            $table->index(['is_automatic', 'is_dynamic']);
            $table->index('last_calculated_at');
            $table->index('customer_count');
            $table->index('created_at');

            $table->foreign('calculated_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customer_segments');
    }
};
