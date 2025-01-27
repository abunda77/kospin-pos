<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Order;

return new class extends Migration
{
    public function up(): void
    {
        // First add the column without unique constraint
        Schema::table('orders', function (Blueprint $table) {
            $table->string('no_order', 6)->nullable()->after('id');
        });

        // Update existing records with sequential numbers
        $orders = Order::orderBy('created_at')->get();
        $counter = 1;
        foreach ($orders as $order) {
            $order->no_order = str_pad($counter, 6, '0', STR_PAD_LEFT);
            $order->save();
            $counter++;
        }

        // Now add the unique constraint
        Schema::table('orders', function (Blueprint $table) {
            $table->unique('no_order');
            $table->string('no_order', 6)->nullable(false)->change();
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropUnique(['no_order']);
            $table->dropColumn('no_order');
        });
    }
};