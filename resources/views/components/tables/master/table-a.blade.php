<div class="mb-4 flex gap-2">
    <form action="{{ route('table-a.import') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <input type="file" name="file" required>
        <button class="btn">Import CSV</button>
    </form>

    <a href="{{ route('table-a.export.pdf') }}" class="btn">Export PDF</a>
</div>

<form method="POST" action="{{ route('table-a.bulk-delete') }}">
@csrf

<table class="min-w-full">
    <thead>
        <tr>
            <th><input type="checkbox" onclick="toggle(this)"></th>
            <th>Kode Toko Baru</th>
            <th>Kode Toko Lama</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        @foreach($data as $row)
        <tr>
            <td>
                <input type="checkbox" name="ids[]" value="{{ $row->kode_toko_baru }}">
            </td>
            <td>{{ $row->kode_toko_baru }}</td>
            <td>{{ $row->kode_toko_lama }}</td>
            <td>
                <form method="POST" action="{{ route('table-a.delete') }}">
                    @csrf
                    <input type="hidden" name="id" value="{{ $row->kode_toko_baru }}">
                    <button onclick="return confirm('Hapus?')">🗑</button>
                </form>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>

<button class="btn-danger mt-2">Delete Selected</button>
</form>

<script>
function toggle(source){
    document.querySelectorAll('input[name="ids[]"]').forEach(cb => cb.checked = source.checked);
}
</script>
