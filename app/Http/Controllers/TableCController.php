<?php

namespace App\Http\Controllers;

use App\Models\TableA;
use App\Models\TableC;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Barryvdh\DomPDF\Facade\Pdf;

class TableCController extends Controller
{
    public function index(Request $request)
    {
        $tableC = TableC::orderBy('kode_toko')
            ->paginate(10, ['*'], 'page_c')
            ->withQueryString();

        return view('pages.tables.basic-tables', compact('tableC'));
    }

    public function create()
    {
        return view('pages.tables.table-c-create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'kode_toko'   => 'required|numeric|exists:table_a,kode_toko_baru|unique:table_c,kode_toko',
            'area_sales' => 'required|string|max:10',
        ]);

        TableC::create($request->only('kode_toko', 'area_sales'));

        return redirect()->route('basic-tables')
            ->with('success', 'Data Table C berhasil ditambahkan');
    }

    public function edit($id)
    {
        return view('pages.tables.table-c-edit', [
            'data' => TableC::where('kode_toko', $id)->firstOrFail()
        ]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'area_sales' => 'required|string|max:10',
        ]);

        TableC::where('kode_toko', $id)
            ->update(['area_sales' => $request->area_sales]);

        return redirect()->route('basic-tables')
            ->with('success', 'Data Table C berhasil diupdate');
    }

    public function destroy($id)
    {
        TableC::where('kode_toko', $id)->delete();

        return back()->with('success', 'Data Table C berhasil dihapus');
    }

    public function bulkDelete(Request $request)
    {
        $request->validate(['ids' => 'required|array']);

        TableC::whereIn('kode_toko', $request->ids)->delete();

        return back()->with('success', 'Data Table C terpilih berhasil dihapus');
    }

    public function importPreview(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:csv,txt'
        ]);

        $path = $request->file('file')->getRealPath();
        $file = fopen($path, 'r');
        $header = fgetcsv($file);

        if ($header !== ['kode_toko', 'area_sales']) {
            return back()->with('error', 'Header CSV harus: kode_toko,area_sales');
        }

        $rows = [];
        $hasError = false;

        while ($row = fgetcsv($file)) {
            if (count($row) !== 2) continue;

            $data = array_combine($header, $row);

            $v = Validator::make($data, [
                'kode_toko'   => 'required|numeric|exists:table_a,kode_toko_baru|unique:table_c,kode_toko',
                'area_sales' => 'required|string|max:10',
            ]);

            $data['error'] = $v->errors()->first();
            if ($data['error']) $hasError = true;

            $rows[] = $data;
        }

        fclose($file);

        return redirect()
            ->route('table-c.create')
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

            TableC::create([
                'kode_toko'   => $row['kode_toko'],
                'area_sales' => $row['area_sales'],
            ]);
        }

        return redirect()->route('basic-tables')
            ->with('success', 'Import Table C berhasil');
    }

    public function exportCsv(): StreamedResponse
    {
        return response()->streamDownload(function () {
            $h = fopen('php://output', 'w');
            fputcsv($h, ['kode_toko', 'area_sales']);

            TableC::orderBy('kode_toko')->chunk(100, function ($records) use ($h) {
                foreach ($records as $r) {
                    fputcsv($h, [$r->kode_toko, $r->area_sales]);
                }
            });

            fclose($h);
        }, 'table_c_' . now()->format('Ymd') . '.csv');
    }

    public function exportPdf()
    {
        $data = TableC::orderBy('kode_toko')->get();

        return Pdf::loadView('exports.table-c-pdf', compact('data'))
            ->download('table_c_' . now()->format('Ymd') . '.pdf');
    }
}
