<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payment_methods', function (Blueprint $table) {
            if (!Schema::hasColumn('payment_methods', 'gateway')) {
                $table->string('gateway')->nullable()->after('is_cash');
            }

            if (!Schema::hasColumn('payment_methods', 'gateway_config')) {
                $table->json('gateway_config')->nullable()->after('gateway');
            }

            if (!Schema::hasColumn('payment_methods', 'is_active')) {
                $table->boolean('is_active')->default(true)->after('gateway_config');
            }
        });
    }

    public function down(): void
    {
        Schema::table('payment_methods', function (Blueprint $table) {
            $table->dropColumn(['gateway', 'gateway_config', 'is_active']);
        });
    }
};
