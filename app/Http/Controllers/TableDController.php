<?php

namespace App\Http\Controllers;

use App\Models\TableD;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Barryvdh\DomPDF\Facade\Pdf;

class TableDController extends Controller
{
    public function index(Request $request)
    {
        $query = TableD::query();

        if ($request->filled('search_d')) {
            $query->where(function ($q) use ($request) {
                $q->where('kode_sales', 'like', "%{$request->search_d}%")
                  ->orWhere('nama_sales', 'like', "%{$request->search_d}%");
            });
        }

        $sortField = $request->get('sort', 'kode_sales');
        $sortDir   = $request->get('dir', 'asc');

        if (in_array($sortField, ['kode_sales', 'nama_sales'])) {
            $query->orderBy($sortField, $sortDir);
        }

        $tableD = $query->paginate(10, ['*'], 'page_d')->withQueryString();

        return view('pages.tables.basic-tables', compact('tableD'));
    }

    public function create()
    {
        return view('pages.tables.table-d-create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'kode_sales' => 'required|string|unique:table_d,kode_sales',
            'nama_sales' => 'required|string',
        ]);

        TableD::create([
            'kode_sales' => (string) $request->kode_sales,
            'nama_sales' => $request->nama_sales,
        ]);

        return redirect()->route('basic-tables')
            ->with('success', 'Data Sales berhasil ditambahkan');
    }

    public function edit($id)
    {
        return view('pages.tables.table-d-edit', [
            'data' => TableD::where('kode_sales', (string) $id)->firstOrFail()
        ]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_sales' => 'required|string',
        ]);

        TableD::where('kode_sales', (string) $id)
            ->update(['nama_sales' => $request->nama_sales]);

        return redirect()->route('basic-tables')
            ->with('success', 'Data Sales berhasil diupdate');
    }

    public function destroy($id)
    {
        TableD::where('kode_sales', (string) $id)->delete();

        return back()->with('success', 'Data Sales berhasil dihapus');
    }

    public function bulkDelete(Request $request)
    {
        $request->validate(['ids' => 'required|array']);

        $ids = array_map('strval', $request->ids);

        TableD::whereIn('kode_sales', $ids)->delete();

        return back()->with('success', 'Data terpilih berhasil dihapus');
    }

    public function importPreview(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:csv,txt'
        ]);

        $path = $request->file('file')->getRealPath();
        $file = fopen($path, 'r');
        $header = fgetcsv($file);

        if ($header !== ['kode_sales', 'nama_sales']) {
            fclose($file);
            return back()->with('error', 'Header CSV harus: kode_sales,nama_sales');
        }

        $rows = [];
        $hasError = false;

        while ($row = fgetcsv($file)) {
            if (count($row) !== 2) continue;

            $data = array_combine($header, $row);

            $v = Validator::make($data, [
                'kode_sales' => 'required|string|unique:table_d,kode_sales',
                'nama_sales' => 'required|string',
            ]);

            $data['error'] = $v->errors()->first();
            if ($data['error']) $hasError = true;

            $rows[] = $data;
        }

        fclose($file);

        return redirect()
            ->route('table-d.create')
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

            TableD::create([
                'kode_sales' => (string) $row['kode_sales'],
                'nama_sales' => $row['nama_sales'],
            ]);
        }

        return redirect()->route('basic-tables')
            ->with('success', 'Import Sales berhasil');
    }

    public function exportCsv(): StreamedResponse
    {
        return response()->streamDownload(function () {
            $h = fopen('php://output', 'w');
            fputcsv($h, ['kode_sales', 'nama_sales']);

            TableD::orderBy('kode_sales')->chunk(100, function ($records) use ($h) {
                foreach ($records as $r) {
                    fputcsv($h, [$r->kode_sales, $r->nama_sales]);
                }
            });

            fclose($h);
        }, 'table_d_' . now()->format('Ymd') . '.csv');
    }

    public function exportPdf()
    {
        $data = TableD::orderBy('kode_sales')->get();

        return Pdf::loadView('exports.table-d-pdf', compact('data'))
            ->download('table_d_' . now()->format('Ymd') . '.pdf');
    }
}
