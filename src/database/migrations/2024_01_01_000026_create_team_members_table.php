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
        Schema::create('team_members', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('team_id');
            $table->unsignedBigInteger('employee_id');
            $table->boolean('is_manager')->default(false);
            $table->date('joined_at')->nullable();
            $table->date('left_at')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();

            // Indexes
            $table->index('team_id');
            $table->index('employee_id');
            $table->index('is_manager');
            $table->index('joined_at');
            $table->index('left_at');

            // Unique constraint - an employee can only be in a team once
            $table->unique(['team_id', 'employee_id']);

            // Foreign key constraints
            $table->foreign('team_id')
                ->references('id')
                ->on('teams')
                ->onDelete('cascade');

            $table->foreign('employee_id')
                ->references('id')
                ->on('employees')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('team_members');
    }
};
