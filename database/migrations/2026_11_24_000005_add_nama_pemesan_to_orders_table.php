<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tambah kolom nama_pemesan (nullable) ke orders.
     * Sesuai panduan MED-WEB-01 Modul 3: form pesanan memuat
     * "Nama Pemesan / Catatan Meja". Kolom dibuat nullable agar
     * pesanan lama (yang sudah ada) tetap valid.
     */
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('nama_pemesan')->nullable()->after('nomor_meja');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('nama_pemesan');
        });
    }
};
