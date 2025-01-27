<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // First drop the foreign key in order_products table
        Schema::table('order_products', function (Blueprint $table) {
            $table->dropForeign(['order_id']);
        });

        // Change orders.id to UUID
        Schema::table('orders', function (Blueprint $table) {
            $table->uuid('id')->change();
        });

        // Update order_products.order_id to UUID and recreate foreign key
        Schema::table('order_products', function (Blueprint $table) {
            $table->uuid('order_id')->change();
            $table->foreign('order_id')->references('id')->on('orders')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        // First drop the foreign key
        Schema::table('order_products', function (Blueprint $table) {
            $table->dropForeign(['order_id']);
        });

        // Revert orders.id back to bigIncrements
        Schema::table('orders', function (Blueprint $table) {
            $table->bigIncrements('id')->change();
        });

        // Revert order_products.order_id and recreate foreign key
        Schema::table('order_products', function (Blueprint $table) {
            $table->unsignedBigInteger('order_id')->change();
            $table->foreign('order_id')->references('id')->on('orders')->onDelete('cascade');
        });
    }
};