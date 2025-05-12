<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Customer extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $primaryKey = 'kode_customer';
    protected $table = 'customer';
    public $incrementing = false;
    protected $fillable = [
        'kode_customer',
        'nama_customer',
    ];
    public $timestamps = false;
}
