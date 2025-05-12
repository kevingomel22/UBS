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
        Schema::create('penjualan', function (Blueprint $table) {
            $table->char('no_faktur', 6)->primary();
            $table->char('kode_customer', 4);
            $table->char('kode_jenis_transaksi', 1);
            $table->date('tgl_faktur');
            $table->decimal('total_bruto', 15, 2);
            $table->decimal('total_diskon', 15, 2);
            $table->decimal('total_jumlah', 15, 2);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('penjualan');
    }
};
