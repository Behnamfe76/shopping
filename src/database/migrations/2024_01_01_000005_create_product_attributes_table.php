<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_attributes', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('type')->default('text');
            $table->string('input_type')->default('text');
            $table->boolean('is_required')->default(false);
            $table->boolean('is_searchable')->default(false);
            $table->boolean('is_filterable')->default(false);
            $table->boolean('is_comparable')->default(false);
            $table->boolean('is_visible')->default(true);
            $table->integer('sort_order')->default(0);
            $table->text('validation_rules')->nullable();
            $table->string('default_value')->nullable();
            $table->string('unit', 50)->nullable();
            $table->string('group', 100)->nullable();
            $table->boolean('is_system')->default(false);
            $table->boolean('is_active')->default(true);
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->text('meta_keywords')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Add indexes
            $table->index(['type']);
            $table->index(['input_type']);
            $table->index(['group']);
            $table->index(['is_required']);
            $table->index(['is_searchable']);
            $table->index(['is_filterable']);
            $table->index(['is_comparable']);
            $table->index(['is_visible']);
            $table->index(['is_system']);
            $table->index(['is_active']);
            $table->index(['sort_order']);
            $table->index(['created_by']);
            $table->index(['updated_by']);

            // Add foreign key constraints
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('updated_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_attributes');
    }
};
