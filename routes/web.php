<?php

use App\Http\Controllers\BarangController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\PenjualanController;
use App\Models\Customer;
use App\Models\Penjualan;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('penjualan.create');
});

// BARANG
Route::get('barang/create', [BarangController::class, 'create'])->name('barang.create');
Route::post('barang/store', [BarangController::class, 'store'])->name('barang.store');
Route::get('/search-barang', [BarangController::class, 'search'])->name('barang.search');
Route::get('barang', [BarangController::class, 'index'])->name('barang.index');
Route::get('barang/data', [BarangController::class, 'getData'])->name('barang.data');
Route::get('barang/{kode_barang}/edit', [BarangController::class, 'edit'])->name('barang.edit');
Route::put('barang/{kode_barang}', [BarangController::class, 'update'])->name('barang.update');
Route::delete('barang/{kode_barang}', [BarangController::class, 'destroy'])->name('barang.destroy');

// CUSTOMER
Route::get('customer/create', [CustomerController::class, 'create'])->name('customer.create');
Route::post('customer/store', [CustomerController::class, 'store'])->name('customer.store');
Route::get('customer', [CustomerController::class, 'index'])->name('customer.index');
Route::get('customer/data', [CustomerController::class, 'getData'])->name('customer.data');
Route::get('customer/{kode_customer}/edit', [CustomerController::class, 'edit'])->name('customer.edit');
Route::put('customer/{kode_customer}', [CustomerController::class, 'update'])->name('customer.update');
Route::delete('customer/{kode_customer}', [CustomerController::class, 'destroy'])->name('customer.destroy');


Route::get('penjualan/create', [PenjualanController::class, 'create'])->name('penjualan.create');
Route::post('penjualan/store', [PenjualanController::class, 'store'])->name('penjualan.store');
Route::get('penjualan/search', [PenjualanController::class, 'searchTransaksi']);
Route::get('penjualan/{no_faktur}', [PenjualanController::class, 'search']);
Route::delete('/penjualan/{no_faktur}', [PenjualanController::class, 'destroy']);
Route::put('/penjualan/update/{no_faktur}', [PenjualanController::class, 'update']);
Route::post('/penjualan/save', [PenjualanController::class, 'handleSave']);
Route::post('/penjualan/preview', [PenjualanController::class, 'preview'])->name('penjualan.preview');
Route::post('penjualan/print', [PenjualanController::class, 'printFaktur'])->name('penjualan.print');
Route::get('/penjualan/export-csv/{noFaktur}', [PenjualanController::class, 'exportCSV'])->name('penjualan.export');
