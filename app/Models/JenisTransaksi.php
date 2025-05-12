<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class JenisTransaksi extends Model
{
    use HasFactory;
    protected $table = 'jenis_transaksi';
    public $timestamps = false;
}
