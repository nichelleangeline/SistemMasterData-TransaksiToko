@extends('layouts.app')

@section('content')
<div class="p-6">
    {{-- Breadcrumb --}}
    <x-common.page-breadcrumb pageTitle="Master Tables Management" />

    {{-- Notifikasi Sukses --}}
    @if(session('success'))
        <div class="mb-6 flex items-center p-4 bg-green-50 border-l-4 border-green-500 text-green-700 rounded shadow-sm" role="alert">
            <svg class="w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
            </svg>
            <p class="text-sm font-bold">{{ session('success') }}</p>
        </div>
    @endif

    {{-- Notifikasi Error --}}
    @if(session('error') || $errors->any())
        <div class="mb-6 flex items-center p-4 bg-red-50 border-l-4 border-red-500 text-red-700 rounded shadow-sm" role="alert">
            <svg class="w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
            </svg>
            <p class="text-sm font-bold">{{ session('error') ?: 'Terjadi kesalahan pada input data.' }}</p>
        </div>
    @endif

    <div class="space-y-12 pb-20">

        {{-- TABLE A --}}
        <x-tables.master-table
            title="Table A - Mapping Kode Toko"
            :data="$tableA"
            searchName="search_a"
            :columns="[
                'kode_toko_baru' => 'Kode Toko Baru', 
                'kode_toko_lama' => 'Kode Toko Lama'
            ]"
            :routes="[
                'create'    => route('table-a.create'), 
                'edit'      => route('table-a.edit', ':id'), 
                'delete'    => route('table-a.destroy', ':id'), 
                'bulk'      => route('table-a.bulk-delete'), 
                'exportCsv' => route('table-a.export.csv'), 
                'exportPdf' => route('table-a.export.pdf')
            ]"
            primaryKey="kode_toko_baru"
        />

        {{-- TABLE B --}}
        <x-tables.master-table
            title="Table B - Transaksi"
            :data="$tableB"
            searchName="search_b"
            :columns="[
                'kode_toko' => 'Kode Toko', 
                'nominal_transaksi' => 'Nominal Transaksi'
            ]"
            :routes="[
                'create'    => route('table-b.create'), 
                'edit'      => route('table-b.edit', ':id'), 
                'delete'    => route('table-b.destroy', ':id'), 
                'bulk'      => route('table-b.bulk-delete'), 
                'exportCsv' => route('table-b.export.csv'), 
                'exportPdf' => route('table-b.export.pdf')
            ]"
            primaryKey="kode_toko"
        />

        {{-- TABLE C --}}
        <x-tables.master-table
            title="Table C - Area Sales"
            :data="$tableC"
            searchName="search_c"
            :columns="[
                'kode_toko' => 'Kode Toko', 
                'area_sales' => 'Area Sales'
            ]"
            :routes="[
                'create'    => route('table-c.create'), 
                'edit'      => route('table-c.edit', ':id'), 
                'delete'    => route('table-c.destroy', ':id'), 
                'bulk'      => route('table-c.bulk-delete'), 
                'exportCsv' => route('table-c.export.csv'), 
                'exportPdf' => route('table-c.export.pdf')
            ]"
            primaryKey="kode_toko"
        />

        {{-- TABLE D --}}
        <x-tables.master-table
            title="Table D - Sales Personnel"
            :data="$tableD"
            searchName="search_d"
            :columns="[
                'kode_sales' => 'Kode Sales', 
                'nama_sales' => 'Nama Sales'
            ]"
            :routes="[
                'create'    => route('table-d.create'), 
                'edit'      => route('table-d.edit', ':id'), 
                'delete'    => route('table-d.destroy', ':id'), 
                'bulk'      => route('table-d.bulk-delete'), 
                'exportCsv' => route('table-d.export.csv'), 
                'exportPdf' => route('table-d.export.pdf')
            ]"
            primaryKey="kode_sales"
        />

    </div>
</div>
@endsection