@props(['tableD'])

@php
    function sortLink($label,$field){
        $dir=request('dir','asc')==='asc'?'desc':'asc';
        return '<a href="'.request()->fullUrlWithQuery(['sort'=>$field,'dir'=>$dir]).'">'.$label.'</a>';
    }
@endphp

<div x-data="{selected:[],singleDelete(id){this.selected=[id];this.$refs.form.submit()}}" class="border rounded bg-white">

<div class="px-6 py-4 border-b flex justify-between">
<h3 class="font-semibold">Table D – Sales</h3>
<a href="{{ route('table-d.create') }}" class="bg-green-600 text-white px-4 py-2 rounded">+ Tambah</a>
</div>

<form x-ref="form" method="POST" action="{{ route('table-d.bulk-delete') }}">
@csrf
<template x-for="id in selected"><input type="hidden" name="ids[]" :value="id"></template>

<table class="min-w-full">
<thead>
<tr>
<th></th>
<th>{!! sortLink('Kode Sales','kode_sales') !!}</th>
<th>{!! sortLink('Nama Sales','nama_sales') !!}</th>
<th>Action</th>
</tr>
</thead>
<tbody>
@foreach($tableD as $row)
<tr>
<td><input type="checkbox" value="{{ $row->kode_sales }}" x-model="selected"></td>
<td>{{ $row->kode_sales }}</td>
<td>{{ $row->nama_sales }}</td>
<td>
<button type="button" @click="singleDelete('{{ $row->kode_sales }}')" class="bg-red-600 text-white px-2 py-1">Delete</button>
</td>
</tr>
@endforeach
</tbody>
</table>
</form>

{{ $tableD->links() }}
</div>
