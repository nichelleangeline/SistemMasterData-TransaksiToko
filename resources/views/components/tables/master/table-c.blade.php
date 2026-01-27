<form method="POST" action="{{ route('table-c.bulk-delete') }}" class="space-y-3">
@csrf
<div class="flex gap-2">
  <form action="{{ route('table-c.store') }}" method="POST">@csrf
    <input name="kode_toko" placeholder="Kode Toko" class="border p-2">
    <input name="area_sales" placeholder="Area" class="border p-2">
    <button class="btn-primary">Create</button>
  </form>

  <form action="{{ route('table-c.import') }}" method="POST" enctype="multipart/form-data">@csrf
    <input type="file" name="file" required>
    <button class="btn-secondary">Upload Excel</button>
  </form>

  <a href="{{ route('table-c.export.excel') }}" class="btn-success">Excel</a>
  <a href="{{ route('table-c.export.pdf') }}" class="btn-danger">PDF</a>
  <button class="btn-warning">Delete Selected</button>
</div>

<table class="w-full">
<thead>
<tr>
  <th><input type="checkbox" onclick="toggleAll(this)"></th>
  <th>Kode Toko</th>
  <th>Area</th>
</tr>
</thead>
<tbody>
@foreach($data as $r)
<tr>
  <td><input type="checkbox" name="ids[]" value="{{ $r->kode_toko }}"></td>
  <td>{{ $r->kode_toko }}</td>
  <td>{{ $r->area_sales }}</td>
</tr>
@endforeach
</tbody>
</table>
</form>
