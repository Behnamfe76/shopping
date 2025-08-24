<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('brands', function (Blueprint $table) {
            $table->text('description')->nullable()->after('slug');
            $table->string('website')->nullable()->after('description');
            $table->string('email')->nullable()->after('website');
            $table->string('phone')->nullable()->after('email');
            $table->integer('founded_year')->nullable()->after('phone');
            $table->string('headquarters')->nullable()->after('founded_year');
            $table->string('logo_url')->nullable()->after('headquarters');
            $table->string('banner_url')->nullable()->after('logo_url');
            $table->string('meta_title')->nullable()->after('banner_url');
            $table->text('meta_description')->nullable()->after('meta_title');
            $table->text('meta_keywords')->nullable()->after('meta_description');
            $table->boolean('is_active')->default(true)->after('meta_keywords');
            $table->boolean('is_featured')->default(false)->after('is_active');
            $table->integer('sort_order')->default(0)->after('is_featured');
        });
    }

    public function down(): void
    {
        Schema::table('brands', function (Blueprint $table) {
            $table->dropColumn([
                'description',
                'website',
                'email',
                'phone',
                'founded_year',
                'headquarters',
                'logo_url',
                'banner_url',
                'meta_title',
                'meta_description',
                'meta_keywords',
                'is_active',
                'is_featured',
                'sort_order'
            ]);
        });
    }
};
