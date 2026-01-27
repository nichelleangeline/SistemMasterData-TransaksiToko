<?php

namespace App\Http\Controllers;

use App\Models\TableA;
use App\Models\TableB;
use App\Models\TableC;
use App\Models\TableD;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // 1. REVENUE: Total nominal_transaksi dari Table B
        $totalSales = TableB::sum('nominal_transaksi') ?? 0;

        // 2. TOTAL MASTER: Hitung baris di Table A
        $totalToko = TableA::count();

        // 3. TOKO AKTIF: Hitung Kode Toko unik di Table B yang bertransaksi
        $tokoAktif = TableB::distinct('kode_toko')->count('kode_toko');

        // 4. PENETRATION: (Toko Aktif / Total Master) * 100
        $penetration = $totalToko > 0 ? ($tokoAktif / $totalToko) * 100 : 0;

        // 5. AOV (Average Order Value): Total Revenue / Total Jumlah Baris Transaksi
        $totalTrx = TableB::count();
        $aov = $totalTrx > 0 ? $totalSales / $totalTrx : 0;

        // 6. UNCOVERED: Toko di Table A yang kode_toko_baru-nya TIDAK ADA di Table B
        $uncovered = TableA::whereNotExists(function ($query) {
            $query->select(DB::raw(1))
                  ->from('table_b')
                  ->whereRaw('table_b.kode_toko = table_a.kode_toko_baru');
        })->count();

        // 7. LEGACY USE: Transaksi di Table B yang kode_toko-nya cocok dengan kode_toko_lama di Table A
        $legacyCount = TableB::whereIn('kode_toko', function($query) {
            $query->select('kode_toko_lama')->from('table_a');
        })->count();

        // 8. VIP CUSTOMERS: Toko yang TOTAL belanjanya > Rata-rata belanja per toko
        // Hitung rata-rata belanja per toko dulu
        $avgPerStore = $tokoAktif > 0 ? $totalSales / $tokoAktif : 0;
        $vipCount = TableB::select('kode_toko')
            ->groupBy('kode_toko')
            ->having(DB::raw('SUM(nominal_transaksi)'), '>', $avgPerStore)
            ->get()
            ->count();

        // 9. SALES COUNT: Hitung baris di Table D
        $salesCount = TableD::count();

        // 10. INTEGRITY: % Transaksi di B yang kode_toko-nya ADA di kode_toko_baru Table A (Mapping Sukses)
        $mappedTrx = TableB::whereIn('kode_toko', function($query) {
            $query->select('kode_toko_baru')->from('table_a');
        })->count();
        $integrity = $totalTrx > 0 ? ($mappedTrx / $totalTrx) * 100 : 0;

        // --- CHART: Distribusi Area (Table C) ---
        $areaStats = TableC::select('area_sales', DB::raw('count(*) as jml_toko'))
            ->groupBy('area_sales')
            ->get();

        // --- MODAL PREVIEW: 10 Transaksi Terbesar (Join B & A untuk ID Resmi) ---
        $rawPreview = DB::table('table_b')
            ->leftJoin('table_a', 'table_b.kode_toko', '=', 'table_a.kode_toko_baru')
            ->select('table_b.kode_toko', 'table_a.kode_toko_baru as id_resmi', 'table_b.nominal_transaksi')
            ->orderBy('table_b.nominal_transaksi', 'desc')
            ->limit(10)
            ->get();

        return view('pages.dashboard.sales', compact(
            'totalSales', 'totalToko', 'tokoAktif', 'penetration', 
            'aov', 'uncovered', 'legacyCount', 'vipCount', 
            'salesCount', 'integrity', 'areaStats', 'rawPreview'
        ));
    }
}