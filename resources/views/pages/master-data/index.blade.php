@extends('layouts.app')

@section('content')
<x-common.page-breadcrumb pageTitle="Master Data" />

<div class="space-y-6">
    <x-common.component-card title="Table A">
        <x-tables.master.table-a :data="$tableA"/>
    </x-common.component-card>
</div>
@endsection
