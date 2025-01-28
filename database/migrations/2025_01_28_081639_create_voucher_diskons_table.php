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
        Schema::create('voucher_diskons', function (Blueprint $table) {
            $table->id();
            $table->string('kode_voucher')->unique();
            $table->decimal('nilai_discount', 10, 2);
            $table->enum('jenis_discount', ['prosentase', 'nominal']);
            $table->timestamp('expired_time');
            $table->integer('stok_voucher');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('voucher_diskons');
    }
};
