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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_number')->unique();
            $table->foreignId('user_id')->constrained()->onDelete('restrict');
            $table->enum('tipe_pesanan', ['dine_in', 'takeaway']);
            $table->string('nomor_meja')->nullable();
            $table->decimal('total_subtotal', 12, 2);
            $table->decimal('pajak_nominal', 12, 2);
            $table->decimal('diskon_nominal', 12, 2)->default(0);
            $table->decimal('grand_total', 12, 2);
            $table->enum('metode_bayar', ['tunai', 'qris', 'transfer', 'debit']);
            $table->decimal('uang_dibayar', 12, 2);
            $table->decimal('uang_kembali', 12, 2)->default(0);
            $table->enum('status', ['diproses', 'selesai', 'dibatalkan'])->default('diproses');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
