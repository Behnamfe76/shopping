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
        Schema::create('employee_performance_reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('employees')->onDelete('cascade');
            $table->foreignId('reviewer_id')->constrained('users')->onDelete('cascade');
            $table->date('review_period_start');
            $table->date('review_period_end');
            $table->date('review_date');
            $table->date('next_review_date')->nullable();
            $table->decimal('overall_rating', 2, 1)->comment('1.0 to 5.0 scale');
            $table->decimal('performance_score', 5, 1)->comment('0.0 to 100.0 scale');
            $table->json('goals_achieved')->nullable();
            $table->json('goals_missed')->nullable();
            $table->text('strengths')->nullable();
            $table->text('areas_for_improvement')->nullable();
            $table->text('recommendations')->nullable();
            $table->text('employee_comments')->nullable();
            $table->text('reviewer_comments')->nullable();
            $table->enum('status', [
                'draft',
                'submitted',
                'pending_approval',
                'approved',
                'rejected',
                'overdue',
            ])->default('draft');
            $table->boolean('is_approved')->default(false);
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Indexes for performance
            $table->index(['employee_id', 'review_date']);
            $table->index(['reviewer_id', 'review_date']);
            $table->index(['status', 'review_date']);
            $table->index(['overall_rating']);
            $table->index(['performance_score']);
            $table->index(['review_period_start', 'review_period_end'], 'epr_period_index');
            $table->index(['next_review_date']);
            $table->index(['is_approved', 'approved_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employee_performance_reviews');
    }
};
