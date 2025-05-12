<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Penjualan extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $table = 'penjualan';
    protected $primaryKey = 'no_faktur';
    public $incrementing = false;
    protected $fillable = [
        'no_faktur',
        'kode_customer',
        'kode_jenis_transaksi',
        'tgl_faktur',
        'total_bruto',
        'total_diskon',
        'total_jumlah',
    ];
    public $timestamps = false;
    public function customer()
    {
        return $this->belongsTo(Customer::class, 'kode_customer', 'kode_customer');
    }

    public function jenisTransaksi()
    {
        // Menyatakan bahwa penjualan memiliki satu jenis transaksi melalui 'kode_jenis_transaksi'
        return $this->belongsTo(JenisTransaksi::class, 'kode_jenis_transaksi', 'kode_jenis_transaksi');
    }

    public function detailPenjualan()
    {
        return $this->hasMany(DetailPenjualan::class, 'no_faktur', 'no_faktur');
    }
}
