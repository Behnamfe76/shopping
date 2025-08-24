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
        Schema::create('employee_emergency_contacts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('employees')->onDelete('cascade');
            $table->string('contact_name', 100);
            $table->enum('relationship', ['spouse', 'parent', 'child', 'sibling', 'friend', 'other']);
            $table->string('phone_primary', 20);
            $table->string('phone_secondary', 20)->nullable();
            $table->string('email', 255)->nullable();
            $table->text('address')->nullable();
            $table->string('city', 100)->nullable();
            $table->string('state', 100)->nullable();
            $table->string('postal_code', 20)->nullable();
            $table->string('country', 100)->nullable();
            $table->boolean('is_primary')->default(false);
            $table->boolean('is_active')->default(true);
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Indexes for performance
            $table->index('employee_id');
            $table->index('relationship');
            $table->index('contact_name');
            $table->index('phone_primary');
            $table->index('phone_secondary');
            $table->index('email');
            $table->index('city');
            $table->index('state');
            $table->index('country');
            $table->index('is_primary');
            $table->index('is_active');
            $table->index(['employee_id', 'is_primary']);
            $table->index(['employee_id', 'is_active']);
            $table->index(['employee_id', 'relationship']);
            $table->index('created_at');
            $table->index('updated_at');

            // Unique constraint to ensure only one primary contact per employee
            $table->unique(['employee_id', 'is_primary'], 'unique_primary_contact_per_employee');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employee_emergency_contacts');
    }
};
