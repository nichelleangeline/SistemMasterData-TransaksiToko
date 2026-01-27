<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TableD extends Model
{
    protected $table = 'table_d';
    protected $primaryKey = 'kode_sales';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'kode_sales',
        'nama_sales',
    ];
}
