@extends('layouts.app')

@section('content')
<x-common.page-breadcrumb pageTitle="Import CSV - Table B" />

<x-common.component-card title="Upload CSV">
<form method="POST"
      action="{{ route('table-b.import.preview') }}"
      enctype="multipart/form-data">
@csrf

<div>
    <label class="block mb-1 text-sm font-medium">Upload CSV</label>
    <input type="file" name="file" accept=".csv"
        class="w-full rounded-lg border px-4 py-2" required>
    <p class="text-sm text-gray-500 mt-1">
        Header wajib: <b>kode_toko,nominal_transaksi</b>
    </p>
</div>

<button class="mt-3 rounded-lg bg-brand-500 px-5 py-2 text-white">
    Preview
</button>
</form>
</x-common.component-card>

@if(isset($data))
<x-common.component-card title="Preview Data CSV">
<form method="POST" action="{{ route('table-b.import.confirm') }}">
@csrf
<input type="hidden" name="rows" value="{{ json_encode($data) }}">

<div class="overflow-x-auto">
<table class="min-w-full">
<thead>
<tr class="border-y border-gray-100">
    <th class="px-6 py-3 text-theme-xs">Kode Toko</th>
    <th class="px-6 py-3 text-theme-xs">Nominal Transaksi</th>
    <th class="px-6 py-3 text-theme-xs">Status</th>
</tr>
</thead>

<tbody class="divide-y">
@foreach($data as $row)
<tr class="{{ $row['error'] ? 'bg-red-50' : '' }}">
    <td class="px-6 py-3">{{ $row['kode_toko'] }}</td>
    <td class="px-6 py-3">{{ $row['nominal_transaksi'] }}</td>
    <td class="px-6 py-3 text-sm">
        @if($row['error'])
            <span class="text-red-600">{{ $row['error'] }}</span>
        @else
            <span class="text-green-600">OK</span>
        @endif
    </td>
</tr>
@endforeach
</tbody>
</table>
</div>

<div class="mt-4 flex gap-3">
    <button class="rounded-lg bg-brand-500 px-5 py-2 text-white">
        Confirm Import
    </button>

    <a href="{{ route('basic-tables') }}"
       class="rounded-lg border px-5 py-2">
        Batal
    </a>
</div>
</form>
</x-common.component-card>
@endif
@endsection
