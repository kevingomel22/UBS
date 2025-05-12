<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class DetailPenjualan extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'detail_penjualan';
    protected $dates = ['deleted_at'];
    protected $primaryKey = ['no_faktur', 'kode_barang'];
    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = [
        'no_faktur',
        'kode_barang',
        'harga',
        'qty',
        'diskon',
        'bruto',
        'jumlah',
    ];
    public $timestamps = false;

    public function penjualan()
    {
        return $this->belongsTo(Penjualan::class, 'no_faktur', 'no_faktur');
    }
    public function barang()
    {
        return $this->belongsTo(Barang::class, 'kode_barang', 'kode_barang');
    }
}
