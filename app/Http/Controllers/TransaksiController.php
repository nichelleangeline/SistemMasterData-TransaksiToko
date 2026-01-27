<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\Models\TableA;
use App\Models\TableB;

class TransaksiController extends Controller
{
    /**
     * =========================
     * LIST TRANSAKSI
     * =========================
     * View: pages.tables.table-b-import
     */
    public function index()
    {
        /*
         * Ambil transaksi + mapping toko lama/baru + area
         * sesuai logika bisnis yang benar
         */
        $data = DB::table('table_b as b')
            ->leftJoin('table_a as a', function ($join) {
                $join->on('b.kode_toko', '=', 'a.kode_toko_lama')
                     ->orOn('b.kode_toko', '=', 'a.kode_toko_baru');
            })
            ->leftJoin('table_c as c', 'c.kode_toko', '=', DB::raw('COALESCE(a.kode_toko_baru, b.kode_toko)'))
            ->select(
                DB::raw('COALESCE(a.kode_toko_baru, b.kode_toko) as kode_toko_final'),
                'b.kode_toko as kode_toko_asli',
                'c.area_sales',
                'b.nominal_transaksi'
            )
            ->orderBy('kode_toko_final')
            ->get();

        // PAKAI VIEW YANG SUDAH ADA
        return view('pages.tables.table-b-import', compact('data'));
    }

    /**
     * =========================
     * FORM CREATE TRANSAKSI
     * =========================
     * View: pages.tables.table-b-create
     */
    public function create()
    {
        // Ambil daftar TOKO BARU sebagai sumber input
        $tokoBaru = TableA::orderBy('kode_toko_baru')->get();

        // PAKAI VIEW YANG SUDAH ADA
        return view('pages.tables.table-b-create', compact('tokoBaru'));
    }

    /**
     * =========================
     * SIMPAN TRANSAKSI
     * =========================
     */
    public function store(Request $request)
    {
        $request->validate([
            'kode_toko_baru'     => 'required|exists:table_a,kode_toko_baru',
            'nominal_transaksi' => 'required|numeric|min:0'
        ]);

        // Ambil mapping toko
        $mapping = TableA::where('kode_toko_baru', $request->kode_toko_baru)->first();

        // LOGIKA INTI:
        // kalau ada toko lama → simpan transaksi ke kode toko lama
        // kalau tidak → simpan ke toko baru
        $kodeDipakai = $mapping->kode_toko_lama ?? $mapping->kode_toko_baru;

        TableB::create([
            'kode_toko'          => $kodeDipakai,
            'nominal_transaksi'  => $request->nominal_transaksi
        ]);

        return redirect()
            ->route('transaksi.index')
            ->with('success', 'Transaksi berhasil disimpan');
    }
}
