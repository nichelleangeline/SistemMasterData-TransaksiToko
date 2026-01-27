<form method="POST" action="{{ route('table-b.bulk-delete') }}" class="space-y-3">
@csrf
<div class="flex gap-2">
  <form action="{{ route('table-b.store') }}" method="POST">@csrf
    <input name="kode_toko" placeholder="Kode Toko" class="border p-2">
    <input name="nominal_transaksi" placeholder="Nominal" class="border p-2">
    <button class="btn-primary">Create</button>
  </form>

  <form action="{{ route('table-b.import') }}" method="POST" enctype="multipart/form-data">@csrf
    <input type="file" name="file" required>
    <button class="btn-secondary">Upload Excel</button>
  </form>

  <a href="{{ route('table-b.export.excel') }}" class="btn-success">Excel</a>
  <a href="{{ route('table-b.export.pdf') }}" class="btn-danger">PDF</a>
  <button class="btn-warning">Delete Selected</button>
</div>

<table class="w-full">
<thead>
<tr>
  <th><input type="checkbox" onclick="toggleAll(this)"></th>
  <th>Kode Toko</th>
  <th>Nominal</th>
</tr>
</thead>
<tbody>
@foreach($data as $r)
<tr>
  <td><input type="checkbox" name="ids[]" value="{{ $r->kode_toko }}"></td>
  <td>{{ $r->kode_toko }}</td>
  <td>{{ $r->nominal_transaksi }}</td>
</tr>
@endforeach
</tbody>
</table>
</form>
