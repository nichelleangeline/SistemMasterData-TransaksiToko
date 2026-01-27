@extends('layouts.app')

@section('content')

<x-common.page-breadcrumb pageTitle="Tambah Data Table D" />

@if (session('error'))
    <div class="mb-4 rounded-lg border border-red-300 bg-red-100 px-4 py-3 text-red-700">
        {{ session('error') }}
    </div>
@endif

@if (session('success'))
    <div class="mb-4 rounded-lg border border-green-300 bg-green-100 px-4 py-3 text-green-700">
        {{ session('success') }}
    </div>
@endif

@if ($errors->any())
    <div class="mb-4 rounded-lg border border-red-300 bg-red-50 px-4 py-3 text-red-700">
        <ul class="list-disc list-inside text-sm">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="flex justify-end mb-4">
    <button
        type="button"
        onclick="document.getElementById('importCsv').classList.toggle('hidden')"
        class="rounded-lg bg-gray-900 px-4 py-2 text-white">
        Import CSV
    </button>
</div>

<x-common.component-card title="Form Input Manual">
    <form method="POST" action="{{ route('table-d.store') }}" class="space-y-5">
        @csrf

        <div>
            <label class="text-sm font-medium">Kode Sales</label>
            <input
                type="text"
                name="kode_sales"
                value="{{ old('kode_sales') }}"
                required
                class="w-full rounded-lg border px-4 py-2">
            @error('kode_sales')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label class="text-sm font-medium">Nama Sales</label>
            <input
                type="text"
                name="nama_sales"
                value="{{ old('nama_sales') }}"
                required
                class="w-full rounded-lg border px-4 py-2">
            @error('nama_sales')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <button
            type="submit"
            class="rounded-lg bg-brand-500 px-5 py-2 text-white">
            Simpan
        </button>
    </form>
</x-common.component-card>

<div id="importCsv" class="{{ session('previewData') || session('hasError') || session('error') ? '' : 'hidden' }} mt-6">
<x-common.component-card title="Import CSV Table D">

    <form
        method="POST"
        action="{{ route('table-d.import.preview') }}"
        enctype="multipart/form-data"
        class="space-y-4">
        @csrf

        <input
            type="file"
            name="file"
            accept=".csv"
            required
            class="w-full rounded-lg border px-4 py-2">

        <p class="text-sm text-gray-500">
            Header wajib: <b>kode_sales,nama_sales</b>
        </p>

        <button
            type="submit"
            class="rounded-lg bg-gray-900 px-4 py-2 text-white">
            Preview CSV
        </button>
    </form>

    @if (session('previewData'))
        <hr class="my-4">

        @if (session('hasError'))
            <div class="mb-3 rounded-lg border border-red-300 bg-red-100 px-4 py-2 text-sm text-red-700">
                Ada data bermasalah. Perbaiki CSV sebelum import.
            </div>
        @endif

        <form method="POST" action="{{ route('table-d.import.confirm') }}">
            @csrf

            <input
                type="hidden"
                name="rows"
                value="{{ json_encode(session('previewData')) }}">

            <div class="overflow-x-auto">
                <table class="min-w-full border rounded-lg">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-2 text-center text-sm">Kode Sales</th>
                            <th class="px-4 py-2 text-center text-sm">Nama Sales</th>
                            <th class="px-4 py-2 text-center text-sm">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach (session('previewData') as $row)
                            <tr class="border-t {{ !empty($row['error']) ? 'bg-red-50' : '' }}">
                                <td class="px-4 py-2 text-center text-sm">
                                    {{ $row['kode_sales'] ?? '-' }}
                                </td>
                                <td class="px-4 py-2 text-center text-sm">
                                    {{ $row['nama_sales'] ?? '-' }}
                                </td>
                                <td class="px-4 py-2 text-center text-sm">
                                    @if (!empty($row['error']))
                                        <span class="text-red-600 font-semibold">
                                            {{ $row['error'] }}
                                        </span>
                                    @else
                                        <span class="text-green-600 font-semibold">
                                            OK
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <button
                type="submit"
                class="mt-4 rounded-lg bg-green-600 px-4 py-2 text-white"
                {{ session('hasError') ? 'disabled' : '' }}>
                Confirm Import
            </button>
        </form>
    @endif

</x-common.component-card>
</div>

@endsection
