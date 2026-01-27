@extends('layouts.app')

@section('content')

<x-common.page-breadcrumb pageTitle="Tambah Mapping Kode Toko" />

@if (session('error'))
    <div class="mb-4 rounded-lg bg-red-100 border border-red-300 px-4 py-3 text-red-700">
        {{ session('error') }}
    </div>
@endif

@if (session('success'))
    <div class="mb-4 rounded-lg bg-green-100 border border-green-300 px-4 py-3 text-green-700">
        {{ session('success') }}
    </div>
@endif

@if ($errors->any())
    <div class="mb-4 rounded-lg bg-red-50 border border-red-300 px-4 py-3 text-red-700">
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
    <form method="POST" action="{{ route('table-a.store') }}" class="space-y-5">
        @csrf

        <div>
            <label class="block mb-1 text-sm font-medium">
                Kode Toko Baru
            </label>
            <input
                type="number"
                name="kode_toko_baru"
                value="{{ old('kode_toko_baru') }}"
                required
                class="w-full rounded-lg border px-4 py-2"
            >
            @error('kode_toko_baru')
                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label class="block mb-1 text-sm font-medium">
                Kode Toko Lama
            </label>
            <input
                type="number"
                name="kode_toko_lama"
                value="{{ old('kode_toko_lama') }}"
                class="w-full rounded-lg border px-4 py-2"
            >
            @error('kode_toko_lama')
                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
            @enderror
        </div>

        <button
            type="submit"
            class="rounded-lg bg-brand-500 px-5 py-2 text-white">
            Simpan
        </button>
    </form>
</x-common.component-card>

<div id="importCsv" class="{{ session('previewData') ? '' : 'hidden' }} mt-6">

<x-common.component-card title="Import CSV">

    {{-- UPLOAD & PREVIEW --}}
    <form
        method="POST"
        action="{{ route('table-a.import.preview') }}"
        enctype="multipart/form-data"
        class="space-y-4">
        @csrf

        <div>
            <label class="block mb-1 text-sm font-medium">
                Upload File CSV
            </label>

            <input
                type="file"
                name="file"
                accept=".csv"
                required
                class="w-full rounded-lg border px-4 py-2"
            >

            <p class="mt-1 text-sm text-gray-500">
                Header CSV wajib:
                <b>kode_toko_baru,kode_toko_lama</b>
            </p>

            @error('file')
                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
            @enderror
        </div>

        <button
            type="submit"
            class="rounded-lg bg-gray-900 px-4 py-2 text-white">
            Preview CSV
        </button>
    </form>

    @if (session('previewData'))
        <hr class="my-4">

        <form method="POST" action="{{ route('table-a.import.confirm') }}">
            @csrf

            <input
                type="hidden"
                name="rows"
                value="{{ json_encode(session('previewData')) }}"
            >

            <div class="overflow-x-auto">
                <table class="min-w-full border rounded-lg">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-2 text-left text-sm">Kode Toko Baru</th>
                            <th class="px-4 py-2 text-left text-sm">Kode Toko Lama</th>
                            <th class="px-4 py-2 text-left text-sm">Status</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach (session('previewData') as $row)
                            <tr class="border-t {{ !empty($row['error']) ? 'bg-red-50' : '' }}">
                                <td class="px-4 py-2 text-sm">
                                    {{ $row['kode_toko_baru'] ?? '-' }}
                                </td>
                                <td class="px-4 py-2 text-sm">
                                    {{ $row['kode_toko_lama'] ?? '-' }}
                                </td>
                                <td class="px-4 py-2 text-sm">
                                    @if (!empty($row['error']))
                                        <span class="text-red-600 font-medium">
                                            {{ $row['error'] }}
                                        </span>
                                    @else
                                        <span class="text-green-600 font-medium">
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

            @if (session('hasError'))
                <p class="mt-2 text-sm text-red-600">
                    Masih ada data error. Perbaiki CSV sebelum import.
                </p>
            @endif

        </form>
    @endif

</x-common.component-card>
</div>

@endsection
