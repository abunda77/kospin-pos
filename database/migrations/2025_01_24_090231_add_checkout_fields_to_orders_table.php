<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('whatsapp')->nullable();
            $table->text('address')->nullable();
            $table->enum('status', ['pending', 'processing', 'completed', 'cancelled'])
                  ->default('pending');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['whatsapp', 'address', 'status']);
        });
    }
};
