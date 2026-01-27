@props(['tableB'])

@php
    function sortLink($label, $field) {
        $currentSort = request('sort');
        $currentDir  = request('dir', 'asc');

        $dir  = ($currentSort === $field && $currentDir === 'asc') ? 'desc' : 'asc';
        $icon = $currentSort === $field ? ($currentDir === 'asc' ? '↑' : '↓') : '';

        $url = request()->fullUrlWithQuery([
            'sort' => $field,
            'dir'  => $dir,
        ]);

        return '<a href="'.$url.'" class="flex items-center gap-1 hover:underline">'
                .$label.' '.$icon.
               '</a>';
    }
@endphp

<div
    x-data="{
        selected: [],
        toggleAll(e) {
            this.selected = e.target.checked
                ? [...document.querySelectorAll('.row-checkbox')].map(cb => cb.value)
                : [];
        },
        singleDelete(id) {
            this.selected = [id];
            this.$nextTick(() => this.$refs.bulkForm.submit());
        }
    }"
    class="overflow-hidden rounded-2xl border border-gray-200 bg-white"
>

    {{-- HEADER --}}
    <div class="flex flex-col gap-4 px-6 py-4 border-b sm:flex-row sm:items-center sm:justify-between">
        <h3 class="text-lg font-semibold">Table B – Nominal Transaksi</h3>

        <div class="flex gap-2">
            <a href="{{ route('table-b.create') }}"
               class="rounded-lg bg-green-600 px-4 py-2 text-sm text-white hover:bg-green-700">
                + Tambah Data
            </a>

            <a href="{{ route('table-b.export.csv') }}"
               class="rounded-lg bg-gray-900 px-4 py-2 text-sm text-white">
                Export CSV
            </a>

            <a href="{{ route('table-b.export.pdf') }}"
               class="rounded-lg bg-gray-900 px-4 py-2 text-sm text-white">
                Export PDF
            </a>
        </div>
    </div>


    {{-- BULK DELETE --}}
    <form x-ref="bulkForm" method="POST" action="{{ route('table-b.bulk-delete') }}">
        @csrf

        <template x-for="id in selected" :key="id">
            <input type="hidden" name="ids[]" :value="id">
        </template>

        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead>
                    <tr class="border-y">
                        <th class="px-6 py-3">
                            <input type="checkbox" @change="toggleAll">
                        </th>
                        <th class="px-6 py-3 text-xs text-gray-500">
                            {!! sortLink('Kode Toko','kode_toko') !!}
                        </th>
                        <th class="px-6 py-3 text-xs text-gray-500">
                            {!! sortLink('Nominal Transaksi','nominal_transaksi') !!}
                        </th>
                        <th class="px-6 py-3 text-xs text-gray-500">Action</th>
                    </tr>
                </thead>

                <tbody class="divide-y">
                    @forelse($tableB as $row)
                    <tr>
                        <td class="px-6 py-3">
                            <input type="checkbox"
                                   class="row-checkbox"
                                   value="{{ $row->kode_toko }}"
                                   x-model="selected">
                        </td>

                        <td class="px-6 py-3 text-sm font-medium">
                            {{ $row->kode_toko }}
                        </td>

                        <td class="px-6 py-3 text-sm">
                            {{ number_format($row->nominal_transaksi) }}
                        </td>

                        <td class="px-6 py-3">
                            <div class="flex gap-2">
                                <a href="{{ route('table-b.edit',$row->kode_toko) }}"
                                   class="rounded bg-yellow-400 px-3 py-1.5 text-xs">
                                    Edit
                                </a>

                                <button type="button"
                                        @click="singleDelete('{{ $row->kode_toko }}')"
                                        class="rounded bg-red-600 px-3 py-1.5 text-xs text-white">
                                    Delete
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-6 py-6 text-center text-sm text-gray-500">
                            Data tidak ditemukan
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="px-6 py-4">
            <button x-show="selected.length"
                    class="rounded-lg bg-red-700 px-4 py-2 text-sm text-white">
                Hapus Terpilih (<span x-text="selected.length"></span>)
            </button>
        </div>
    </form>

    <div class="px-6 py-4 border-t">
        {{ $tableB->links() }}
    </div>
</div>
