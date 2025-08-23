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
        Schema::table('providers', function (Blueprint $table) {
            // Add foreign key for user_id
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

            // Add foreign key for country (assuming countries table exists)
            // $table->foreign('country')->references('code')->on('countries')->onDelete('restrict');

            // Add foreign key for state (assuming states table exists)
            // $table->foreign('state')->references('code')->on('states')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('providers', function (Blueprint $table) {
            // Drop foreign keys
            $table->dropForeign(['user_id']);
            // $table->dropForeign(['country']);
            // $table->dropForeign(['state']);
        });
    }
};
