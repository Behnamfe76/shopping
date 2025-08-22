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
        Schema::table('employees', function (Blueprint $table) {
            // Add indexes for better query performance
            $table->index('user_id');
            $table->index('employee_number');
            $table->index('email');
            $table->index('phone');
            $table->index('status');
            $table->index('employment_type');
            $table->index('department');
            $table->index('position');
            $table->index('manager_id');
            $table->index('hire_date');
            $table->index('termination_date');
            $table->index('performance_rating');
            $table->index('last_review_date');
            $table->index('next_review_date');
            $table->index('created_at');
            $table->index('updated_at');

            // Composite indexes for common queries
            $table->index(['status', 'department']);
            $table->index(['status', 'employment_type']);
            $table->index(['department', 'position']);
            $table->index(['hire_date', 'status']);
            $table->index(['performance_rating', 'status']);
            $table->index(['manager_id', 'status']);

            // Full-text search indexes
            $table->fullText(['first_name', 'last_name', 'email']);
            $table->fullText(['skills', 'certifications']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            // Remove indexes
            $table->dropIndex(['user_id']);
            $table->dropIndex(['employee_number']);
            $table->dropIndex(['email']);
            $table->dropIndex(['phone']);
            $table->dropIndex(['status']);
            $table->dropIndex(['employment_type']);
            $table->dropIndex(['department']);
            $table->dropIndex(['position']);
            $table->dropIndex(['manager_id']);
            $table->dropIndex(['hire_date']);
            $table->dropIndex(['termination_date']);
            $table->dropIndex(['performance_rating']);
            $table->dropIndex(['last_review_date']);
            $table->dropIndex(['next_review_date']);
            $table->dropIndex(['created_at']);
            $table->dropIndex(['updated_at']);

            // Remove composite indexes
            $table->dropIndex(['status', 'department']);
            $table->dropIndex(['status', 'employment_type']);
            $table->dropIndex(['department', 'position']);
            $table->dropIndex(['hire_date', 'status']);
            $table->dropIndex(['performance_rating', 'status']);
            $table->dropIndex(['manager_id', 'status']);

            // Remove full-text indexes
            $table->dropFullText(['first_name', 'last_name', 'email']);
            $table->dropFullText(['skills', 'certifications']);
        });
    }
};
