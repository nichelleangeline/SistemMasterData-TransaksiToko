@props(['tableC'])

@php
    function sortLink($label, $field) {
        $currentSort = request('sort');
        $currentDir  = request('dir', 'asc');
        $dir  = ($currentSort === $field && $currentDir === 'asc') ? 'desc' : 'asc';
        $icon = $currentSort === $field ? ($currentDir === 'asc' ? '↑' : '↓') : '';
        $url = request()->fullUrlWithQuery(['sort'=>$field,'dir'=>$dir]);
        return '<a href="'.$url.'" class="flex items-center gap-1 hover:underline">'.$label.' '.$icon.'</a>';
    }
@endphp

<div x-data="{selected:[],toggleAll(e){this.selected=e.target.checked?[...document.querySelectorAll('.row-checkbox')].map(cb=>cb.value):[]},singleDelete(id){this.selected=[id];this.$nextTick(()=>this.$refs.bulkForm.submit())}}" class="overflow-hidden rounded-2xl border bg-white">

<div class="flex justify-between px-6 py-4 border-b">
    <h3 class="text-lg font-semibold">Table C – Area Sales</h3>
    <div class="flex gap-2">
        <a href="{{ route('table-c.create') }}" class="bg-green-600 text-white px-4 py-2 rounded">+ Tambah Data</a>
        <a href="{{ route('table-c.export.csv') }}" class="bg-gray-900 text-white px-4 py-2 rounded">Export CSV</a>
        <a href="{{ route('table-c.export.pdf') }}" class="bg-gray-900 text-white px-4 py-2 rounded">Export PDF</a>
    </div>
</div>

<form x-ref="bulkForm" method="POST" action="{{ route('table-c.bulk-delete') }}">
@csrf
<template x-for="id in selected"><input type="hidden" name="ids[]" :value="id"></template>

<table class="min-w-full">
<thead>
<tr class="border-y">
<th class="px-6"><input type="checkbox" @change="toggleAll"></th>
<th class="px-6">{!! sortLink('Kode Toko','kode_toko') !!}</th>
<th class="px-6">{!! sortLink('Area Sales','area_sales') !!}</th>
<th class="px-6">Action</th>
</tr>
</thead>
<tbody>
@foreach($tableC as $row)
<tr>
<td class="px-6"><input type="checkbox" class="row-checkbox" value="{{ $row->kode_toko }}" x-model="selected"></td>
<td class="px-6">{{ $row->kode_toko }}</td>
<td class="px-6">{{ $row->area_sales }}</td>
<td class="px-6">
<button @click="singleDelete('{{ $row->kode_toko }}')" type="button" class="bg-red-600 text-white px-3 py-1 rounded">Delete</button>
</td>
</tr>
@endforeach
</tbody>
</table>

<button x-show="selected.length" class="bg-red-700 text-white px-4 py-2 m-4 rounded">Hapus Terpilih</button>
</form>

{{ $tableC->links() }}
</div>
