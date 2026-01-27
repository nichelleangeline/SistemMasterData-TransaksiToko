<?php

namespace App\Http\Controllers;

use App\Models\TableA;
use App\Models\TableB;
use App\Models\TableC;
use App\Models\TableD;
use Illuminate\Http\Request;

// PASTIKAN NAMA INI TIDAK ADA DI FILE LAIN
class MasterTableController extends Controller
{
    public function index(Request $r)
    {
        $sort = $r->get('sort');
        $dir  = $r->get('dir', 'asc');

        // Logic Table A
        $queryA = TableA::query();
        if ($r->filled('search_a')) {
            $queryA->where('kode_toko_baru', 'like', "%{$r->search_a}%");
        }
        $tableA = $queryA->orderBy(in_array($sort, ['kode_toko_baru', 'kode_toko_lama']) ? $sort : 'kode_toko_baru', $dir)
                         ->paginate(5, ['*'], 'page_a')->withQueryString();

        // Logic Table B
        $queryB = TableB::query();
        if ($r->filled('search_b')) {
            $queryB->where('kode_toko', 'like', "%{$r->search_b}%");
        }
        $tableB = $queryB->orderBy(in_array($sort, ['kode_toko', 'nominal_transaksi']) ? $sort : 'kode_toko', $dir)
                         ->paginate(5, ['*'], 'page_b')->withQueryString();

        // Logic Table C
        $queryC = TableC::query();
        if ($r->filled('search_c')) {
            $queryC->where('area_sales', 'like', "%{$r->search_c}%");
        }
        $tableC = $queryC->orderBy(in_array($sort, ['kode_toko', 'area_sales']) ? $sort : 'kode_toko', $dir)
                         ->paginate(5, ['*'], 'page_c')->withQueryString();

        // Logic Table D
        $queryD = TableD::query();
        if ($r->filled('search_d')) {
            $queryD->where('nama_sales', 'like', "%{$r->search_d}%");
        }
        $tableD = $queryD->orderBy(in_array($sort, ['kode_sales', 'nama_sales']) ? $sort : 'kode_sales', $dir)
                         ->paginate(5, ['*'], 'page_d')->withQueryString();

        return view('pages.tables.basic-tables', compact('tableA', 'tableB', 'tableC', 'tableD'));
    }
}