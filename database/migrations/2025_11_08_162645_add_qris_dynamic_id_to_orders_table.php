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
            $table->unsignedBigInteger('qris_dynamic_id')->nullable()->after('payment_details');
            $table->foreign('qris_dynamic_id')->references('id')->on('qris_dynamics')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign(['qris_dynamic_id']);
            $table->dropColumn('qris_dynamic_id');
        });
    }
};
