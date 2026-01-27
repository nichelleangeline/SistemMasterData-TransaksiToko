@extends('layouts.app')

@section('content')
  <div class="grid grid-cols-12 gap-4 md:gap-6">
    {{-- Metrics: Kirim total revenue dan jumlah toko --}}
    <div class="col-span-12 space-y-6 xl:col-span-7">
      <x-ecommerce.ecommerce-metrics 
        :total-revenue="$totalRevenue" 
        :total-stores="$activeStoresCount" 
      />
      
      {{-- Chart berdasarkan Area --}}
      <x-ecommerce.monthly-sale :chart-data="$salesByArea" />
    </div>

    <div class="col-span-12 xl:col-span-5">
        <x-ecommerce.monthly-target />
    </div>

    {{-- List Transaksi Terakhir --}}
    <div class="col-span-12">
      <x-ecommerce.recent-orders :orders="$recentTransactions" />
    </div>
  </div>
@endsection