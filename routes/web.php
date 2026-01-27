<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MasterTableController;
use App\Http\Controllers\TransaksiController;

use App\Http\Controllers\TableAController;
use App\Http\Controllers\TableBController;
use App\Http\Controllers\TableCController;
use App\Http\Controllers\TableDController;

/*
|--------------------------------------------------------------------------
| DASHBOARD
|--------------------------------------------------------------------------
*/
Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
Route::get('/export-kpi/{type}', [DashboardController::class, 'exportKpi'])->name('kpi.export');

/*
|--------------------------------------------------------------------------
| MASTER TABLE VIEW (ADMIN / DEBUG)
|--------------------------------------------------------------------------
*/
Route::get('/basic-tables', [MasterTableController::class, 'index'])
    ->name('basic-tables');

/*
|--------------------------------------------------------------------------
| TRANSAKSI (SATU-SATUNYA CARA INPUT TRANSAKSI)
|--------------------------------------------------------------------------
*/
Route::prefix('transaksi')->name('transaksi.')->group(function () {
    Route::get('/', [TransaksiController::class, 'index'])->name('index');
    Route::get('/create', [TransaksiController::class, 'create'])->name('create');
    Route::post('/', [TransaksiController::class, 'store'])->name('store');
});

/*
|--------------------------------------------------------------------------
| TABLE A (MAPPING TOKO)
|--------------------------------------------------------------------------
*/
Route::prefix('table-a')->name('table-a.')->group(function () {

    Route::get('/create', [TableAController::class, 'create'])->name('create');
    Route::post('/store', [TableAController::class, 'store'])->name('store');

    // DEFENSIVE: kalau ada GET nyasar
    Route::get('/import/preview', function () {
        return redirect()->route('table-a.create');
    });

    Route::post('/import/preview', [TableAController::class, 'importPreview'])
        ->name('import.preview');

    Route::post('/import/confirm', [TableAController::class, 'importConfirm'])
        ->name('import.confirm');

    Route::get('/{id}/edit', [TableAController::class, 'edit'])->name('edit');
    Route::put('/{id}', [TableAController::class, 'update'])->name('update');
    Route::delete('/{id}', [TableAController::class, 'destroy'])->name('destroy');

    Route::post('/bulk-delete', [TableAController::class, 'bulkDelete'])->name('bulk-delete');

    Route::get('/export/csv', [TableAController::class, 'exportCsv'])->name('export.csv');
    Route::get('/export/pdf', [TableAController::class, 'exportPdf'])->name('export.pdf');
});

/*
|--------------------------------------------------------------------------
| TABLE B (TRANSAKSI MENTAH – DIJAGA KETAT)
|--------------------------------------------------------------------------
*/
/*
|--------------------------------------------------------------------------
| TABLE B (TRANSAKSI MENTAH – DEFENSIVE ROUTES)
|--------------------------------------------------------------------------
*/
Route::prefix('table-b')->name('table-b.')->group(function () {

    // LIST
    Route::get('/', [TableBController::class, 'index'])->name('index');

    // =========================
    // DEFENSIVE ROUTES (BIAR BLADE LAMA AMAN)
    // =========================

    // create → arahkan ke transaksi
    Route::get('/create', function () {
        return redirect()->route('transaksi.create');
    })->name('create');

    Route::post('/store', function () {
        return redirect()->route('transaksi.create');
    })->name('store');

    // edit → TIDAK BOLEH EDIT TRANSAKSI MENTAH
    Route::get('/{id}/edit', function () {
        return redirect()->route('table-b.index')
            ->with('error', 'Transaksi tidak bisa diedit langsung.');
    })->name('edit');

    // update → block
    Route::put('/{id}', function () {
        return redirect()->route('table-b.index')
            ->with('error', 'Update transaksi diblokir.');
    })->name('update');

    // delete (boleh, admin only)
    Route::delete('/{id}', [TableBController::class, 'destroy'])->name('destroy');

    Route::post('/bulk-delete', [TableBController::class, 'bulkDelete'])->name('bulk-delete');

    // =========================
    // IMPORT (KALAU MASIH DIPAKAI)
    // =========================
    Route::get('/import/preview', function () {
        return redirect()->route('table-b.index');
    });

    Route::post('/import/preview', [TableBController::class, 'importPreview'])
        ->name('import.preview');

    Route::post('/import/confirm', [TableBController::class, 'importConfirm'])
        ->name('import.confirm');

    // EXPORT
    Route::get('/export/csv', [TableBController::class, 'exportCsv'])->name('export.csv');
    Route::get('/export/pdf', [TableBController::class, 'exportPdf'])->name('export.pdf');
});

/*
|--------------------------------------------------------------------------
| TABLE C (AREA TOKO BARU)
|--------------------------------------------------------------------------
*/
Route::prefix('table-c')->name('table-c.')->group(function () {

    Route::get('/create', [TableCController::class, 'create'])->name('create');
    Route::post('/store', [TableCController::class, 'store'])->name('store');

    Route::get('/{id}/edit', [TableCController::class, 'edit'])->name('edit');
    Route::put('/{id}', [TableCController::class, 'update'])->name('update');
    Route::delete('/{id}', [TableCController::class, 'destroy'])->name('destroy');

    Route::post('/bulk-delete', [TableCController::class, 'bulkDelete'])->name('bulk-delete');

    Route::get('/import/preview', function () {
        return redirect()->route('table-c.create');
    });

    Route::post('/import/preview', [TableCController::class, 'importPreview'])
        ->name('import.preview');

    Route::post('/import/confirm', [TableCController::class, 'importConfirm'])
        ->name('import.confirm');

    Route::get('/export/csv', [TableCController::class, 'exportCsv'])->name('export.csv');
    Route::get('/export/pdf', [TableCController::class, 'exportPdf'])->name('export.pdf');
});

/*
|--------------------------------------------------------------------------
| TABLE D (SALES)
|--------------------------------------------------------------------------
*/
Route::prefix('table-d')->name('table-d.')->group(function () {

    Route::get('/create', [TableDController::class, 'create'])->name('create');
    Route::post('/store', [TableDController::class, 'store'])->name('store');

    Route::get('/{id}/edit', [TableDController::class, 'edit'])->name('edit');
    Route::put('/{id}', [TableDController::class, 'update'])->name('update');
    Route::delete('/{id}', [TableDController::class, 'destroy'])->name('destroy');

    Route::post('/bulk-delete', [TableDController::class, 'bulkDelete'])->name('bulk-delete');

    Route::get('/import/preview', function () {
        return redirect()->route('table-d.create');
    });

    Route::post('/import/preview', [TableDController::class, 'importPreview'])
        ->name('import.preview');

    Route::post('/import/confirm', [TableDController::class, 'importConfirm'])
        ->name('import.confirm');

    Route::get('/export/csv', [TableDController::class, 'exportCsv'])->name('export.csv');
    Route::get('/export/pdf', [TableDController::class, 'exportPdf'])->name('export.pdf');
});
