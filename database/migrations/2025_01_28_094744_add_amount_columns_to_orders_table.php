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

            if (!Schema::hasColumn('orders', 'subtotal_amount')) {
                $table->decimal('subtotal_amount', 10, 2)->default(0);
            }

            if (!Schema::hasColumn('orders', 'discount_amount')) {
                $table->decimal('discount_amount', 10, 2)->default(0);
            }

            if (!Schema::hasColumn('orders', 'total_amount')) {
                $table->decimal('total_amount', 10, 2)->default(0);
            }

            if (!Schema::hasColumn('orders', 'voucher_id')) {
                $table->unsignedBigInteger('voucher_id')->nullable();
                $table->foreign('voucher_id')->references('id')->on('voucher_diskons')->nullOnDelete();
            }
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
