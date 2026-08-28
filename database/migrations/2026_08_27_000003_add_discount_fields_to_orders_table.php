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
        Schema::table('orders', function (Blueprint $table) {
            $table->decimal('subtotal_amount', 10, 2)->nullable()->after('postal_code');
            $table->decimal('discount_amount', 10, 2)->default(0.00)->after('subtotal_amount');
            $table->string('coupon_code')->nullable()->after('discount_amount');
            $table->decimal('tax_amount', 10, 2)->default(0.00)->after('coupon_code');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['subtotal_amount', 'discount_amount', 'coupon_code', 'tax_amount']);
        });
    }
};
