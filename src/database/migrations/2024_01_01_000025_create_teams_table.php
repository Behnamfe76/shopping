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
        Schema::create('teams', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->unique();
            $table->text('description')->nullable();
            $table->unsignedBigInteger('department_id');
            $table->string('location')->nullable();
            $table->integer('member_limit')->nullable();
            $table->boolean('is_active')->default(true);
            $table->enum('status', ['active', 'inactive', 'archived', 'pending', 'suspended'])->default('active');
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index('name');
            $table->index('code');
            $table->index('department_id');
            $table->index('location');
            $table->index('status');
            $table->index('is_active');
            $table->index('created_at');
            $table->index('updated_at');

            // Foreign key constraints
            $table->foreign('department_id')
                ->references('id')
                ->on('departments')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('teams');
    }
};
