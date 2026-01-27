@props([
    'title',
    'data',
    'columns',     // ['field' => 'Label']
    'routes',      // ['create','edit','delete','bulk','exportCsv','exportPdf']
    'primaryKey',
])

@php
$sortLink = function ($label, $field) {
    $currentSort = request('sort');
    $currentDir  = request('dir', 'asc');

    $dir  = ($currentSort === $field && $currentDir === 'asc') ? 'desc' : 'asc';
    $icon = $currentSort === $field ? ($currentDir === 'asc' ? '↑' : '↓') : '';

    $url = request()->fullUrlWithQuery([
        'sort' => $field,
        'dir'  => $dir,
    ]);

    return '<a href="'.$url.'" class="inline-flex items-center gap-1 hover:underline">'
        .$label.' '.$icon.
        '</a>';
};

$alignClass = function ($field) {
    return str_contains($field, 'nama')
        ? 'text-left'
        : 'text-center';
};
@endphp

<div
    x-data="{
        selected: [],
        toggleAll(e) {
            this.selected = e.target.checked
                ? [...document.querySelectorAll('.row-checkbox')].map(cb => cb.value)
                : [];
        }
    }"
    class="overflow-hidden rounded-2xl border border-gray-200 bg-white"
>

    {{-- HEADER --}}
    <div class="flex flex-col gap-4 px-6 py-4 border-b sm:flex-row sm:items-center sm:justify-between">
        <h3 class="text-lg font-semibold">{{ $title }}</h3>

        <div class="flex gap-2 flex-wrap">
            {{-- CREATE --}}
            @if(isset($routes['create']))
            <a href="{{ $routes['create'] }}"
               class="rounded-lg bg-green-600 px-4 py-2 text-sm text-white hover:bg-green-700">
                + Tambah Data
            </a>
            @endif

            {{-- EXPORT --}}
            @if(isset($routes['exportCsv']))
            <a href="{{ $routes['exportCsv'] }}"
               class="rounded-lg bg-gray-900 px-4 py-2 text-sm text-white">
                Export CSV
            </a>
            @endif

            @if(isset($routes['exportPdf']))
            <a href="{{ $routes['exportPdf'] }}"
               class="rounded-lg bg-gray-900 px-4 py-2 text-sm text-white">
                Export PDF
            </a>
            @endif
        </div>
    </div>

    {{-- BULK DELETE FORM --}}
    <form method="POST"
          action="{{ $routes['bulk'] }}"
          onsubmit="return confirm('Hapus data terpilih?')">
        @csrf

        <template x-for="id in selected" :key="id">
            <input type="hidden" name="ids[]" :value="id">
        </template>

        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead>
                    <tr class="border-y">
                        {{-- CHECKBOX HEADER --}}
                        <th class="px-6 py-3 text-center align-middle">
                            <input type="checkbox" @change="toggleAll">
                        </th>

                        @foreach($columns as $field => $label)
                        <th class="px-6 py-3 text-xs text-gray-500 {{ $alignClass($field) }}">
                            <div class="{{ str_contains($field,'nama') ? 'flex justify-start' : 'flex justify-center' }}">
                                {!! $sortLink($label, $field) !!}
                            </div>
                        </th>
                        @endforeach

                        <th class="px-6 py-3 text-center text-xs text-gray-500">
                            Action
                        </th>
                    </tr>
                </thead>

                <tbody class="divide-y">
                    @forelse ($data as $row)
                    <tr class="hover:bg-gray-50">
                        {{-- CHECKBOX --}}
                        <td class="px-6 py-3 text-center align-middle">
                            <input
                                type="checkbox"
                                class="row-checkbox"
                                value="{{ $row->{$primaryKey} }}"
                                x-model="selected"
                            >
                        </td>

                        @foreach($columns as $field => $label)
                        <td class="px-6 py-3 text-sm {{ $alignClass($field) }}">
                            {{ $row->{$field} ?? '-' }}
                        </td>
                        @endforeach

                        {{-- ACTION --}}
                        <td class="px-6 py-3 text-center">
                            <div class="flex justify-center gap-2">
                                {{-- EDIT --}}
                                <a href="{{ str_replace(':id', $row->{$primaryKey}, $routes['edit']) }}"
                                   class="rounded bg-yellow-400 px-3 py-1.5 text-xs">
                                    Edit
                                </a>

                                {{-- DELETE --}}
                                <form method="POST"
                                      action="{{ str_replace(':id', $row->{$primaryKey}, $routes['delete']) }}"
                                      onsubmit="return confirm('Yakin ingin menghapus data ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button
                                        type="submit"
                                        class="rounded bg-red-600 px-3 py-1.5 text-xs text-white">
                                        Hapus
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="{{ count($columns) + 2 }}"
                            class="px-6 py-6 text-center text-sm text-gray-500">
                            Data tidak ditemukan
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- BULK DELETE BUTTON --}}
        <div class="px-6 py-4">
            <button
                x-show="selected.length > 0"
                class="rounded-lg bg-red-700 px-4 py-2 text-sm text-white">
                Hapus Terpilih (<span x-text="selected.length"></span>)
            </button>
        </div>
    </form>

    {{-- PAGINATION --}}
    <div class="px-6 py-4 border-t">
        {{ $data->links() }}
    </div>
</div>
