<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Penjualan extends Model
{
    use HasFactory;
    protected $fillable = ['produk_id', 'jumlah', 'total_harga'];
    // relasi
    public function produk()
    {
        return $this->belongsTo(Produk::class, 'produk_id');    
    }
}
