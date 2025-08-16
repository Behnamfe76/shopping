<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('product_attributes', function (Blueprint $table) {
            $table->text('description')->nullable()->after('slug');
            $table->string('type')->default('text')->after('description');
            $table->string('input_type')->default('text')->after('type');
            $table->boolean('is_required')->default(false)->after('input_type');
            $table->boolean('is_searchable')->default(false)->after('is_required');
            $table->boolean('is_filterable')->default(false)->after('is_searchable');
            $table->boolean('is_comparable')->default(false)->after('is_filterable');
            $table->boolean('is_visible')->default(true)->after('is_comparable');
            $table->integer('sort_order')->default(0)->after('is_visible');
            $table->text('validation_rules')->nullable()->after('sort_order');
            $table->string('default_value')->nullable()->after('validation_rules');
            $table->string('unit', 50)->nullable()->after('default_value');
            $table->string('group', 100)->nullable()->after('unit');
            $table->boolean('is_system')->default(false)->after('group');
            $table->boolean('is_active')->default(true)->after('is_system');
            $table->string('meta_title')->nullable()->after('is_active');
            $table->text('meta_description')->nullable()->after('meta_title');
            $table->text('meta_keywords')->nullable()->after('meta_description');
            $table->unsignedBigInteger('created_by')->nullable()->after('meta_keywords');
            $table->unsignedBigInteger('updated_by')->nullable()->after('created_by');

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
        Schema::table('product_attributes', function (Blueprint $table) {
            $table->dropForeign(['created_by']);
            $table->dropForeign(['updated_by']);

            $table->dropIndex(['type']);
            $table->dropIndex(['input_type']);
            $table->dropIndex(['group']);
            $table->dropIndex(['is_required']);
            $table->dropIndex(['is_searchable']);
            $table->dropIndex(['is_filterable']);
            $table->dropIndex(['is_comparable']);
            $table->dropIndex(['is_visible']);
            $table->dropIndex(['is_system']);
            $table->dropIndex(['is_active']);
            $table->dropIndex(['sort_order']);
            $table->dropIndex(['created_by']);
            $table->dropIndex(['updated_by']);

            $table->dropColumn([
                'description',
                'type',
                'input_type',
                'is_required',
                'is_searchable',
                'is_filterable',
                'is_comparable',
                'is_visible',
                'sort_order',
                'validation_rules',
                'default_value',
                'unit',
                'group',
                'is_system',
                'is_active',
                'meta_title',
                'meta_description',
                'meta_keywords',
                'created_by',
                'updated_by',
            ]);
        });
    }
};

