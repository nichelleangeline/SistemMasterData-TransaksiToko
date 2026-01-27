@extends('layouts.app')

@section('content')
<x-common.page-breadcrumb pageTitle="Edit Mapping Kode Toko" />

<x-common.component-card title="Form Edit Data">
    <form method="POST"
          action="{{ route('table-a.update', $data->kode_toko_baru) }}"
          class="space-y-5">
        @csrf
        @method('PUT')

        {{-- KODE TOKO BARU (READ ONLY) --}}
        <div>
            <label class="block mb-1 text-sm font-medium">
                Kode Toko Baru
            </label>
            <input
                type="number"
                value="{{ $data->kode_toko_baru }}"
                disabled
                class="w-full rounded-lg border bg-gray-100 px-4 py-2 text-gray-600 cursor-not-allowed"
            />
            <p class="mt-1 text-sm text-gray-500">
                Kode toko baru tidak dapat diubah
            </p>
        </div>

        {{-- KODE TOKO LAMA --}}
        <div>
            <label class="block mb-1 text-sm font-medium">
                Kode Toko Lama
            </label>
            <input
                type="number"
                name="kode_toko_lama"
                value="{{ old('kode_toko_lama', $data->kode_toko_lama) }}"
                class="w-full rounded-lg border px-4 py-2"
            />
            @error('kode_toko_lama')
                <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="flex gap-3">
            <button type="submit"
                class="rounded-lg bg-brand-500 px-5 py-2 text-white">
                Update
            </button>

            <a href="{{ route('basic-tables') }}"
               class="rounded-lg border px-5 py-2">
                Batal
            </a>
        </div>
    </form>
</x-common.component-card>
@endsection
