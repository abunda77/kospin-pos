<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            if (!Schema::hasColumn('orders', 'transaction_id')) {
                $table->string('transaction_id')->nullable();
            }

            if (!Schema::hasColumn('orders', 'payment_url')) {
                $table->string('payment_url')->nullable();
            }

            if (!Schema::hasColumn('orders', 'payment_details')) {
                $table->json('payment_details')->nullable();
            }

            if (!Schema::hasColumn('orders', 'subtotal_amount')) {
                $table->decimal('subtotal_amount', 12, 2)->nullable();
            }

            if (!Schema::hasColumn('orders', 'discount_amount')) {
                $table->decimal('discount_amount', 12, 2)->nullable();
            }

            if (!Schema::hasColumn('orders', 'total_amount')) {
                $table->decimal('total_amount', 12, 2)->nullable();
            }

            if (!Schema::hasColumn('orders', 'whatsapp')) {
                $table->string('whatsapp')->nullable();
            }

            if (!Schema::hasColumn('orders', 'address')) {
                $table->text('address')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn([
                'transaction_id',
                'payment_url',
                'payment_details',
                'subtotal_amount',
                'discount_amount',
                'total_amount',
                'whatsapp',
                'address'
            ]);
        });
    }
};
