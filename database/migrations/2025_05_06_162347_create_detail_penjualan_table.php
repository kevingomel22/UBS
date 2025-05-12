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
        Schema::create('detail_penjualan', function (Blueprint $table) {
            $table->char('no_faktur', 6);
            $table->char('kode_barang', 10);
            $table->decimal('harga', 15, 2);
            $table->decimal('qty', 15, 2);
            $table->decimal('diskon', 15, 2);
            $table->decimal('bruto', 15, 2);
            $table->decimal('jumlah', 15, 2);
            $table->primary(['no_faktur', 'kode_barang']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detail_penjualan');
    }
};
