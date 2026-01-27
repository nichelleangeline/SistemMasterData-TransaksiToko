<?php

namespace App\Http\Controllers;

use App\Models\TableA;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Barryvdh\DomPDF\Facade\Pdf;

class TableAController extends Controller
{
    public function index(Request $r)
    {
        $query = TableA::query();

        if ($r->filled('search_a')) {
            $query->where(function ($q) use ($r) {
                $q->where('kode_toko_baru', 'like', "%{$r->search_a}%")
                  ->orWhere('kode_toko_lama', 'like', "%{$r->search_a}%");
            });
        }

        $sortField = $r->get('sort', 'kode_toko_baru');
        $sortDir   = $r->get('dir', 'asc');

        $allowedFields = ['kode_toko_baru', 'kode_toko_lama'];
        $query->orderBy(
            in_array($sortField, $allowedFields) ? $sortField : 'kode_toko_baru',
            $sortDir
        );

        $tableA = $query->paginate(10, ['*'], 'page_a')->withQueryString();

        return view('pages.tables.basic-tables', compact('tableA'));
    }

    public function create()
    {
        return view('pages.tables.table-a-create');
    }

    public function store(Request $r)
    {
        $r->validate([
            'kode_toko_baru' => 'required|numeric|unique:table_a,kode_toko_baru',
            'kode_toko_lama' => 'nullable|numeric'
        ]);

        TableA::create($r->only('kode_toko_baru', 'kode_toko_lama'));

        return redirect()->route('basic-tables')->with('success', 'Data berhasil ditambahkan');
    }

    public function edit($id)
    {
        return view('pages.tables.table-a-edit', [
            'data' => TableA::where('kode_toko_baru', $id)->firstOrFail()
        ]);
    }

    public function update(Request $r, $id)
    {
        $r->validate([
            'kode_toko_lama' => 'nullable|numeric'
        ]);

        TableA::where('kode_toko_baru', $id)
            ->update(['kode_toko_lama' => $r->kode_toko_lama]);

        return redirect()->route('basic-tables')->with('success', 'Data berhasil diupdate');
    }

    public function destroy($id)
    {
        TableA::where('kode_toko_baru', $id)->delete();
        return back()->with('success', 'Data berhasil dihapus');
    }

    public function bulkDelete(Request $r)
    {
        $r->validate(['ids' => 'required|array']);
        TableA::whereIn('kode_toko_baru', $r->ids)->delete();
        return back()->with('success', 'Data terpilih berhasil dihapus');
    }

    /* ================= IMPORT PREVIEW (FIXED) ================= */
    public function importPreview(Request $r)
{
    $r->validate([
        'file' => 'required|mimes:csv,txt'
    ]);

    $file = fopen($r->file('file')->getRealPath(), 'r');
    $header = fgetcsv($file);

    // VALIDASI HEADER
    if (!$header || !in_array('kode_toko_baru', $header)) {
        fclose($file);
        return redirect()
            ->route('table-a.create')
            ->withErrors(['file' => 'Header CSV wajib: kode_toko_baru,kode_toko_lama']);
    }

    $rows = [];
    $hasError = false;

    while ($row = fgetcsv($file)) {
        if (count($header) !== count($row)) continue;

        $data = array_combine($header, $row);

        $v = \Validator::make($data, [
            'kode_toko_baru' => 'required|numeric',
            'kode_toko_lama' => 'nullable|numeric'
        ]);

        if (
            !empty($data['kode_toko_baru']) &&
            \App\Models\TableA::where('kode_toko_baru', $data['kode_toko_baru'])->exists()
        ) {
            $v->errors()->add('kode_toko_baru', 'Sudah ada di database');
        }

        $data['error'] = $v->errors()->first();
        if ($data['error']) $hasError = true;

        $rows[] = $data;
    }

    fclose($file);

    // 🔥 PENTING: REDIRECT KE CREATE, BUKAN KE PREVIEW
    return redirect()
        ->route('table-a.create')
        ->with('previewData', $rows)
        ->with('hasError', $hasError);
}

public function importConfirm(Request $r)
    {
        $rows = json_decode($r->rows, true);
        if (!$rows) return redirect()->back()->with('error', 'Tidak ada data');

        foreach ($rows as $row) {
            if (!empty($row['error'])) continue;

            TableA::updateOrCreate(
                ['kode_toko_baru' => $row['kode_toko_baru']],
                ['kode_toko_lama' => $row['kode_toko_lama'] ?? null]
            );
        }

        return redirect()->route('basic-tables')->with('success', 'Import selesai');
    }

    public function exportCsv(): StreamedResponse
    {
        return response()->streamDownload(function () {
            $h = fopen('php://output', 'w');
            fputcsv($h, ['kode_toko_baru', 'kode_toko_lama']);

            TableA::orderBy('kode_toko_baru')->chunk(100, function ($records) use ($h) {
                foreach ($records as $r) {
                    fputcsv($h, [$r->kode_toko_baru, $r->kode_toko_lama]);
                }
            });

            fclose($h);
        }, 'table_a_' . now()->format('Ymd') . '.csv');
    }

    public function exportPdf()
    {
        $data = TableA::orderBy('kode_toko_baru')->get();
        return Pdf::loadView('exports.table-a-pdf', compact('data'))
            ->download('table_a_' . now()->format('Ymd') . '.pdf');
    }
}
