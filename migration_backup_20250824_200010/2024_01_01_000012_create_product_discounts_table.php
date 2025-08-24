<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_discounts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->enum('discount_type', ['percent', 'fixed']);
            $table->decimal('amount', 10, 2);
            $table->date('start_date');
            $table->date('end_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_discounts');
    }
};
