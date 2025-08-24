<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('product_meta', function (Blueprint $table) {
            $table->string('meta_type')->default('text')->after('meta_value');
            $table->boolean('is_public')->default(true)->after('meta_type');
            $table->boolean('is_searchable')->default(false)->after('is_public');
            $table->boolean('is_filterable')->default(false)->after('is_searchable');
            $table->integer('sort_order')->default(0)->after('is_filterable');
            $table->text('description')->nullable()->after('sort_order');
            $table->text('validation_rules')->nullable()->after('description');
            $table->unsignedBigInteger('created_by')->nullable()->after('validation_rules');
            $table->unsignedBigInteger('updated_by')->nullable()->after('created_by');

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
        Schema::table('product_meta', function (Blueprint $table) {
            $table->dropForeign(['created_by']);
            $table->dropForeign(['updated_by']);
            $table->dropIndex(['product_id', 'meta_key']);
            $table->dropIndex(['meta_type']);
            $table->dropIndex(['is_public']);
            $table->dropIndex(['is_searchable']);
            $table->dropIndex(['is_filterable']);
            $table->dropIndex(['created_by']);
            $table->dropIndex(['updated_by']);
            $table->dropColumn([
                'meta_type',
                'is_public',
                'is_searchable',
                'is_filterable',
                'sort_order',
                'description',
                'validation_rules',
                'created_by',
                'updated_by'
            ]);
        });
    }
};
