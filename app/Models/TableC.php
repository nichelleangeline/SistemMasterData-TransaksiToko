<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TableC extends Model
{
    protected $table = 'table_c';
    protected $primaryKey = 'kode_toko';
    public $incrementing = false;
    protected $keyType = 'int';
    public $timestamps = false;

    protected $fillable = [
        'kode_toko',
        'area_sales',
    ];
}
