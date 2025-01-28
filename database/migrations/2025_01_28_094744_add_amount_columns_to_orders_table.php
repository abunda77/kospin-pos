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
            // Pastikan total_price memiliki default value
            $table->decimal('total_price', 10, 2)->default(0)->change();

            $table->decimal('subtotal_amount', 10, 2)->after('total_price')->default(0);
            $table->decimal('discount_amount', 10, 2)->after('subtotal_amount')->default(0);
            $table->decimal('total_amount', 10, 2)->after('discount_amount')->default(0);
            $table->unsignedBigInteger('voucher_id')->nullable()->after('total_amount');
            $table->foreign('voucher_id')->references('id')->on('voucher_diskons')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign(['voucher_id']);
            $table->dropColumn(['subtotal_amount', 'discount_amount', 'total_amount', 'voucher_id']);
            $table->decimal('total_price', 10, 2)->change();
        });
    }
};
