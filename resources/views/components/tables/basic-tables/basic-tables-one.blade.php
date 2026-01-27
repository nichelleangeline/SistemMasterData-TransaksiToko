@props(['tableA'])

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
        <h3 class="text-lg font-semibold">Mapping Kode Toko</h3>

        <div class="flex gap-2">
            <a href="{{ route('table-a.create') }}"
               class="rounded-lg bg-green-600 px-4 py-2 text-sm text-white hover:bg-green-700">
                + Tambah Data
            </a>

            <a href="{{ route('table-a.export.csv') }}"
               class="rounded-lg bg-gray-900 px-4 py-2 text-sm text-white">
                Export CSV
            </a>

            <a href="{{ route('table-a.export.pdf') }}"
               class="rounded-lg bg-gray-900 px-4 py-2 text-sm text-white">
                Export PDF
            </a>
        </div>
    </div>

    {{-- BULK DELETE --}}
    <form
        x-ref="bulkForm"
        method="POST"
        action="{{ route('table-a.bulk-delete') }}"
        onsubmit="return confirm('Hapus data terpilih?')"
    >
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
                        <th class="px-6 py-3 text-left text-xs text-gray-500">
                            {!! sortLink('Kode Toko Baru', 'kode_toko_baru') !!}
                        </th>
                        <th class="px-6 py-3 text-left text-xs text-gray-500">
                            {!! sortLink('Kode Toko Lama', 'kode_toko_lama') !!}
                        </th>
                        <th class="px-6 py-3 text-left text-xs text-gray-500">
                            Action
                        </th>
                    </tr>
                </thead>

                <tbody class="divide-y">
                    @forelse ($tableA as $row)
                    <tr>
                        <td class="px-6 py-3">
                            <input
                                type="checkbox"
                                class="row-checkbox"
                                value="{{ $row->kode_toko_baru }}"
                                x-model="selected"
                            >
                        </td>

                        <td class="px-6 py-3 text-sm font-medium">
                            {{ $row->kode_toko_baru }}
                        </td>

                        <td class="px-6 py-3 text-sm text-gray-500">
                            {{ $row->kode_toko_lama ?? '-' }}
                        </td>

                        <td class="px-6 py-3">
                            <div class="flex gap-2">
                                <a href="{{ route('table-a.edit',$row->kode_toko_baru) }}"
                                   class="rounded bg-yellow-400 px-3 py-1.5 text-xs">
                                    Edit
                                </a>

                                <button
                                    type="button"
                                    @click="singleDelete('{{ $row->kode_toko_baru }}')"
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
            <button
                type="submit"
                x-show="selected.length > 0"
                class="rounded-lg bg-red-700 px-4 py-2 text-sm text-white">
                Hapus Terpilih (<span x-text="selected.length"></span>)
            </button>
        </div>
    </form>

    <div class="px-6 py-4 border-t">
        {{ $tableA->links() }}
    </div>
</div>
