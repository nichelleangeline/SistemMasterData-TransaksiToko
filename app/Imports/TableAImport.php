<?php

namespace App\Imports;

use App\Models\TableA;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class TableAImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        return new TableA([
            'kode_toko_baru' => $row['kode_toko_baru'],
            'kode_toko_lama' => $row['kode_toko_lama'] ?? null,
        ]);
    }
}
