<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_meta', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->string('meta_key');
            $table->text('meta_value');
            $table->string('meta_type')->default('text');
            $table->boolean('is_public')->default(true);
            $table->boolean('is_searchable')->default(false);
            $table->boolean('is_filterable')->default(false);
            $table->integer('sort_order')->default(0);
            $table->text('description')->nullable();
            $table->text('validation_rules')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();

            // Add indexes for better performance
            $table->index(['product_id', 'meta_key']);
            $table->index(['meta_type']);
            $table->index(['is_public']);
            $table->index(['is_searchable']);
            $table->index(['is_filterable']);
            $table->index(['created_by']);
            $table->index(['updated_by']);

            // Add foreign key constraints
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('updated_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_meta');
    }
};
