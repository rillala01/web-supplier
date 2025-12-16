<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Produk extends Model
{
    use HasFactory;
    protected $fillable = ['nama_produk', 'jumlah_stok', 'harga_produk'];

    // relasi
    public function penjualans()
    {
        return $this->hasMany(Penjualan::class, 'produk_id');
    }

}
