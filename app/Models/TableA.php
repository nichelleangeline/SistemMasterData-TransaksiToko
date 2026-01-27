<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TableA extends Model
{
    protected $table = 'table_a';
    protected $primaryKey = 'kode_toko_baru';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'kode_toko_baru',
        'kode_toko_lama',
    ];

    // transaksi dari toko lama
    public function transaksiLama()
    {
        return $this->hasMany(TableB::class, 'kode_toko', 'kode_toko_lama');
    }

    // transaksi dari toko baru
    public function transaksiBaru()
    {
        return $this->hasMany(TableB::class, 'kode_toko', 'kode_toko_baru');
    }

    // area (WAJIB lewat toko baru)
    public function area()
    {
        return $this->belongsTo(TableC::class, 'kode_toko_baru', 'kode_toko');
    }
}
