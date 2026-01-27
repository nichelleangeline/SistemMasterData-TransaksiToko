@extends('layouts.app')

@section('content')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<div class="p-6 bg-[#F8FAFC] min-h-screen font-inter" x-data="{ modalOpen: false, kpiTitle: '', kpiType: '', kpiInfo: '' }">
    
    <div class="mb-6 flex justify-between items-center">
        <div>
            <h2 class="text-2xl font-extrabold text-slate-800 uppercase tracking-tighter">Dashboard KPI</h2>
            <p class="text-slate-500 text-sm font-medium">Data Terintegrasi dari Table A, B, C, dan D</p>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4 mb-8">
        @php
            $kpis = [
                ['label' => 'Total Revenue', 'val' => 'Rp '.number_format($totalSales,0,',','.'), 'type' => 'revenue', 'info' => 'Total nominal transaksi dari Table B.', 'color' => 'blue'],
                ['label' => 'Market Penetration', 'val' => round($penetration,1).'%', 'type' => 'penetration', 'info' => 'Persentase Toko Aktif vs Total Master Toko.', 'color' => 'emerald'],
                ['label' => 'Avg Order Value', 'val' => 'Rp '.number_format($aov,0,',','.'), 'type' => 'aov', 'info' => 'Rata-rata nilai belanja per toko.', 'color' => 'violet'],
                ['label' => 'Uncovered Market', 'val' => $uncovered.' Toko', 'type' => 'uncovered', 'info' => 'Toko di Master (A) yang belum belanja di (B).', 'color' => 'rose'],
                ['label' => 'Legacy Trx Use', 'val' => $legacyCount, 'type' => 'legacy', 'info' => 'Transaksi yang masih memakai Kode Toko Lama.', 'color' => 'amber'],
                ['label' => 'Active Stores', 'val' => $tokoAktif, 'type' => 'active', 'info' => 'Jumlah toko unik yang bertransaksi.', 'color' => 'sky'],
                ['label' => 'Total Master', 'val' => $totalToko, 'type' => 'master', 'info' => 'Total baris data di Table A.', 'color' => 'slate'],
                ['label' => 'VIP Customers', 'val' => $vipCount, 'type' => 'vip', 'info' => 'Toko dengan belanja di atas rata-rata.', 'color' => 'indigo'],
                ['label' => 'Sales Personnel', 'val' => $salesCount, 'type' => 'sales', 'info' => 'Jumlah tim sales aktif di Table D.', 'color' => 'teal'],
                ['label' => 'Data Integrity', 'val' => round($integrity,1).'%', 'type' => 'integrity', 'info' => 'Persentase kesuksesan mapping kode baru.', 'color' => 'pink'],
            ];
        @endphp

        @foreach($kpis as $k)
        <div @click="modalOpen = true; kpiTitle = '{{ $k['label'] }}'; kpiType = '{{ $k['type'] }}'; kpiInfo = '{{ $k['info'] }}'" 
             class="bg-white p-5 rounded-xl border border-slate-200 shadow-sm hover:border-{{ $k['color'] }}-500 cursor-pointer transition-all group">
            <div class="flex items-center justify-between mb-2">
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest group-hover:text-{{ $k['color'] }}-500">{{ $k['label'] }}</span>
                <span class="cursor-help text-slate-300 hover:text-slate-600" title="{{ $k['info'] }}">
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
                </span>
            </div>
            <h3 class="text-xl font-black text-slate-800 tracking-tight">{{ $k['val'] }}</h3>
            <p class="text-[9px] text-slate-400 mt-2 font-medium italic">Klik untuk raw data</p>
        </div>
        @endforeach
    </div>

    <div class="grid grid-cols-12 gap-6 mb-8">
        <div class="col-span-12 lg:col-span-8 bg-white p-6 rounded-xl border border-slate-200 shadow-sm">
            <h3 class="font-bold text-slate-800 mb-6 uppercase text-xs tracking-widest">Store Distribution by Area</h3>
            <canvas id="mainChart" height="110"></canvas>
        </div>
        <div class="col-span-12 lg:col-span-4 bg-white p-6 rounded-xl border border-slate-200 shadow-sm">
            <h3 class="font-bold text-slate-800 mb-6 uppercase text-xs tracking-widest">Market Status</h3>
            <canvas id="statusChart"></canvas>
        </div>
    </div>

    <div x-show="modalOpen" class="fixed inset-0 z-[9999] flex items-center justify-center bg-slate-900/60 backdrop-blur-sm p-4" x-cloak>
        <div class="bg-white rounded-2xl w-full max-w-4xl shadow-2xl overflow-hidden shadow-black/20" @click.away="modalOpen = false">
            <div class="p-6 border-b flex justify-between items-center bg-slate-50">
                <div>
                    <h2 class="text-xl font-black text-slate-800 uppercase tracking-tight" x-text="kpiTitle"></h2>
                    <p class="text-xs text-slate-500 font-medium" x-text="kpiInfo"></p>
                </div>
                <button @click="modalOpen = false" class="text-slate-400 hover:text-black text-3xl font-light">&times;</button>
            </div>
            
            <div class="p-8">
                <div class="flex gap-3 mb-6">
                    <a :href="'/export-raw/' + kpiType" class="bg-slate-900 text-white px-5 py-2.5 rounded-lg text-[10px] font-bold uppercase tracking-widest hover:bg-black transition-all">Download CSV (Excel)</a>
                    <button onclick="window.print()" class="bg-slate-100 text-slate-600 px-5 py-2.5 rounded-lg text-[10px] font-bold uppercase tracking-widest hover:bg-slate-200 transition-all">Download PDF</button>
                </div>

                <div class="rounded-xl border border-slate-100 overflow-hidden">
                    <table class="w-full text-left border-collapse">
                        <thead class="bg-slate-50 text-[10px] font-bold uppercase text-slate-400">
                            <tr>
                                <th class="p-4 border-b">ID Toko (Raw)</th>
                                <th class="p-4 border-b text-center">System Mapping ID</th>
                                <th class="p-4 border-b text-right">Nominal</th>
                            </tr>
                        </thead>
                        <tbody class="text-sm font-medium">
                            @foreach($rawPreview as $row)
                            <tr class="hover:bg-slate-50 border-b border-slate-50 last:border-0">
                                <td class="p-4 text-slate-600 font-mono">{{ $row->kode_toko }}</td>
                                <td class="p-4 text-center font-bold text-slate-900">{{ $row->id_resmi ?? '???' }}</td>
                                <td class="p-4 text-right font-black text-slate-900 text-base">Rp {{ number_format($row->nominal_transaksi, 0, ',', '.') }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <p class="mt-4 text-[10px] text-slate-400 text-center font-medium italic">* Menampilkan 10 transaksi dengan nominal tertinggi untuk verifikasi cepat.</p>
            </div>
        </div>
    </div>
</div>

<script>
    // Bar Chart
    new Chart(document.getElementById('mainChart'), {
        type: 'bar',
        data: {
            labels: {!! json_encode($areaStats->pluck('area_sales')) !!},
            datasets: [{
                label: 'Jumlah Toko',
                data: {!! json_encode($areaStats->pluck('jml_toko')) !!},
                backgroundColor: '#3C50E0',
                borderRadius: 5
            }]
        },
        options: { plugins: { legend: { display: false } } }
    });

    // Donut Chart
    new Chart(document.getElementById('statusChart'), {
        type: 'doughnut',
        data: {
            labels: ['Aktif', 'Inaktif'],
            datasets: [{
                data: [{{ $tokoAktif }}, {{ $uncovered }}],
                backgroundColor: ['#10B981', '#F1F5F9'],
                borderWidth: 0
            }]
        },
        options: { cutout: '80%', plugins: { legend: { position: 'bottom' } } }
    });
</script>
@endsection