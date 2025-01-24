<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->foreignId('anggota_id')
                  ->nullable()
                  ->after('payment_method_id')
                  ->constrained('anggotas')
                  ->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign(['anggota_id']);
            $table->dropColumn('anggota_id');
        });
    }
};
