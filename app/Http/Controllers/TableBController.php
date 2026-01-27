<?php

namespace App\Http\Controllers;

use App\Models\TableB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Barryvdh\DomPDF\Facade\Pdf;

class TableBController extends Controller
{
    public function index(Request $request)
    {
        $query = TableB::query();

        if ($request->filled('search_b')) {
            $query->where('kode_toko', 'like', "%{$request->search_b}%");
        }

        $sortField = $request->get('sort', 'kode_toko');
        $sortDir   = $request->get('dir', 'asc');

        if (in_array($sortField, ['kode_toko', 'nominal_transaksi'])) {
            $query->orderBy($sortField, $sortDir);
        }

        $tableB = $query->paginate(10, ['*'], 'page_b')->withQueryString();

        return view('pages.tables.basic-tables', compact('tableB'));
    }

    public function create()
    {
        return view('pages.tables.table-b-create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'kode_toko' => 'required|numeric|unique:table_b,kode_toko',
            'nominal_transaksi' => 'required|numeric|min:0'
        ]);

        TableB::create($request->only('kode_toko', 'nominal_transaksi'));

        return redirect()->route('basic-tables')
            ->with('success', 'Data Table B berhasil ditambahkan');
    }

    public function edit($id)
    {
        return view('pages.tables.table-b-edit', [
            'data' => TableB::where('kode_toko', $id)->firstOrFail()
        ]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nominal_transaksi' => 'required|numeric|min:0'
        ]);

        TableB::where('kode_toko', $id)
            ->update(['nominal_transaksi' => $request->nominal_transaksi]);

        return redirect()->route('basic-tables')
            ->with('success', 'Data Table B berhasil diupdate');
    }

    public function destroy($id)
    {
        TableB::where('kode_toko', $id)->delete();

        return back()->with('success', 'Data Table B berhasil dihapus');
    }

    public function bulkDelete(Request $request)
    {
        $request->validate(['ids' => 'required|array']);

        TableB::whereIn('kode_toko', $request->ids)->delete();

        return back()->with('success', 'Data Table B terpilih berhasil dihapus');
    }

    public function importPreview(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:csv,txt'
        ]);

        $path = $request->file('file')->getRealPath();
        $file = fopen($path, 'r');
        $header = fgetcsv($file);

        if ($header !== ['kode_toko', 'nominal_transaksi']) {
            return back()->with('error', 'Header CSV harus: kode_toko,nominal_transaksi');
        }

        $rows = [];
        $hasError = false;

        while ($row = fgetcsv($file)) {
            if (count($row) !== 2) continue;

            $data = array_combine($header, $row);

            $v = Validator::make($data, [
                'kode_toko' => 'required|numeric|unique:table_b,kode_toko',
                'nominal_transaksi' => 'required|numeric|min:0'
            ]);

            $data['error'] = $v->errors()->first();
            if ($data['error']) $hasError = true;

            $rows[] = $data;
        }

        fclose($file);

        return redirect()
            ->route('table-b.create')
            ->with('previewData', $rows)
            ->with('hasError', $hasError);
    }

    public function importConfirm(Request $request)
    {
        $rows = json_decode($request->rows, true);

        if (!$rows) {
            return back()->with('error', 'Data import tidak ditemukan');
        }

        foreach ($rows as $row) {
            if (!empty($row['error'])) continue;

            TableB::create([
                'kode_toko' => $row['kode_toko'],
                'nominal_transaksi' => $row['nominal_transaksi']
            ]);
        }

        return redirect()->route('basic-tables')
            ->with('success', 'Import Table B berhasil');
    }

    public function exportCsv(): StreamedResponse
    {
        return response()->streamDownload(function () {
            $h = fopen('php://output', 'w');
            fputcsv($h, ['kode_toko', 'nominal_transaksi']);

            TableB::orderBy('kode_toko')->chunk(100, function ($records) use ($h) {
                foreach ($records as $r) {
                    fputcsv($h, [$r->kode_toko, $r->nominal_transaksi]);
                }
            });

            fclose($h);
        }, 'table_b_' . now()->format('Ymd') . '.csv');
    }

    public function exportPdf()
    {
        $data = TableB::orderBy('kode_toko')->get();

        return Pdf::loadView('exports.table-b-pdf', compact('data'))
            ->download('table_b_' . now()->format('Ymd') . '.pdf');
    }
}
