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
        Schema::create('qris_dynamics', function (Blueprint $table) {
            $table->id();
            $table->foreignId('qris_static_id')->nullable()->constrained()->nullOnDelete();
            $table->string('merchant_name');
            $table->text('qris_string');
            $table->decimal('amount', 15, 2);
            $table->string('fee_type')->default('Rupiah');
            $table->decimal('fee_value', 15, 2)->default(0);
            $table->string('qr_image_path')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('qris_dynamics');
    }
};
