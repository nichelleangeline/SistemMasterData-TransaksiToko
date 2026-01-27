@extends('layouts.app')

@section('content')
<x-common.page-breadcrumb pageTitle="Edit Data Table B" />

<x-common.component-card title="Form Edit Data">
    <form method="POST"
          action="{{ route('table-b.update', $data->kode_toko) }}"
          class="space-y-5">
        @csrf
        @method('PUT')

        {{-- KODE TOKO (READ ONLY) --}}
        <div>
            <label class="block mb-1 text-sm font-medium">
                Kode Toko
            </label>
            <input
                type="number"
                value="{{ $data->kode_toko }}"
                disabled
                class="w-full rounded-lg border bg-gray-100 px-4 py-2 text-gray-600 cursor-not-allowed"
            />
            <p class="mt-1 text-sm text-gray-500">
                Kode toko tidak dapat diubah
            </p>
        </div>

        {{-- NOMINAL TRANSAKSI --}}
        <div>
            <label class="block mb-1 text-sm font-medium">
                Nominal Transaksi
            </label>
            <input
                type="number"
                name="nominal_transaksi"
                value="{{ old('nominal_transaksi', $data->nominal_transaksi) }}"
                class="w-full rounded-lg border px-4 py-2"
            />
            @error('nominal_transaksi')
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
