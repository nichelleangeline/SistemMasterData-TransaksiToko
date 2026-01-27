<form method="POST" action="{{ route('table-d.bulk-delete') }}" class="space-y-3">
@csrf
<div class="flex gap-2">
  <form action="{{ route('table-d.store') }}" method="POST">@csrf
    <input name="kode_sales" placeholder="Kode Sales" class="border p-2">
    <input name="nama_sales" placeholder="Nama" class="border p-2">
    <button class="btn-primary">Create</button>
  </form>

  <form action="{{ route('table-d.import') }}" method="POST" enctype="multipart/form-data">@csrf
    <input type="file" name="file" required>
    <button class="btn-secondary">Upload Excel</button>
  </form>

  <a href="{{ route('table-d.export.excel') }}" class="btn-success">Excel</a>
  <a href="{{ route('table-d.export.pdf') }}" class="btn-danger">PDF</a>
  <button class="btn-warning">Delete Selected</button>
</div>

<table class="w-full">
<thead>
<tr>
  <th><input type="checkbox" onclick="toggleAll(this)"></th>
  <th>Kode Sales</th>
  <th>Nama</th>
</tr>
</thead>
<tbody>
@foreach($data as $r)
<tr>
  <td><input type="checkbox" name="ids[]" value="{{ $r->kode_sales }}"></td>
  <td>{{ $r->kode_sales }}</td>
  <td>{{ $r->nama_sales }}</td>
</tr>
@endforeach
</tbody>
</table>
</form>
