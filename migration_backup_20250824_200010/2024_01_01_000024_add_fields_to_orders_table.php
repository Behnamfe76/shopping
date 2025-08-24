<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->text('notes')->nullable()->after('placed_at');
            $table->string('tracking_number')->nullable()->after('notes');
            $table->timestamp('estimated_delivery')->nullable()->after('tracking_number');
            $table->timestamp('actual_delivery')->nullable()->after('estimated_delivery');
            $table->decimal('tax_amount', 10, 2)->default(0)->after('shipping_amount');
            $table->string('currency', 3)->default('USD')->after('tax_amount');
            $table->decimal('exchange_rate', 10, 6)->default(1.000000)->after('currency');
            $table->string('coupon_code')->nullable()->after('exchange_rate');
            $table->decimal('coupon_discount', 10, 2)->default(0)->after('coupon_code');
            $table->decimal('subtotal', 10, 2)->default(0)->after('coupon_discount');
            $table->decimal('grand_total', 10, 2)->default(0)->after('subtotal');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn([
                'notes',
                'tracking_number',
                'estimated_delivery',
                'actual_delivery',
                'tax_amount',
                'currency',
                'exchange_rate',
                'coupon_code',
                'coupon_discount',
                'subtotal',
                'grand_total'
            ]);
        });
    }
};
