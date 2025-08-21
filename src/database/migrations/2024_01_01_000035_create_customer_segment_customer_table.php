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
        Schema::create('customer_segment_customer', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('customer_segment_id');
            $table->unsignedBigInteger('customer_id');
            $table->timestamp('added_at')->useCurrent();
            $table->unsignedBigInteger('added_by')->nullable();
            $table->timestamps();

            $table->unique(['customer_segment_id', 'customer_id']);
            $table->index(['customer_segment_id', 'added_at']);
            $table->index(['customer_id', 'added_at']);

            $table->foreign('customer_segment_id')->references('id')->on('customer_segments')->onDelete('cascade');
            $table->foreign('customer_id')->references('id')->on('customers')->onDelete('cascade');
            $table->foreign('added_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customer_segment_customer');
    }
};
